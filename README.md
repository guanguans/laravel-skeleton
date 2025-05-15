# laravel-skeleton

> This project collects the most commonly used Laravel extension packages, as well as usage examples of some functional features, for reference in daily development. - 本项目收集了最常用 Laravel 扩展包、以及一些功能特性的使用范例供日常开发参考使用。

[![tests](https://github.com/guanguans/laravel-skeleton/workflows/tests/badge.svg)](https://github.com/guanguans/laravel-skeleton/actions)
[![phpstan](https://github.com/guanguans/laravel-skeleton/actions/workflows/phpstan.yml/badge.svg)](https://github.com/guanguans/laravel-skeleton/actions/workflows/phpstan.yml)
[![check & fix styling](https://github.com/guanguans/laravel-skeleton/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/guanguans/laravel-skeleton/actions)
[![codecov](https://codecov.io/gh/guanguans/laravel-skeleton/branch/main/graph/badge.svg?token=URGFAWS6S4)](https://codecov.io/gh/guanguans/laravel-skeleton)
![GitHub Tag](https://img.shields.io/github/v/tag/guanguans/laravel-skeleton)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/guanguans/laravel-skeleton)
![GitHub License](https://img.shields.io/github/license/guanguans/laravel-skeleton)

## Reference

<details>
<summary>Composer scripts</summary>

```shell
composer ai-commit
composer ai-commit-bito
composer ai-commit-bito-no-verify
composer ai-commit-github-copilot
composer ai-commit-github-copilot-no-verify
composer ai-commit-github-models
composer ai-commit-github-models-no-verify
composer ai-commit-no-verify
composer art
composer art-xdebug
composer artisan
composer artisan-envy-prune
composer artisan-envy-sync
composer artisan-ide-helper
composer artisan-solo
composer artisan-solo-dumps
composer artisan-xdebug
composer benchmark
composer cghooks
composer cghooks-ignore
composer checks
composer class-leak
composer class-leak-check
composer composer-bin-all-update
composer composer-check-platform-reqs
composer composer-dependency-analyser
composer composer-diff
composer composer-disable-process-timeout
composer composer-normalize
composer composer-normalize-dry-run
composer composer-updater
composer composer-updater-dry-run
composer composer-updater-laravel10
composer composer-updater-laravel11
composer composer-validate
composer composer-vendor-patches
composer dev
composer env-put-composer-memory-unlimited
composer env-put-php
composer env-put-xdebug-off
composer env-put-xdebug-on
composer envoy
composer envoy-local
composer envoy-ssh-testing
composer envoy-testing
composer facade-lint
composer facade-update
composer install-project
composer jack
composer jack-breakpoint
composer jack-open-versions
composer jack-open-versions-dev
composer jack-open-versions-dev-dry-run
composer jack-open-versions-dry-run
composer json-lint
composer lint
composer md-fix
composer md-lint
composer neon-lint
composer normalized
composer normalized-dry-run
composer peck
composer peck-init
composer pest
composer pest-bail
composer pest-coverage
composer pest-migrate-configuration
composer pest-parallel
composer pest-profile
composer pest-type-coverage
composer pest-update-snapshots
composer phploc
composer phpmd
composer phpmetrics
composer phpmnd
composer phpstan
composer phpstan-baseline
composer phpstan-split-baseline
composer pint
composer pint-bail
composer pint-dirty
composer pint-dirty-bail
composer pint-dirty-test
composer pint-test
composer rector
composer rector-clear-cache
composer rector-clear-cache-dry-run
composer rector-custom-rule
composer rector-detect-node
composer rector-dry-run
composer rector-list-rules
composer rector-only
composer rector-only-dry-run
composer rector-setup-ci
composer release
composer release-1.0.0-BETA1
composer release-1.0.0-BETA1-dry-run
composer release-major
composer release-major-dry-run
composer release-minor
composer release-minor-dry-run
composer release-patch
composer release-patch-dry-run
composer roave-infection-static-analysis-plugin
composer roave-no-leaks
composer sk
composer sk-alice-yaml-fixtures-to-php
composer sk-check-commented-code
composer sk-check-conflicts
composer sk-dump-editorconfig
composer sk-finalize-classes
composer sk-finalize-classes-dry-run
composer sk-find-multi-classes
composer sk-generate-symfony-config-builders
composer sk-multi-package-composer-stats
composer sk-namespace-to-psr-4
composer sk-pretty-json
composer sk-pretty-json-dry-run
composer sk-privatize-constants
composer sk-search-regex
composer sk-split-config-per-package
composer sk-spot-lazy-traits
composer style-fix
composer style-lint
composer test
composer test-bail
composer test-coverage
composer test-migrate-configuration
composer test-parallel
composer test-profile
composer test-type-coverage
composer test-update-snapshots
composer text-fix
composer text-lint
composer trap
composer trufflehog
composer user-composer
composer user-php
composer var-dump-server
composer vhs
composer yaml-lint
composer zh-fix
composer zh-lint
```
</details>

<details>
<summary>Packages</summary>

* [24slides/laravel-saml2](https://github.com/24Slides/laravel-saml2) - SAML2 Service Provider integration to your Laravel 5.4+ application, based on OneLogin toolkit
* [aaronfrancis/fast-paginate](https://github.com/aarondfrancis/fast-paginate) - Fast paginate for Laravel
* [aaronfrancis/flaky](https://github.com/aarondfrancis/flaky) - A Laravel package to elegantly handle flaky operations.
* [ackintosh/ganesha](https://github.com/ackintosh/ganesha) - PHP implementation of Circuit Breaker pattern
* [akaunting/laravel-money](https://github.com/akaunting/laravel-money) - Currency formatting and conversion package for Laravel
* [alexandre-daubois/monolog-processor-collection](https://github.com/alexandre-daubois/monolog-processor-collection) - A collection of Monolog processors
* [algolia/scout-extended](https://github.com/algolia/scout-extended) - Scout Extended extends Laravel Scout adding algolia-specific features
* [appstract/laravel-blade-directives](https://github.com/appstract/laravel-blade-directives) - Handy Blade directives
* [appstract/laravel-opcache](https://github.com/appstract/laravel-opcache) - PHP OPcache Artisan commands for Laravel.
* [arifhp86/laravel-clear-expired-cache-file](https://github.com/arifhp86/laravel-clear-expired-cache-file) - Remove laravel expired cache file/folder
* [astrotomic/laravel-translatable](https://github.com/Astrotomic/laravel-translatable) - A Laravel package for multilingual models
* [awobaz/compoships](https://github.com/topclaudy/compoships) - Laravel relationships with support for composite/multiple keys
* [axlon/laravel-postal-code-validation](https://github.com/axlon/laravel-postal-code-validation) - Worldwide postal code validation for Laravel and Lumen
* [azjezz/psl](https://github.com/azjezz/psl) - PHP Standard Library
* [balping/json-raw-encoder](git@gitlab.com:balping/json-raw-encoder.git) - Encode arrays to json with raw JS objects (eg. callbacks) in them
* [beyondcode/laravel-mailbox](https://github.com/beyondcode/laravel-mailbox) - Handle incoming emails in your Laravel application.
* [biiiiiigmonster/hasin](https://github.com/biiiiiigmonster/hasin) - Laravel framework relation has in implement
* [binarytorch/larecipe](https://github.com/saleem-hadad/larecipe) - Generate gorgeous recipes for your Laravel applications using MarkDown
* [bramus/monolog-colored-line-formatter](https://github.com/bramus/monolog-colored-line-formatter) - Colored Line Formatter for Monolog
* [calebporzio/sushi](https://github.com/calebporzio/sushi) - Eloquent's missing "array" driver.
* [cerbero/command-validator](https://github.com/cerbero90/command-validator) - Laravel package to validate the input of console commands.
* [cerbero/enum](https://github.com/cerbero90/enum) - Zero-dependencies package to supercharge enum functionalities.
* [cesargb/laravel-magiclink](https://github.com/cesargb/laravel-magiclink) - Create secure link for access to private data or login in Laravel without password
* [christophrumpel/artisan-benchmark](https://github.com/christophrumpel/artisan-benchmark) - Benchmark Artisan Commands
* [cknow/laravel-money](https://github.com/cknow/laravel-money) - Laravel Money
* [crell/attributeutils](https://github.com/Crell/AttributeUtils) - A robust, flexible attribute handling framework
* [cviebrock/eloquent-sluggable](https://github.com/cviebrock/eloquent-sluggable) - Easy creation of slugs for your Eloquent models in Laravel
* [cweagans/composer-patches](https://github.com/cweagans/composer-patches) - Provides a way to patch Composer packages.
* [dcblogdev/laravel-sent-emails](https://github.com/dcblogdev/laravel-sent-emails) - Store outgoing emails in Laravel
* [divineomega/password_exposed](https://github.com/DivineOmega/password_exposed/releases) - This PHP package provides a `password_exposed` helper function, that uses the haveibeenpwned.com API to check if a password has been exposed in a data breach.
* [dragon-code/laravel-data-dumper](https://github.com/TheDragonCode/laravel-data-dumper) - Adding data from certain tables when executing the `php artisan schema:dump` console command
* [dragon-code/support](https://github.com/TheDragonCode/support) - Support package is a collection of helpers and tools for any project.
* [dyrynda/laravel-cascade-soft-deletes](https://github.com/michaeldyrynda/laravel-cascade-soft-deletes) - Cascading deletes for Eloquent models that implement soft deletes
* [echolabsdev/prism](https://github.com/prism-php/prism) - A powerful Laravel package for integrating Large Language Models (LLMs) into your applications.
* [elao/enum](https://github.com/Elao/PhpEnums) - Extended PHP enums capabilities and frameworks integrations
* [elastic/ecs-logging](https://github.com/elastic/ecs-logging-php) - Format and enrich your log files in the elastic common schema
* [elasticsearch/elasticsearch](https://github.com/elastic/elasticsearch-php) - PHP Client for Elasticsearch
* [emreyarligan/enum-concern](https://github.com/emreyarligan/enum-concern) - A PHP package for effortless Enumeration handling with Laravel Collections 📦 ✨
* [fig/cache-util](https://github.com/php-fig/cache-util) - Useful utility classes and traits for implementing the PSR cache standard
* [fig/event-dispatcher-util](https://github.com/php-fig/event-dispatcher-util) - Useful utility classes and traits for implementing the PSR events standard
* [fntneves/laravel-transactional-events](https://github.com/fntneves/laravel-transactional-events) - Transaction-aware Event Dispatcher for Laravel
* [genealabs/laravel-caffeine](https://github.com/mikebronner/laravel-caffeine) - Keeping Your Laravel Forms Awake
* [graham-campbell/result-type](https://github.com/GrahamCampbell/Result-Type) - An Implementation Of The Result Type
* [guanguans/laravel-api-response](https://github.com/guanguans/laravel-api-response) - Normalize and standardize Laravel API response data structures. - 规范化和标准化 Laravel API 响应数据结构。
* [guanguans/laravel-exception-notify](https://github.com/guanguans/laravel-exception-notify) - Monitor exception and report to the notification channels(Dump、Log、Mail、AnPush、Bark、Chanify、DingTalk、Discord、Gitter、GoogleChat、IGot、Lark、Mattermost、MicrosoftTeams、NowPush、Ntfy、Push、Pushback、PushBullet、PushDeer、PushMe、Pushover、PushPlus、QQ、RocketChat、ServerChan、ShowdocPush、SimplePush、Slack、Telegram、WeWork、WPush、XiZhi、YiFengChuanHua、Zulip).
* [hamidrezaniazi/pecs](https://github.com/hamidrezaniazi/pecs) - PHP ECS (Elastic Common Schema): Simplify logging with the power of elastic common schema.
* [hisorange/browser-detect](https://github.com/hisorange/browser-detect) - Browser & Mobile detection package for Laravel.
* [hosmelq/laravel-pulse-schedule](https://github.com/hosmelq/laravel-pulse-schedule) - Laravel Pulse card that list all scheduled tasks.
* [huangdijia/laravel-horizon-restart](https://github.com/huangdijia/laravel-horizon-restart) - Horizon Restart for Laravel.
* [inertiajs/inertia-laravel](https://github.com/inertiajs/inertia-laravel) - The Laravel adapter for Inertia.js.
* [jasny/sso](https://github.com/jasny/sso) - Simple Single Sign-On
* [jenssegers/agent](https://github.com/jenssegers/agent) - Desktop/mobile user agent parser with support for Laravel, based on Mobiledetect
* [jpkleemans/attribute-events](https://github.com/jpkleemans/attribute-events) - 🔥 Fire events on attribute changes of your Eloquent model
* [juliomotol/laravel-auth-timeout](https://github.com/juliomotol/laravel-auth-timeout) - Authentication Timeout for Laravel
* [kevinrob/guzzle-cache-middleware](https://github.com/Kevinrob/guzzle-cache-middleware) - A HTTP/1.1 Cache for Guzzle 6. It's a simple Middleware to be added in the HandlerStack. (RFC 7234)
* [kirkbushell/eloquence](https://github.com/kirkbushell/eloquence) - A set of extensions adding additional functionality and consistency to Laravel's awesome Eloquent library.
* [kirschbaum-development/eloquent-power-joins](https://github.com/kirschbaum-development/eloquent-power-joins) - The Laravel magic applied to joins.
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
* [maantje/pulse-database](https://github.com/maantje/pulse-database) - A Laravel Pulse card for database status
* [mcamara/laravel-localization](https://github.com/mcamara/laravel-localization) - Easy localization for Laravel
* [mpyw/laravel-cached-database-stickiness](https://github.com/mpyw/laravel-cached-database-stickiness) - Guarantee database stickiness over the same user's consecutive requests
* [mtownsend/read-time](https://github.com/mtownsend5512/read-time) - A PHP package to show users how long it takes to read content.
* [naoray/laravel-github-monolog](https://github.com/Naoray/laravel-github-monolog) - Log driver to store logs as github issues
* [nette/utils](https://github.com/nette/utils) - 🛠  Nette Utils: lightweight utilities for string & array manipulation, image handling, safe JSON encoding/decoding, validation, slug or strong password generating etc.
* [nunomaduro/laravel-console-task](https://github.com/nunomaduro/laravel-console-task) - Laravel Console Task is a output method for your Laravel/Laravel Zero commands.
* [nunomaduro/laravel-optimize-database](https://github.com/nunomaduro/laravel-optimize-database) - Publishes migrations that make your database production ready.
* [nwidart/laravel-modules](https://github.com/nWidart/laravel-modules) - Laravel Module management
* [opcodesio/log-viewer](https://github.com/opcodesio/log-viewer) - Fast and easy-to-use log viewer for your Laravel application
* [orchestra/sidekick](https://github.com/orchestral/sidekick) - Packages Toolkit Utilities and Helpers for Laravel
* [overtrue/laravel-uploader](https://github.com/overtrue/laravel-uploader) - An upload component for Laravel.
* [perryvandermeer/laravel-console-validator](https://github.com/PerryvanderMeer/laravel-console-validator) - Validate arguments for Laravel commands
* [phiki/phiki](https://github.com/phikiphp/phiki) - Syntax highlighting using TextMate grammars in PHP.
* [php-ds/php-ds](https://github.com/php-ds/polyfill) - 
* [php-open-source-saver/jwt-auth](https://github.com/PHP-Open-Source-Saver/jwt-auth) - JSON Web Token Authentication for Laravel and Lumen
* [php-static-analysis/attributes](https://github.com/php-static-analysis/attributes) - Attributes used instead of PHPDocs for static analysis tools
* [phpyh/lru-memoizer](https://github.com/phpyh/lru-memoizer) - PHPyh LRU Memoizer
* [pion/laravel-chunk-upload](https://github.com/pionl/laravel-chunk-upload) - Service for chunked upload with several js providers
* [prinsfrank/standards](https://github.com/PrinsFrank/standards) - A collection of standards as PHP Enums: ISO3166, ISO4217, ISO639...
* [proengsoft/laravel-jsvalidation](https://github.com/proengsoft/laravel-jsvalidation) - Validate forms transparently with Javascript reusing your Laravel Validation Rules, Messages, and FormRequest
* [propaganistas/laravel-disposable-email](https://github.com/Propaganistas/Laravel-Disposable-Email) - Disposable email validator
* [rap2hpoutre/laravel-log-viewer](https://github.com/rap2hpoutre/laravel-log-viewer) - A Laravel log reader
* [reinink/remember-query-strings](https://github.com/reinink/remember-query-strings) - Laravel middleware that automatically remembers and restores query strings.
* [rennokki/laravel-eloquent-query-cache](https://github.com/renoki-co/laravel-eloquent-query-cache) - Adding cache on your Laravel Eloquent queries' results is now a breeze.
* [robclancy/presenter](https://github.com/robclancy/presenter) - Decorate your objects using presenters. Primarily to keep presentation logic out of your models.
* [ryangjchandler/bearer](https://github.com/ryangjchandler/bearer) - Minimalistic token-based authentication for Laravel API endpoints.
* [ryangjchandler/orbit](https://github.com/ryangjchandler/orbit) - A flat-file database driver for Eloquent.
* [salsify/json-streaming-parser](https://github.com/salsify/jsonstreamingparser) - A streaming parser for JSON in PHP.
* [skywarth/chaotic-schedule](https://github.com/skywarth/chaotic-schedule) - Randomize scheduled command execution time and date intervals
* [socialiteproviders/weixin](https://github.com/SocialiteProviders/Weixin) - Weixin OAuth2 Provider for Laravel Socialite
* [socialiteproviders/weixin-web](https://github.com/SocialiteProviders/Weixin-Web) - Weixin-Web OAuth2 Provider for Laravel Socialite
* [spatie/db-dumper](https://github.com/spatie/db-dumper) - Dump databases
* [spatie/fork](https://github.com/spatie/fork) - A lightweight solution for running code concurrently in PHP
* [spatie/guzzle-rate-limiter-middleware](https://github.com/spatie/guzzle-rate-limiter-middleware) - A rate limiter for Guzzle
* [spatie/invade](https://github.com/spatie/invade) - A PHP function to work with private properties and methods
* [spatie/laravel-activitylog](https://github.com/spatie/laravel-activitylog) - A very simple activity logger to monitor the users of your website or application
* [spatie/laravel-backup](https://github.com/spatie/laravel-backup) - A Laravel package to backup your application
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
* [staudenmeir/laravel-cte](https://github.com/staudenmeir/laravel-cte/tree/v12.1) - Laravel queries with common table expressions
* [staudenmeir/laravel-migration-views](https://github.com/staudenmeir/laravel-migration-views) - Laravel database migrations with SQL views
* [stevebauman/location](https://github.com/stevebauman/location) - Retrieve a user's location by their IP Address
* [stillat/blade-parser](https://github.com/Stillat/blade-parser) - 
* [swiss-devjoy/laravel-optimize-sqlite](https://github.com/Swiss-Devjoy/laravel-optimize-sqlite) - Optimize your SQLite database for production in Laravel
* [symfony/polyfill-php84](https://github.com/symfony/polyfill-php84) - Symfony polyfill backporting some PHP 8.4+ features to lower PHP versions
* [teamtnt/laravel-scout-tntsearch-driver](https://github.com/teamtnt/laravel-scout-tntsearch-driver) - Driver for Laravel Scout search package based on https://github.com/teamtnt/tntsearch
* [tightenco/parental](https://github.com/tighten/parental) - A simple eloquent trait that allows relationships to be accessed through child models.
* [tightenco/ziggy](https://github.com/tighten/ziggy) - Use your Laravel named routes in JavaScript.
* [tpetry/laravel-mysql-explain](https://github.com/tpetry/laravel-mysql-explain) - Get Visual MySQL EXPLAIN for Laravel.
* [unicodeveloper/laravel-password](https://github.com/unicodeveloper/laravel-password) - Protect your users from entering dumb and common passwords
* [utopia-php/system](https://github.com/utopia-php/system) - A simple library for obtaining information about the host's system.
* [valorin/pwned-validator](https://github.com/valorin/pwned-validator) - Super simple Laravel Validator for checking password via the Pwned Passwords service of Have I Been Pwned
* [vectorface/whip](https://github.com/Vectorface/whip) - A PHP class for retrieving accurate IP address information for the client.
* [vinkla/hashids](https://github.com/vinkla/laravel-hashids) - A Hashids bridge for Laravel
* [watson/validating](https://github.com/dwightwatson/validating) - Eloquent model validating trait.
* [wendelladriel/laravel-validated-dto](https://github.com/WendellAdriel/laravel-validated-dto) - Data Transfer Objects with validation for Laravel applications
* [wikimedia/composer-merge-plugin](https://github.com/wikimedia/composer-merge-plugin) - Composer plugin to merge multiple composer.json files
* [wireui/wireui](https://github.com/wireui/wireui) - TallStack components
* [zenstruck/stream](https://github.com/zenstruck/stream) - Object wrapper for PHP resources.
* [andreaselia/laravel-api-to-postman](https://github.com/andreaselia/laravel-api-to-postman) - Generate a Postman collection automatically from your Laravel API
* [bamarni/composer-bin-plugin](https://github.com/bamarni/composer-bin-plugin) - No conflicts for your bin dependencies
* [barryvdh/laravel-ide-helper](https://github.com/barryvdh/laravel-ide-helper) - Laravel IDE Helper, generates correct PHPDocs for all Facade classes, to improve auto-completion.
* [beyondcode/laravel-query-detector](https://github.com/beyondcode/laravel-query-detector) - Laravel N+1 Query Detector
* [brainmaestro/composer-git-hooks](https://github.com/BrainMaestro/composer-git-hooks) - Easily manage git hooks in your composer config
* [buggregator/trap](https://github.com/buggregator/trap) - A simple and powerful tool for debugging PHP applications.
* [composer/composer](https://github.com/composer/composer) - Composer helps you declare, manage and install dependencies of PHP projects. It ensures you have the right stack everywhere.
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
* [ergebnis/rector-rules](https://github.com/ergebnis/rector-rules) - Provides rules for rector/rector.
* [fakerphp/faker](https://github.com/FakerPHP/Faker) - Faker is a PHP library that generates fake data for you.
* [guanguans/ai-commit](https://github.com/guanguans/ai-commit) - Automagically generate conventional git commit message with AI. - 使用 AI 自动生成约定式 git 提交信息。
* [guanguans/laravel-soar](https://github.com/guanguans/laravel-soar) - SQL optimizer and rewriter for laravel. - laravel 的 SQL 优化器和重写器。
* [guanguans/monorepo-builder-worker](https://github.com/guanguans/monorepo-builder-worker) - List of release worker collections for symplify/monorepo-builder.
* [ion-bazan/composer-diff](https://github.com/IonBazan/composer-diff) - Compares composer.lock changes and generates Markdown report so you can use it in PR description.
* [jasonmccreary/laravel-test-assertions](https://github.com/jasonmccreary/laravel-test-assertions) - A set of helpful assertions when testing Laravel applications.
* [josezenem/laravel-make-migration-pivot](https://github.com/josezenem/laravel-make-migration-pivot) - Make Laravel pivot tables using the new Laravel 9 closure migrations.
* [kitloong/laravel-migrations-generator](https://github.com/kitloong/laravel-migrations-generator) - Generates Laravel Migrations from an existing database
* [knuckleswtf/scribe](https://github.com/knuckleswtf/scribe) - Generate API documentation for humans from your Laravel codebase.✍
* [laracraft-tech/laravel-schema-rules](https://github.com/laracraft-tech/laravel-schema-rules) - Automatically generate Laravel validation rules based on your database table schema!
* [larastan/larastan](https://github.com/larastan/larastan) - Larastan - Discover bugs in your code without running it. A phpstan/phpstan extension for Laravel
* [laravel-lang/common](https://github.com/Laravel-Lang/common) - Easily connect the necessary language packs to the application
* [laravel-shift/factory-generator](https://github.com/laravel-shift/factory-generator) - Generate factories from existing models
* [laravel/envoy](https://github.com/laravel/envoy) - Elegant SSH tasks for PHP.
* [laravel/facade-documenter](https://github.com/laravel/facade-documenter/tree/main) - 
* [laravel/pail](https://github.com/laravel/pail) - Easily delve into your Laravel application's log files directly from the command line.
* [laravel/pint](https://github.com/laravel/pint) - An opinionated code formatter for PHP.
* [laravel/sail](https://github.com/laravel/sail) - Docker files for running a basic Laravel application.
* [laravel/telescope](https://github.com/laravel/telescope) - An elegant debug assistant for the Laravel framework.
* [mockery/mockery](https://github.com/mockery/mockery) - Mockery is a simple yet flexible PHP mock object framework
* [msamgan/laravel-env-keys-checker](https://github.com/msamgan/laravel-env-keys-checker) - check if all the keys are available in all the .env files.
* [muhammadhuzaifa/telescope-guzzle-watcher](https://github.com/huzaifaarain/telescope-guzzle-watcher) - Telescope Guzzle Watcher provide a custom watcher for intercepting http requests made via guzzlehttp/guzzle php library. The package uses the on_stats request option for extracting the request/response data. The watcher intercept and log the request into the Laravel Telescope HTTP Client Watcher.
* [nunomaduro/collision](https://github.com/nunomaduro/collision) - Cli error handling for console/command-line PHP applications.
* [orangehill/iseed](https://github.com/orangehill/iseed) - Generate a new Laravel database seed file based on data from the existing database table.
* [peckphp/peck](https://github.com/peckphp/peck) - Peck is a powerful CLI tool designed to identify pure wording or spelling (grammar) mistakes in your codebase.
* [pestphp/pest](https://github.com/pestphp/pest) - The elegant PHP Testing Framework.
* [php-static-analysis/rector-rule](https://github.com/php-static-analysis/rector-rule) - RectorPHP rule to convert PHPDoc annotations for static analysis to PHP attributes
* [phpstan/extension-installer](https://github.com/phpstan/extension-installer) - Composer plugin for automatic installation of PHPStan extensions
* [phpstan/phpstan-deprecation-rules](https://github.com/phpstan/phpstan-deprecation-rules) - PHPStan rules for detecting usage of deprecated classes, methods, properties, constants and traits.
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
* [shipmonk/phpstan-baseline-per-identifier](https://github.com/shipmonk-rnd/phpstan-baseline-per-identifier) - Split your PHPStan baseline into multiple files, one per error identifier. Supports both neon baseline and PHP baseline.
* [soloterm/dumps](https://github.com/soloterm/dumps) - A Laravel command to intercept dumps from your Laravel application.
* [soloterm/solo](https://github.com/soloterm/solo) - A Laravel package to run multiple commands at once, to aid in local development.
* [spatie/laravel-error-solutions](https://github.com/spatie/laravel-error-solutions) - Display solutions on the Laravel error page
* [spatie/laravel-horizon-watcher](https://github.com/spatie/laravel-horizon-watcher) - Automatically restart Horizon when local PHP files change
* [spatie/laravel-ignition](https://github.com/spatie/laravel-ignition) - A beautiful error page for Laravel applications.
* [spatie/laravel-login-link](https://github.com/spatie/laravel-login-link) - Quickly login to your local environment
* [spatie/laravel-stubs](https://github.com/spatie/laravel-stubs) - Opinionated Laravel stubs
* [spaze/phpstan-disallowed-calls](https://github.com/spaze/phpstan-disallowed-calls) - PHPStan rules to detect disallowed method & function calls, constant, namespace, attribute & superglobal usages, with powerful rules to re-allow a call or a usage in places where it should be allowed.
* [sti3bas/laravel-scout-array-driver](https://github.com/Sti3bas/laravel-scout-array-driver) - Array driver for Laravel Scout
* [symfony/thanks](https://github.com/symfony/thanks) - Encourages sending ⭐ and 💵 to fellow PHP package maintainers (not limited to Symfony components)!
* [symplify/easy-coding-standard](https://github.com/easy-coding-standard/easy-coding-standard) - Use Coding Standard with 0-knowledge of PHP-CS-Fixer and PHP_CodeSniffer
* [symplify/phpstan-rules](https://github.com/symplify/phpstan-rules) - Set of Symplify rules for PHPStan
* [symplify/vendor-patches](https://github.com/symplify/vendor-patches) - Generate vendor patches for packages with single command
* [thedoctor0/laravel-factory-generator](https://github.com/TheDoctor0/laravel-factory-generator) - Automatically generate Laravel factories for your models.
* [tomasvotruba/class-leak](https://github.com/TomasVotruba/class-leak) - Detect leaking classes
* [tomasvotruba/cognitive-complexity](https://github.com/TomasVotruba/cognitive-complexity) - PHPStan rules to measure cognitive complexity of your classes and methods
* [tomasvotruba/type-coverage](https://github.com/TomasVotruba/type-coverage) - Measure type coverage of your project
* [vcian/laravel-db-auditor](https://github.com/vcian/laravel-db-auditor) - Database DB Auditor provide leverage to audit your MySql,sqlite, PostgreSQL database standards and also provide options to add constraints in table.
* [worksome/envy](https://github.com/worksome/envy) - Automatically keep your .env files in sync.
* [yamadashy/phpstan-friendly-formatter](https://github.com/yamadashy/phpstan-friendly-formatter) - Simple error formatter for PHPStan that display code frame
</details>

<details>
<summary>App tree</summary>

```shell
app
├── Casts
│   ├── Base64Cast.php
│   ├── CallbackGetCast.php
│   ├── CallbackSetCast.php
│   ├── CommaSeparatedToArrayCast.php
│   ├── CommaSeparatedToArrayCastUsing.php
│   ├── CommaSeparatedToIntegerArrayCast.php
│   └── CurrencyCast.php
├── Console
│   └── Commands
│       ├── CachePruneCommand.php
│       ├── CheckServiceProviderCommand.php
│       ├── ClearAllCommand.php
│       ├── ClearLogsCommand.php
│       ├── Command.php
│       ├── Concerns
│       │   ├── AskForPassword.php
│       │   ├── Graceful.php
│       │   └── Rescuer.php
│       ├── FindDumpStatementCommand.php
│       ├── FindStaticMethodsCommand.php
│       ├── GenerateSitemapCommand.php
│       ├── HealthCheckCommand.php
│       ├── IdeHelperChoresCommand.php
│       ├── InflectorCommand.php
│       ├── InitCommand.php
│       ├── MigrateFromMysqlToSqlite.php
│       ├── OpcacheUrlCommand.php
│       ├── OptimizeAllCommand.php
│       ├── PerformDatabaseBackupCommand.php
│       ├── ShowUnsupportedRequiresCommand.php
│       └── UpdateReadmeCommand.php
├── Enums
│   ├── CacheKeyEnum.php
│   └── ConfigurationKey.php
├── Exceptions
│   ├── BadRequestHttpException.php
│   ├── InvalidRepeatRequestException.php
│   └── VerifyEmailException.php
├── Http
│   ├── Controllers
│   │   ├── Api
│   │   │   ├── AuthController.php
│   │   │   ├── ChunkUploadController.php
│   │   │   ├── Controller.php
│   │   │   ├── CurdController.php
│   │   │   ├── PingController.php
│   │   │   └── UploadController.php
│   │   └── Controller.php
│   ├── Middleware
│   │   ├── AbortIf.php
│   │   ├── AbortIfProduction.php
│   │   ├── AddContentLength.php
│   │   ├── BasicAuthentication.php
│   │   ├── Cors.php
│   │   ├── DisableFloc.php
│   │   ├── EnsureVerifiedEmailsForSignInUsers.php
│   │   ├── HasValidSignature.php
│   │   ├── HttpsProtocol.php
│   │   ├── IsDeveloper.php
│   │   ├── IsRouteIgnored.php
│   │   ├── Localization.php
│   │   ├── LogHttp.php
│   │   ├── MustBeAdmin.php
│   │   ├── RedirectUppercase.php
│   │   ├── RequiredJson.php
│   │   ├── SetAcceptHeader.php
│   │   ├── SetDefaultLocaleForUrls.php
│   │   ├── SetJsonResponseEncodingOptions.php
│   │   ├── SetLocale.php
│   │   ├── SetTimezone.php
│   │   ├── VerifyFormPaginate.php
│   │   ├── VerifyFormPassword.php
│   │   ├── VerifyJsonContent.php
│   │   ├── VerifySignature.php
│   │   └── VerifyUserAbility.php
│   ├── Requests
│   │   ├── Auth
│   │   │   ├── AuthRequest.php
│   │   │   └── IndexRequest.php
│   │   └── FormRequest.php
│   └── Resources
│       ├── UserCollection.php
│       └── UserResource.php
├── Jobs
│   ├── Middleware
│   │   ├── EnsureTokenIsValid.php
│   │   └── RateLimitedForJob.php
│   └── SendThirdPartyRequestJob.php
├── Listeners
│   ├── AuthSubscriber.php
│   ├── CollectGarbageListener.php
│   ├── ContextSubscriber.php
│   ├── LogMailListener.php
│   ├── MaintenanceModeDisabledNotificationListener.php
│   ├── MaintenanceModeEnabledNotificationListener.php
│   ├── PrepareRequestListener.php
│   ├── RecordRequestIdentifiersListener.php
│   ├── RunCommandInDebugModeListener.php
│   └── TraceEventListener.php
├── Mail
│   └── UserRegisteredMail.php
├── Models
│   ├── BaseModel.php
│   ├── Concerns
│   │   ├── BelongsToCreator.php
│   │   ├── CacheCleaner.php
│   │   ├── ForceUseIndexable.php
│   │   ├── GetModelByUuid.php
│   │   ├── HasPivot.php
│   │   ├── HasSchemalessAttributes.php
│   │   ├── HasWrappedApiTokens.php
│   │   ├── Nullable.php
│   │   ├── Observable.php
│   │   ├── Pipeable.php
│   │   ├── SerializeDate.php
│   │   ├── Trashed.php
│   │   └── UuidGenerator.php
│   ├── DatabaseNotification.php
│   ├── Example.php
│   ├── HttpLog.php
│   ├── JWTUser.php
│   ├── Movie.php
│   ├── PersonalAccessToken.php
│   ├── Pivots
│   │   ├── MorphPivotWithCreatorPivot.php
│   │   └── PivotWithCreatorPivot.php
│   ├── Province.php
│   └── User.php
├── Notifications
│   ├── SlowQueryLoggedNotification.php
│   └── WelcomeNotification.php
├── Observers
│   └── UserObserver.php
├── Policies
│   ├── Policy.php
│   └── UserPolicy.php
├── Providers
│   ├── AppServiceProvider.php
│   ├── AuthServiceProvider.php
│   ├── AutowiredServiceProvider.php
│   ├── CacheServiceProvider.php
│   ├── ConsoleServiceProvider.php
│   ├── DatabaseServiceProvider.php
│   ├── EventServiceProvider.php
│   ├── HttpServiceProvider.php
│   ├── LogServiceProvider.php
│   ├── PackageServiceProvider.php
│   ├── PaginatorServiceProvider.php
│   ├── QueueServiceProvider.php
│   ├── RouteServiceProvider.php
│   ├── SupportServiceProvider.php
│   ├── UnlessProductionAggregateServiceProvider.php
│   ├── ValidatorServiceProvider.php
│   ├── ViewServiceProvider.php
│   ├── WhenLocalAggregateServiceProvider.php
│   └── WhenTestingAggregateServiceProvider.php
├── Rules
│   ├── AddressIPV4Rule.php
│   ├── AddressIPV6Rule.php
│   ├── AggregateRule.php
│   ├── BankCardRule.php
│   ├── Base64Rule.php
│   ├── BetweenWordsRule.php
│   ├── BitcoinAddressRule.php
│   ├── CallbackRule.php
│   ├── CamelCaseRule.php
│   ├── CapitalCharWithNumberRule.php
│   ├── CarNumberRule.php
│   ├── ChineseNameRule.php
│   ├── Concerns
│   │   ├── DataAware.php
│   │   └── ValidatorAware.php
│   ├── CurrentUserPasswordRule.php
│   ├── DefaultRule.php
│   ├── DomainRule.php
│   ├── DuplicateRule.php
│   ├── EmailRule.php
│   ├── EvenNumberRule.php
│   ├── HexColorRule.php
│   ├── HexRule.php
│   ├── HtmlCleanRule.php
│   ├── HtmlTagRule.php
│   ├── IdCardRule.php
│   ├── ImeiRule.php
│   ├── InstanceofRule.php
│   ├── IntegerBooleanRule.php
│   ├── IpRule.php
│   ├── JwtRule.php
│   ├── KebabCaseRule.php
│   ├── LenientPortRule.php
│   ├── LocationCoordinatesRule.php
│   ├── MacAddressRule.php
│   ├── MaxUploadSizeRule.php
│   ├── MimeTypeRule.php
│   ├── NotDisposableEmailRule.php
│   ├── OddNumberRule.php
│   ├── PhoneCisRule.php
│   ├── PhoneRule.php
│   ├── PhoneWorldRule.php
│   ├── PortRule.php
│   ├── PostalCodeRule.php
│   ├── RegexRule.php
│   ├── Rule.php
│   ├── SemverRule.php
│   ├── SlugRule.php
│   ├── SnakeCaseRule.php
│   ├── StrongPassword.php
│   ├── TimezoneRule.php
│   ├── UlidRule.php
│   ├── UrlRule.php
│   ├── UuidRule.php
│   └── WithoutWhitespaceRule.php
├── Support
│   ├── Attributes
│   │   ├── Autowired.php
│   │   ├── Ignore.php
│   │   └── Mixin.php
│   ├── BitEncoder.php
│   ├── Bootstrap
│   │   └── OutOfMemoryBootstrap.php
│   ├── Clients
│   │   ├── AbstractClient.php
│   │   └── PushDeer.php
│   ├── Console
│   │   ├── ProgressBarFactory.php
│   │   └── SymfonyStyleFactory.php
│   ├── Contracts
│   │   ├── BitEncoderContract.php
│   │   └── SignerContract.php
│   ├── Facades
│   │   ├── Elasticsearch.php
│   │   └── PushDeer.php
│   ├── Guzzle
│   │   └── CircuitBreakerMiddleware.php
│   ├── Managers
│   │   └── ElasticsearchManager.php
│   ├── Mixins
│   │   ├── BlueprintMixin.php
│   │   ├── CarbonMixin.php
│   │   ├── CollectionMixin.php
│   │   ├── CommandMixin.php
│   │   ├── GrammarMixin.php
│   │   ├── MySqlGrammarMixin.php
│   │   ├── PendingRequestMixin.php
│   │   ├── QueryBuilder
│   │   │   ├── OrderByWithQueryBuilderMixin.php
│   │   │   ├── QueryBuilderMixin.php
│   │   │   ├── WhereEndsWithQueryBuilderMixin.php
│   │   │   ├── WhereFindInSetQueryBuilderMixin.php
│   │   │   ├── WhereFullTextQueryBuilderMixin.php
│   │   │   ├── WhereInsQueryBuilderMixin.php
│   │   │   ├── WhereLikeQueryBuilderMixin.php
│   │   │   └── WhereStartsWithQueryBuilderMixin.php
│   │   ├── RequestMixin.php
│   │   ├── ResponseFactoryMixin.php
│   │   ├── RouterMixin.php
│   │   ├── SchedulingEventMixin.php
│   │   ├── StrMixin.php
│   │   ├── StringableMixin.php
│   │   ├── UploadedFileMixin.php
│   │   └── ViteMixin.php
│   ├── Monolog
│   │   ├── EcsFormatterTapper.php
│   │   ├── Formatter
│   │   │   └── EloquentLogHttpModelFormatter.php
│   │   ├── Handler
│   │   │   └── EloquentHandler.php
│   │   └── Processor
│   │       ├── AppendExtraDataProcessor.php
│   │       └── EloquentLogHttpModelProcessor.php
│   ├── PHPStan
│   │   └── ForbiddenGlobalFunctionsRule.php
│   ├── PhpCsFixer
│   │   └── PintFixer.php
│   ├── PhpUserFilters
│   │   └── CallbackFilter.php
│   ├── Rectors
│   │   ├── ClassHandleMethodRector.php
│   │   └── RenameToPsrNameRector.php
│   ├── Signers
│   │   └── HmacSigner.php
│   ├── Sse
│   │   ├── CloseServerSentEventException.php
│   │   └── ServerSentEvent.php
│   ├── StreamWrappers
│   │   ├── Concerns
│   │   │   ├── HasContext.php
│   │   │   └── Nameable.php
│   │   ├── GlobStreamWrapper.php
│   │   ├── StreamWrapper.php
│   │   └── UserFileStreamWrapper.php
│   ├── Traits
│   │   ├── Configurable.php
│   │   ├── Copyable.php
│   │   ├── Immutable.php
│   │   ├── Makeable.php
│   │   ├── Sanitizeable.php
│   │   ├── SetStateable.php
│   │   ├── Singletonable.php
│   │   └── WithPipeArgs.php
│   └── helpers.php
└── View
    ├── Components
    │   └── AlertComponent.php
    ├── Composers
    │   └── RequestComposer.php
    └── Creators
        └── RequestCreator.php

55 directories, 255 files

```
</details>

## Testing

```shell
composer test
```

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
