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
use PhpParser\Error;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ParsePHPFileToASTCommand extends Command
{
    /** @var string */
    protected $signature = '
        parse-php-file
        {file : The parsed file}
        {--m|parse-mode=1 : The mode(1,2,3,4) to use for the PHP parser}
        {--M|memory-limit= : The memory limit to use for the PHP parser}';

    /** @var string */
    protected $description = 'Parse a PHP file to AST.';

    /** @var \PhpParser\Parser */
    private $parser;

    /** @var \PhpParser\NodeFinder */
    private $nodeFinder;

    /** @var \PhpParser\PrettyPrinter\Standard */
    private $prettyPrinter;

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

    public function handle()
    {
        try {
            $contents = file_get_contents($file = base_path($this->argument('file')));
            $nodes = $this->parser->parse($contents);
            dump($nodes);
        } catch (Error $e) {
            $this->output->error(sprintf('The file of %s parse error: %s.', $file, $e->getMessage()));

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    protected function checkOptions()
    {
        if (! file_exists(base_path($this->argument('file')))) {
            throw new \InvalidArgumentException(sprintf('The file of %s does not exist.', $this->argument('file')));
        }

        if (! \in_array($this->option('parse-mode'), [
            ParserFactory::PREFER_PHP7,
            ParserFactory::PREFER_PHP5,
            ParserFactory::ONLY_PHP7,
            ParserFactory::ONLY_PHP5, ])
        ) {
            throw new \InvalidArgumentException('The parse-mode option is not valid(1,2,3,4).');
        }
    }

    protected function initializeEnvs()
    {
        $xdebug = new XdebugHandler(__CLASS__);
        $xdebug->check();
        unset($xdebug);

        \extension_loaded('xdebug') and ini_set('xdebug.max_nesting_level', 2048);
        ini_set('zend.assertions', 0);
        $this->option('memory-limit') and ini_set('memory_limit', $this->option('memory-limit'));
    }

    protected function initializeProperties()
    {
        $this->parser = (new ParserFactory())->create((int) $this->option('parse-mode'));
        $this->nodeFinder = new NodeFinder();
        $this->prettyPrinter = new Standard();
    }
}
