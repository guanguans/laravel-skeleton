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
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class FindDumpCommand extends Command
{
    /** @var string */
    protected $signature = <<<'signature'
        find:dump
        {--dir=* : The directories to search for files}
        {--path=* : The paths to search for files}
        {--name=* : The names to search for files}
        {--not-path=* : The paths to exclude from the search}
        {--not-name=* : The names to exclude from the search}
        {--m|parse-mode=1 : The mode(1,2,3,4) to use for the PHP parser}
        {--M|memory-limit= : The memory limit to use for the PHP parser}'
    signature;
    /** @var string */
    protected $description = 'Find dump calls in PHP files.';

    /** @var \Symfony\Component\Finder\Finder */
    private $fileFinder;
    /** @var \PhpParser\Parser */
    private $parser;
    /** @var \PhpParser\NodeFinder */
    private $nodeFinder;

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->checkOptions();
        $this->initializeEnvs();
        $this->initializeProperties();
    }

    public function handle()
    {
        $findInfos = [];
        $this->withProgressBar($this->fileFinder, function (SplFileInfo $fileInfo) use (&$findInfos) {
            try {
                $nodes = $this->parser->parse(file_get_contents($fileInfo->getRealPath()));
            } catch (Error $e) {
                $this->newLine();
                $this->error(sprintf("The file of %s parse error: %s.", $fileInfo->getRealPath(), $e->getMessage()));

                return;
            }

            $dumpNodes = $this->nodeFinder->find($nodes, function (Node $node) {
                foreach ([Node\Stmt\Echo_::class, Node\Expr\Print_::class, Node\Expr\Exit_::class] as $stmt) {
                    if ($node instanceof $stmt) {
                        return true;
                    }
                }

                if (! $node instanceof Node\Stmt\Expression || ! $node->expr instanceof Node\Expr\FuncCall || ! $node->expr->name instanceof Node\Name) {
                    return false;
                }

                if (in_array($node->expr->name->toString(), ['printf', 'vprintf', 'var_dump', 'dump', 'dd', 'print_r', 'var_export'])) {
                    return true;
                }

                return false;
            });

            $findInfos[] = array_map(function (Node $dumpNode) use ($fileInfo) {
                if ($dumpNode instanceof Node\Stmt\Expression && $dumpNode->expr instanceof Node\Expr\FuncCall) {
                    $name = $dumpNode->expr->name->parts[0];
                    $type = 'func';
                } else {
                    $name = Str::of(class_basename(get_class($dumpNode)))->lower()->replaceLast('_', '');
                    $type = 'struct';
                }

                return [
                    'file' => Str::replace(base_path(), '', $fileInfo->getRealPath()),
                    'line' => $dumpNode->getAttribute('startLine'),
                    'type' => $type,
                    'name' => $name,
                ];
            }, $dumpNodes);
        });

        $this->newLine();
        $this->table(['File', 'Line', 'Type', 'Name'], array_merge(...$findInfos));
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
                'in' => $this->option('dir') ?: [app_path()],
                'path' => $this->option('path') ?: [],
                'notPath' => $this->option('not-path') ?: [],
                'name' => $this->option('name') ?: ['*.php'],
                'notName' => $this->option('not-name') ?: [],
            ];
            foreach ($methods as $method => $parameters) {
                $finder->{$method}($parameters);
            }
        });

        $this->parser = (new ParserFactory())->create((int)$this->option('parse-mode'));
        $this->nodeFinder = new NodeFinder();
    }
}
