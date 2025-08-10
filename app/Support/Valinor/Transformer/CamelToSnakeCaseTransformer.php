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

namespace App\Support\Valinor\Transformer;

/**
 * @internal
 *
 * @see https://github.com/kreait/firebase-php/tree/7.x/src/Firebase/Valinor
 * @see https://valinor.cuyz.io/latest/serialization/common-transformers-examples/#transforming-property-name-to-snake_case
 */
final class CamelToSnakeCaseTransformer
{
    public function __invoke(object $object, callable $next): mixed
    {
        $result = $next();

        if (!\is_array($result)) {
            return $result;
        }

        $snakeCased = [];

        foreach ($result as $key => $value) {
            $newKey = preg_replace('/[A-Z]/', '_$0', lcfirst($key));
            \assert(\is_string($newKey));

            $newKey = strtolower($newKey);

            $snakeCased[$newKey] = $value;
        }

        return $snakeCased;
    }
}
