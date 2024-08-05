<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Facades;

use App\Support\ElasticsearchManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string getDefaultDriver()
 * @method static \Elastic\Elasticsearch\Client connection(string|null $connection = null)
 * @method static mixed driver(string|null $driver = null)
 * @method static \App\Support\ElasticsearchManager extend(string $driver, \Closure $callback)
 * @method static array getDrivers()
 * @method static \Illuminate\Contracts\Container\Container getContainer()
 * @method static \App\Support\ElasticsearchManager setContainer(\Illuminate\Contracts\Container\Container $container)
 * @method static \App\Support\ElasticsearchManager forgetDrivers()
 * @method static \App\Support\ElasticsearchManager|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \App\Support\ElasticsearchManager|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static void tap(callable|null $callback = null)
 * @method static \Elastic\Transport\Transport getTransport()
 * @method static \Psr\Log\LoggerInterface getLogger()
 * @method static \Elastic\Elasticsearch\Client setAsync(bool $async)
 * @method static bool getAsync()
 * @method static \Elastic\Elasticsearch\Client setElasticMetaHeader(bool $active)
 * @method static bool getElasticMetaHeader()
 * @method static \Elastic\Elasticsearch\Client setResponseException(bool $active)
 * @method static bool getResponseException()
 * @method static void sendRequest(\Psr\Http\Message\RequestInterface $request)
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise bulk(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise clearScroll(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise closePointInTime(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise count(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise create(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise delete(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise deleteByQuery(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise deleteByQueryRethrottle(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise deleteScript(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise exists(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise existsSource(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise explain(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise fieldCaps(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise get(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise getScript(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise getScriptContext(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise getScriptLanguages(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise getSource(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise healthReport(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise index(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise info(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise knnSearch(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise mget(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise msearch(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise msearchTemplate(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise mtermvectors(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise openPointInTime(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise ping(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise putScript(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise rankEval(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise reindex(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise reindexRethrottle(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise renderSearchTemplate(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise scriptsPainlessExecute(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise scroll(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise search(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise searchMvt(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise searchShards(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise searchTemplate(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise termsEnum(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise termvectors(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise update(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise updateByQuery(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise updateByQueryRethrottle(array $params = [])
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
 * @method static \Elastic\Elasticsearch\Endpoints\QueryRuleset queryRuleset()
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
 * @see \App\Support\ElasticsearchManager
 */
class Elasticsearch extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ElasticsearchManager::class;
    }
}
