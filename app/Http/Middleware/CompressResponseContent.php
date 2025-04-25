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

namespace App\Http\Middleware;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * @see https://github.com/vrkansagara/LaraOutPress
 */
class CompressResponseContent
{
    /** @var list<\Closure> */
    private static array $skipCallbacks = [];

    /** @var array<string, string> */
    private static array $replacementRules = [];

    /**
     * @noinspection RedundantDocCommentTagInspection
     *
     * @param \Closure(\Illuminate\Http\Request): (JsonResponse|RedirectResponse|Response) $next
     */
    public function handle(Request $request, \Closure $next, bool $debug = false): SymfonyResponse
    {
        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);

        if ($this->shouldntCompress($request)) {
            return $response;
        }

        $content = $response->getContent();
        $compressedContent = $this->compress($content);

        if ($debug) {
            $compressedContent .= $this->debugInformation($content, $compressedContent);
        }

        $response->setContent($compressedContent);
        $response->headers->remove('Content-Length');

        ini_set('pcre.recursion_limit', '16777');
        ini_set('zlib.output_compression', '4096'); // Some browser cant get content type.
        ini_set('zlib.output_compression_level', '-1'); // Let server decide.

        return $response;
    }

    /**
     * Register a callback that instructs the middleware to be skipped.
     */
    public static function skipWhen(\Closure $callback): void
    {
        static::$skipCallbacks[] = $callback;
    }

    /**
     * @param array<string, string> $replacementRules
     */
    public static function mergeReplacementRules(array $replacementRules): void
    {
        static::$replacementRules = [...static::$replacementRules, ...$replacementRules];
    }

    protected function shouldCompress(Request $request): bool
    {
        return !$this->shouldntCompress($request);
    }

    protected function shouldntCompress(Request $request): bool
    {
        if ($request->expectsJson()) {
            return true;
        }

        foreach (static::$skipCallbacks as $callback) {
            if ($callback($request)) {
                return true;
            }
        }

        return false;
    }

    protected function debugInformation(string $content, string $compressedContent): string
    {
        $compressingContentSize = \strlen($content);
        $compressedContentSize = \strlen($compressedContent);

        $compressingFormattedContentSize = $this->formatBytes($compressingContentSize);
        $compressedFormattedContentSize = $this->formatBytes($compressedContentSize);
        $percentReduction = \sprintf('%.02F%%', (1 - $compressedContentSize / $compressingContentSize) * 100);

        return /** @lang HTML */ <<<HTML
            <br>
            <table style="border: 1px solid #7fa273;background-color: #ffffaa;text-align: center;position: fixed;right: 20px;bottom: 20px;">
                <tr>
                    <th>Compressing</th>
                    <th>Compressed</th>
                    <th>Percent reduction</th>
                </tr>
                <tr>
                    <td>$compressingFormattedContentSize</td>
                    <td>$compressedFormattedContentSize</td>
                    <td>$percentReduction</td>
                </tr>
            </table>
            HTML;
    }

    protected function compress(string $content): string
    {
        $compressedContent = $this->compressJavascript($content);

        return $this->compressHtml($compressedContent);
    }

    protected function compressJavascript(string $content): string
    {
        // JavaScript compressor by John Elliot <jj5@jj5.net>
        $replaceRules = [
            '#\'([^\n\']*?)/\*([^\n\']*)\'#' => "'\1/'+\\'\\'+'*\2'",
            // remove comments from ' strings
            '#\"([^\n\"]*?)/\*([^\n\"]*)\"#' => '"\1/"+\'\'+"*\2"',
            // remove comments from " strings
            '#/\*.*?\*/#s' => '', // strip C style comments
            '#[\r\n]+#' => "\n",
            // remove blank lines and \r's
            '#\n([ \t]*//.*?\n)*#s' => "\n",
            // strip line comments (whole line only)
            '#([^\\])//([^\'"\n]*)\n#s' => "\\1\n",
            // strip line comments
            // (that aren't possibly in strings or regex's)
            '#\n\s+#' => "\n", // strip excess whitespace
            '#\s+\n#' => "\n", // strip excess whitespace
            '#(//[^\n]*\n)#s' => "\\1\n",
            // extra line feed after any comments left
            // (important given later replacements)
            '#/([\'"])\+\'\'\+([\'"])\*#' => '/*',
            // restore comments in strings
        ];
        $compressedContent = preg_replace(array_keys($replaceRules), $replaceRules, $content);

        $replaceRules = [
            "&&\n" => '&&',
            "||\n" => '||',
            "(\n" => '(',
            ")\n" => ')',
            "[\n" => '[',
            "]\n" => ']',
            "+\n" => '+',
            ",\n" => ',',
            "?\n" => '?',
            ":\n" => ':',
            ";\n" => ';',
            "{\n" => '{',
            // "}\n" => "}", // because I forget to put semicolons after function assignments
            "\n]" => ']',
            "\n)" => ')',
            "\n}" => '}',
            "\n\n" => "\n",
        ];
        $compressedContent = str_replace(array_keys($replaceRules), $replaceRules, $compressedContent);

        return trim($compressedContent);
    }

    protected function compressHtml(string $content): string
    {
        $whiteSpaceRules = [
            '/(\s)+/s' => '\\1', // shorten multiple whitespace sequences
            '#>\s+<#' => ">\n<", // Strip excess whitespace using new line
            "#\n\\s+<#" => "\n<", // strip excess whitespace using new line
            '/\>[^\S ]+/s' => '>',
            // Strip all whitespaces after tags, except space
            '/[^\S ]+\</s' => '<', // strip whitespaces before tags, except space
        /**
         * '/\s+    # Match one or more whitespace characters
         * (?!      # but only if it is impossible to match...
         * [^<>]*   # any characters except angle brackets
         * >        # followed by a closing bracket.
         * )        # End of lookahead
         * /x',.
         */

            // Remove all whitespaces except content between html tags.
            // MOST DANGEROUS
            // '/\s+(?![^<>]*>)/x' => '',
        ];

        $commentRules = [
            '/<!--.*?-->/ms' => '', // Remove all html comment.,
        ];

        $replaceWordsRules = [
            // OldWord will be replaced by the NewWord
            // OldWord <-> NewWord DO NOT REMOVE THIS LINE. {REFERENCE LINE}
            // '/\bOldWord\b/i' => 'NewWord'
        ];

        $rules = [...$replaceWordsRules, ...$commentRules, ...$whiteSpaceRules, ...static::$replacementRules];

        $compressedContent = preg_replace(array_keys($rules), $rules, $content);

        return trim($compressedContent);
    }

    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        if (0 < $bytes) {
            $index = (int) floor(log($bytes) / log(1024));

            $sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

            return \sprintf('%.02F', round($bytes / (1024 ** $index), $precision)) * 1 .' '.$sizes[$index];
        }

        return '0';
    }

    /**
     * This method will no longer support.
     *
     * @deprecated
     */
    protected function simpleCompression(string $content): string
    {
        /**
         * To remove useless whitespace from generated HTML, except for Javascript.
         * [Regex Source]
         * https://github.com/bcit-ci/codeigniter/wiki/compress-html-output
         * http://stackoverflow.com/questions/5312349/minifying-final-html-output-using-regular-expressions-with-codeigniter
         * %           # Collapse ws everywhere but in blacklisted elements.
         * (?>         # Match all whitespaces other than single space.
         * [^\S ]\s*   # Either one [\t\r\n\f\v] and zero or more ws,
         * | \s{2,}    # or two or more consecutive-any-whitespace.
         * )           # Note: The remaining regex consumes no text at all...
         * (?=         # Ensure we are not in a blacklist tag.
         * (?:         # Begin (unnecessary) group.
         * (?:         # Zero or more of...
         * [^<]++      # Either one or more non-"<"
         * | <         # or a < starting a non-blacklist tag.
         * (?!/?(?:textarea|pre)\b)
         * )*+         # (This could be "unroll-the-loop"ified.)
         * )           # End (unnecessary) group.
         * (?:         # Begin alternation group.
         * <           # Either a blacklist start tag.
         * (?>textarea|pre)\b
         * | \z        # or end of file.
         * )           # End alternation group.
         * )           # If we made it here, we are not in a blacklist tag.
         * %ix.
         */
        $regexOfRemoveWhiteSpace = '%(?>[^\S ]\s*| \s{2,})(?=(?:[^<]++| <(?!/?(?:textarea|pre)\b))*+(?:<(?>textarea|pre)\b|\z))%ix';
        $compressedContent = preg_replace($regexOfRemoveWhiteSpace, '', $content);

        // We are going to check if processing has working
        if (null === $compressedContent) {
            $compressedContent = $content;
        }

        return trim($compressedContent);
    }
}
