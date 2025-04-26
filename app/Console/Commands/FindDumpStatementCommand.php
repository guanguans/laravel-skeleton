<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeFinder;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use SebastianBergmann\Timer\ResourceUsageFormatter;
use SebastianBergmann\Timer\Timer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class FindDumpStatementCommand extends Command
{
    protected $signature = <<<'EOD'
        find:dump-statement
        {--dir=* : The directories to search for files}
        {--path=* : The paths to search for files}
        {--name=* : The names to search for files}
        {--not-path=* : The paths to exclude from the search}
        {--not-name=* : The names to exclude from the search}
        {--s|struct=* : The structs to search}
        {--f|func=* : The functions to search}
        {--M|memory-limit= : The memory limit to use for the PHP parser}
        EOD;
    protected $description = 'Find dump statements in PHP files.';

    /** @var array<string, list<string>> */
    private array $statements = [
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
            'var_export',
        ],
    ];
    private ?Finder $fileFinder = null;
    private ?Parser $parser = null;
    private ?NodeFinder $nodeFinder = null;
    private ?Standard $prettyPrinter = null;
    private ?ResourceUsageFormatter $resourceUsageFormatter = null;

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    public function isEnabled(): bool
    {
        return !$this->laravel->isProduction();
    }

    /**
     * @noinspection SensitiveParameterInspection
     */
    public function handle(Timer $timer): int
    {
        $timer->start();
        $this->withProgressBar($this->fileFinder, function (SplFileInfo $fileInfo) use (&$findInfos, &$odd): void {
            try {
                $nodes = $this->parser->parse($fileInfo->getContents());
            } catch (Error $error) {
                $this->newLine();
                $this->error(\sprintf('The file of %s parse error: %s.', $fileInfo->getRealPath(), $error->getMessage()));

                return;
            }

            $dumpNodes = $this->nodeFinder->find($nodes, function (Node $node) {
                if (
                    $node instanceof Expression
                    && $node->expr instanceof FuncCall
                    && $node->expr->name instanceof Name
                    && \in_array($node->expr->name->toString(), $this->statements['func'], true)
                ) {
                    return true;
                }

                return Str::of(class_basename($node::class))
                    ->lower()
                    ->replaceLast('_', '')
                    ->is($this->statements['struct']);
            });

            if ([] === $dumpNodes) {
                return;
            }

            $findInfos[] = array_map(function (Node $dumpNode) use ($fileInfo, $odd): array {
                if ($dumpNode instanceof Expression && $dumpNode->expr instanceof FuncCall) {
                    $name = "<fg=cyan>{$dumpNode->expr->name->getFirst()}</>";
                    $type = '<fg=cyan>func</>';
                } else {
                    $name = Str::of(class_basename($dumpNode::class))->lower()->replaceLast('_', '')->pipe(
                        static fn (Stringable $name): string => "<fg=red>$name</>"
                    );
                    $type = '<fg=red>struct</>';
                }

                $file = Str::of($fileInfo->getRealPath())->replace(base_path().\DIRECTORY_SEPARATOR, '')->pipe(
                    static fn (Stringable $file): string => $odd ? "<fg=green>$file</>" : "<fg=blue>$file</>"
                );
                $startLine = Str::of($dumpNode->getAttribute('startLine'))->pipe(
                    static fn (Stringable $startLine): string => $odd ? "<fg=green>$startLine</>" : "<fg=blue>$startLine</>"
                );
                $formattedCode = Str::of($this->prettyPrinter->prettyPrint([$dumpNode]))->pipe(
                    static fn (Stringable $formattedCode): string => $odd ? "<fg=green>$formattedCode</>" : "<fg=blue>$formattedCode</>"
                );

                return [
                    'index' => null,
                    'name' => $name,
                    'type' => $type,
                    'file' => $file,
                    'start_line' => $startLine,
                    'formatted_code' => $formattedCode,
                ];
            }, $dumpNodes);

            $odd = !$odd;
        });

        $this->newLine();

        if (empty($findInfos)) {
            $this->info('The print statement was not found.');
            $this->info($this->resourceUsageFormatter->resourceUsage($timer->stop()));

            return self::INVALID;
        }

        $findInfos = array_map(static function (array $info, $index): array {
            ++$index;
            $info['index'] = "<fg=yellow>$index</>";

            return $info;
        }, $findInfos = array_merge([], ...$findInfos), array_keys($findInfos));

        $this->table(
            array_map(static fn ($name) => Str::of($name)->snake()->replace('_', ' ')->title(), array_keys($findInfos[0])),
            $findInfos
        );

        $this->components->info($this->resourceUsageFormatter->resourceUsage($timer->stop()));

        return self::SUCCESS;
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->checkOptions();
        $this->initializeEnvs();
        $this->initializeProperties();
    }

    protected function checkOptions(): void
    {
        if ($this->option('struct')) {
            $this->statements['struct'] = array_intersect($this->statements['struct'], $this->option('struct'));
        }

        if ($this->option('func')) {
            $this->statements['func'] = array_intersect($this->statements['func'], $this->option('func'));
        }
    }

    protected function initializeEnvs(): void
    {
        $this->option('memory-limit') and ini_set('memory_limit', $this->option('memory-limit'));
    }

    protected function initializeProperties(): void
    {
        $this->fileFinder = tap(
            Finder::create()->files()->ignoreDotFiles(true)->ignoreVCS(true),
            function (Finder $finder): void {
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
            }
        );

        $this->parser = (new ParserFactory)->createForHostVersion();
        $this->nodeFinder = new NodeFinder;
        $this->prettyPrinter = new Standard;
        $this->resourceUsageFormatter = new ResourceUsageFormatter;
    }
}
