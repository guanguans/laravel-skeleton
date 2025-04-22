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

namespace App\Providers;

use App\Support\Contracts\ShouldRegisterContract;
use App\View\Components\AlertComponent;
use App\View\Composers\RequestComposer;
use App\View\Creators\RequestCreator;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\View\View;

class ViewServiceProvider extends ServiceProvider implements ShouldRegisterContract
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    public function boot(): void
    {
        // Paginator::useBootstrap();
        // Paginator::useBootstrapFour();
        // Paginator::useBootstrapFive();
        // Paginator::defaultView('pagination::bulma');
        // Paginator::defaultSimpleView('pagination::simple-bulma');
        // Blade::withoutDoubleEncoding(); // 禁用 HTML 实体双重编码

        // Vite::useWaterfallPrefetching(concurrency: 10);
        // Vite::useAggressivePrefetching();
        // Vite::usePrefetchStrategy('waterfall', ['concurrency' => 1]);
        // Vite::useBuildDirectory('.build');
        // Vite::prefetch(4);

        // @see https://www.harrisrafto.eu/simplifying-view-path-management-with-laravels-prependlocation/
        // View::prependLocation(resource_path('custom-views'));

        $this->extendView();
    }

    public function shouldRegister(): bool
    {
        return true;
    }

    /**
     * @throws BindingResolutionException
     */
    private function extendView(): void
    {
        /** @see https://www.harrisrafto.eu/simplifying-view-logic-with-laravel-blades-service-injection */
        // resources/views/dashboard.blade.php
        // @inject('metrics', 'App\Services\DashboardMetricsService');

        /** @see https://www.harrisrafto.eu/enhancing-frontend-interactivity-with-laravel-blade-fragments */
        // return view('dashboard', ['users' => $users])->fragment('user-list');

        // 合成器
        $this->app->make(Factory::class)->composer('*', RequestComposer::class);
        $this->app->make(Factory::class)->composer('*', function (View $view): void {
            $view->with('request', $this->app->make(Request::class))
                ->with('user', $this->app->make(\Illuminate\Contracts\Auth\Factory::class)->user())
                ->with('config', $this->app->make(Repository::class));
        });

        // 构造器
        $this->app->make(Factory::class)->creator('*', RequestCreator::class);
        $this->app->make(Factory::class)->creator('*', function (View $view): void {
            $view->with('request', $this->app->make(Request::class))
                ->with('user', $this->app->make(\Illuminate\Contracts\Auth\Factory::class)->user())
                ->with('config', $this->app->make(Repository::class));
        });

        // 共享数据
        $this->app->make(Factory::class)->share('request', $this->app->make(Request::class));
        $this->app->make(Factory::class)->share('user', $this->app->make(\Illuminate\Contracts\Auth\Factory::class)->user());
        $this->app->make(Factory::class)->share('config', $this->app->make(Repository::class));

        // 注册组件
        Blade::component('alert', AlertComponent::class);

        /*
         * 扩展 Blade
         *
         * ```blade
         *
         * @datetime($timestamp, $format)
         * ```
         */
        Blade::directive('datetime', static function (string $expression): string {
            // 通用解析表达式
            $parts = value(static function (string $expression): array {
                // clean
                $parts = array_map(trim(...), explode(',', Blade::stripParentheses($expression)));
                // filter
                $parts = array_filter($parts, static fn (string $part): bool => '' !== $part);

                // default
                return $parts + ['time()', "'Y m d H:i:s'"];
            }, $expression);

            $newExpression = implode(', ', array_reverse($parts));

            return "<?php echo date($newExpression);?>";
        });

        /*
         * 自定义 if 声明
         *
         * ```blade
         *
         * @disk('local')
         *     <! --应用正在使用 local 存储...-->
         *
         * @elsedisk('s3')
         *     <! --应用正在使用 s3 存储...-->
         *
         * @else
         *     <! --应用正在使用其他存储...-->
         *
         * @enddisk
         *
         * @unlessdisk('local')
         *     < ! --应用当前没有使用 local 存储...-->
         *
         * @enddisk
         * ```
         */
        Blade::if('disk', static fn ($value): bool => config('filesystems.default') === $value);

        // 回显变量
        Blade::stringable(static fn (Request $request): string => json_encode(
            $request->all(),
            \JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT
        ));

        // Directive::callback('limit', static fn ($value, $limit = 100, $end = '...') => Str::limit(
        //     $value,
        //     $limit,
        //     $end
        // ));
        //
        // Directive::compile('slugify', static fn (
        //     $title,
        //     $separator = '-',
        //     $language = 'en',
        //     $dictionary = ['@' => 'at']
        /* ): string => '<?php echo '.Str::class.'::slug($title, $separator, $language, $dictionary); ?>'); */
    }
}
