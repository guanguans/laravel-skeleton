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

namespace App\Support\Managers;

use App\Exceptions\InvalidArgumentException;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\ConfigException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Manager;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Tappable;

/**
 * @mixin  \Elastic\Elasticsearch\Client
 */
final class ElasticsearchManager extends Manager
{
    use Conditionable;
    use Tappable;

    #[\Override]
    public function getDefaultDriver(): string
    {
        return $this->config->get('services.elasticsearch.default');
    }

    public function connection(?string $connection = null): Client
    {
        return $this->driver($connection);
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     * @noinspection MissingParentCallInspection
     *
     * @throws \Throwable
     * @throws BindingResolutionException
     * @throws ConfigException
     */
    #[\Override]
    protected function createDriver(mixed $driver): Client
    {
        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver);
        }

        $driverKey = "services.elasticsearch.connections.$driver";

        throw_unless(
            $this->config->has($driverKey),
            InvalidArgumentException::class,
            "Connection [$driver] not supported."
        );

        $config = $this->prepareConfig($this->config->get($driverKey));

        $method = \sprintf('create%sDriver', Str::studly($driver));

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

                    /** @noinspection PhpPossiblePolymorphicInvocationInspection */
                    return isset($environment) && !$this->container->environment($environment);
                })
                ->all();
        }

        if (isset($config['logger'])) {
            $value = $config['logger'];

            $config['logger'] = \is_string($value) && $this->config->has("logging.channels.$value")
                ? Log::channel($value)
                : make($value);
        }

        return $config;
    }
}
