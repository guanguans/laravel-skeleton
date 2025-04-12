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

class CurdController extends Controller
{
    protected Model|string $modelClass;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function index(Request $request): JsonResponse
    {
        $models = $this->modelClass::query()
            ->paginate($request->get('per_page'));

        return $this->apiResponse()->success($models);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function store(Request $request): JsonResponse
    {
        $this->modelClass::query()->create($request->post());

        return $this->apiResponse()->ok();
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function show(int $id): JsonResponse
    {
        return $this->apiResponse()->success($this->findModel($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $this->findModel($id)->updateOrFail($request->post());

        return $this->apiResponse()->ok();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function destroy(int $id): JsonResponse
    {
        $this->findModel($id)->delete();

        return $this->apiResponse()->ok();
    }

    /**
     * Find model.
     */
    protected function findModel(int $id, array $columns = ['*']): Model
    {
        return $this->modelClass::query()->findOrFail($id, $columns);
    }
}
