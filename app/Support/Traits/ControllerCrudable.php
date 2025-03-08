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

namespace App\Support\Traits;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
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
     */
    public function index(Request $request): JsonResponse|View
    {
        $items = $this->modelClass::search($request->all());

        if ($request->ajax() || $request->wantsJson()) {
            if (property_exists($this->modelClass, 'resourceForSearch')) {
                return $items;
            }

            return new JsonResponse($items);
        }

        return view($this->getViewPath().'.index', ['items' => $items]);
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
     */
    public function store(Request $request): JsonResponse|Redirector|RedirectResponse
    {
        if ($request->ajax() || $request->wantsJson()) {
            $validation = Validator::make($request->all(), $this->modelClass::validateOn());

            if ($validation->fails()) {
                return new JsonResponse(['error' => true, 'errors' => $validation->errors()->messages()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        } else {
            $this->validate($request, $this->modelClass::validateOn());
        }

        $model = new $this->modelClass;
        $model->fill($request->only($model->getFillable()));
        $model->save();

        $this->handleFileUploads($request, $model);

        if ($request->ajax() || $request->wantsJson()) {
            return $this->jsonModel($model);
        }

        $url = $request->input('url_return') ?: $this->getViewPath(true).'/'.$model->id;

        return redirect($url)->with('flash_message', trans('crud.added'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, mixed $id): JsonResponse|View
    {
        if ($request->has('with_trashed') && property_exists($this->modelClass, 'withTrashedForbidden')) {
            $model = $this->modelClass::withTrashed()->findOrFail($id);
        } else {
            $model = $this->modelClass::findOrFail($id);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return $this->jsonModel($model);
        }

        return view($this->getViewPath().'.show', ['model' => $model]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(mixed $id): View
    {
        $model = $this->modelClass::findOrFail($id);

        return view($this->getViewPath().'.edit', ['model' => $model]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, mixed $id): JsonResponse|Redirector|RedirectResponse
    {
        /** @var \App\Http\Controllers\Controller $this */
        if ($this->isAjax($request)) {
            $validation = Validator::make($request->all(), $this->modelClass::validateOn('update', $id));

            if ($validation->fails()) {
                return new JsonResponse(['error' => true, 'errors' => $validation->errors()->messages()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        } else {
            $this->validate($request, $this->modelClass::validateOn('update', $id));
        }

        $model = $this->modelClass::findOrFail($id);
        $this->handleFileUploads($request, $model);
        $model->update($request->only($model->getFillable()));

        $url = $request->input('url_return') ?: $this->getViewPath(true).'/'.$model->id;

        return $this->isAjax($request)
            ? $this->jsonModel($model)
            : redirect($url)->with('flash_message', trans('crud.updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, mixed $id): JsonResponse|Redirector|RedirectResponse
    {
        if ($request->has('with_trashed') && property_exists($this->modelClass, 'withTrashedForbidden')) {
            $model = $this->modelClass::withTrashed()->findOrFail($id);
            $count = $model->deleted_at ? $model->forceDelete() : $this->modelClass::destroy($id);
        } else {
            $count = $this->modelClass::destroy($id);
        }

        $url = $request->input('url_return') ?: $this->getViewPath(true);
        $success = 0 < $count;
        $error = !$success;
        $message = $success ? __('crud.deleted') : __('No records were deleted');

        return $this->isAjax($request)
            ? new JsonResponse(['success' => $success, 'error' => $error, 'message' => $message])
            : redirect($url)->with('flash_message', $message);
    }

    public function handleFileUploads(Request $request, ?Model $model = null): void
    {
        $fileUploads = $this->modelClass::fileUploads($model);

        foreach ($fileUploads as $fileUpload => $fileData) {
            if ($request->hasFile($fileUpload)) {
                $file = $request->file($fileUpload);
                $upload = Storage::putFileAs(
                    $fileData['path'],
                    $file,
                    $fileData['name'] ?? $file->getClientOriginalName()
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
        $output = isset($this->modelClass::$resourceForSearch)
            ? new $this->modelClass::$resourceForSearch($model)
            : $model;

        return new JsonResponse($output);
    }
}
