<?php

namespace App\Traits;

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
 * @see https://github.com/thiagoprz/crud-tools
 */
trait ControllerCrud
{
    /**
     * Disabling logs if not needed
     *
     * @var bool
     */
    public $disableLogs = false;

    /**
     * @param $forRedirect
     * @return string
     */
    public function getViewPath($forRedirect = false): string
    {
        $ns_prefix = '';
        $ns_prefix_arr = explode('\\', (new \ReflectionObject($this))->getNamespaceName());
        if (end($ns_prefix_arr) != 'Controllers') {
            $ns_prefix = strtolower(end($ns_prefix_arr)) . ($forRedirect ? '/' : '.');
        }
        $model_name_arr = explode('\\', $this->modelClass);

        return $ns_prefix . strtolower(end($model_name_arr));
    }

    /**
     * List index
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
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

        return view($this->getViewPath() . '.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view($this->getViewPath() . '.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $validation = Validator::make($request->all(), $this->modelClass::validateOn());
            if ($validation->fails()) {
                return response()->json([
                    'error' => true,'errors' => $validation->errors()->messages()
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
        $url = ! $request->input('url_return') ? $this->getViewPath(true) . '/' . $model->id : $request->input('url_return');

        return redirect($url)->with('flash_message', trans('crud.added'));
    }

    /**
     * Display the specified resource.
     *
     *
     * @param  mixed $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, $id)
    {
        if (isset($request->with_trashed) && ! isset($this->modelClass::$withTrashedForbidden)) {
            $model = $this->modelClass::withTrashed()->findOrFail($id);
        } else {
            $model = $this->modelClass::findOrFail($id);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return $this->jsonModel($model);
        }

        return view($this->getViewPath() . '.show', ! $this->disableLogs ? compact('model', 'logs') : compact('model'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  mixed $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $model = $this->modelClass::findOrFail($id);

        return view($this->getViewPath() . '.edit', compact('model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  mixed $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        if ($this->isAjax($request)) {
            $validation = Validator::make($request->all(), $this->modelClass::validateOn('update', $id));
            if ($validation->fails()) {
                return response()->json([
                    'error' => true,'errors' => $validation->errors()->messages()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        } else {
            $this->validate($request, $this->modelClass::validateOn('update', $id));
        }
        $model = $this->modelClass::findOrFail($id);
        $this->handleFileUploads($request, $model);
        $model->update($request->only($model->getFillable()));
        $url = ! $request->input('url_return') ? $this->getViewPath(true) . '/' . $model->id : $request->input('url_return');

        return $this->isAjax($request) ? $this->jsonModel($model) : redirect($url)->with('flash_message', trans('crud.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        if (isset($request->with_trashed) && property_exists($this->modelClass, 'withTrashedForbidden')) {
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

        return $this->isAjax($request) ? response()->json(compact('success', 'error', 'message')) : redirect($url)->with('flash_message', $message);
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function isAjax(Request $request): bool
    {
        return $request->ajax() || $request->wantsJson();
    }

    /**
     * Returns JSON representation of object
     *
     * @param $model
     * @return JsonResponse
     */
    private function jsonModel($model): JsonResponse
    {
        /** @var string $resourceForSearch */
        $output = isset($this->modelClass::$resourceForSearch) ? new $this->modelClass::$resourceForSearch($model) : $model;

        return response()->json($output);
    }

    /**
     * @param Request $request
     * @param \Illuminate\Database\Eloquent\Model|null $model
     * @return void
     */
    public function handleFileUploads(Request $request, $model = null): void
    {
        $file_uploads = $this->modelClass::fileUploads($model);
        foreach ($file_uploads as $file_upload => $file_data) {
            if ($request->hasFile($file_upload)) {
                $file = $request->file($file_upload);
                $upload = Storage::putFileAs($file_data['path'], $file, ! isset($file_data['name']) ? $file->getClientOriginalName() : $file_data['name']);
                $requestData[$file_upload] = $upload;
            }
        }
    }
}
