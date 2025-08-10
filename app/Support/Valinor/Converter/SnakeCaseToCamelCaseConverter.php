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

namespace App\Support\Valinor\Converter;

/**
 * @internal
 *
 * @see https://github.com/kreait/firebase-php/tree/7.x/src/Firebase/Valinor
 * @see https://valinor.cuyz.io/latest/how-to/convert-input/#converting-keys-format-from-snake_case-to-camelcase
 */
final class SnakeCaseToCamelCaseConverter
{
    public function __invoke(mixed $values, callable $next): object
    {
        if ($values instanceof \Traversable) {
            $values = iterator_to_array($values);
        }

        if (!\is_array($values)) {
            return $next($values);
        }

        $camelCaseConverted = array_combine(
            array_map(
                static fn (int|string $key): string => lcfirst(str_replace('_', '', ucwords((string) $key, '_'))),
                array_keys($values),
            ),
            $values,
        );

        return $next($camelCaseConverted);
    }
}
