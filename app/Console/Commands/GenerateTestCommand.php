<?php

namespace App\Console\Commands;

use Error;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
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
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
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

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $this->nodeFinder = new NodeFinder();
        $this->nodeDumper = new NodeDumper();
        $this->prettyPrinter = new Standard();
        $this->nodeTraverser = new NodeTraverser();
    }

    public function handle()
    {
        $files = Finder::create()
            ->files()
            ->in([app_path('Services'), app_path('Support'), app_path('Traits')])
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
                $testClassPath = base_path("tests/Unit/$testClassBaseName/$testClassName.php") ;
                if (file_exists($testClassPath)) {
                    continue;
                }
                $testClassDir = dirname($testClassPath);
                $testClassMethodNames = array_map(function (ClassMethod $node) {
                    return 'test_' . Str::snake($node->name->name);
                }, array_filter($classNode->getMethods(), function (ClassMethod $node) {
                    return $node->isPublic() && ! $node->isAbstract();
                }));

                // 构建抽象语法树
                $testClassBuilder = new \PhpParser\Builder\Class_($testClassName);
                $testClassBuilder->extend(new Node\Name('TestCase'));
                $testClassBuilder->addStmts(array_map(function ($testMethodName) {
                    $method = new Method($testMethodName);
                    $method->makePublic();

                    return $method->getNode();
                }, $testClassMethodNames));

                $testNamespaceBuilder = new Namespace_($testClassNamespace);
                $testNamespaceBuilder->addStmt(new Use_('Tests\TestCase',  Node\Stmt\Use_::TYPE_NORMAL));
                $testNamespaceBuilder->addStmt($testClassBuilder->getNode());

                // 打印输出语法树
                file_exists($testClassDir) or mkdir($testClassDir, 0755, true);
                file_put_contents($testClassPath, $this->prettyPrinter->prettyPrintFile(Arr::wrap($testNamespaceBuilder->getNode())));
            }
        }
    }
}
