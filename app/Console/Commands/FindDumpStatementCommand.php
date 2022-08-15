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
use Illuminate\Support\Stringable;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use SebastianBergmann\Timer\ResourceUsageFormatter;
use SebastianBergmann\Timer\Timer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class FindDumpStatementCommand extends Command
{
    /** @var string */
    protected $signature = <<<'signature'
        find:dump
        {--dir=* : The directories to search for files}
        {--path=* : The paths to search for files}
        {--name=* : The names to search for files}
        {--not-path=* : The paths to exclude from the search}
        {--not-name=* : The names to exclude from the search}
        {--s|struct=* : The structs to search}
        {--f|func=* : The funcs to search}
        {--m|parse-mode=1 : The mode(1,2,3,4) to use for the PHP parser}
        {--M|memory-limit= : The memory limit to use for the PHP parser}'
    signature;
    /** @var string */
    protected $description = 'Find dump statement calls in PHP files.';
    /** @var \string[][] */
    private $statements = [
        'struct' => [
            'echo',
            'print',
            'die',
            'exit',
        ],
        'func' => [
            'printf',
            'vprintf',
            'var_dump',
            'dump',
            'dd',
            'print_r',
            'var_export'
        ]
    ];

    /** @var \Symfony\Component\Finder\Finder */
    private $fileFinder;
    /** @var \PhpParser\Parser */
    private $parser;
    /** @var \PhpParser\NodeFinder */
    private $nodeFinder;
    /** @var \PhpParser\PrettyPrinter\Standard */
    private $prettyPrinter;
    /** @var \SebastianBergmann\Timer\ResourceUsageFormatter */
    private $resourceUsageFormatter;

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->checkOptions();
        $this->initializeEnvs();
        $this->initializeProperties();
    }

    public function handle(Timer $timer)
    {
        $timer->start();
        $findInfos = [];
        $odd = true;

        $this->withProgressBar($this->fileFinder, function (SplFileInfo $fileInfo) use (&$findInfos, &$odd) {
            try {
                $nodes = $this->parser->parse(file_get_contents($fileInfo->getRealPath()));
            } catch (Error $e) {
                $this->newLine();
                $this->error(sprintf("The file of %s parse error: %s.", $fileInfo->getRealPath(), $e->getMessage()));

                return;
            }

            $dumpNodes = $this->nodeFinder->find($nodes, function (Node $node) {
                if (
                    $node instanceof Node\Stmt\Expression
                    && $node->expr instanceof Node\Expr\FuncCall
                    && $node->expr->name instanceof Node\Name
                    && in_array($node->expr->name->toString(), $this->statements['func'])
                ) {
                    return true;
                }

                return Str::of(class_basename(get_class($node)))
                    ->lower()
                    ->replaceLast('_', '')
                    ->is($this->statements['struct']);
            });
            if (empty($dumpNodes)) {
                return;
            }

            $findInfos[] = array_map(function (Node $dumpNode) use ($fileInfo, $odd) {
                if ($dumpNode instanceof Node\Stmt\Expression && $dumpNode->expr instanceof Node\Expr\FuncCall) {
                    $name = "<fg=cyan>{$dumpNode->expr->name->parts[0]}</>";
                    $type = '<fg=cyan>func</>';
                } else {
                    $name = Str::of(class_basename(get_class($dumpNode)))->lower()->replaceLast('_', '')->pipe(function (Stringable $name) {
                        return "<fg=red>$name</>";
                    });
                    $type = '<fg=red>struct</>';
                }

                $file = Str::of($fileInfo->getRealPath())->replace(base_path().DIRECTORY_SEPARATOR, '')->pipe(function (Stringable $file) use ($odd) {
                    return $odd ? "<fg=blue>$file</>" : "<fg=green>$file</>";
                });
                $line = Str::of($dumpNode->getAttribute('startLine'))->pipe(function (Stringable $line) use ($odd) {
                    return $odd ? "<fg=blue>$line</>" : "<fg=green>$line</>";
                });
                $formattedCode = Str::of($this->prettyPrinter->prettyPrint([$dumpNode]))->pipe(function (Stringable $formattedCode) use ($odd) {
                    return $odd ? "<fg=blue>$formattedCode</>" : "<fg=green>$formattedCode</>";
                });

                return [
                    'index' => null,
                    'name' => $name,
                    'type' => $type,
                    'file' => $file,
                    'line' => $line,
                    'formatted_code' => $formattedCode,
                ];
            }, $dumpNodes);

            $odd = ! $odd;
        });

        $this->newLine();

        if (empty($findInfos)) {
            $this->info('The print statement was not found.');
            $this->info($this->resourceUsageFormatter->resourceUsage($timer->stop()));

            return 0;
        }

        $findInfos = array_map(function ($info, $index) {
            $index++;
            $info['index'] = "<fg=yellow>$index</>";

            return $info;
        }, $findInfos = array_merge([], ...$findInfos), array_keys($findInfos));

        $this->table(array_map(function ($name) {
            return Str::of($name)->snake()->replace('_', ' ')->title();
        }, array_keys($findInfos[0])), $findInfos);

        $this->info($this->resourceUsageFormatter->resourceUsage($timer->stop()));

        return 1;
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

        if ($this->option('struct')) {
            $this->statements['struct'] = array_intersect($this->statements['struct'], $this->option('struct'));
        }

        if ($this->option('func')) {
            $this->statements['func'] = array_intersect($this->statements['func'], $this->option('func'));
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
            $methods = [
                'in' => $this->option('dir') ?: [base_path()],
                'path' => $this->option('path') ?: [],
                'notPath' => $this->option('not-path') ?: ['vendor', 'storage'],
                'name' => $this->option('name') ?: ['*.php'],
                'notName' => $this->option('not-name') ?: [],
            ];
            foreach ($methods as $method => $parameters) {
                $finder->{$method}($parameters);
            }
        });

        $this->parser = (new ParserFactory())->create((int)$this->option('parse-mode'));
        $this->nodeFinder = new NodeFinder();
        $this->prettyPrinter = new Standard();
        $this->resourceUsageFormatter = new ResourceUsageFormatter();
    }
}
