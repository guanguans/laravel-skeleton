<?php

namespace App\Traits;

use App\Support\Facades\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

trait WithJsonResponse
{
    /**
     * Return an success response.
     *
     * @param  null  $data
     * @param  string  $message
     * @param  int  $code
     * @param  array  $headers
     * @param  int  $option
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function success($data = null, string $message = '', $code = 200, array $headers = [], $option = 0)
    {
        return Response::success($data, $message, $code, $headers, $option);
    }

    /**
     * Alias of success method, no need to specify data parameter.
     *
     * @param  string  $message
     * @param  int  $code
     * @param  array  $headers
     * @param  int  $option
     * @return JsonResponse|JsonResource
     */
    public function ok(string $message = '', int $code = 200, array $headers = [], int $option = 0)
    {
        return $this->success([], $message, $code, $headers, $option);
    }

    /**
     *  Respond with an accepted response and associate a location and/or content if provided.
     *
     * @param  null  $data
     * @param  string  $message
     * @param  string  $location
     *
     * @return JsonResponse|JsonResource
     */
    public function accepted($data = null, string $message = '', string $location = '')
    {
        $response = $this->success($data, $message, 202);
        if ($location) {
            $response->header('Location', $location);
        }

        return $response;
    }

    /**
     * Respond with a created response and associate a location if provided.
     *
     * @param  null  $data
     * @param  string  $message
     * @param  string  $location
     *
     * @return JsonResponse|JsonResource
     */
    public function created($data = null, string $message = '', string $location = '')
    {
        $response = $this->success($data, $message, 201);
        if ($location) {
            $response->header('Location', $location);
        }

        return $response;
    }

    /**
     * Respond with a no content response.
     *
     * @param  string  $message
     *
     * @return JsonResponse|JsonResource
     */
    public function noContent(string $message = '')
    {
        return $this->success(null, $message, 204);
    }

    /**
     * Return a 400 bad request error.
     *
     * @param  null|string  $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorBadRequest(?string $message = '')
    {
        return $this->fail($message, 400);
    }

    /**
     * Return a 401 unauthorized error.
     *
     * @param  string  $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorUnauthorized(string $message = '')
    {
        return $this->fail($message, 401);
    }

    /**
     * Return a 403 forbidden error.
     *
     * @param  string  $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorForbidden(string $message = '')
    {
        return $this->fail($message, 403);
    }

    /**
     * Return a 404 not found error.
     *
     * @param  string  $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorNotFound(string $message = '')
    {
        return $this->fail($message, 404);
    }

    /**
     * Return a 405 method not allowed error.
     *
     * @param  string  $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorMethodNotAllowed(string $message = '')
    {
        return $this->fail($message, 405);
    }

    /**
     * Return a 500 internal server error.
     *
     * @param  string  $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorInternal(string $message = '')
    {
        return $this->fail($message);
    }

    /**
     * Return an fail response.
     *
     * @param  string  $message
     * @param  int  $code
     * @param  null  $errors
     * @param  array  $header
     * @param  int  $options
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fail(string $message = '', int $code = 500, $errors = null, array $header = [], int $options = 0)
    {
        return Response::fail($message, $code, $errors, $header, $options);
    }
}
