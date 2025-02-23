<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
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

        return $this->success($models);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function store(Request $request): JsonResponse
    {
        $this->modelClass::query()->create($request->post());

        return $this->ok();
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function show(int $id): JsonResponse
    {
        return $this->success($this->findModel($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $this->findModel($id)->updateOrFail($request->post());

        return $this->ok();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function destroy(int $id): JsonResponse
    {
        $this->findModel($id)->delete();

        return $this->ok();
    }

    /**
     * Find model.
     */
    protected function findModel(int $id, array $columns = ['*']): Model
    {
        return $this->modelClass::query()->findOrFail($id, $columns);
    }
}
