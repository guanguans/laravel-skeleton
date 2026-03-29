<?php

/** @noinspection PhpFullyQualifiedNameUsageInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
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
 * @method static \Elastic\Elasticsearch\Client setServerless(bool $value)
 * @method static bool getServerless()
 * @method static void sendRequest(\Psr\Http\Message\RequestInterface $request)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed bulk(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed clearScroll(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed closePointInTime(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed count(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed create(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed delete(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed deleteByQuery(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed deleteByQueryRethrottle(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed deleteScript(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed exists(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed existsSource(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed explain(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed fieldCaps(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed get(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed getScript(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed getScriptContext(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed getScriptLanguages(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed getSource(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed healthReport(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed index(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed info(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed mget(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed msearch(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed msearchTemplate(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed mtermvectors(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed openPointInTime(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed ping(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed putScript(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed rankEval(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed reindex(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed reindexRethrottle(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed renderSearchTemplate(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed scriptsPainlessExecute(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed scroll(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed search(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed searchMvt(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed searchShards(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed searchTemplate(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed termsEnum(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed termvectors(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed update(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed updateByQuery(array $params = null)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|mixed updateByQueryRethrottle(array $params = null)
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
 * @method static \Elastic\Elasticsearch\Endpoints\Project project()
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
 * @method static \Elastic\Elasticsearch\Endpoints\Streams streams()
 * @method static \Elastic\Elasticsearch\Endpoints\Synonyms synonyms()
 * @method static \Elastic\Elasticsearch\Endpoints\Tasks tasks()
 * @method static \Elastic\Elasticsearch\Endpoints\TextStructure textStructure()
 * @method static \Elastic\Elasticsearch\Endpoints\Transform transform()
 * @method static \Elastic\Elasticsearch\Endpoints\Watcher watcher()
 * @method static \Elastic\Elasticsearch\Endpoints\Xpack xpack()
 *
 * @see \App\Support\Managers\ElasticsearchManager
 */
final class Elasticsearch extends Facade
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
