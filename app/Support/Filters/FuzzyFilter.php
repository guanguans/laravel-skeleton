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

namespace App\Support\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\Filters\Filter;

/**
 * @template TModelClass of Model
 *
 * @implements Filter<TModelClass>
 *
 * @see https://github.com/wayofdev/laravel-starter-tpl/blob/develop/app/src/Infrastructure/Filters/FuzzyFilter.php
 */
class FuzzyFilter implements Filter
{
    /** @var list<string> */
    private readonly array $fields;

    public function __construct(string ...$fields)
    {
        $this->fields = $fields;
    }

    /**
     * @param Builder<TModelClass> $query
     *
     * @return Builder<TModelClass>
     */
    #[\Override]
    public function __invoke(Builder $query, mixed $value, string $property): Builder
    {
        $query->where(function (Builder $query) use ($value): void {
            foreach ($this->fields as $field) {
                $values = (array) $value;

                foreach ($values as $item) {
                    $query->orWhere($field, 'LIKE', "%$item%");
                }
            }
        });

        return $query;
    }
}
