<?php

namespace App\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Arr;
use Jiannei\Response\Laravel\Response as JianneiResponse;

class Response extends JianneiResponse
{
    public function success($data = [], string $message = '', int $code = 200, array $headers = [], int $option = 0)
    {
        if ($data instanceof ResourceCollection) {
            return $this->formatResourceCollectionResponse(...func_get_args());
        }

        if ($data instanceof JsonResource || $data instanceof \Illuminate\Http\Resources\Json\JsonResource) {
            return $this->formatResourceResponse(...func_get_args());
        }

        if ($data instanceof AbstractPaginator) {
            return $this->formatPaginatedResponse(...func_get_args());
        }

        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        return $this->formatArrayResponse(Arr::wrap($data), $message, $code, $headers, $option);
    }
}
