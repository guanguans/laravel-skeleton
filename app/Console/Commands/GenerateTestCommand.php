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
    protected $signature = 'generate:test';

    protected $description = 'Generate test.';

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

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

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
        $files = Finder::create()
            ->files()
            ->in([app_path('Services'), app_path('Support'), app_path('Traits')])
            ->notPath(['Macros', 'Facades'])
            ->name('*.php')
            ->sortByName();

        foreach ($files as $file) {
            try {
                $stmts = $this->parser->parse(file_get_contents($file->getRealPath()));
            } catch (Error $e) {
                $this->error(sprintf("The file of %s parse error: %s.", $file->getRealPath(), $e->getMessage()));

                continue;
            }

            $classNodes = $this->nodeFinder->findInstanceOf($stmts, Class_::class);
            /** @var Class_ $classNode */
            foreach ($classNodes as $classNode) {
                // 准备基本信息
                $originalClassName = $classNode->name->name;
                $testClassName = "{$originalClassName}Test";
                $testClassBaseName = str_replace(app_path().DIRECTORY_SEPARATOR, '', $file->getPath());
                $testClassNamespace = 'Tests\\Unit\\'.str_replace(DIRECTORY_SEPARATOR, '\\', $testClassBaseName);
                $testClassFullName = $testClassNamespace.'\\'.$testClassName;
                $testClassPath = base_path("tests/Unit/$testClassBaseName/$testClassName.php") ;
                $testClassDir = dirname($testClassPath);

                $testClassDiffMethodNodes = array_map(function (ClassMethod $node) {
                    $node->flags = 0;
                    $node->byRef = false;
                    $node->name->name = 'test_' . Str::snake($node->name->name);
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
                    return Str::snake($node->name->name);
                }, $originalClassMethodNames);
                if (file_exists($testClassPath)) {
                    $originalTestReflectionClass = new ReflectionClass($testClassFullName);
                    $originalTestClassMethodNames = array_filter(array_map(function (ReflectionMethod $method) {
                        return $method->getName();
                    }, $originalTestReflectionClass->getMethods(ReflectionMethod::IS_PUBLIC)), function ($name) {
                        return Str::startsWith($name, 'test');
                    });

                    $testClassDiffMethodNames = array_diff($testClassDiffMethodNames, $originalTestClassMethodNames);
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
                $stmts = $this->parser->parse(
                    file_exists($testClassPath)
                    ? file_get_contents($testClassPath)
                    : file_get_contents(base_path('tests/Unit/ExampleTest.php'))
                );
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
