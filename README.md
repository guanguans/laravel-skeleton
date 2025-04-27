# laravel-skeleton

> 本项目收集了最常用 Laravel 扩展包、以及一些功能特性的使用范例供日常开发参考使用。

[![tests](https://github.com/guanguans/laravel-skeleton/workflows/tests/badge.svg)](https://github.com/guanguans/laravel-skeleton/actions)
[![phpstan](https://github.com/guanguans/laravel-skeleton/actions/workflows/phpstan.yml/badge.svg)](https://github.com/guanguans/laravel-skeleton/actions/workflows/phpstan.yml)
[![check & fix styling](https://github.com/guanguans/laravel-skeleton/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/guanguans/laravel-skeleton/actions)
[![codecov](https://codecov.io/gh/guanguans/laravel-skeleton/branch/main/graph/badge.svg?token=URGFAWS6S4)](https://codecov.io/gh/guanguans/laravel-skeleton)
[![Latest Stable Version](https://poser.pugx.org/guanguans/laravel-skeleton/v)](https://packagist.org/packages/guanguans/laravel-skeleton)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/guanguans/laravel-skeleton)
[![Total Downloads](https://poser.pugx.org/guanguans/laravel-skeleton/downloads)](https://packagist.org/packages/guanguans/laravel-skeleton)
[![License](https://poser.pugx.org/guanguans/laravel-skeleton/license)](https://packagist.org/packages/guanguans/laravel-skeleton)

## Composer 脚本

```shell
composer ai-commit                      # AI 提交
composer checks                         # 基本检查
composer composer-check-platform-reqs   # PHP cli 环境检查
composer composer-normalize             # composer.json 文件修复
composer composer-require-checker       # composer require 检查
composer composer-unused-checker        # composer unused 检查
composer composer-updater               # composer 更新依赖
composer composer-updater-dry-run       # composer 尝试更新依赖
composer composer-validate              # composer.json 文件验证
composer docs-generate                  # API 文档生成
composer envoy-testing                  # envoy 部署
composer envy-prune                     # env 文件修剪
composer envy-sync                      # env 文件同步
composer facade-lint                    # 门面检查
composer facade-update                  # 门面更新
composer ide-helper                     # IDE 帮助文件
composer lint                           # 语法检查
composer phpstan                        # 静态检查
composer pint                           # 代码风格修复
composer pint-test                      # 代码风格检查
composer rector                         # 代码重构(risky)
composer rector-dry-run                 # 代码重构检查
composer sk-check-commented-code        # 注释检查
composer sk-check-conflicts             # git 合并冲突检查
composer sk-finalize-classes            # finalize 类
composer sk-finalize-classes-dry-run    # finalize 类检查
composer tlint                          # laravel 代码风格修复
composer tlint-format                   # laravel 代码风格检查
composer trap                           # 启动 trap 调试服务
composer var-dump-server                # 启动变量打印服务
```

## 应用目录结构

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
│       ├── ClearAllCommand.php
│       ├── ClearLogsCommand.php
│       ├── Command.php
│       ├── Concerns
│       │   ├── AskForPassword.php
│       │   ├── Configureable.php
│       │   └── Rescuer.php
│       ├── FindCommand.php
│       ├── FindDumpStatementCommand.php
│       ├── FindStaticMethodsCommand.php
│       ├── GenerateSitemapCommand.php
│       ├── GenerateTestsCommand.php
│       ├── HealthCheckCommand.php
│       ├── IdeHelperChoresCommand.php
│       ├── InflectorCommand.php
│       ├── InitCommand.php
│       ├── MigrateFromMysqlToSqlite.php
│       ├── OpcacheUrlCommand.php
│       ├── OptimizeAllCommand.php
│       ├── PerformDatabaseBackupCommand.php
│       └── ShowUnsupportedRequiresCommand.php
├── Enums
│   ├── CacheKeyEnum.php
│   ├── Configuration.php
│   ├── HealthCheckStatusEnum.php
│   ├── IntegerBooleanEnum.php
│   └── StringBooleanEnum.php
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
│   │   ├── CSP.php
│   │   ├── CompressResponseContent.php
│   │   ├── Cors.php
│   │   ├── DisableFloc.php
│   │   ├── ETag.php
│   │   ├── EnsureVerifiedEmailsForSignInUsers.php
│   │   ├── HasValidSignature.php
│   │   ├── HttpsProtocol.php
│   │   ├── IsDeveloper.php
│   │   ├── IsRouteIgnored.php
│   │   ├── Localization.php
│   │   ├── LogAllRequests.php
│   │   ├── LogHttp.php
│   │   ├── MustBeAdmin.php
│   │   ├── RedirectUppercase.php
│   │   ├── RequiredJson.php
│   │   ├── SetAcceptHeader.php
│   │   ├── SetDefaultLocaleForUrls.php
│   │   ├── SetJsonResponseEncodingOptions.php
│   │   ├── SetLocale.php
│   │   ├── SetLocales.php
│   │   ├── SetTimezone.php
│   │   ├── UserLocale.php
│   │   ├── VerifyCommonParameters.php
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
│   ├── CollectGarbageListener.php
│   ├── LogActivity.php
│   ├── LogMailListener.php
│   ├── MaintenanceModeDisabledNotificationListener.php
│   ├── MaintenanceModeEnabledNotificationListener.php
│   ├── RecordRequestIdentifiersListener.php
│   ├── RunCommandInDebugModeListener.php
│   ├── SetRequestIdListener.php
│   └── LogContextSubscriber.php
├── Mail
│   └── UserRegisteredMail.php
├── Models
│   ├── Concerns
│   │   ├── AllowedFilterable.php
│   │   ├── BelongsToCreator.php
│   │   ├── CacheCleaner.php
│   │   ├── ForceUseIndexable.php
│   │   ├── GetModelByUuid.php
│   │   ├── HasPivot.php
│   │   ├── HasWrappedApiTokens.php
│   │   ├── IndexHintsable.php
│   │   ├── Nullable.php
│   │   ├── Observable.php
│   │   ├── Pipeable.php
│   │   ├── SerializeDate.php
│   │   ├── Trashed.php
│   │   └── UuidGenerator.php
│   ├── DatabaseNotification.php
│   ├── HttpLog.php
│   ├── JWTUser.php
│   ├── Model.php
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
│   ├── HtmlTagRule.php
│   ├── HtmlCleanRule.php
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
│   ├── Autowire.php
│   ├── BitEncoder.php
│   ├── Bootstrappers
│   │   ├── OutOfMemoryBootstrapper.php
│   │   └── SetRequestIdGlobalBootstrapper.php
│   ├── Clients
│   │   ├── AbstractClient.php
│   │   └── PushDeer.php
│   ├── Console
│   │   ├── ProgressBarFactory.php
│   │   └── SymfonyStyleFactory.php
│   ├── Contracts
│   │   ├── BitEncoderContract.php
│   │   └── SignerContract.php
│   ├── Curl.php
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
│   │   ├── ModelMixin.php
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
│   │   │   ├── WhereNotQueryBuilderMixin.php
│   │   │   └── WhereStartsWithQueryBuilderMixin.php
│   │   ├── RequestMixin.php
│   │   ├── ResponseFactoryMixin.php
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
│   │   ├── Disenchant.php
│   │   ├── Immutable.php
│   │   ├── Makeable.php
│   │   ├── Sanitizeable.php
│   │   ├── Singletonable.php
│   │   ├── Uncloneable.php
│   │   ├── Unconstructable.php
│   │   └── WithMiddlewareArgs.php
│   └── helpers.php
└── View
    ├── Components
    │   └── AlertComponent.php
    ├── Composers
    │   └── RequestComposer.php
    └── Creators
        └── RequestCreator.php

55 directories, 268 files

```
