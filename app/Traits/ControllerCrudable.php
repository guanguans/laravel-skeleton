<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * Trait ControllerCrud.
 *
 * @property \Illuminate\Database\Eloquent\Model|string $modelClass
 *
 * @mixin \App\Http\Controllers\Controller
 *
 * @see https://github.com/thiagoprz/crud-tools
 */
trait ControllerCrudable
{
    public function getViewPath(bool $forRedirect = false): string
    {
        $nsPrefix = '';
        $nsPrefixes = explode('\\', (new \ReflectionObject($this))->getNamespaceName());
        if ('Controllers' !== end($nsPrefixes)) {
            $nsPrefix = strtolower(end($nsPrefixes)).($forRedirect ? '/' : '.');
        }
        $modelNames = explode('\\', $this->modelClass);

        return $nsPrefix.strtolower(end($modelNames));
    }

    /**
     * List index.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $items = $this->modelClass::search($request->all());
        if ($request->ajax() || $request->wantsJson()) {
            if (property_exists($this->modelClass, 'resourceForSearch')) {
                return $items;
            }

            return response()->json($items);
        }

        return view($this->getViewPath().'.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view($this->getViewPath().'.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $validation = Validator::make($request->all(), $this->modelClass::validateOn());
            if ($validation->fails()) {
                return response()->json([
                    'error' => true, 'errors' => $validation->errors()->messages(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        } else {
            $this->validate($request, $this->modelClass::validateOn());
        }

        $model = new $this->modelClass();
        $model->fill($request->only($model->getFillable()));
        $model->save();

        $this->handleFileUploads($request, $model);
        if ($request->ajax() || $request->wantsJson()) {
            return $this->jsonModel($model);
        }

        $url = ! $request->input('url_return')
            ? $this->getViewPath(true).'/'.$model->id
            : $request->input('url_return');

        return redirect($url)->with('flash_message', trans('crud.added'));
    }

    /**
     * Display the specified resource.
     *
     * @param  mixed  $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        if ($request->has('with_trashed') && property_exists($this->modelClass, 'withTrashedForbidden')) {
            $model = $this->modelClass::withTrashed()->findOrFail($id);
        } else {
            $model = $this->modelClass::findOrFail($id);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return $this->jsonModel($model);
        }

        return view($this->getViewPath().'.show', compact('model'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  mixed  $id
     */
    public function edit($id): View
    {
        $model = $this->modelClass::findOrFail($id);

        return view($this->getViewPath().'.edit', compact('model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  mixed  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        /** @var \App\Http\Controllers\Controller $this */
        if ($this->isAjax($request)) {
            $validation = Validator::make($request->all(), $this->modelClass::validateOn('update', $id));
            if ($validation->fails()) {
                return response()->json([
                    'error' => true, 'errors' => $validation->errors()->messages(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        } else {
            $this->validate($request, $this->modelClass::validateOn('update', $id));
        }

        $model = $this->modelClass::findOrFail($id);
        $this->handleFileUploads($request, $model);
        $model->update($request->only($model->getFillable()));

        $url = ! $request->input('url_return')
            ? $this->getViewPath(true).'/'.$model->id
            : $request->input('url_return');

        return $this->isAjax($request)
            ? $this->jsonModel($model)
            : redirect($url)->with('flash_message', trans('crud.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  mixed  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, $id)
    {
        if ($request->has('with_trashed') && property_exists($this->modelClass, 'withTrashedForbidden')) {
            $model = $this->modelClass::withTrashed()->findOrFail($id);
            if ($model->deleted_at) {
                $count = $model->forceDelete();
            } else {
                $count = $this->modelClass::destroy($id);
            }
        } else {
            $count = $this->modelClass::destroy($id);
        }

        $url = ! $request->input('url_return') ? $this->getViewPath(true) : $request->input('url_return');
        $success = $count > 0;
        $error = ! $success;
        $message = ! $success ? __('No records were deleted') : __('crud.deleted');

        return $this->isAjax($request)
            ? response()->json(compact('success', 'error', 'message'))
            : redirect($url)->with('flash_message', $message);
    }

    /**
     * @param  ?Model  $model
     */
    public function handleFileUploads(Request $request, ?Model $model = null): void
    {
        $fileUploads = $this->modelClass::fileUploads($model);
        foreach ($fileUploads as $fileUpload => $fileData) {
            if ($request->hasFile($fileUpload)) {
                $file = $request->file($fileUpload);
                $upload = Storage::putFileAs(
                    $fileData['path'],
                    $file,
                    ! isset($fileData['name']) ? $file->getClientOriginalName() : $fileData['name']
                );
                $requestData[$fileUpload] = $upload;
            }
        }
    }

    private function isAjax(Request $request): bool
    {
        return $request->ajax() || $request->wantsJson();
    }

    /**
     * Returns JSON representation of object.
     */
    private function jsonModel(Model $model): JsonResponse
    {
        /** @var string $resourceForSearch */
        $output = isset($this->modelClass::$resourceForSearch)
            ? new $this->modelClass::$resourceForSearch($model)
            : $model;

        return response()->json($output);
    }
}
