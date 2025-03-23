<?php

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

return [

    /*
     * By default the package will use the `include`, `filter`, `sort`
     * and `fields` query parameters as described in the readme.
     *
     * You can customize these query string parameters here.
     */
    'parameters' => [
        'include' => 'include',

        'filter' => 'filter',

        'sort' => 'sort',

        'fields' => 'fields',

        'append' => 'append',
    ],

    /*
     * Related model counts are included using the relationship name suffixed with this string.
     * For example: GET /users?include=postsCount
     */
    'count_suffix' => 'Count',

    /*
     * Related model exists are included using the relationship name suffixed with this string.
     * For example: GET /users?include=postsExists
     */
    'exists_suffix' => 'Exists',

    /*
     * By default the package will throw an `InvalidFilterQuery` exception when a filter in the
     * URL is not allowed in the `allowedFilters()` method.
     */
    'disable_invalid_filter_query_exception' => false,

    /*
     * By default the package will throw an `InvalidSortQuery` exception when a sort in the
     * URL is not allowed in the `allowedSorts()` method.
     */
    'disable_invalid_sort_query_exception' => false,

    /*
     * By default the package will throw an `InvalidIncludeQuery` exception when an include in the
     * URL is not allowed in the `allowedIncludes()` method.
     */
    'disable_invalid_includes_query_exception' => false,

    /*
     * By default, the package expects relationship names to be snake case plural when using fields[relationship].
     * For example, fetching the id and name for a userOwner relation would look like this:
     * GET /users?fields[user_owner]=id,name
     *
     * Set this to `false` if you don't want that and keep the requested relationship names as-is and allows you to
     * request the fields using a camelCase relationship name:
     * GET /users?fields[userOwner]=id,name
     */
    'convert_relation_names_to_snake_case_plural' => true,

    /*
     * By default, the package expects relationship names to be snake case plural when using fields[relationship].
     * For example, fetching the id and name for a userOwner relation would look like this:
     * GET /users?fields[user_owner]=id,name
     *
     * Set this to one of `snake_case`, `camelCase` or `none` if you want to enable table name resolution in addition to the relation name resolution
     * GET /users?include=topOrders&fields[orders]=id,name
     */
    'convert_relation_table_name_strategy' => false,

    /*
     * By default, the package expects the field names to match the database names
     * For example, fetching the field named firstName would look like this:
     * GET /users?fields=firstName
     *
     * Set this to `true` if you want to convert the firstName into first_name for the underlying query
     */
    'convert_field_names_to_snake_case' => false,
];
