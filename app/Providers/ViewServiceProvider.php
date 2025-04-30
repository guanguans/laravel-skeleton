<?php

/** @noinspection PhpUnusedAliasInspection */

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

use App\Models\User;
use App\View\Components\AlertComponent;
use App\View\Composers\RequestComposer;
use App\View\Creators\RequestCreator;
use Illuminate\Foundation\Exceptions\RegisterErrorViewPaths;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\View\View;
use Stillat\BladeDirectives\Support\Facades\Directive;
use Symfony\Component\HttpFoundation\Response;

final class ViewServiceProvider extends ServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }
    private const int HTTP_STATUS_CODE_LIMIT = 600;

    /**
     * @see https://github.com/laravel/framework/issues/3022
     */
    public function boot(): void
    {
        $this->ever();
        $this->never();
    }

    private function ever(): void
    {
        $this->whenever(true, function (): void {
            Event::listen('creating: errors.*', static function (string $event): void {
                /**
                 * @see https://github.com/laravel/framework/issues/30226
                 */
                sscanf($event, 'creating: errors.%d', $statusCode);

                if (Response::HTTP_BAD_REQUEST <= $statusCode && self::HTTP_STATUS_CODE_LIMIT > $statusCode) {
                    (new RegisterErrorViewPaths)();
                }
            });
            $this->extendBlade();
            $this->extendView();
        });
    }

    private function never(): void
    {
        $this->whenever(false, static function (): void {
            Vite::prefetch(4);
            Vite::useAggressivePrefetching();
            Vite::useBuildDirectory('.build');
            Vite::usePrefetchStrategy('waterfall', ['concurrency' => 1]);
            Vite::useWaterfallPrefetching(concurrency: 10);

            Blade::withoutDoubleEncoding(); // 禁用 HTML 实体双重编码

            /**
             * @see https://www.harrisrafto.eu/simplifying-view-path-management-with-laravels-prependlocation/
             */
            ViewFacade::prependLocation(resource_path('custom-views'));

            /**
             * @see https://www.harrisrafto.eu/enhancing-frontend-interactivity-with-laravel-blade-fragments
             */
            view('welcome', ['users' => User::query()->get()])->fragment('user-list');

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
        });
    }

    private function extendBlade(): void
    {
        /**
         * 注册组件.
         */
        Blade::component('alert', AlertComponent::class);

        /**
         * 扩展 Blade.
         *
         * ```blade
         *
         * @datetime($timestamp, $format)
         * ```
         */
        Blade::directive('datetime', static function (string $expression): string {
            /**
             * 通用解析表达式.
             */
            $parts = value(
                static function (string $expression): array {
                    // clean
                    $parts = array_map(trim(...), explode(',', Blade::stripParentheses($expression)));

                    // filter
                    $parts = array_filter($parts, static fn (string $part): bool => '' !== $part);

                    // default
                    return $parts + ['time()', "'Y m d H:i:s'"];
                },
                $expression
            );

            $newExpression = implode(', ', array_reverse($parts));

            return "<?php echo date($newExpression);?>";
        });

        /**
         * 自定义 if 声明.
         */
        Blade::if('disk', static fn (string $value): bool => config('filesystems.default') === $value);

        /**
         * 回显变量.
         */
        Blade::stringable(static fn (Request $request): string => json_encode(
            $request->all(),
            \JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT
        ));
    }

    /**
     * @see https://www.harrisrafto.eu/simplifying-view-logic-with-laravel-blades-service-injection
     */
    private function extendView(): void
    {
        /**
         * 合成器.
         */
        ViewFacade::composer('*', RequestComposer::class);
        ViewFacade::composer('*', static function (View $view): void {
            $view->with('request', RequestFacade::getFacadeRoot())
                ->with('user', auth()->user())
                ->with('config', config());
        });

        /**
         * 构造器.
         */
        ViewFacade::creator('*', RequestCreator::class);
        ViewFacade::creator('*', static function (View $view): void {
            $view->with('request', RequestFacade::getFacadeRoot())
                ->with('user', auth()->user())
                ->with('config', config());
        });

        /**
         * 共享数据.
         */
        ViewFacade::share('request', RequestFacade::getFacadeRoot());
        ViewFacade::share('user', auth()->user());
        ViewFacade::share('config', config());
    }
}
