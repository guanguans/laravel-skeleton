<?php

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

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
use RuntimeException;
use SebastianBergmann\Timer\ResourceUsageFormatter;
use SebastianBergmann\Timer\Timer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class GenerateTestsCommand extends Command
{
    /** @var string */
    protected $signature = '
        generate:tests
        {--dir=* : The directories to search for files}
        {--path=* : The paths to search for files}
        {--name=* : The names to search for files}
        {--not-path=* : The paths to exclude from the search}
        {--not-name=* : The names to exclude from the search}
        {--base-namespace=Tests\Unit : The base namespace for the generated tests}
        {--base-dir=./tests/Unit/ : The base directory for the generated tests}
        {--t|template-file=./tests/Unit/ExampleTest.php : The template file to use for the generated tests}
        {--f|method-format=snake : The format(snake/camel) to use for the method names}
        {--m|parse-mode=1 : The mode(1,2,3,4) to use for the PHP parser}
        {--M|memory-limit= : The memory limit to use for the PHP parser}';

    /** @var string */
    protected $description = 'Generate tests for the given files';

    /** @var array */
    private static $statistics = [
        'scanned_files' => 0,
        'scanned_classes' => 0,
        'related_classes' => 0,
        'added_methods' => 0,
    ];

    /** @var \Symfony\Component\Finder\Finder */
    private $fileFinder;

    /** @var \SebastianBergmann\Timer\ResourceUsageFormatter */
    private $resourceUsageFormatter;

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

    /** @var \PhpParser\PrettyPrinter\Standard */
    private $prettyPrinter;

    /** @var \PhpParser\NodeTraverser */
    private $nodeTraverser;

    /** @var \PhpParser\NodeVisitor\CloningVisitor */
    private $cloningVisitor;

    private $classUpdatingVisitor;

    public function isEnabled()
    {
        return ! $this->laravel->isProduction();
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->checkOptions();
        $this->initializeEnvs();
        $this->initializeProperties();
    }

    public function handle(Timer $timer): int
    {
        $timer->start();
        $this->withProgressBar($this->fileFinder, function (SplFileInfo $fileInfo): void {
            try {
                $originalNodes = $this->parser->parse($fileInfo->getContents());
            } catch (Error $error) {
                $this->newLine();
                $this->error(sprintf('The file of %s parse error: %s.', $fileInfo->getRealPath(), $error->getMessage()));

                return;
            }

            $originalNamespaceNodes = $this->nodeFinder->find($originalNodes, static fn (Node $node): bool => $node instanceof Node\Stmt\Namespace_ && $node->name);

            /** @var Node\Stmt\Namespace_ $originalNamespaceNode */
            foreach ($originalNamespaceNodes as $originalNamespaceNode) {
                $originalClassNamespace = $originalNamespaceNode->name->toString();

                /** @var Class_[]|Trait_[] $originalClassNodes */
                $originalClassNodes = $this->nodeFinder->find($originalNamespaceNode, static fn (Node $node): bool => ($node instanceof Class_ || $node instanceof Trait_) && $node->name);
                self::$statistics['scanned_classes'] += \count($originalClassNodes);
                foreach ($originalClassNodes as $originalClassNode) {
                    // 准备基本信息
                    $testClassNamespace = Str::finish($this->option('base-namespace'), '\\').$originalClassNamespace;
                    $testClassName = "{$originalClassNode->name->name}Test";
                    $testClassFullName = $testClassNamespace.'\\'.$testClassName;
                    $testClassBaseName = str_replace('\\', DIRECTORY_SEPARATOR, $originalClassNamespace);
                    $testClassFile = Str::finish($this->option('base-dir'), DIRECTORY_SEPARATOR).$testClassBaseName.DIRECTORY_SEPARATOR."$testClassName.php";

                    // 获取需要生成的测试方法节点
                    $testClassAddedMethodNodes = array_map(fn (ClassMethod $node) => tap(
                        $this->builderFactory
                            ->method(Str::{$this->option('method-format')}('test_'.Str::snake($node->name->name)))
                            ->makePublic()
                            ->getNode()
                    )->setAttribute('isAdded', true), array_filter($originalClassNode->getMethods(), static fn (ClassMethod $node): bool => $node->isPublic() && ! $node->isAbstract() && $node->name->toString() !== '__construct'));
                    if ($isExistsTestClassFile = file_exists($testClassFile)) {
                        $originalTestClassMethodNames = array_filter(array_map(fn (ReflectionMethod $method) => Str::{$this->option('method-format')}($method->getName()), (new ReflectionClass($testClassFullName))->getMethods(ReflectionMethod::IS_PUBLIC)), static fn ($name) => Str::startsWith($name, 'test'));

                        $testClassAddedMethodNodes = array_filter($testClassAddedMethodNodes, static fn (ClassMethod $node): bool => ! \in_array($node->name->name, $originalTestClassMethodNames, true));
                        if ($testClassAddedMethodNodes === []) {
                            continue;
                        }
                    }

                    // 修改抽象语法树(遍历节点)

                    // $originalTestClassNodes = $isExistsTestClassFile
                    //     ? $this->parser->parse(file_get_contents($testClassFile), $this->errorHandler)
                    //     : $this->templateTestClassNodes;

                    $originalTestClassNodes = $this->parser->parse(
                        $isExistsTestClassFile ? file_get_contents($testClassFile) : file_get_contents($this->option('template-file')),
                        $this->errorHandler
                    );

                    $this->classUpdatingVisitor->testClassNamespace = $testClassNamespace;
                    $this->classUpdatingVisitor->testClassName = $testClassName;
                    $this->classUpdatingVisitor->testClassAddedMethodNodes = $testClassAddedMethodNodes;

                    $nodeTraverser = clone $this->nodeTraverser;
                    $nodeTraverser->addVisitor($this->classUpdatingVisitor);
                    $testClassNodes = $nodeTraverser->traverse($originalTestClassNodes);

                    // 打印输出语法树
                    if (! file_exists($testClassDir = \dirname($testClassFile)) && ! mkdir($testClassDir, 0755, true) && ! is_dir($testClassDir)) {
                        throw new RuntimeException(sprintf('Directory "%s" was not created', $testClassDir));
                    }

                    file_put_contents($testClassFile, $this->prettyPrinter->printFormatPreserving($testClassNodes, $originalTestClassNodes, $this->lexer->getTokens()));

                    ++self::$statistics['related_classes'];
                    self::$statistics['added_methods'] += \count($testClassAddedMethodNodes);
                }
            }
        });

        $this->newLine();
        $this->table(array_map(static fn ($name) => Str::of($name)->snake()->replace('_', ' ')->title(), array_keys(self::$statistics)), [self::$statistics]);

        $this->info($this->resourceUsageFormatter->resourceUsage($timer->stop()));

        return self::SUCCESS;
    }

    protected function checkOptions()
    {
        if (! \in_array($this->option('parse-mode'), [
            ParserFactory::PREFER_PHP7,
            ParserFactory::PREFER_PHP5,
            ParserFactory::ONLY_PHP7,
            ParserFactory::ONLY_PHP5, ])
        ) {
            $this->error('The parse-mode option is not valid(1,2,3,4).');

            exit(1);
        }

        if (! \in_array($this->option('method-format'), ['snake', 'camel'])) {
            $this->error('The method-format option is not valid(snake/camel).');

            exit(1);
        }

        if (! $this->option('base-namespace')) {
            $this->error('The base-namespace option is required.');

            exit(1);
        }

        if (! $this->option('base-dir') || ! file_exists($this->option('base-dir')) || ! is_dir($this->option('base-dir'))) {
            $this->error('The base-dir option is not a valid directory.');

            exit(1);
        }

        if (! $this->option('template-file') || ! file_exists($this->option('template-file')) || ! is_file($this->option('template-file'))) {
            $this->error('The template-file option is not a valid file.');

            exit(1);
        }
    }

    protected function initializeEnvs()
    {
        $xdebug = new XdebugHandler(self::class);
        $xdebug->check();
        unset($xdebug);

        \extension_loaded('xdebug') and ini_set('xdebug.max_nesting_level', 2048);
        ini_set('zend.assertions', 0);
        $this->option('memory-limit') and ini_set('memory_limit', $this->option('memory-limit'));
    }

    protected function initializeProperties()
    {
        $this->fileFinder = tap(Finder::create()->files()->ignoreDotFiles(true)->ignoreVCS(true), function (Finder $finder): void {
            $methods = [
                'in' => $this->option('dir') ?: [app_path('Services'), app_path('Support'), app_path('Traits')],
                'path' => $this->option('path') ?: [],
                'notPath' => $this->option('not-path') ?: ['tests', 'Tests', 'test', 'Test', 'Macros', 'Facades'],
                'name' => $this->option('name') ?: ['*.php'],
                'notName' => $this->option('not-name') ?: ['*Test.php', '*TestCase.php', '*.blade.php'],
            ];
            foreach ($methods as $method => $parameters) {
                $finder->{$method}($parameters);
            }

            self::$statistics['scanned_files'] = $finder->count();
        });

        $this->resourceUsageFormatter = new ResourceUsageFormatter();
        $this->lexer = new Emulative(['usedAttributes' => ['comments', 'startLine', 'endLine', 'startTokenPos', 'endTokenPos']]);
        $this->parser = (new ParserFactory())->create((int) $this->option('parse-mode'), $this->lexer);
        $this->errorHandler = new Collecting();
        $this->builderFactory = new BuilderFactory();
        $this->nodeFinder = new NodeFinder();
        // $this->nodeDumper = new NodeDumper();
        // $this->jsonDecoder = new JsonDecoder();
        $this->nodeTraverser = new NodeTraverser();
        // $this->parentConnectingVisitor = new ParentConnectingVisitor();
        // $this->nodeConnectingVisitor = new NodeConnectingVisitor();
        $this->cloningVisitor = new CloningVisitor();
        $this->nodeTraverser->addVisitor($this->cloningVisitor);

        $this->classUpdatingVisitor = new class('', '', []) extends NodeVisitorAbstract
        {
            /** @var string */
            public $testClassNamespace;

            /** @var string */
            public $testClassName;

            /** @var \PhpParser\Node\Stmt\ClassMethod[] */
            public $testClassAddedMethodNodes = [];

            public function __construct(string $testClassNamespace, string $testClassName, array $testClassAddedMethodNodes)
            {
                $this->testClassNamespace = $testClassNamespace;
                $this->testClassName = $testClassName;
                $this->testClassAddedMethodNodes = $testClassAddedMethodNodes;
            }

            public function leaveNode(Node $node)
            {
                if ($node instanceof Node\Stmt\Namespace_) {
                    $node->name = new Node\Name($this->testClassNamespace);
                }

                if ($node instanceof Node\Stmt\Class_) {
                    $node->name->name = $this->testClassName;
                    $node->stmts = array_merge($node->stmts, $this->testClassAddedMethodNodes);
                }
            }
        };

        $this->prettyPrinter = new class() extends Standard
        {
            protected function pStmt_ClassMethod(ClassMethod $node)
            {
                return ($node->getAttribute('isAdded') ? $this->nl : '').parent::pStmt_ClassMethod($node);
            }
        };
    }
}
