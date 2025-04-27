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

namespace App\Http\Controllers\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class CurdController extends Controller
{
    /** @var class-string<\Illuminate\Database\Eloquent\Model> */
    protected string $modelClass;

    public function index(Request $request): JsonResponse
    {
        return $this->apiResponse()->success(
            $this->modelClass::query()->simplePaginate($request->get('per_page'))
        );
    }

    public function store(Request $request): JsonResponse
    {
        return $this->apiResponse()->success(
            $this->modelClass::query()->create($request->post())
        );
    }

    public function show(int $id): JsonResponse
    {
        return $this->apiResponse()->success($this->findModel($id));
    }

    /**
     * @throws \Throwable
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $model = $this->findModel($id);
        $model->updateOrFail($request->post());

        return $this->apiResponse()->success($model);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->findModel($id)->delete();

        return $this->apiResponse()->ok();
    }

    protected function findModel(int $id, array $columns = ['*']): Model
    {
        return $this->modelClass::query()->findOrFail($id, $columns);
    }
}
