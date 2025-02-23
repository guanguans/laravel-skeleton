<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CurdController extends Controller
{
    /**
     * @var \Illuminate\Database\Eloquent\Model|string
     */
    protected $modelClass;

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
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function show($id): JsonResponse
    {
        return $this->success($this->findModel($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function update(Request $request, $id): JsonResponse
    {
        $this->findModel($id)->updateOrFail($request->post());

        return $this->ok();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function destroy($id): JsonResponse
    {
        $this->findModel($id)->delete();

        return $this->ok();
    }

    /**
     * Find model.
     *
     * @param  int  $id
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function findModel($id, $columns = ['*'])
    {
        return $this->modelClass::query()->findOrFail($id, $columns);
    }
}
