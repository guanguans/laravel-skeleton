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

class GenerateTestsCommand extends Command
{
    protected $signature = 'generate:tests
                            {--in-dir=* : The directories to search for files}
                            {--path=* : The paths to search for files}
                            {--name=* : The names to search for files}
                            {--not-path=* : The paths to exclude from the search}
                            {--not-name=* : The names to exclude from the search}
                            {--base-namespace=Tests\\Unit : The base namespace for the generated tests}
                            {--base-dir=./tests/Unit/ : The base directory for the generated tests}
                            {--default-class=./tests/Unit/ExampleTest.php : The default class to use for the generated tests}
                            {--f|method-format=snake : The format to use for the method names}
                            {--m|parse-mode=1 : The mode to use for the PHP parser}
                            {--M|memory-limit= : The memory limit to use for the PHP parser}';

    protected $description = 'Generate tests for the given files.';

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

            $originalNamespaceNodes = $this->nodeFinder->find($originalNodes, function (Node $node) {
                return $node instanceof Node\Stmt\Namespace_ && $node->name;
            });

            foreach ($originalNamespaceNodes as $originalNamespaceNode) {
                $originalClassNamespace = $originalNamespaceNode->name->toString();

                $originalClassNodes = $this->nodeFinder->find($originalNamespaceNode, function (Node $node) {
                    return ($node instanceof Class_ || $node instanceof Trait_) && $node->name;
                });
                self::$statistics['all_classes'] += count($originalClassNodes);
                /** @var Class_|Trait_ $originalClassNode */
                foreach ($originalClassNodes as $originalClassNode) {
                    // 准备基本信息
                    $testClassNamespace = Str::finish($this->option('base-namespace'), '\\').$originalClassNamespace;
                    $testClassName = "{$originalClassNode->name->name}Test";
                    $testClassFullName = $testClassNamespace.'\\'.$testClassName;
                    $testClassBaseName = str_replace('\\', DIRECTORY_SEPARATOR, $originalClassNamespace);
                    $testClassPath = Str::finish($this->option('base-dir'), DIRECTORY_SEPARATOR). $testClassBaseName.DIRECTORY_SEPARATOR."$testClassName.php";

                    // 默认生成源类的全部方法节点
                    $testClassDiffMethodNodes = array_map(function (ClassMethod $node) {
                        return tap(
                            $this->builderFactory
                                ->method(Str::{$this->option('method-format')}('test_' . Str::snake($node->name->name)))
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
                            array_map([Str::class, $this->option('method-format')], $testClassDiffMethodNames),
                            array_map([Str::class, $this->option('method-format')], $originalTestClassMethodNames)
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
                        file_exists($testClassPath) ? file_get_contents($testClassPath) : file_get_contents($this->option('default-class')),
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

        if (! $this->option('base-namespace')) {
            $this->error('The base-namespace option is required.');
            exit(1);
        }

        if (! $this->option('base-dir')) {
            $this->error('The base-dir option is required.');
            exit(1);
        }

        if (! file_exists($this->option('base-dir'))) {
            $this->error('The base-dir option is not a valid directory.');
            exit(1);
        }

        if (! in_array($this->option('method-format'), ['snake','camel'])) {
            $this->error('The method-format option is not valid(snake/camel).');
            exit(1);
        }

        if (! $this->option('default-class')) {
            $this->error('The default-class option is required.');
            exit(1);
        }

        if (! file_exists($this->option('default-class'))) {
            $this->error('The default-class option is not a valid file.');
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
        $this->option('memory-limit') and ini_set('memory_limit', $this->option('memory-limit'));
    }

    protected function initializeProperties()
    {
        $this->fileFinder = tap(Finder::create()->files()->ignoreDotFiles(true)->ignoreVCS(true), function (Finder $finder) {
            $operations = [
                'in' => $this->option('in-dir') ?: [app_path('Services'), app_path('Support'), app_path('Traits')],
                'path' => $this->option('path') ?: [],
                'notPath' => $this->option('not-path') ?: ['Macros', 'Facades'],
                'name' => $this->option('name') ?: ['*.php'],
                'notName' => $this->option('not-name') ?: [],
            ];
            foreach ($operations as $operation => $parameters) {
                $finder->{$operation}($parameters);
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
