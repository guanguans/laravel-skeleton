<?php

namespace App\Support\Signer;

abstract class Signer
{
    abstract public function sign(array $payload): string;

    abstract public function validate(string $signature, array $payload): bool;

    protected function sort(array $payload): array
    {
        ksort($payload);

        foreach ($payload as &$item) {
            is_array($item) and $item = $this->sort($item);
        }

        return $payload;
    }

    protected function transformToPreEncryptedData(array $payload): string
    {
        $sortedPayload = $this->sort($payload);

        return http_build_query($sortedPayload);
    }

    /**
     * 转换值是多维数组的情况下
     *
     * @param  string  $key
     * @param  array|object  $value
     *
     * @return string
     */
    public function transformToQueryStr(string $key, $value): string
    {
        $queryStr = '';

        foreach ($value as $k => $v) {
            if (is_null($v)) {
                continue;
            }
            $v === 0 and $v = '0';
            $v === false and $v = '0';

            $queryStrKey = "{$key}[{$k}]";
            if (is_array($v) || is_object($v)) {
                $queryStr .= $this->transformToQueryStr($queryStrKey, $v);
            } else {
                $queryStr .= "$queryStrKey=$v&";
            }
        }

        return $queryStr;
    }

    /**
     * http_build_query 的实现。
     * ```
     * $queryPayload = [
     *     'user' => [
     *         'name' => 'Bob Smith',
     *         'age' => 47,
     *         'sex' => 'M',
     *         'dob' => '5/12/1956'
     *     ],
     *     'pastimes' => ['golf', 'opera', 'poker', 'rap'],
     *     'children' => [
     *         'sally' => ['age' => 8, 'sex' => null],
     *         'bobby' => ['sex' => 'M', 'age' => 12],
     *     ],
     *     'CEO1' => null,
     *     'CEO2' => false,
     *     'CEO3' => true,
     *     'CEO4' => 0,
     *     'CEO5' => 1,
     *     'CEO6' => 0.0,
     *     'CEO7' => 0.1,
     *     'CEO8' => [],
     *     'CEO9' => '',
     *     'CE10' => new \stdClass(),
     * ];
     * ```
     *
     * @param  array  $queryPayload
     * @param  bool  $isUrlencoded
     *
     * @return string
     */
    public function httpBuildQuery(array $queryPayload, bool $isUrlencoded = true): string
    {
        reset($queryPayload);

        $queryStr = '';

        foreach ($queryPayload as $key => $value) {
            // 特殊值处理
            if (is_null($value)) {
                continue;
            }
            $value === 0 and $value = '0';
            $value === false and $value = '0';

            if (is_array($value) || is_object($value)) {
                $queryStr .= $this->transformToQueryStr($key, $value);
            } else {
                $queryStr .= "$key=$value&";
            }
        }

        $queryStr = substr($queryStr, 0, -1);

        $isUrlencoded and $queryStr = urlencode($queryStr);

        return $queryStr;
    }
}
