<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
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
    /** @var array<string> */
    private readonly array $fields;

    public function __construct(string ...$fields)
    {
        $this->fields = $fields;
    }

    /**
     * @param  Builder<TModelClass>  $query
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
