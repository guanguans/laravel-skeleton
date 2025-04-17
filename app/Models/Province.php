<?php

/** @noinspection ClassOverridesFieldOfSuperClassInspection */
declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class Province extends Model
{
    use Sushi;

    public function getRows(): array
    {
        return [
            ['name' => '北京'],
            ['name' => '天津'],
            ['name' => '河北'],
            ['name' => '山西'],
            ['name' => '内蒙古'],
            ['name' => '辽宁'],
            ['name' => '吉林'],
            ['name' => '黑龙江'],
            ['name' => '上海'],
            ['name' => '江苏'],
            ['name' => '浙江'],
            ['name' => '安徽'],
            ['name' => '福建'],
            ['name' => '江西'],
            ['name' => '山东'],
            ['name' => '河南'],
            ['name' => '湖北'],
            ['name' => '湖南'],
            ['name' => '广东'],
            ['name' => '广西'],
            ['name' => '海南'],
            ['name' => '重庆'],
            ['name' => '四川'],
            ['name' => '贵州'],
            ['name' => '云南'],
            ['name' => '西藏'],
            ['name' => '陕西'],
            ['name' => '甘肃'],
            ['name' => '青海'],
            ['name' => '宁夏'],
            ['name' => '新疆'],
            ['name' => '台湾'],
            ['name' => '香港'],
            ['name' => '澳门'],
        ];
    }
}
