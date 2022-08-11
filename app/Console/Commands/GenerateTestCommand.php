<?php

namespace App\Console\Commands;

use Error;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use PhpParser\Builder\Method;
use PhpParser\Builder\Namespace_;
use PhpParser\Builder\Use_;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\NodeDumper;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class GenerateTestCommand extends Command
{
    protected $signature = 'generate:test-method
                            {--test-class-base-namespace=Tests\\Unit : Base namespace of the test class}
                            {--test-class-base-dirname=tests/Unit/ : Base dirname of the test class}
                            {--test-method-format=snake : Format of the test method}
                            {--default-test-class-path=tests/Unit/ExampleTest.php : Path of the default test class}';

    protected $description = 'Generate test method.';

    /**
     * @var \PhpParser\Parser
     */
    private $parser;

    /**
     * @var \PhpParser\NodeFinder
     */
    private $nodeFinder;

    /**
     * @var \PhpParser\NodeDumper
     */
    private $nodeDumper;

    /**
     * @var \PhpParser\PrettyPrinter\Standard
     */
    private $prettyPrinter;

    /**
     * @var \PhpParser\NodeTraverser
     */
    private $nodeTraverser;

    /**
     * @var \PhpParser\NodeVisitorAbstract
     */
    private $nodeVisitor;

    /**
     * @var array
     */
    private $config = [];

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->config = [
            'finder' => [
                'in' => [app_path('Services'), app_path('Support'), app_path('Traits')],
                'notPath' => ['Macros', 'Facades'],
            ],
            'test_class_base_namespace' => 'Tests\\Unit\\',
            'test_class_base_dirname' => base_path('tests/Unit/'),
            'test_method_format' => 'snake', // snake, camel
            'default_test_class_path' => base_path('tests/Unit/ExampleTest.php')
        ];
        if ($baseNamespace = $this->option('test-class-base-namespace')) {
            $this->config['test_class_base_namespace'] = Str::finish($baseNamespace, '\\');
        }
        if ($baseDirname = $this->option('test-class-base-dirname')) {
            $this->config['test_class_base_dirname'] = file_exists($baseDirname = Str::finish($baseDirname, '/')) ? $baseDirname : base_path($baseDirname);
        }
        if ($methodFormat = $this->option('test-method-format')) {
            $this->config['test_method_format'] = $methodFormat;
        }
        if ($defaultTestClassPath = $this->option('default-test-class-path')) {
            $this->config['default_test_class_path'] = file_exists($defaultTestClassPath) ? $defaultTestClassPath : base_path($defaultTestClassPath);
        }

        $this->parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $this->nodeFinder = new NodeFinder();
        $this->nodeDumper = new NodeDumper();
        $this->prettyPrinter = new Standard();
        $this->nodeTraverser = new NodeTraverser();
        $this->nodeVisitor = new class('', '', []) extends NodeVisitorAbstract {
            /** @var string */
            private $testClassNamespace;
            /** @var string */
            private $testClassName;
            /** @var \PhpParser\Node\Stmt\ClassMethod[] */
            private $testClassMethodNodes;

            public function __construct(string $testClassNamespace, string $testClassName, array $testClassMethodNodes)
            {
                $this->testClassNamespace = $testClassNamespace;
                $this->testClassName = $testClassName;
                $this->testClassMethodNodes = $testClassMethodNodes;
            }

            public function leaveNode(Node $node)
            {
                if ($node instanceof  Node\Stmt\Namespace_) {
                    $node->name = new Node\Name($this->testClassNamespace);
                }

                if ($node instanceof Node\Stmt\Class_) {
                    $node->name->name = $this->testClassName;
                    $node->stmts = array_merge($node->stmts, $this->testClassMethodNodes);
                }
            }

            public function setTestClassNamespace(string $testClassNamespace)
            {
                $this->testClassNamespace = $testClassNamespace;
            }

            public function setTestClassName(string $testClassName)
            {
                $this->testClassName = $testClassName;
            }

            public function setTestClassMethodNodes(array $testClassMethodNodes)
            {
                $this->testClassMethodNodes = $testClassMethodNodes;
            }
        };
    }

    public function handle()
    {
        $files = tap(Finder::create()->files()->name('*.php')->sortByName(), function (Finder $finder) {
            foreach ($this->config['finder'] as $key => $value) {
                $finder->{$key}($value);
            }
        });

        foreach ($files as $file) {
            try {
                $stmts = $this->parser->parse(file_get_contents($file->getRealPath()));
            } catch (Error $e) {
                $this->error(sprintf("The file of %s parse error: %s.", $file->getRealPath(), $e->getMessage()));

                continue;
            }

            $classNodes = $this->nodeFinder->find($stmts, function (Node $node) {
                return $node instanceof Class_ || $node instanceof Trait_;
            });
            /** @var Class_|Trait_ $classNode */
            foreach ($classNodes as $classNode) {
                // 准备基本信息
                $originalClassName = $classNode->name->name;
                $testClassName = "{$originalClassName}Test";
                $testClassBaseName = str_replace(app_path().DIRECTORY_SEPARATOR, '', $file->getPath());
                $testClassNamespace = $this->config['test_class_base_namespace'].str_replace(DIRECTORY_SEPARATOR, '\\', $testClassBaseName);
                $testClassFullName = $testClassNamespace.'\\'.$testClassName;
                $testClassPath = $this->config['test_class_base_dirname']. "$testClassBaseName/$testClassName.php";
                $testClassDir = dirname($testClassPath);

                $testClassDiffMethodNodes = array_map(function (ClassMethod $node) {
                    $node->flags = Node\Stmt\Class_::MODIFIER_PUBLIC;
                    $node->byRef = false;
                    $node->name->name = Str::{$this->config['test_method_format']}('test_' . Str::snake($node->name->name));
                    $node->params = [];
                    $node->returnType = null;
                    $node->stmts = [];
                    $node->attrGroups = [];
                    $node->setAttribute('comments', []);

                    return $node;
                }, $originalClassMethodNames = array_filter($classNode->getMethods(), function (ClassMethod $node) {
                    return $node->isPublic() && ! $node->isAbstract();
                }));
                $testClassDiffMethodNames = array_map(function (ClassMethod $node) {
                    return $node->name->name;
                }, $originalClassMethodNames);
                if (file_exists($testClassPath)) {
                    $originalTestReflectionClass = new ReflectionClass($testClassFullName);
                    $originalTestClassMethodNames = array_filter(array_map(function (ReflectionMethod $method) {
                        return $method->getName();
                    }, $originalTestReflectionClass->getMethods(ReflectionMethod::IS_PUBLIC)), function ($name) {
                        return Str::startsWith($name, 'test');
                    });

                    $testClassDiffMethodNames = array_diff(
                        array_map([Str::class, $this->config['test_method_format']], $testClassDiffMethodNames),
                        array_map([Str::class, $this->config['test_method_format']], $originalTestClassMethodNames)
                    );
                    if (empty($testClassDiffMethodNames)) {
                        continue;
                    }

                    $testClassDiffMethodNodes = array_filter($testClassDiffMethodNodes, function (ClassMethod $node) use ($testClassDiffMethodNames) {
                        return in_array($node->name->name, $testClassDiffMethodNames, true);
                    });
                }

                // // 构建抽象语法树()
                // $testClassBuilder = new \PhpParser\Builder\Class_($testClassName);
                // $testClassBuilder->extend(new Node\Name('TestCase'));
                // $testClassDiffMethodNodes = array_map(function ($testMethodName) {
                //     $method = new Method($testMethodName);
                //     $method->makePublic();
                //
                //     return $method->getNode();
                // }, $testClassDiffMethodNames);
                // $testClassBuilder->addStmts($testClassDiffMethodNodes);
                // $testClassNode = $testClassBuilder->getNode();
                //
                // $testNamespaceBuilder = new Namespace_($testClassNamespace);
                // $testNamespaceBuilder->addStmt(new Use_('PHPUnit\Framework\TestCase',  Node\Stmt\Use_::TYPE_NORMAL));
                // $testNamespaceBuilder->addStmt($testClassNode);
                // $testNodes = [$testNamespaceBuilder->getNode()];

                // 修改抽象语法树(遍历节点)
                $stmts = $this->parser->parse(file_exists($testClassPath) ? file_get_contents($testClassPath) : file_get_contents($this->config['default_test_class_path']));
                $nodeVisitor = tap($this->nodeVisitor, function ($nodeVisitor) use ($testClassNamespace, $testClassName, $testClassDiffMethodNodes) {
                    $nodeVisitor->setTestClassNamespace($testClassNamespace);
                    $nodeVisitor->setTestClassName($testClassName);
                    $nodeVisitor->setTestClassMethodNodes($testClassDiffMethodNodes);
                });
                $nodeTraverser = clone $this->nodeTraverser;
                $nodeTraverser->addVisitor($nodeVisitor);
                $testNodes = $nodeTraverser->traverse($stmts);

                // 打印输出语法树
                file_exists($testClassDir) or mkdir($testClassDir, 0755, true);
                file_put_contents($testClassPath, $this->prettyPrinter->prettyPrintFile($testNodes));
            }
        }
    }
}
