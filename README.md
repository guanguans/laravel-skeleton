# laravel-skeleton

> This project collects the most commonly used Laravel extension packages, as well as usage examples of some functional features, for reference in daily development. - 本项目收集了最常用 Laravel 扩展包、以及一些功能特性的使用范例供日常开发参考使用。

[![Ask DeepWiki](https://deepwiki.com/badge.svg)](https://deepwiki.com/guanguans/laravel-skeleton)
[![zread](https://img.shields.io/badge/Ask_Zread-_.svg?style=flat&color=00b0aa&labelColor=000000&logo=data%3Aimage%2Fsvg%2Bxml%3Bbase64%2CPHN2ZyB3aWR0aD0iMTYiIGhlaWdodD0iMTYiIHZpZXdCb3g9IjAgMCAxNiAxNiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTQuOTYxNTYgMS42MDAxSDIuMjQxNTZDMS44ODgxIDEuNjAwMSAxLjYwMTU2IDEuODg2NjQgMS42MDE1NiAyLjI0MDFWNC45NjAxQzEuNjAxNTYgNS4zMTM1NiAxLjg4ODEgNS42MDAxIDIuMjQxNTYgNS42MDAxSDQuOTYxNTZDNS4zMTUwMiA1LjYwMDEgNS42MDE1NiA1LjMxMzU2IDUuNjAxNTYgNC45NjAxVjIuMjQwMUM1LjYwMTU2IDEuODg2NjQgNS4zMTUwMiAxLjYwMDEgNC45NjE1NiAxLjYwMDFaIiBmaWxsPSIjZmZmIi8%2BCjxwYXRoIGQ9Ik00Ljk2MTU2IDEwLjM5OTlIMi4yNDE1NkMxLjg4ODEgMTAuMzk5OSAxLjYwMTU2IDEwLjY4NjQgMS42MDE1NiAxMS4wMzk5VjEzLjc1OTlDMS42MDE1NiAxNC4xMTM0IDEuODg4MSAxNC4zOTk5IDIuMjQxNTYgMTQuMzk5OUg0Ljk2MTU2QzUuMzE1MDIgMTQuMzk5OSA1LjYwMTU2IDE0LjExMzQgNS42MDE1NiAxMy43NTk5VjExLjAzOTlDNS42MDE1NiAxMC42ODY0IDUuMzE1MDIgMTAuMzk5OSA0Ljk2MTU2IDEwLjM5OTlaIiBmaWxsPSIjZmZmIi8%2BCjxwYXRoIGQ9Ik0xMy43NTg0IDEuNjAwMUgxMS4wMzg0QzEwLjY4NSAxLjYwMDEgMTAuMzk4NCAxLjg4NjY0IDEwLjM5ODQgMi4yNDAxVjQuOTYwMUMxMC4zOTg0IDUuMzEzNTYgMTAuNjg1IDUuNjAwMSAxMS4wMzg0IDUuNjAwMUgxMy43NTg0QzE0LjExMTkgNS42MDAxIDE0LjM5ODQgNS4zMTM1NiAxNC4zOTg0IDQuOTYwMVYyLjI0MDFDMTQuMzk4NCAxLjg4NjY0IDE0LjExMTkgMS42MDAxIDEzLjc1ODQgMS42MDAxWiIgZmlsbD0iI2ZmZiIvPgo8cGF0aCBkPSJNNCAxMkwxMiA0TDQgMTJaIiBmaWxsPSIjZmZmIi8%2BCjxwYXRoIGQ9Ik00IDEyTDEyIDQiIHN0cm9rZT0iI2ZmZiIgc3Ryb2tlLXdpZHRoPSIxLjUiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIvPgo8L3N2Zz4K&logoColor=ffffff)](https://zread.ai/guanguans/laravel-skeleton)
[![tests](https://github.com/guanguans/laravel-skeleton/actions/workflows/tests.yml/badge.svg)](https://github.com/guanguans/laravel-skeleton/actions/workflows/tests.yml)
[![php-cs-fixer](https://github.com/guanguans/laravel-skeleton/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/guanguans/laravel-skeleton/actions/workflows/php-cs-fixer.yml)
[![codecov](https://codecov.io/gh/guanguans/laravel-skeleton/branch/main/graph/badge.svg?token=URGFAWS6S4)](https://codecov.io/gh/guanguans/laravel-skeleton)
![GitHub Tag](https://img.shields.io/github/v/tag/guanguans/laravel-skeleton)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/guanguans/laravel-skeleton)
![GitHub License](https://img.shields.io/github/license/guanguans/laravel-skeleton)

## Reference

<details>
<summary>Composer scripts</summary>

```shell
composer actionlint
composer app:find-static-methods
composer app:fix-neon
composer app:generate-gitleaks-ignore
composer app:install-project
composer app:lint-readme
composer app:parse-phpdoc
composer argtyper
composer argtyper:add-types
composer artisan
composer artisan:envy-prune
composer artisan:envy-sync
composer artisan:ide-helper
composer artisan:solo
composer artisan:solo-dumps
composer artisan:xdebug
composer benchmark
composer blade-formatter
composer blade-formatter:check-formatted
composer blade-formatter:write
composer cghooks
composer cghooks:upsert
composer checks
composer checks:optional
composer checks:required
composer class-leak
composer class-leak:check
composer composer-bump
composer composer-bump:all
composer composer-bump:vendor-bin-common
composer composer-bump:vendor-bin-php85
composer composer-config:disable-process-timeout
composer composer-dependency-analyser
composer composer:audit
composer composer:bin-all-update
composer composer:check-platform-reqs
composer composer:diff
composer composer:normalize
composer composer:normalize-dry-run
composer composer:unlink
composer composer:validate
composer detect-collisions
composer dev
composer ecs
composer ecs:check
composer ecs:check-output-format-json
composer ecs:fix
composer ecs:list-checkers
composer envoy
composer envoy:local
composer envoy:ssh-testing
composer envoy:testing
composer facade:lint
composer facade:update
composer git-chglog
composer gitleaks
composer gitleaks:generate-baseline
composer grumphp
composer jack
composer jack:breakpoint
composer jack:breakpoint-dev
composer jack:open-versions
composer jack:open-versions-dev
composer jack:open-versions-dev-dry-run
composer jack:open-versions-dry-run
composer jack:raise-to-installed
composer jack:raise-to-installed-dry-run
composer jsonlint
composer lint-md
composer lint-md:fix
composer lint-md:prototype
composer mago
composer mago:format
composer mago:format-dry-run
composer mago:init
composer mago:lint
composer mago:lint-compilation
composer mago:lint-dry-run
composer mago:lint-list-rules
composer mago:lint-semantics-only
composer monorepo-builder
composer monorepo-builder:release
composer monorepo-builder:release-1.0.0-BETA1
composer monorepo-builder:release-1.0.0-BETA1-dry-run
composer monorepo-builder:release-major
composer monorepo-builder:release-major-dry-run
composer monorepo-builder:release-minor
composer monorepo-builder:release-minor-dry-run
composer monorepo-builder:release-patch
composer monorepo-builder:release-patch-dry-run
composer neon-lint
composer peck
composer peck:ignore-all
composer peck:init
composer pest
composer pest:bail
composer pest:ci
composer pest:coverage
composer pest:debug
composer pest:dirty
composer pest:drift
composer pest:generate-baseline
composer pest:headed
composer pest:highest
composer pest:migrate-configuration
composer pest:mutate
composer pest:parallel
composer pest:profanity
composer pest:profile
composer pest:retry
composer pest:todos
composer pest:type-coverage
composer pest:update-snapshots
composer php-cs-fixer
composer php-cs-fixer:custom
composer php-cs-fixer:custom-fix
composer php-cs-fixer:custom-fix-dry-run
composer php-cs-fixer:custom-list-files
composer php-cs-fixer:custom-ln-config
composer php-cs-fixer:fix
composer php-cs-fixer:fix-dry-run
composer php-cs-fixer:fix-dry-run-format-annotate-pull-request
composer php-cs-fixer:list-files
composer php-cs-fixer:list-sets
composer php-lint
composer phpbench
composer phpmnd
composer phpstan
composer phpstan:analyse
composer phpstan:analyse-error-format-annotate-pull-request
composer phpstan:analyse-error-format-checkstyle
composer phpstan:analyse-error-format-github
composer phpstan:analyse-error-format-llm
composer phpstan:analyse-error-format-sarif
composer phpstan:analyse-generate-baseline
composer phpstan:analyse-split-baseline
composer phpstan:diagnose
composer phpstan:dump-parameters
composer pint
composer pint:test
composer pint:test-format-annotate-pull-request
composer putenv:composer-memory-unlimited
composer putenv:php
composer putenv:xdebug-off
composer putenv:xdebug-on
composer rector
composer rector:custom-rule
composer rector:list-rules
composer rector:process
composer rector:process-clear-cache
composer rector:process-clear-cache-dry-run
composer rector:process-dry-run
composer rector:process-dry-run-output-format-github
composer rector:process-only
composer rector:process-only-dry-run
composer roave-backward-compatibility-check
composer roave-backward-compatibility-check:format-github-actions
composer rule-doc-generator
composer rule-doc-generator:generate
composer rule-doc-generator:validate
composer setup
composer sk
composer sk:alice-yaml-fixtures-to-php
composer sk:check-commented-code
composer sk:check-conflicts
composer sk:dump-editorconfig
composer sk:finalize-classes
composer sk:finalize-classes-dry-run
composer sk:find-multi-classes
composer sk:generate-symfony-config-builders
composer sk:namespace-to-psr-4
composer sk:namespace-to-psr-4-src
composer sk:namespace-to-psr-4-tests
composer sk:pretty-json
composer sk:pretty-json-dry-run
composer sk:privatize-constants
composer sk:search-regex
composer sk:split-config-per-package
composer sk:spot-lazy-traits
composer test
composer test:coverage
composer todo-lint
composer touch:database-sqlite
composer trap
composer trufflehog
composer typos
composer typos:write-changes
composer var-dump-server:cli
composer var-dump-server:html
composer vendor-patches
composer vhs
composer yaml-lint
composer zhlint
composer zhlint:fix
composer zhlint:prototype
composer zizmor
```

</details>

<details>
<summary>Packages</summary>

* [24slides/laravel-saml2](https://github.com/scaler-tech/laravel-saml2) - SAML2 Service Provider integration for Laravel applications, based on OneLogin toolkit
* [aaronfrancis/fast-paginate](https://github.com/aarondfrancis/fast-paginate) - Fast paginate for Laravel
* [aaronfrancis/flaky](https://github.com/aarondfrancis/flaky) - A Laravel package to elegantly handle flaky operations.
* [ackintosh/ganesha](https://github.com/ackintosh/ganesha) - PHP implementation of Circuit Breaker pattern
* [akaunting/laravel-money](https://github.com/akaunting/laravel-money) - Currency formatting and conversion package for Laravel
* [alexandre-daubois/monolog-processor-collection](https://github.com/alexandre-daubois/monolog-processor-collection) - A collection of Monolog processors
* [algolia/scout-extended](https://github.com/algolia/scout-extended) - Scout Extended extends Laravel Scout adding algolia-specific features
* [algoyounes/circuit-breaker](https://github.com/algoyounes/circuit-breaker) - Circuit Breaker is laravel package that provides a way to handle failures gracefully
* [appstract/laravel-blade-directives](https://github.com/appstract/laravel-blade-directives) - Handy Blade directives
* [appstract/laravel-opcache](https://github.com/appstract/laravel-opcache) - PHP OPcache Artisan commands for Laravel.
* [arifhp86/laravel-clear-expired-cache-file](https://github.com/arifhp86/laravel-clear-expired-cache-file) - Remove laravel expired cache file/folder
* [astrotomic/laravel-translatable](https://github.com/Astrotomic/laravel-translatable) - A Laravel package for multilingual models
* [awobaz/compoships](https://github.com/topclaudy/compoships) - Laravel relationships with support for composite/multiple keys
* [axlon/laravel-postal-code-validation](https://github.com/axlon/laravel-postal-code-validation) - Worldwide postal code validation for Laravel and Lumen
* [balping/json-raw-encoder](git@gitlab.com:balping/json-raw-encoder.git) - Encode arrays to json with raw JS objects (eg. callbacks) in them
* [beyondcode/laravel-mailbox](https://github.com/beyondcode/laravel-mailbox) - Handle incoming emails in your Laravel application.
* [biiiiiigmonster/hasin](https://github.com/biiiiiigmonster/hasin) - Laravel framework relation has in implement
* [binarytorch/larecipe](https://github.com/saleem-hadad/larecipe) - Generate gorgeous recipes for your Laravel applications using MarkDown
* [blade-ui-kit/blade-icons](https://github.com/driesvints/blade-icons) - A package to easily make use of icons in your Laravel Blade views.
* [bramus/monolog-colored-line-formatter](https://github.com/bramus/monolog-colored-line-formatter) - Colored Line Formatter for Monolog
* [calebporzio/sushi](https://github.com/calebporzio/sushi) - Eloquent's missing "array" driver.
* [cerbero/command-validator](https://github.com/cerbero90/command-validator) - Laravel package to validate the input of console commands.
* [cerbero/enum](https://github.com/cerbero90/enum) - Zero-dependencies package to supercharge enum functionalities.
* [cesargb/laravel-magiclink](https://github.com/cesargb/laravel-magiclink) - Create secure link for access to private data or login in Laravel without password
* [christophrumpel/artisan-benchmark](https://github.com/christophrumpel/artisan-benchmark) - Benchmark Artisan Commands
* [cknow/laravel-money](https://github.com/cknow/laravel-money) - Laravel Money
* [cmixin/business-day](https://github.com/kylekatarnls/business-day) - Carbon mixin to handle business days
* [cmixin/business-time](https://github.com/kylekatarnls/business-time) - Carbon mixin to handle business days and opening hours
* [crell/attributeutils](https://github.com/Crell/AttributeUtils) - A robust, flexible attribute handling framework
* [cuyz/valinor](https://github.com/CuyZ/Valinor) - Dependency free PHP library that helps to map any input into a strongly-typed structure.
* [cviebrock/eloquent-sluggable](https://github.com/cviebrock/eloquent-sluggable) - Easy creation of slugs for your Eloquent models in Laravel
* [cweagans/composer-patches](https://github.com/cweagans/composer-patches) - Provides a way to patch Composer packages.
* [dcblogdev/laravel-sent-emails](https://github.com/dcblogdev/laravel-sent-emails) - Store outgoing emails in Laravel
* [divineomega/password_exposed](https://github.com/Jord-JD/password_exposed/releases) - This PHP package provides a `password_exposed` helper function, that uses the haveibeenpwned.com API to check if a password has been exposed in a data breach.
* [dragon-code/laravel-data-dumper](https://github.com/TheDragonCode/laravel-data-dumper) - Adding data from certain tables when executing the `php artisan schema:dump` console command
* [dragon-code/support](https://github.com/TheDragonCode/support) - Support package is a collection of helpers and tools for any project.
* [dyrynda/laravel-cascade-soft-deletes](https://github.com/michaeldyrynda/laravel-cascade-soft-deletes) - Cascading deletes for Eloquent models that implement soft deletes
* [elao/enum](https://github.com/Elao/PhpEnums) - Extended PHP enums capabilities and frameworks integrations
* [elastic/ecs-logging](https://github.com/elastic/ecs-logging-php) - Format and enrich your log files in the elastic common schema
* [elasticsearch/elasticsearch](https://github.com/elastic/elasticsearch-php) - PHP Client for Elasticsearch
* [emreyarligan/enum-concern](https://github.com/emreyarligan/enum-concern) - A PHP package for effortless Enumeration handling with Laravel Collections 📦 ✨
* [fig/cache-util](https://github.com/php-fig/cache-util) - Useful utility classes and traits for implementing the PSR cache standard
* [fig/event-dispatcher-util](https://github.com/php-fig/event-dispatcher-util) - Useful utility classes and traits for implementing the PSR events standard
* [filament/filament](https://github.com/filamentphp/filament) - A collection of full-stack components for accelerated Laravel app development.
* [fntneves/laravel-transactional-events](https://github.com/fntneves/laravel-transactional-events) - Transaction-aware Event Dispatcher for Laravel
* [genealabs/laravel-caffeine](https://github.com/mikebronner/laravel-caffeine) - Keeping Your Laravel Forms Awake
* [graham-campbell/result-type](https://github.com/GrahamCampbell/Result-Type) - An Implementation Of The Result Type
* [guanguans/laravel-api-response](https://github.com/guanguans/laravel-api-response) - Normalize and standardize Laravel API response data structure. - 规范化和标准化 Laravel API 响应数据结构。
* [guanguans/laravel-exception-notify](https://github.com/guanguans/laravel-exception-notify) - Monitor exception and report to the notification channels(Log、Mail、AnPush、Bark、Chanify、DingTalk、Discord、Gitter、GoogleChat、IGot、Lark、Mattermost、MicrosoftTeams、NowPush、Ntfy、Push、Pushback、PushBullet、PushDeer、PushMe、Pushover、PushPlus、QQ、RocketChat、ServerChan、ShowdocPush、SimplePush、Slack、Telegram、WeWork、WPush、XiZhi、YiFengChuanHua、ZohoCliq、ZohoCliqWebHook、Zulip).
* [hamidrezaniazi/pecs](https://github.com/hamidrezaniazi/pecs) - PHP ECS (Elastic Common Schema): Simplify logging with the power of elastic common schema.
* [harris21/laravel-fuse](https://github.com/harris21/laravel-fuse) - Circuit breaker for Laravel queue jobs. Protect your workers from cascading failures.
* [hisorange/browser-detect](https://github.com/hisorange/browser-detect) - Browser & Mobile detection package for Laravel.
* [hosmelq/laravel-pulse-schedule](https://github.com/hosmelq/laravel-pulse-schedule) - Laravel Pulse card that list all scheduled tasks.
* [huangdijia/laravel-horizon-restart](https://github.com/huangdijia/laravel-horizon-restart) - Horizon Restart for Laravel.
* [inertiajs/inertia-laravel](https://github.com/inertiajs/inertia-laravel) - The Laravel adapter for Inertia.js.
* [jasny/sso](https://github.com/jasny/sso) - Simple Single Sign-On
* [jenssegers/agent](https://github.com/jenssegers/agent) - Desktop/mobile user agent parser with support for Laravel, based on Mobiledetect
* [joetannenbaum/chewie](https://github.com/joetannenbaum/chewie)
* [jpkleemans/attribute-events](https://github.com/jpkleemans/attribute-events) - 🔥 Fire events on attribute changes of your Eloquent model
* [juliomotol/laravel-auth-timeout](https://github.com/juliomotol/laravel-auth-timeout) - Authentication Timeout for Laravel
* [kevinrob/guzzle-cache-middleware](https://github.com/Kevinrob/guzzle-cache-middleware) - A HTTP/1.1 Cache for Guzzle 6. It's a simple Middleware to be added in the HandlerStack. (RFC 7234)
* [kirkbushell/eloquence](https://github.com/kirkbushell/eloquence) - A set of extensions adding additional functionality and consistency to Laravel's awesome Eloquent library.
* [kirschbaum-development/eloquent-power-joins](https://github.com/kirschbaum-development/eloquent-power-joins) - The Laravel magic applied to joins.
* [lab404/laravel-impersonate](https://github.com/404labfr/laravel-impersonate) - Laravel Impersonate is a plugin that allows to you to authenticate as your users.
* [laminas/laminas-diagnostics](https://github.com/laminas/laminas-diagnostics) - A set of components for performing diagnostic tests in PHP applications
* [laracasts/presenter](https://github.com/laracasts/Presenter) - Simple view presenters
* [laragear/cache-query](https://github.com/laragear/cache-query) - Remember your query results using only one method. Yes, only one.
* [laragear/discover](https://github.com/Laragear/Discover) - Discover and filter PHP Classes within a directory
* [laragear/populate](https://github.com/Laragear/Populate) - Populate your database with a supercharged, continuable seeder
* [laragear/preload](https://github.com/Laragear/Preload) - Effortlessly make a Preload script for your Laravel application.
* [laragear/two-factor](https://github.com/Laragear/TwoFactor) - On-premises 2FA Authentication for out-of-the-box.
* [laragear/webauthn](https://github.com/Laragear/WebAuthn) - Authenticate users with Passkeys: fingerprints, patterns and biometric data.
* [laravel-frontend-presets/tall](https://github.com/laravel-frontend-presets/tall) - TALL preset for Laravel.
* [laravel-notification-channels/discord](https://github.com/laravel-notification-channels/discord) - Laravel notification driver for Discord.
* [laravel-notification-channels/telegram](https://github.com/laravel-notification-channels/telegram) - Telegram Notifications Channel for Laravel
* [laravel/framework](https://github.com/laravel/framework) - The Laravel Framework.
* [laravel/helpers](https://github.com/laravel/helpers) - Provides backwards compatibility for helpers in the latest Laravel release.
* [laravel/horizon](https://github.com/laravel/horizon) - Dashboard and code-driven configuration for Laravel queues.
* [laravel/octane](https://github.com/laravel/octane) - Supercharge your Laravel application's performance.
* [laravel/pennant](https://github.com/laravel/pennant) - A simple, lightweight library for managing feature flags.
* [laravel/pulse](https://github.com/laravel/pulse) - Laravel Pulse is a real-time application performance monitoring tool and dashboard for your Laravel application.
* [laravel/reverb](https://github.com/laravel/reverb) - Laravel Reverb provides a real-time WebSocket communication backend for Laravel applications.
* [laravel/sanctum](https://github.com/laravel/sanctum) - Laravel Sanctum provides a featherweight authentication system for SPAs and simple APIs.
* [laravel/scout](https://github.com/laravel/scout) - Laravel Scout provides a driver based solution to searching your Eloquent models.
* [laravel/tinker](https://github.com/laravel/tinker) - Powerful REPL for the Laravel framework.
* [laravel/wayfinder](https://github.com/laravel/wayfinder) - Generate TypeScript representations of your Laravel actions and routes.
* [laravolt/avatar](https://github.com/laravolt/avatar) - Turn name, email, and any other string into initial-based avatar or gravatar.
* [leocavalcante/redact-sensitive](https://github.com/leocavalcante/redact-sensitive) - Monolog processor to protect sensitive information from logging
* [leventcz/laravel-top](https://github.com/leventcz/laravel-top) - Real-time monitoring straight from the command line for Laravel applications.
* [livewire/flux](https://github.com/livewire/flux) - The official UI component library for Livewire.
* [lorisleiva/cron-translator](https://github.com/lorisleiva/cron-translator) - Makes CRON expressions human-readable
* [maantje/pulse-database](https://github.com/maantje/pulse-database) - A Laravel Pulse card for database status
* [mcamara/laravel-localization](https://github.com/mcamara/laravel-localization) - Easy localization for Laravel
* [mpyw/laravel-cached-database-stickiness](https://github.com/mpyw/laravel-cached-database-stickiness) - Guarantee database stickiness over the same user's consecutive requests
* [mtownsend/read-time](https://github.com/mtownsend5512/read-time) - A PHP package to show users how long it takes to read content.
* [naoray/laravel-github-monolog](https://github.com/Naoray/laravel-github-monolog) - Log driver to store logs as github issues
* [nette/utils](https://github.com/nette/utils) - 🛠  Nette Utils: lightweight utilities for string & array manipulation, image handling, safe JSON encoding/decoding, validation, slug or strong password generating etc.
* [nunomaduro/essentials](https://github.com/nunomaduro/essentials) - Just better defaults for your Laravel projects.
* [nunomaduro/laravel-console-task](https://github.com/nunomaduro/laravel-console-task) - Laravel Console Task is a output method for your Laravel/Laravel Zero commands.
* [nunomaduro/laravel-optimize-database](https://github.com/nunomaduro/laravel-optimize-database) - Publishes migrations that make your database production ready.
* [nwidart/laravel-modules](https://github.com/nWidart/laravel-modules) - Laravel Module management
* [olvlvl/composer-attribute-collector](https://github.com/olvlvl/composer-attribute-collector) - A convenient and near zero-cost way to retrieve targets of PHP 8 attributes
* [opcodesio/log-viewer](https://github.com/opcodesio/log-viewer) - Fast and easy-to-use log viewer for your Laravel application
* [orchestra/sidekick](https://github.com/orchestral/sidekick) - Packages Toolkit Utilities and Helpers for Laravel
* [overtrue/laravel-uploader](https://github.com/overtrue/laravel-uploader) - An upload component for Laravel.
* [perryvandermeer/laravel-console-validator](https://github.com/PerryvanderMeer/laravel-console-validator) - Validate arguments for Laravel commands
* [phiki/phiki](https://github.com/phikiphp/phiki) - Syntax highlighting using TextMate grammars in PHP.
* [php-ds/php-ds](https://github.com/php-ds/polyfill) - Specialized data structures as alternatives to the PHP array
* [php-open-source-saver/jwt-auth](https://github.com/PHP-Open-Source-Saver/jwt-auth) - JSON Web Token Authentication for Laravel and Lumen
* [php-standard-library/php-standard-library](https://github.com/php-standard-library/php-standard-library) - PHP Standard Library
* [php-static-analysis/attributes](https://github.com/php-static-analysis/attributes) - Attributes used instead of PHPDocs for static analysis tools
* [phpmyadmin/sql-parser](https://github.com/phpmyadmin/sql-parser) - A validating SQL lexer and parser with a focus on MySQL dialect.
* [phpyh/lru-memoizer](https://github.com/phpyh/lru-memoizer) - PHPyh LRU Memoizer
* [pion/laravel-chunk-upload](https://github.com/pionl/laravel-chunk-upload) - Service for chunked upload with several js providers
* [prinsfrank/standards](https://github.com/PrinsFrank/standards) - A collection of standards as PHP Enums: ISO3166, ISO4217, ISO639...
* [prism-php/prism](https://github.com/prism-php/prism) - A powerful Laravel package for integrating Large Language Models (LLMs) into your applications.
* [proengsoft/laravel-jsvalidation](https://github.com/proengsoft/laravel-jsvalidation) - Validate forms transparently with Javascript reusing your Laravel Validation Rules, Messages, and FormRequest
* [propaganistas/laravel-disposable-email](https://github.com/Propaganistas/Laravel-Disposable-Email) - Disposable email validator
* [propaganistas/laravel-phone](https://github.com/Propaganistas/Laravel-Phone) - Adds phone number functionality to Laravel based on Google's libphonenumber API.
* [protonemedia/laravel-cross-eloquent-search](https://github.com/protonemedia/laravel-cross-eloquent-search) - Laravel package to search through multiple Eloquent models. Supports pagination, eager loading relations, single/multiple columns, sorting and scoped queries.
* [protonemedia/laravel-xss-protection](https://github.com/protonemedia/laravel-xss-protection) - Laravel XSS protection, middleware and sanitization
* [reinink/remember-query-strings](https://github.com/reinink/remember-query-strings) - Laravel middleware that automatically remembers and restores query strings.
* [rennokki/laravel-eloquent-query-cache](https://github.com/renoki-co/laravel-eloquent-query-cache) - Adding cache on your Laravel Eloquent queries' results is now a breeze.
* [robclancy/presenter](https://github.com/robclancy/presenter) - Decorate your objects using presenters. Primarily to keep presentation logic out of your models.
* [robsontenorio/mary](https://github.com/robsontenorio/mary) - Gorgeous UI components for Livewire powered by daisyUI and Tailwind
* [ryangjchandler/bearer](https://github.com/ryangjchandler/bearer) - Minimalistic token-based authentication for Laravel API endpoints.
* [ryangjchandler/orbit](https://github.com/ryangjchandler/orbit) - A flat-file database driver for Eloquent.
* [salsify/json-streaming-parser](https://github.com/salsify/jsonstreamingparser) - A streaming parser for JSON in PHP.
* [sheaf/cli](https://github.com/sheafui/cli) - A CLI tool for Sheaf UI
* [skywarth/chaotic-schedule](https://github.com/skywarth/chaotic-schedule) - Randomize scheduled command execution time and date intervals
* [socialiteproviders/weixin](https://github.com/SocialiteProviders/Weixin) - Weixin OAuth2 Provider for Laravel Socialite
* [socialiteproviders/weixin-web](https://github.com/SocialiteProviders/Weixin-Web) - Weixin-Web OAuth2 Provider for Laravel Socialite
* [spatie/db-dumper](https://github.com/spatie/db-dumper) - Dump databases
* [spatie/fork](https://github.com/spatie/fork) - A lightweight solution for running code concurrently in PHP
* [spatie/guzzle-rate-limiter-middleware](https://github.com/spatie/guzzle-rate-limiter-middleware) - A rate limiter for Guzzle
* [spatie/invade](https://github.com/spatie/invade) - A PHP function to work with private properties and methods
* [spatie/laravel-activitylog](https://github.com/spatie/laravel-activitylog) - A very simple activity logger to monitor the users of your website or application
* [spatie/laravel-backup](https://github.com/spatie/laravel-backup) - A Laravel package to backup your application
* [spatie/laravel-collection-macros](https://github.com/spatie/laravel-collection-macros) - A set of useful Laravel collection macros
* [spatie/laravel-cookie-consent](https://github.com/spatie/laravel-cookie-consent) - Make your Laravel app comply with the crazy EU cookie law
* [spatie/laravel-csp](https://github.com/spatie/laravel-csp) - Add CSP headers to the responses of a Laravel app
* [spatie/laravel-data](https://github.com/spatie/laravel-data) - Create unified resources and data transfer objects
* [spatie/laravel-failed-job-monitor](https://github.com/spatie/laravel-failed-job-monitor) - Get notified when a queued job fails
* [spatie/laravel-health](https://github.com/spatie/laravel-health) - Monitor the health of a Laravel application
* [spatie/laravel-http-logger](https://github.com/spatie/laravel-http-logger) - A Laravel package to log HTTP requests
* [spatie/laravel-json-api-paginate](https://github.com/spatie/laravel-json-api-paginate) - A paginator that plays nice with the JSON API spec
* [spatie/laravel-medialibrary](https://github.com/spatie/laravel-medialibrary) - Associate files with Eloquent models
* [spatie/laravel-missing-page-redirector](https://github.com/spatie/laravel-missing-page-redirector) - Redirect missing pages in your Laravel application
* [spatie/laravel-morph-map-generator](https://github.com/spatie/laravel-morph-map-generator) - Automatically generate morph maps in your Laravel application
* [spatie/laravel-query-builder](https://github.com/spatie/laravel-query-builder) - Easily build Eloquent queries from API requests
* [spatie/laravel-responsecache](https://github.com/spatie/laravel-responsecache) - Speed up a Laravel application by caching the entire response
* [spatie/laravel-route-attributes](https://github.com/spatie/laravel-route-attributes) - Auto register routes using PHP attributes
* [spatie/laravel-route-discovery](https://github.com/spatie/laravel-route-discovery) - Auto register routes using PHP attributes
* [spatie/laravel-schedule-monitor](https://github.com/spatie/laravel-schedule-monitor) - Monitor scheduled tasks in a Laravel app
* [spatie/laravel-schemaless-attributes](https://github.com/spatie/laravel-schemaless-attributes) - Add schemaless attributes to Eloquent models
* [spatie/laravel-settings](https://github.com/spatie/laravel-settings) - Store your application settings
* [spatie/laravel-signal-aware-command](https://github.com/spatie/laravel-signal-aware-command) - Handle signals in artisan commands
* [spatie/laravel-sitemap](https://github.com/spatie/laravel-sitemap) - Create and generate sitemaps with ease
* [spatie/laravel-translatable](https://github.com/spatie/laravel-translatable) - A trait to make an Eloquent model hold translations
* [spatie/laravel-webhook-client](https://github.com/spatie/laravel-webhook-client) - Receive webhooks in Laravel apps
* [spatie/packagist-api](https://github.com/spatie/packagist-api) - Fetch package info from Packagist
* [spiral/attributes](https://github.com/spiral/attributes) - PHP attributes reader
* [sqids/sqids](https://github.com/sqids/sqids-php) - Generate short YouTube-looking IDs from numbers
* [square1/laravel-idempotency](https://github.com/square1-io/laravel-idempotency) - Add idempotency to Laravel-based APIs, preventing duplicate request processing.
* [staudenmeir/belongs-to-through](https://github.com/staudenmeir/belongs-to-through) - Laravel Eloquent BelongsToThrough relationships
* [staudenmeir/eloquent-has-many-deep](https://github.com/staudenmeir/eloquent-has-many-deep) - Laravel Eloquent HasManyThrough relationships with unlimited levels
* [staudenmeir/eloquent-json-relations](https://github.com/staudenmeir/eloquent-json-relations) - Laravel Eloquent relationships with JSON keys
* [staudenmeir/laravel-adjacency-list](https://github.com/staudenmeir/laravel-adjacency-list) - Recursive Laravel Eloquent relationships with CTEs
* [staudenmeir/laravel-cte](https://github.com/staudenmeir/laravel-cte) - Laravel queries with common table expressions
* [staudenmeir/laravel-migration-views](https://github.com/staudenmeir/laravel-migration-views) - Laravel database migrations with SQL views
* [stevebauman/location](https://github.com/stevebauman/location) - Retrieve a user's location by their IP Address
* [stillat/blade-parser](https://github.com/Stillat/blade-parser)
* [storviaio/vantage](https://github.com/storviaio/vantage) - Vantage: Strategic queue monitoring and observability for Laravel applications.
* [sunchayn/nimbus](https://github.com/sunchayn/nimbus) - A Laravel package providing an in-browser API client with automatic schema generation, live validation, and built-in authentication with a touch of Laravel-tailored magic for effortless API testing.
* [swiss-devjoy/laravel-optimize-sqlite](https://github.com/Swiss-Devjoy/laravel-optimize-sqlite) - Optimize your SQLite database for production in Laravel
* [symfony/polyfill-php84](https://github.com/symfony/polyfill-php84) - Symfony polyfill backporting some PHP 8.4+ features to lower PHP versions
* [teamtnt/laravel-scout-tntsearch-driver](https://github.com/teamtnt/laravel-scout-tntsearch-driver) - Driver for Laravel Scout search package based on https://github.com/teamtnt/tntsearch
* [tightenco/parental](https://github.com/tighten/parental) - A simple eloquent trait that allows relationships to be accessed through child models.
* [tightenco/ziggy](https://github.com/tighten/ziggy) - Use your Laravel named routes in JavaScript.
* [timacdonald/has-parameters](https://github.com/timacdonald/has-parameters) - A trait that allows you to pass arguments to Laravel middleware in a more PHP'ish way.
* [torann/geoip](https://github.com/Torann/laravel-geoip) - Support for multiple Geographical Location services.
* [unicodeveloper/laravel-password](https://github.com/unicodeveloper/laravel-password) - Protect your users from entering dumb and common passwords
* [utopia-php/system](https://github.com/utopia-php/system) - A simple library for obtaining information about the host's system.
* [valorin/pwned-validator](https://github.com/valorin/pwned-validator) - Super simple Laravel Validator for checking password via the Pwned Passwords service of Have I Been Pwned
* [vectorface/whip](https://github.com/Vectorface/whip) - A PHP class for retrieving accurate IP address information for the client.
* [vinkla/hashids](https://github.com/vinkla/laravel-hashids) - A Hashids bridge for Laravel
* [watson/validating](https://github.com/dwightwatson/validating) - Eloquent model validating trait.
* [wendelladriel/laravel-lift](https://github.com/WendellAdriel/laravel-lift) - Take your Eloquent Models to the next level
* [wendelladriel/laravel-validated-dto](https://github.com/WendellAdriel/laravel-validated-dto) - Data Transfer Objects with validation for Laravel applications
* [wikimedia/composer-merge-plugin](https://github.com/wikimedia/composer-merge-plugin) - Composer plugin to merge multiple composer.json files
* [wildside/userstamps](https://github.com/mattiverse/Laravel-Userstamps) - Laravel Userstamps provides an Eloquent trait which automatically maintains `created_by` and `updated_by` columns on your model, populated by the currently authenticated user in your application.
* [wireui/wireui](https://github.com/wireui/wireui) - TallStack components
* [yiisoft/injector](https://github.com/yiisoft/injector) - PSR-11 compatible injector. Executes a callable and makes an instances by injecting dependencies from a given DI container.
* [zenstruck/stream](https://github.com/zenstruck/stream) - Object wrapper for PHP resources.
* [adamwojs/php-cs-fixer-phpdoc-force-fqcn](https://github.com/adamwojs/php-cs-fixer-phpdoc-force-fqcn)
* [amirami/localizator](https://github.com/amiranagram/localizator) - Localizator is a small tool for Laravel that gives you the ability to extract untranslated strings from project files. It works using the artisan command line and the provided localize command.
* [andreaselia/laravel-api-to-postman](https://github.com/andreaselia/laravel-api-to-postman) - Generate a Postman collection automatically from your Laravel API
* [bamarni/composer-bin-plugin](https://github.com/bamarni/composer-bin-plugin) - No conflicts for your bin dependencies
* [barryvdh/laravel-ide-helper](https://github.com/barryvdh/laravel-ide-helper) - Laravel IDE Helper, generates correct PHPDocs for all Facade classes, to improve auto-completion.
* [beyondcode/laravel-query-detector](https://github.com/beyondcode/laravel-query-detector) - Laravel N+1 Query Detector
* [brainmaestro/composer-git-hooks](https://github.com/BrainMaestro/composer-git-hooks) - Easily manage git hooks in your composer config
* [buggregator/trap](https://github.com/buggregator/trap) - A simple and powerful tool for debugging PHP applications.
* [carthage-software/mago](https://github.com/carthage-software/mago) - Mago is a toolchain for PHP that aims to provide a set of tools to help developers write better code.
* [chrisdicarlo/laravel-config-checker](https://github.com/chrisdicarlo/laravel-config-checker) - Package to check that configuration key references actually exist in your config files.
* [composer/composer](https://github.com/composer/composer) - Composer helps you declare, manage and install dependencies of PHP projects. It ensures you have the right stack everywhere.
* [dealerdirect/phpcodesniffer-composer-installer](https://github.com/PHPCSStandards/composer-installer) - PHP_CodeSniffer Standards Composer Installer Plugin
* [dedoc/scramble](https://github.com/dedoc/scramble) - Automatic generation of API documentation for Laravel applications.
* [defstudio/pest-plugin-laravel-expectations](https://github.com/defstudio/pest-plugin-laravel-expectations) - A plugin to add laravel tailored expectations to Pest
* [deployer/deployer](https://github.com/deployphp/deployer) - Deployment Tool
* [dragon-code/benchmark](https://github.com/TheDragonCode/benchmark) - Simple comparison of code execution speed between different options
* [dragon-code/migrate-db](https://github.com/TheDragonCode/migrate-db) - Easy data transfer from one database to another
* [dragon-code/pretty-routes](https://github.com/TheDragonCode/pretty-routes) - Pretty Routes for Laravel
* [driftingly/rector-laravel](https://github.com/driftingly/rector-laravel) - Rector upgrades rules for Laravel Framework
* [ergebnis/composer-normalize](https://github.com/ergebnis/composer-normalize) - Provides a composer plugin for normalizing composer.json.
* [ergebnis/license](https://github.com/ergebnis/license) - Provides an abstraction of an open-source license.
* [ergebnis/php-cs-fixer-config](https://github.com/ergebnis/php-cs-fixer-config) - Provides a configuration factory and rule set factories for friendsofphp/php-cs-fixer.
* [ergebnis/phpstan-rules](https://github.com/ergebnis/phpstan-rules) - Provides rules for phpstan/phpstan.
* [ergebnis/rector-rules](https://github.com/ergebnis/rector-rules) - Provides rules for rector/rector.
* [fakerphp/faker](https://github.com/FakerPHP/Faker) - Faker is a PHP library that generates fake data for you.
* [guanguans/laravel-soar](https://github.com/guanguans/laravel-soar) - SQL optimizer and rewriter for laravel. - laravel 的 SQL 优化器和重写器。
* [guanguans/monorepo-builder-worker](https://github.com/guanguans/monorepo-builder-worker) - A set of additional release workers for symplify/monorepo-builder.
* [guanguans/php-cs-fixer-custom-fixers](https://github.com/guanguans/php-cs-fixer-custom-fixers) - Use php-cs-fixer to format bats,blade.php,Dockerfile,env,json,md,mdx,sh,sql,tex,text,toml,txt,xml,yaml...files. - 使用 php-cs-fixer 去格式化 bats、blade.php、Dockerfile、env、json、md、mdx、sh、sql、tex、text、toml、txt、xml、yaml...文件。
* [guanguans/phpstan-rules](https://github.com/guanguans/phpstan-rules) - A set of additional rules for phpstan/phpstan. - 一套针对 `phpstan/phpstan` 的附加规则。
* [guanguans/rector-rules](https://github.com/guanguans/rector-rules) - A set of additional rules for rector/rector. - 一套针对 `rector/rector` 的附加规则。
* [ion-bazan/composer-diff](https://github.com/IonBazan/composer-diff) - Compares composer.lock changes and generates Markdown report so you can use it in PR description.
* [jasonmccreary/laravel-test-assertions](https://github.com/jasonmccreary/laravel-test-assertions) - A set of helpful assertions when testing Laravel applications.
* [josezenem/laravel-make-migration-pivot](https://github.com/josezenem/laravel-make-migration-pivot) - Make Laravel pivot tables using the new Laravel 9 closure migrations.
* [kitloong/laravel-migrations-generator](https://github.com/kitloong/laravel-migrations-generator) - Generates Laravel Migrations from an existing database
* [knuckleswtf/scribe](https://github.com/knuckleswtf/scribe) - Generate API documentation for humans from your Laravel codebase.✍
* [laracraft-tech/laravel-schema-rules](https://github.com/laracraft-tech/laravel-schema-rules) - Automatically generate Laravel validation rules based on your database table schema!
* [larastan/larastan](https://github.com/larastan/larastan) - Larastan - Discover bugs in your code without running it. A phpstan/phpstan extension for Laravel
* [laravel-lang/common](https://github.com/Laravel-Lang/common) - Easily connect the necessary language packs to the application
* [laravel-shift/factory-generator](https://github.com/laravel-shift/factory-generator) - Generate factories from existing models
* [laravel/boost](https://github.com/laravel/boost) - Laravel Boost accelerates AI-assisted development by providing the essential context and structure that AI needs to generate high-quality, Laravel-specific code.
* [laravel/browser-kit-testing](https://github.com/laravel/browser-kit-testing) - Provides backwards compatibility for BrowserKit testing in the latest Laravel release.
* [laravel/dusk](https://github.com/laravel/dusk) - Laravel Dusk provides simple end-to-end testing and browser automation.
* [laravel/envoy](https://github.com/laravel/envoy) - Elegant SSH tasks for PHP.
* [laravel/facade-documenter](https://github.com/laravel/facade-documenter/tree/main)
* [laravel/mcp](https://github.com/laravel/mcp) - Rapidly build MCP servers for your Laravel applications.
* [laravel/pail](https://github.com/laravel/pail) - Easily delve into your Laravel application's log files directly from the command line.
* [laravel/pint](https://github.com/laravel/pint) - An opinionated code formatter for PHP.
* [laravel/roster](https://github.com/laravel/roster) - Detect packages & approaches in use within a Laravel project
* [laravel/sail](https://github.com/laravel/sail) - Docker files for running a basic Laravel application.
* [laravel/telescope](https://github.com/laravel/telescope) - An elegant debug assistant for the Laravel framework.
* [mockery/mockery](https://github.com/mockery/mockery) - Mockery is a simple yet flexible PHP mock object framework
* [msamgan/laravel-env-keys-checker](https://github.com/msamgan/laravel-env-keys-checker) - check if all the keys are available in all the .env files.
* [muhammadhuzaifa/telescope-guzzle-watcher](https://github.com/huzaifaarain/telescope-guzzle-watcher) - Telescope Guzzle Watcher provide a custom watcher for intercepting http requests made via guzzlehttp/guzzle php library. The package uses the on_stats request option for extracting the request/response data. The watcher intercept and log the request into the Laravel Telescope HTTP Client Watcher.
* [nunomaduro/collision](https://github.com/nunomaduro/collision) - Cli error handling for console/command-line PHP applications.
* [orangehill/iseed](https://github.com/orangehill/iseed) - Generate a new Laravel database seed file based on data from the existing database table.
* [pb30/phpstan-composer-analysis](https://github.com/pb30/phpstan-composer-analysis)
* [peckphp/peck](https://github.com/peckphp/peck) - Peck is a powerful CLI tool designed to identify pure wording or spelling (grammar) mistakes in your codebase.
* [pestphp/pest](https://github.com/pestphp/pest) - The elegant PHP Testing Framework.
* [php-static-analysis/rector-rule](https://github.com/php-static-analysis/rector-rule) - RectorPHP rule to convert PHPDoc annotations for static analysis to PHP attributes
* [phpcompatibility/php-compatibility](https://github.com/PHPCompatibility/PHPCompatibility) - A set of sniffs for PHP_CodeSniffer that checks for PHP cross-version compatibility.
* [phpstan/extension-installer](https://github.com/phpstan/extension-installer) - Composer plugin for automatic installation of PHPStan extensions
* [phpstan/phpstan-deprecation-rules](https://github.com/phpstan/phpstan-deprecation-rules) - PHPStan rules for detecting usage of deprecated classes, methods, properties, constants and traits.
* [phpstan/phpstan-mockery](https://github.com/phpstan/phpstan-mockery) - PHPStan Mockery extension
* [phpstan/phpstan-strict-rules](https://github.com/phpstan/phpstan-strict-rules) - Extra strict and opinionated rules for PHPStan
* [phpstan/phpstan-webmozart-assert](https://github.com/phpstan/phpstan-webmozart-assert) - PHPStan webmozart/assert extension
* [povils/phpmnd](https://github.com/povils/phpmnd) - A tool to detect Magic numbers in codebase
* [rakutentech/laravel-request-docs](https://github.com/rakutentech/laravel-request-docs) - Automatically generate Laravel docs from request rules, controllers and routes
* [rector/jack](https://github.com/rectorphp/jack) - Swiss knife in pocket of every upgrade architect
* [rector/rector](https://github.com/rectorphp/rector) - Instant Upgrade and Automated Refactoring of any PHP code
* [rector/swiss-knife](https://github.com/rectorphp/swiss-knife) - Swiss knife in pocket of every upgrade architect
* [rector/type-perfect](https://github.com/rectorphp/type-perfect) - Next level type declaration checks
* [reliese/laravel](https://github.com/reliese/laravel) - Reliese Components for Laravel Framework code generation.
* [scalar/laravel](https://github.com/scalar/laravel) - Render your OpenAPI-based API reference
* [shipmonk/composer-dependency-analyser](https://github.com/shipmonk-rnd/composer-dependency-analyser) - Fast detection of composer dependency issues (dead dependencies, shadow dependencies, misplaced dependencies)
* [shipmonk/dead-code-detector](https://github.com/shipmonk-rnd/dead-code-detector) - Dead code detector to find unused PHP code via PHPStan extension. Can automatically remove dead PHP code. Supports libraries like Symfony, Doctrine, PHPUnit etc. Detects dead cycles. Can detect dead code that is tested.
* [shipmonk/memory-scanner](https://github.com/shipmonk-rnd/memory-scanner) - Lightweight PHP library for analyzing memory usage, tracking object references, and debugging memory leaks
* [shipmonk/name-collision-detector](https://github.com/shipmonk-rnd/name-collision-detector) - Simple tool to find ambiguous classes or any other name duplicates within your project.
* [shipmonk/phpstan-baseline-per-identifier](https://github.com/shipmonk-rnd/phpstan-baseline-per-identifier) - Split your PHPStan baseline into multiple files, one per error identifier. Supports both neon baseline and PHP baseline.
* [soloterm/dumps](https://github.com/soloterm/dumps) - A Laravel command to intercept dumps from your Laravel application.
* [soloterm/solo](https://github.com/soloterm/solo) - A Laravel package to run multiple commands at once, to aid in local development.
* [spatie/laravel-error-solutions](https://github.com/spatie/laravel-error-solutions) - Display solutions on the Laravel error page
* [spatie/laravel-horizon-watcher](https://github.com/spatie/laravel-horizon-watcher) - Automatically restart Horizon when local PHP files change
* [spatie/laravel-ignition](https://github.com/spatie/laravel-ignition) - A beautiful error page for Laravel applications.
* [spatie/laravel-login-link](https://github.com/spatie/laravel-login-link) - Quickly login to your local environment
* [spatie/laravel-stubs](https://github.com/spatie/laravel-stubs) - Opinionated Laravel stubs
* [spaze/phpstan-disallowed-calls](https://github.com/spaze/phpstan-disallowed-calls) - PHPStan rules to detect disallowed method & function calls, constant, namespace, attribute, property & superglobal usages, with powerful rules to re-allow a call or a usage in places where it should be allowed.
* [sti3bas/laravel-scout-array-driver](https://github.com/Sti3bas/laravel-scout-array-driver) - Array driver for Laravel Scout
* [symfony/thanks](https://github.com/symfony/thanks) - Encourages sending ⭐ and 💵 to fellow PHP package maintainers (not limited to Symfony components)!
* [symplify/coding-standard](https://github.com/symplify/coding-standard) - Set of Symplify rules for PHP_CodeSniffer and PHP CS Fixer.
* [symplify/easy-coding-standard](https://github.com/easy-coding-standard/easy-coding-standard) - Use Coding Standard with 0-knowledge of PHP-CS-Fixer and PHP_CodeSniffer
* [symplify/phpstan-rules](https://github.com/symplify/phpstan-rules) - Set of Symplify rules for PHPStan
* [symplify/rule-doc-generator-contracts](https://github.com/symplify/rule-doc-generator-contracts) - Contracts for production code of RuleDocGenerator
* [symplify/vendor-patches](https://github.com/symplify/vendor-patches) - Generate vendor patches for packages with single command
* [thedoctor0/laravel-factory-generator](https://github.com/TheDoctor0/laravel-factory-generator) - Automatically generate Laravel factories for your models.
* [tomasvotruba/class-leak](https://github.com/TomasVotruba/class-leak) - Detect leaking classes
* [tomasvotruba/cognitive-complexity](https://github.com/TomasVotruba/cognitive-complexity) - PHPStan rules to measure cognitive complexity of your classes and methods
* [tomasvotruba/ctor](https://github.com/TomasVotruba/ctor) - Prefer constructor over always called setters
* [tomasvotruba/type-coverage](https://github.com/TomasVotruba/type-coverage) - Measure type coverage of your project
* [vcian/laravel-db-auditor](https://github.com/vcian/laravel-db-auditor) - Database DB Auditor provide leverage to audit your MySql,sqlite, PostgreSQL database standards and also provide options to add constraints in table.
* [whatsdiff/whatsdiff](https://github.com/whatsdiff/whatsdiff) - See what's changed in your project's dependencies
* [worksome/envy](https://github.com/worksome/envy) - Automatically keep your .env files in sync.
* [worksome/request-factories](https://github.com/worksome/request-factories) - Test Form Requests in Laravel without all of the boilerplate.
* [yamadashy/phpstan-friendly-formatter](https://github.com/yamadashy/phpstan-friendly-formatter) - Simple error formatter for PHPStan that display code frame

</details>

<details>
<summary>App tree</summary>

```shell
app/
|-- Application.php
|-- Casts/
|   |-- CallbackGetCast.php
|   |-- CallbackSetCast.php
|   |-- CommaSeparatedToArrayCast.php
|   |-- CommaSeparatedToArrayCastUsing.php
|   |-- CommaSeparatedToIntegerArrayCast.php
|   `-- CurrencyCast.php
|-- Console/
|   `-- Commands/
|       |-- CheckServiceProviderCommand.php
|       |-- ClearAllCommand.php
|       |-- ClearLogsCommand.php
|       |-- Command.php
|       |-- Concerns/
|       |   |-- AskForPassword.php
|       |   |-- Graceful.php
|       |   `-- Rescuer.php
|       |-- HealthCheckCommand.php
|       |-- IdeHelperChoresCommand.php
|       |-- InflectorCommand.php
|       |-- OpcacheUrlCommand.php
|       |-- OptimizeAllCommand.php
|       |-- ReferenceCommand.php
|       |-- ShowUnsupportedRequiresCommand.php
|       `-- UpdateReadmeCommand.php
|-- Enums/
|   `-- ConfigurationKey.php
|-- Exceptions/
|   |-- InvalidRepeatRequestException.php
|   `-- VerifyEmailException.php
|-- Http/
|   |-- Controllers/
|   |   |-- Api/
|   |   |   |-- AuthController.php
|   |   |   |-- ChunkUploadController.php
|   |   |   |-- Controller.php
|   |   |   |-- CurdController.php
|   |   |   |-- PingController.php
|   |   |   `-- UploadController.php
|   |   `-- Controller.php
|   |-- Middleware/
|   |   |-- AbortIf.php
|   |   |-- AbortIfProduction.php
|   |   |-- AddContentLength.php
|   |   |-- BasicAuthentication.php
|   |   |-- Cors.php
|   |   |-- DisableFloc.php
|   |   |-- EnsureVerifiedEmailsForSignInUsers.php
|   |   |-- HasValidSignature.php
|   |   |-- HttpsProtocol.php
|   |   |-- IsDeveloper.php
|   |   |-- IsRouteIgnored.php
|   |   |-- Localization.php
|   |   |-- LogHttp.php
|   |   |-- MustBeAdmin.php
|   |   |-- RedirectUppercase.php
|   |   |-- RequiredJson.php
|   |   |-- SetAcceptHeader.php
|   |   |-- SetDefaultLocaleForUrls.php
|   |   |-- SetJsonResponseEncodingOptions.php
|   |   |-- SetLocale.php
|   |   |-- SetTimezone.php
|   |   |-- VerifyFormPaginate.php
|   |   |-- VerifyFormPassword.php
|   |   |-- VerifyJsonContent.php
|   |   |-- VerifySignature.php
|   |   `-- VerifyUserAbility.php
|   |-- Requests/
|   |   |-- Auth/
|   |   |   |-- AuthRequest.php
|   |   |   `-- IndexRequest.php
|   |   `-- FormRequest.php
|   `-- Resources/
|       |-- UserCollection.php
|       `-- UserResource.php
|-- Jobs/
|   |-- Middleware/
|   |   `-- RateLimitedForJob.php
|   `-- SendThirdPartyRequestJob.php
|-- Listeners/
|   |-- CollectGarbageListener.php
|   |-- ContextSubscriber.php
|   |-- PrepareRequestListener.php
|   |-- RecordRequestIdentifiersListener.php
|   |-- RunCommandInDebugModeListener.php
|   `-- TraceEventListener.php
|-- Mail/
|   `-- UserRegisteredMail.php
|-- Models/
|   |-- Model.php
|   |-- Concerns/
|   |   |-- BelongsToCreator.php
|   |   |-- CacheCleaner.php
|   |   |-- ForceUseIndexable.php
|   |   |-- GetModelByUuid.php
|   |   |-- HasPivot.php
|   |   |-- HasSchemalessAttributes.php
|   |   |-- HasWrappedApiTokens.php
|   |   |-- Nullable.php
|   |   |-- Observable.php
|   |   |-- Pipeable.php
|   |   |-- SerializeDate.php
|   |   |-- Trashed.php
|   |   `-- UuidGenerator.php
|   |-- DatabaseNotification.php
|   |-- Example.php
|   |-- HttpLog.php
|   |-- JWTUser.php
|   |-- PersonalAccessToken.php
|   |-- Pivots/
|   |   |-- MorphPivotWithCreatorPivot.php
|   |   `-- PivotWithCreatorPivot.php
|   `-- User.php
|-- Notifications/
|   |-- SlowQueryLoggedNotification.php
|   `-- WelcomeNotification.php
|-- Observers/
|   `-- UserObserver.php
|-- Policies/
|   |-- Policy.php
|   `-- UserPolicy.php
|-- Providers/
|   |-- AppServiceProvider.php
|   |-- AuthServiceProvider.php
|   |-- AutowiredServiceProvider.php
|   |-- CacheServiceProvider.php
|   |-- ConsoleServiceProvider.php
|   |-- DatabaseServiceProvider.php
|   |-- EventServiceProvider.php
|   |-- HttpServiceProvider.php
|   |-- LogServiceProvider.php
|   |-- PackageServiceProvider.php
|   |-- PaginatorServiceProvider.php
|   |-- QueueServiceProvider.php
|   |-- RouteServiceProvider.php
|   |-- SupportServiceProvider.php
|   |-- UnlessProductionAggregateServiceProvider.php
|   |-- ValidatorServiceProvider.php
|   |-- ViewServiceProvider.php
|   |-- WhenLocalAggregateServiceProvider.php
|   `-- WhenTestingAggregateServiceProvider.php
|-- Rules/
|   |-- AbstractAggregateRule.php
|   |-- AbstractRegexRule.php
|   |-- AbstractRule.php
|   |-- AddressIpV4Rule.php
|   |-- AddressIpV6Rule.php
|   |-- BankCardRule.php
|   |-- Base64Rule.php
|   |-- BetweenWordsRule.php
|   |-- BitcoinAddressRule.php
|   |-- CallbackRule.php
|   |-- CamelCaseRule.php
|   |-- CapitalCharWithNumberRule.php
|   |-- CarNumberRule.php
|   |-- ChineseNameRule.php
|   |-- Concerns/
|   |   |-- DataAware.php
|   |   `-- ValidatorAware.php
|   |-- CurrentUserPasswordRule.php
|   |-- DefaultRule.php
|   |-- DomainRule.php
|   |-- DuplicateRule.php
|   |-- EmailRule.php
|   |-- EvenNumberRule.php
|   |-- HexColorRule.php
|   |-- HexRule.php
|   |-- HtmlCleanRule.php
|   |-- HtmlTagRule.php
|   |-- IdCardRule.php
|   |-- ImeiRule.php
|   |-- InstanceofRule.php
|   |-- IntegerBooleanRule.php
|   |-- IpRule.php
|   |-- JwtRule.php
|   |-- KebabCaseRule.php
|   |-- LenientPortRule.php
|   |-- LocationCoordinatesRule.php
|   |-- MacAddressRule.php
|   |-- MaxUploadSizeRule.php
|   |-- MimeTypeRule.php
|   |-- NotDisposableEmailRule.php
|   |-- OddNumberRule.php
|   |-- PhoneCisRule.php
|   |-- PhoneRule.php
|   |-- PhoneWorldRule.php
|   |-- PortRule.php
|   |-- PostalCodeRule.php
|   |-- SemverRule.php
|   |-- SlugRule.php
|   |-- SnakeCaseRule.php
|   |-- StrongPassword.php
|   |-- TimezoneRule.php
|   |-- UlidRule.php
|   |-- UrlRule.php
|   |-- UuidRule.php
|   `-- WithoutWhitespaceRule.php
|-- Support/
|   |-- Attribute/
|   |   |-- Autowired.php
|   |   |-- Elasticsearch.php
|   |   |-- Ignore.php
|   |   `-- Mixin.php
|   |-- BitEncoder.php
|   |-- Bootstrap/
|   |   `-- OutOfMemoryBootstrap.php
|   |-- Carbon/
|   |   `-- NullCarbon.php
|   |-- Client/
|   |   |-- AbstractClient.php
|   |   `-- PushDeer.php
|   |-- ComposerScripts.php
|   |-- Console/
|   |   `-- ProgressBarFactory.php
|   |-- Contract/
|   |   |-- BitEncoderContract.php
|   |   `-- SignerContract.php
|   |-- Facade/
|   |   |-- Elasticsearch.php
|   |   `-- PushDeer.php
|   |-- Guzzle/
|   |   `-- CircuitBreakerMiddleware.php
|   |-- Manager/
|   |   `-- ElasticsearchManager.php
|   |-- Mixin/
|   |   |-- BlueprintMixin.php
|   |   |-- CarbonMixin.php
|   |   |-- CollectionMixin.php
|   |   |-- CommandMixin.php
|   |   |-- GrammarMixin.php
|   |   |-- MySqlGrammarMixin.php
|   |   |-- PendingRequestMixin.php
|   |   |-- QueryBuilder/
|   |   |   |-- OrderByWithQueryBuilderMixin.php
|   |   |   |-- QueryBuilderMixin.php
|   |   |   |-- WhereEndsWithQueryBuilderMixin.php
|   |   |   |-- WhereFindInSetQueryBuilderMixin.php
|   |   |   |-- WhereFullTextQueryBuilderMixin.php
|   |   |   |-- WhereInsQueryBuilderMixin.php
|   |   |   |-- WhereLikeQueryBuilderMixin.php
|   |   |   `-- WhereStartsWithQueryBuilderMixin.php
|   |   |-- RequestMixin.php
|   |   |-- ResponseFactoryMixin.php
|   |   |-- RouterMixin.php
|   |   |-- SchedulingEventMixin.php
|   |   |-- StrMixin.php
|   |   |-- StringableMixin.php
|   |   |-- UploadedFileMixin.php
|   |   `-- ViteMixin.php
|   |-- Monolog/
|   |   |-- EcsFormatterTapper.php
|   |   |-- Formatter/
|   |   |   `-- EloquentLogHttpModelFormatter.php
|   |   |-- Handler/
|   |   |   `-- EloquentHandler.php
|   |   `-- Processor/
|   |       |-- AppendExtraDataProcessor.php
|   |       `-- EloquentLogHttpModelProcessor.php
|   |-- PhpUserFilter/
|   |   `-- CallbackFilter.php
|   |-- Rector/
|   |   |-- ClassHandleMethodRector.php
|   |   `-- rules-overview.md
|   |-- Signer/
|   |   |-- HmacSigner.php
|   |   `-- Utils.php
|   |-- Sse/
|   |   |-- CloseServerSentEventException.php
|   |   `-- ServerSentEvent.php
|   |-- StreamWrapper/
|   |   |-- Concerns/
|   |   |   |-- HasContext.php
|   |   |   `-- Nameable.php
|   |   |-- GlobStreamWrapper.php
|   |   |-- StreamWrapper.php
|   |   `-- UserFileStreamWrapper.php
|   |-- Trait/
|   |   |-- Configurable.php
|   |   |-- Copyable.php
|   |   |-- Immutable.php
|   |   |-- Makeable.php
|   |   |-- Sanitizeable.php
|   |   |-- SetStateable.php
|   |   |-- Singletonable.php
|   |   `-- WithPipeArgs.php
|   |-- VarDumper/
|   |   `-- ServerDumper.php
|   `-- helpers.php
`-- View/
    |-- Components/
    |   `-- AlertComponent.php
    |-- Composers/
    |   `-- RequestComposer.php
    `-- Creators/
        `-- RequestCreator.php

55 directories, 242 files

```

</details>

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

* [guanguans](https://github.com/guanguans)
* [All Contributors](../../contributors)

## Thanks

[![](https://resources.jetbrains.com/storage/products/company/brand/logos/jb_beam.svg)](https://www.jetbrains.com/?from=https://github.com/guanguans)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
