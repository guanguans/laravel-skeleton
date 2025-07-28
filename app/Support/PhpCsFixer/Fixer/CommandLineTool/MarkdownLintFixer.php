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

namespace App\Support\PhpCsFixer\Fixer\CommandLineTool;

use Symfony\Component\Process\Process;

/**
 * @see https://github.com/igorshubovych/markdownlint-cli
 */
final class MarkdownLintFixer extends AbstractCommandLineToolFixer
{
    // protected function isSuccessfulProcess(Process $process): bool
    // {
    //     return parent::isSuccessfulProcess($process) || $process->getExitCode() === 1;
    // }

    #[\Override]
    protected function defaultCommand(): array
    {
        return ['markdownlint'];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function requiredOptions(): array
    {
        return [
            '--fix',
            '--dot',
            ...$this->disableNonFixableRulesOptions(),
            /**
             * @see https://github.com/DavidAnson/markdownlint/blob/main/doc/Rules.md
             */
            '--disable' => [
                /**
                 * 可以自动修复的规则.
                 */
                // 'MD001', // heading-increment - 标题级别递增
                // 'MD003', // heading-style - 标题样式
                // 'MD004', // ul-style - 无序列表样式
                // 'MD005', // list-indent - 列表缩进
                // 'MD006', // ul-start-left - 无序列表起始位置
                // 'MD007', // ul-indent - 无序列表缩进
                // 'MD009', // no-trailing-spaces - 行尾空格
                // 'MD010', // no-hard-tabs - 硬制表符
                // 'MD011', // no-reversed-links - 反向链接
                // 'MD012', // no-multiple-blanks - 多个空行
                // 'MD018', // no-missing-space-atx - ATX 标题缺少空格
                // 'MD019', // no-multiple-space-atx - ATX 标题多个空格
                // 'MD020', // no-missing-space-closed-atx - 封闭 ATX 标题缺少空格
                // 'MD021', // no-multiple-space-closed-atx - 封闭 ATX 标题多个空格
                // 'MD022', // blanks-around-headings - 标题周围空行
                // 'MD023', // heading-start-left - 标题起始位置
                // 'MD026', // no-trailing-punctuation - 标题尾部标点
                // 'MD027', // no-multiple-space-blockquote - 块引用多个空格
                // 'MD028', // no-blanks-blockquote - 块引用空行
                // 'MD030', // list-marker-space - 列表标记空格
                // 'MD031', // blanks-around-fences - 代码块周围空行
                // 'MD032', // blanks-around-lists - 列表周围空行
                'MD034', // no-bare-urls - 裸 URL
                // 'MD037', // no-space-in-emphasis - 强调符号内空格
                // 'MD038', // no-space-in-code - 行内代码空格
                // 'MD039', // no-space-in-links - 链接内空格
                // 'MD044', // proper-names - 专有名词
                // 'MD047', // single-trailing-newline - 文件末尾换行
                // 'MD049', // emphasis-style - 强调样式
                // 'MD050', // strong-style - 粗体样式
                // 'MD051', // link-fragments - 链接片段
                // 'MD053', // link-image-reference-definitions - 链接图片引用定义
                // 'MD058', // blanks-around-tables - 表格周围空行
            ],
        ];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function silentOptions(): array
    {
        return ['--quiet'];
    }

    #[\Override]
    protected function extensions(): array
    {
        return ['md', 'markdown'];
    }

    private function disableNonFixableRulesOptions(): array
    {
        return array_reduce(
            [
                /**
                 * 需要手动修复的规则.
                 */
                'MD002', // first-heading-h1 - 第一个标题应为 H1
                'MD008', // no-trailing-spaces-blockquote - 块引用行尾空格
                'MD013', // line-length - 行长度限制
                'MD014', // no-shell-dollars - Shell 命令不要 $ 前缀
                'MD015', // no-shell-dollars - Shell 命令不要 $ 前缀
                'MD016', // no-shell-dollars - Shell 命令不要 $ 前缀
                'MD017', // no-shell-dollars - Shell 命令不要 $ 前缀
                'MD024', // no-duplicate-heading - 重复标题
                'MD025', // single-title - 单个标题
                'MD029', // ol-prefix - 有序列表前缀
                'MD033', // no-inline-html - 内联 HTML
                'MD035', // hr-style - 水平线样式
                'MD036', // no-emphasis-as-heading - 不要用强调作标题
                'MD040', // fenced-code-language - 代码块语言
                'MD041', // first-line-heading - 第一行标题
                'MD042', // no-empty-links - 空链接
                'MD043', // required-headings - 必需标题
                'MD045', // no-alt-text - 图片替代文本
                'MD046', // code-block-style - 代码块样式
                'MD048', // code-fence-style - 代码围栏样式
                'MD052', // reference-links-images - 引用链接图片
                'MD054', // link-image-style - 链接图片样式
                'MD055', // table-pipe-style - 表格管道样式
                'MD056', // table-column-count - 表格列数
                'MD059', // descriptive-link-text - 链接文本应具有描述性
            ],
            static fn (array $options, string $rule): array => [...$options, '--disable', $rule],
            []
        );
    }
}
