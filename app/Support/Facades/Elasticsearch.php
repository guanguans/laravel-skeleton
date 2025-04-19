<?php

/** @noinspection PhpFullyQualifiedNameUsageInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Support\Facades;

use App\Support\Managers\ElasticsearchManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string getDefaultDriver()
 * @method static \Elastic\Elasticsearch\Client connection(string|null $connection = null)
 * @method static mixed driver(string|null $driver = null)
 * @method static \App\Support\Managers\ElasticsearchManager extend(string $driver, \Closure $callback)
 * @method static array getDrivers()
 * @method static \Illuminate\Contracts\Container\Container getContainer()
 * @method static \App\Support\Managers\ElasticsearchManager setContainer(\Illuminate\Contracts\Container\Container $container)
 * @method static \App\Support\Managers\ElasticsearchManager forgetDrivers()
 * @method static \App\Support\Managers\ElasticsearchManager|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \App\Support\Managers\ElasticsearchManager|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \Illuminate\Support\HigherOrderTapProxy|\App\Support\Managers\ElasticsearchManager tap(callable|null $callback = null)
 * @method static \Elastic\Transport\Transport getTransport()
 * @method static \Psr\Log\LoggerInterface getLogger()
 * @method static \Elastic\Elasticsearch\Client setAsync(bool $async)
 * @method static bool getAsync()
 * @method static \Elastic\Elasticsearch\Client setElasticMetaHeader(bool $active)
 * @method static bool getElasticMetaHeader()
 * @method static \Elastic\Elasticsearch\Client setResponseException(bool $active)
 * @method static bool getResponseException()
 * @method static void sendRequest(\Psr\Http\Message\RequestInterface $request)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise bulk(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise clearScroll(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise closePointInTime(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise count(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise create(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise delete(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise deleteByQuery(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise deleteByQueryRethrottle(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise deleteScript(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise exists(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise existsSource(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise explain(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise fieldCaps(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise get(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise getScript(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise getScriptContext(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise getScriptLanguages(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise getSource(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise healthReport(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise index(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise info(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise knnSearch(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise mget(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise msearch(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise msearchTemplate(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise mtermvectors(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise openPointInTime(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise ping(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise putScript(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise rankEval(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise reindex(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise reindexRethrottle(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise renderSearchTemplate(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise scriptsPainlessExecute(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise scroll(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise search(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise searchMvt(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise searchShards(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise searchTemplate(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise termsEnum(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise termvectors(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise update(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise updateByQuery(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise updateByQueryRethrottle(array|null $params = null)
 * @method static \Elastic\Elasticsearch\Endpoints\AsyncSearch asyncSearch()
 * @method static \Elastic\Elasticsearch\Endpoints\Autoscaling autoscaling()
 * @method static \Elastic\Elasticsearch\Endpoints\Cat cat()
 * @method static \Elastic\Elasticsearch\Endpoints\Ccr ccr()
 * @method static \Elastic\Elasticsearch\Endpoints\Cluster cluster()
 * @method static \Elastic\Elasticsearch\Endpoints\Connector connector()
 * @method static \Elastic\Elasticsearch\Endpoints\DanglingIndices danglingIndices()
 * @method static \Elastic\Elasticsearch\Endpoints\Enrich enrich()
 * @method static \Elastic\Elasticsearch\Endpoints\Eql eql()
 * @method static \Elastic\Elasticsearch\Endpoints\Esql esql()
 * @method static \Elastic\Elasticsearch\Endpoints\Features features()
 * @method static \Elastic\Elasticsearch\Endpoints\Fleet fleet()
 * @method static \Elastic\Elasticsearch\Endpoints\Graph graph()
 * @method static \Elastic\Elasticsearch\Endpoints\Ilm ilm()
 * @method static \Elastic\Elasticsearch\Endpoints\Indices indices()
 * @method static \Elastic\Elasticsearch\Endpoints\Inference inference()
 * @method static \Elastic\Elasticsearch\Endpoints\Ingest ingest()
 * @method static \Elastic\Elasticsearch\Endpoints\License license()
 * @method static \Elastic\Elasticsearch\Endpoints\Logstash logstash()
 * @method static \Elastic\Elasticsearch\Endpoints\Migration migration()
 * @method static \Elastic\Elasticsearch\Endpoints\Ml ml()
 * @method static \Elastic\Elasticsearch\Endpoints\Monitoring monitoring()
 * @method static \Elastic\Elasticsearch\Endpoints\Nodes nodes()
 * @method static \Elastic\Elasticsearch\Endpoints\Profiling profiling()
 * @method static \Elastic\Elasticsearch\Endpoints\QueryRules queryRules()
 * @method static \Elastic\Elasticsearch\Endpoints\Rollup rollup()
 * @method static \Elastic\Elasticsearch\Endpoints\SearchApplication searchApplication()
 * @method static \Elastic\Elasticsearch\Endpoints\SearchableSnapshots searchableSnapshots()
 * @method static \Elastic\Elasticsearch\Endpoints\Security security()
 * @method static \Elastic\Elasticsearch\Endpoints\Shutdown shutdown()
 * @method static \Elastic\Elasticsearch\Endpoints\Simulate simulate()
 * @method static \Elastic\Elasticsearch\Endpoints\Slm slm()
 * @method static \Elastic\Elasticsearch\Endpoints\Snapshot snapshot()
 * @method static \Elastic\Elasticsearch\Endpoints\Sql sql()
 * @method static \Elastic\Elasticsearch\Endpoints\Ssl ssl()
 * @method static \Elastic\Elasticsearch\Endpoints\Synonyms synonyms()
 * @method static \Elastic\Elasticsearch\Endpoints\Tasks tasks()
 * @method static \Elastic\Elasticsearch\Endpoints\TextStructure textStructure()
 * @method static \Elastic\Elasticsearch\Endpoints\Transform transform()
 * @method static \Elastic\Elasticsearch\Endpoints\Watcher watcher()
 * @method static \Elastic\Elasticsearch\Endpoints\Xpack xpack()
 *
 * @see \App\Support\Managers\ElasticsearchManager
 */
class Elasticsearch extends Facade
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected static function getFacadeAccessor(): string
    {
        return ElasticsearchManager::class;
    }
}
