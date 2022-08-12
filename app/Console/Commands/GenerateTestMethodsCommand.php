<?php

namespace App\Console\Commands;

use Composer\XdebugHandler\XdebugHandler;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use PhpParser\BuilderFactory;
use PhpParser\Error;
use PhpParser\ErrorHandler\Collecting;
use PhpParser\JsonDecoder;
use PhpParser\Lexer\Emulative;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\NodeDumper;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\CloningVisitor;
use PhpParser\NodeVisitor\NodeConnectingVisitor;
use PhpParser\NodeVisitor\ParentConnectingVisitor;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class GenerateTestMethodsCommand extends Command
{
    protected $signature = 'generate:test-methods
                            {--in-dirs=* : Dirs to search for files}
                            {--not-paths=* : Paths to exclude from the search}
                            {--not-names=* : Names to exclude from the search}
                            {--parse-mode=1 : Parse mode of the PHP parser factory}
                            {--test-class-base-namespace=Tests\\Unit : Base namespace of the test class}
                            {--test-class-base-dirname=tests/Unit/ : Base dirname of the test class}
                            {--test-method-format=snake : Format of the test method}
                            {--default-test-class-path=tests/Unit/ExampleTest.php : Path of the default test class}';

    protected $description = 'Generate test methods.';

    /** @var array */
    private static $statistics = [
        'all_files' => 0,
        'all_classes' => 0,
        'related_classes' => 0,
        'added_methods' => 0
    ];
    /** @var \Symfony\Component\Finder\Finder */
    private $fileFinder;
    /** @var \PhpParser\Lexer\Emulative */
    private $lexer;
    /** @var \PhpParser\Parser */
    private $parser;
    /** @var \PhpParser\ErrorHandler\Collecting */
    private $errorHandler;
    /** @var \PhpParser\BuilderFactory */
    private $builderFactory;
    /** @var \PhpParser\NodeFinder */
    private $nodeFinder;
    /** @var \PhpParser\NodeDumper */
    private $nodeDumper;
    /** @var \PhpParser\JsonDecoder */
    private $jsonDecoder;
    /** @var \PhpParser\PrettyPrinter\Standard */
    private $prettyPrinter;
    /** @var \PhpParser\NodeTraverser */
    private $nodeTraverser;
    /** @var \PhpParser\NodeVisitor\ParentConnectingVisitor */
    private $parentConnectingVisitor;
    /** @var \PhpParser\NodeVisitor\NodeConnectingVisitor */
    private $nodeConnectingVisitor;
    /** @var \PhpParser\NodeVisitor\CloningVisitor */
    private $cloningVisitor;
    /** @var \PhpParser\NodeVisitorAbstract */
    private $classUpdatingVisitor;

    /** @var array */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->checkOptions();
        $this->initializeEnvs();
        $this->initializeProperties();
    }

    public function handle()
    {
        $this->withProgressBar($this->fileFinder, function (SplFileInfo $fileInfo) {
            try {
                $originalNodes = $this->parser->parse(file_get_contents($fileInfo->getRealPath()));
            } catch (Error $e) {
                $this->newLine();
                $this->error(sprintf("The file of %s parse error: %s.", $fileInfo->getRealPath(), $e->getMessage()));

                return;
            }

            $originalClassNodes = $this->nodeFinder->find($originalNodes, function (Node $node) {
                return ($node instanceof Class_ || $node instanceof Trait_) && $node->name;
            });
            /** @var Class_|Trait_ $originalClassNode */
            foreach ($originalClassNodes as $originalClassNode) {
                self::$statistics['all_classes']++;

                // 准备基本信息
                $testClassName = "{$originalClassNode->name->name}Test";
                $testClassBaseName = str_replace(app_path().DIRECTORY_SEPARATOR, '', $fileInfo->getPath());
                $testClassNamespace = Str::finish($this->option('test-class-base-namespace'), '\\').str_replace(DIRECTORY_SEPARATOR, '\\', $testClassBaseName);
                $testClassFullName = $testClassNamespace.'\\'.$testClassName;
                $testClassPath = Str::finish($this->option('test-class-base-dirname'), '/'). "$testClassBaseName/$testClassName.php";

                // 默认生成源类的全部方法节点
                $testClassDiffMethodNodes = array_map(function (ClassMethod $node) {
                    return tap(
                        $this->builderFactory
                        ->method(Str::{$this->option('test-method-format')}('test_' . Str::snake($node->name->name)))
                        ->makePublic()
                        ->getNode()
                    )->setAttribute('isAdded', true);
                }, array_filter($originalClassNode->getMethods(), function (ClassMethod $node) {
                    return $node->isPublic() && ! $node->isAbstract();
                }));
                $testClassDiffMethodNames = array_map(function (ClassMethod $node) {
                    return $node->name->name;
                }, $testClassDiffMethodNodes);

                // 获取需要生成的测试方法节点
                if (file_exists($testClassPath)) {
                    $originalTestClassMethodNames = array_filter(array_map(function (ReflectionMethod $method) {
                        return $method->getName();
                    }, (new ReflectionClass($testClassFullName))->getMethods(ReflectionMethod::IS_PUBLIC)), function ($name) {
                        return Str::startsWith($name, 'test');
                    });

                    $testClassDiffMethodNames = array_diff(
                        array_map([Str::class, $this->option('test-method-format')], $testClassDiffMethodNames),
                        array_map([Str::class, $this->option('test-method-format')], $originalTestClassMethodNames)
                    );
                    if (empty($testClassDiffMethodNames)) {
                        continue;
                    }

                    $testClassDiffMethodNodes = array_filter($testClassDiffMethodNodes, function (ClassMethod $node) use ($testClassDiffMethodNames) {
                        return in_array($node->name->name, $testClassDiffMethodNames, true);
                    });
                }

                // 修改抽象语法树(遍历节点)
                $originalTestClassNodes = $this->parser->parse(
                    file_exists($testClassPath) ? file_get_contents($testClassPath) : file_get_contents($this->option('default-test-class-path')),
                    $this->errorHandler
                );
                $nodeTraverser = clone $this->nodeTraverser;
                $nodeTraverser->addVisitor(tap($this->classUpdatingVisitor, function (NodeVisitorAbstract $nodeVisitor) use ($testClassNamespace, $testClassName, $testClassDiffMethodNodes) {
                    $nodeVisitor->testClassNamespace = $testClassNamespace;
                    $nodeVisitor->testClassName = $testClassName;
                    $nodeVisitor->testClassDiffMethodNodes = $testClassDiffMethodNodes;
                }));
                $testClassNodes = $nodeTraverser->traverse($originalTestClassNodes);

                // 打印输出语法树
                file_exists($testClassDir = dirname($testClassPath)) or mkdir($testClassDir, 0755, true);
                // file_put_contents($testClassPath, $this->prettyPrinter->prettyPrintFile($testClassNodes));
                file_put_contents($testClassPath, $this->prettyPrinter->printFormatPreserving($testClassNodes, $originalTestClassNodes, $this->lexer->getTokens()));

                self::$statistics['related_classes']++;
                self::$statistics['added_methods'] += count($testClassDiffMethodNodes);
            }
        });

        $this->newLine();
        $this->table(['All files', 'All classes', 'Related classes', 'Added methods'], [self::$statistics]);

        return 0;
    }

    protected function checkOptions()
    {
        if (! in_array($this->option('parse-mode'), [
            ParserFactory::PREFER_PHP7,
            ParserFactory::PREFER_PHP5,
            ParserFactory::ONLY_PHP7,
            ParserFactory::ONLY_PHP5])
        ) {
            $this->error('The parse-mode option is not valid(1,2,3,4).');
            exit(1);
        }

        if (! $this->option('test-class-base-namespace')) {
            $this->error('The test-class-base-namespace option is required.');
            exit(1);
        }

        if (! $this->option('test-class-base-dirname')) {
            $this->error('The test-class-base-dirname option is required.');
            exit(1);
        }

        if (! file_exists($this->option('test-class-base-dirname'))) {
            $this->error('The test-class-base-dirname option is not a valid directory.');
            exit(1);
        }

        if (! in_array($this->option('test-method-format'), ['snake','camel'])) {
            $this->error('The test-method-format option is not valid(snake/camel).');
            exit(1);
        }

        if (! $this->option('default-test-class-path')) {
            $this->error('The default-test-class-path option is required.');
            exit(1);
        }

        if (! file_exists($this->option('default-test-class-path'))) {
            $this->error('The default-test-class-path option is not a valid file.');
            exit(1);
        }
    }

    protected function initializeEnvs()
    {
        $xdebug = new XdebugHandler(__CLASS__);
        $xdebug->check();
        unset($xdebug);

        extension_loaded('xdebug') and ini_set('xdebug.max_nesting_level', 2048);
        ini_set('zend.assertions', 0);
    }

    protected function initializeProperties()
    {
        $this->fileFinder = tap(Finder::create()->files()->name('*.php'), function (Finder $finder) {
            $finderOperationalOptions = [
                'in' => $this->option('in-dirs') ?: [app_path('Services'), app_path('Support'), app_path('Traits')],
                'notPath' => $this->option('not-paths') ?: ['Macros', 'Facades'],
                'notName' => $this->option('not-names') ?: [],
            ];
            foreach ($finderOperationalOptions as $operating => $options) {
                $finder->{$operating}($options);
            }

            self::$statistics['all_files'] = $finder->count();
        });

        $this->lexer = new Emulative(['usedAttributes' => [
            'comments',
            'startLine', 'endLine',
            'startTokenPos', 'endTokenPos',
        ]]);

        $this->parser = (new ParserFactory())->create((int)$this->option('parse-mode'), $this->lexer);
        $this->errorHandler = new Collecting();
        $this->builderFactory = new BuilderFactory();
        $this->nodeFinder = new NodeFinder();
        $this->nodeDumper = new NodeDumper();
        $this->jsonDecoder = new JsonDecoder();
        $this->nodeTraverser = new NodeTraverser();
        $this->parentConnectingVisitor = new ParentConnectingVisitor();
        $this->nodeConnectingVisitor = new NodeConnectingVisitor();
        $this->cloningVisitor = new CloningVisitor();
        $this->nodeTraverser->addVisitor($this->cloningVisitor);

        $this->classUpdatingVisitor = new class('', '', []) extends NodeVisitorAbstract {
            /** @var string */
            public $testClassNamespace;
            /** @var string */
            public $testClassName;
            /** @var \PhpParser\Node\Stmt\ClassMethod[] */
            public $testClassDiffMethodNodes = [];

            public function __construct(string $testClassNamespace, string $testClassName, array $testClassDiffMethodNodes)
            {
                $this->testClassNamespace = $testClassNamespace;
                $this->testClassName = $testClassName;
                $this->testClassDiffMethodNodes = $testClassDiffMethodNodes;
            }

            public function leaveNode(Node $node)
            {
                if ($node instanceof  Node\Stmt\Namespace_) {
                    $node->name = new Node\Name($this->testClassNamespace);
                }

                if ($node instanceof Node\Stmt\Class_) {
                    $node->name->name = $this->testClassName;
                    $node->stmts = array_merge($node->stmts, $this->testClassDiffMethodNodes);
                }
            }
        };

        $this->prettyPrinter = new class() extends Standard {
            protected function pStmt_ClassMethod(ClassMethod $node)
            {
                return ($node->getAttribute('isAdded') ? $this->nl : '') . parent::pStmt_ClassMethod($node);
            }
        };
    }
}
