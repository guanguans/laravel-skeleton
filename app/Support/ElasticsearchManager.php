<?php

declare(strict_types=1);

namespace App\Support;

use App\Exceptions\InvalidArgumentException;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Manager;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Tappable;

/**
 * @mixin  \Elasticsearch\Client
 */
class ElasticsearchManager extends Manager
{
    use Conditionable;
    use Tappable;

    public function getDefaultDriver(): string
    {
        return $this->config->get('services.elasticsearch.default');
    }

    public function connection(?string $connection = null): Client
    {
        return $this->driver($connection);
    }

    /**
     * @throws BindingResolutionException
     */
    protected function createDriver($driver): Client
    {
        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver);
        }

        $driverKey = "services.elasticsearch.connections.$driver";

        if (! $this->config->has($driverKey)) {
            throw new InvalidArgumentException("Connection [$driver] not supported.");
        }

        $config = $this->prepareConfig($this->config->get($driverKey));

        $method = sprintf('create%sDriver', Str::studly($driver));

        if (method_exists($this, $method)) {
            return $this->{$method}($config);
        }

        $quiet = (bool) Arr::pull($config, 'quiet');

        return ClientBuilder::fromConfig($config, $quiet);
    }

    /**
     * @throws BindingResolutionException
     */
    private function prepareConfig(array $config): array
    {
        $default = [
            'quiet' => false,
        ];

        $main = Arr::except($this->config->get('services.elasticsearch', []), ['default', 'connections']);

        $config = array_replace_recursive($default, $main, $config);

        if (isset($config['hosts'])) {
            $config['hosts'] = collect($config['hosts'])
                ->reject(function (array|string $host, int|string $index): bool {
                    \is_string($index) and $environment = $index;
                    \is_array($host) and isset($host['env']) and $environment = $host['env'];

                    return isset($environment) && $environment !== $this->config->get('app.env');
                })
                ->all();
        }

        foreach (['logger', 'tracer'] as $key) {
            if (! isset($config[$key])) {
                continue;
            }

            $value = $config[$key];

            $config[$key] = $this->config->has("logging.channels.$value") ? Log::channel($value) : make($value);
        }

        return $config;
    }
}
