# laravel-skeleton

> 本项目收集了最常用 Laravel 扩展包、以及一些功能特性的使用范例供日常开发参考使用。

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
│   ├── CommaSeparatedToIntegerArrayCast.php
│   ├── CurrencyCast.php
│   └── DecimalCast.php
├── Console
│   ├── Commands
│   │   ├── ClearAllCommand.php
│   │   ├── ClearLogsCommand.php
│   │   ├── Command.php
│   │   ├── Concerns
│   │   │   └── PrettyCommandOutput.php
│   │   ├── FindDumpStatementCommand.php
│   │   ├── GenerateTestsCommand.php
│   │   ├── HealthCheckCommand.php
│   │   ├── InflectorCommand.php
│   │   ├── ListSchedule.php
│   │   ├── OpcacheUrlCommand.php
│   │   ├── OpenAIHelpCommand.php
│   │   ├── OptimizeAllCommand.php
│   │   └── ParsePHPFileToASTCommand.php
│   └── Kernel.php
├── Enums
│   ├── BooleanEnum.php
│   ├── Enum.php
│   ├── HealthCheckStateEnum.php
│   ├── HttpStatusCodeEnum.php
│   ├── IntegerBooleanEnum.php
│   └── StringBooleanEnum.php
├── Events
│   └── UserLoggedInEvent.php
├── Exceptions
│   ├── BadRequestException.php
│   ├── Handler.php
│   ├── InvalidRepeatRequestException.php
│   └── InvalidRequestParameterException.php
├── Http
│   ├── Controllers
│   │   ├── Api
│   │   │   ├── AuthController.php
│   │   │   ├── Controller.php
│   │   │   ├── CurdController.php
│   │   │   └── PingController.php
│   │   └── Controller.php
│   ├── Kernel.php
│   ├── Middleware
│   │   ├── AbortIf.php
│   │   ├── Authenticate.php
│   │   ├── CompressResponseContent.php
│   │   ├── ETag.php
│   │   ├── EncryptCookies.php
│   │   ├── IsRouteIgnored.php
│   │   ├── LogHttp.php
│   │   ├── PreventRequestsDuringMaintenance.php
│   │   ├── RedirectIfAuthenticated.php
│   │   ├── SetAcceptHeader.php
│   │   ├── SetJsonResponseEncodingOptions.php
│   │   ├── TrimStrings.php
│   │   ├── TrustHosts.php
│   │   ├── TrustProxies.php
│   │   ├── UserLocale.php
│   │   ├── VerifyCommonParameters.php
│   │   ├── VerifyCsrfToken.php
│   │   ├── VerifyJsonContent.php
│   │   ├── VerifyProductionEnvironment.php
│   │   └── VerifySignature.php
│   ├── Requests
│   │   ├── Auth
│   │   │   ├── AuthRequest.php
│   │   │   └── IndexRequest.php
│   │   └── FormRequest.php
│   └── Resources
│       ├── UserCollection.php
│       └── UserResource.php
├── Jobs
├── Listeners
│   ├── CollectGarbageListener.php
│   └── ShareLogContextSubscriber.php
├── Mail
│   └── UserRegisteredMail.php
├── Models
│   ├── Concerns
│   │   ├── AllowedFilterable.php
│   │   ├── BelongsToCreator.php
│   │   ├── Filterable.php
│   │   ├── Fireworks.php
│   │   ├── ForceUseIndexable.php
│   │   ├── HasPivot.php
│   │   ├── HasWrapedApiTokens.php
│   │   ├── IndexHintsable.php
│   │   ├── Nullable.php
│   │   ├── Observable.php
│   │   ├── Pipeable.php
│   │   ├── SerializeDate.php
│   │   ├── SimpleNode.php
│   │   ├── Sortable.php
│   │   └── UsingUuidAsPrimaryKey.php
│   ├── HttpLog.php
│   ├── JWTUser.php
│   ├── Model.php
│   ├── PersonalAccessToken.php
│   ├── Pivots
│   │   ├── MorphPivotWithCreatorPivot.php
│   │   └── PivotWithCreatorPivot.php
│   ├── Scopes
│   └── User.php
├── Notifications
│   └── WelcomeNotification.php
├── Observers
│   └── UserObserver.php
├── Policies
│   ├── Policy.php
│   └── UserPolicy.php
├── Providers
│   ├── AppServiceProvider.php
│   ├── AuthServiceProvider.php
│   ├── BroadcastServiceProvider.php
│   ├── EventServiceProvider.php
│   ├── ExtendServiceProvider.php
│   ├── RouteServiceProvider.php
│   └── TelescopeServiceProvider.php
├── Rules
│   ├── AddressIPV4Rule.php
│   ├── AddressIPV6Rule.php
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
│   ├── HtmlcleanRule.php
│   ├── IdCardRule.php
│   ├── ImeiRule.php
│   ├── InstanceofRule.php
│   ├── IntegerBooleanRule.php
│   ├── IpRule.php
│   ├── JwtRule.php
│   ├── KebabCaseRule.php
│   ├── LocationCoordinatesRule.php
│   ├── MacAddressRule.php
│   ├── MimeTypeRule.php
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
│   ├── StrictIdCardRule.php
│   ├── StrongPassword.php
│   ├── TimezoneRule.php
│   ├── UlidRule.php
│   ├── UrlRule.php
│   ├── UuidRule.php
│   └── WithoutWhitespaceRule.php
├── Services
├── Support
│   ├── AbstractRepository.php
│   ├── Attributes
│   │   └── Ignore.php
│   ├── BitEncoder.php
│   ├── ConsoleWriter.php
│   ├── Contracts
│   │   ├── BitEncoderContract.php
│   │   └── SignerContract.php
│   ├── Discover.php
│   ├── ElasticsearchManager.php
│   ├── Facades
│   │   ├── Elasticsearch.php
│   │   ├── OpenAI.php
│   │   └── PushDeer.php
│   ├── FluentAssert.php
│   ├── FoundationSDK.php
│   ├── HandlerStack.php
│   ├── HmacSigner.php
│   ├── Http
│   │   ├── Client.php
│   │   ├── Concerns
│   │   │   ├── ConcreteConfigMethods.php
│   │   │   ├── ConcreteEasyHttpRequestMethods.php
│   │   │   └── ConcreteHttpRequestMethods.php
│   │   ├── Contracts
│   │   │   ├── ClientInterface.php
│   │   │   ├── Handler.php
│   │   │   └── Throwable.php
│   │   ├── Exceptions
│   │   │   └── RuntimeException.php
│   │   ├── GuzzlyClient.php
│   │   ├── Handlers
│   │   │   ├── FgcHandler.php
│   │   │   └── StreamHandler.php
│   │   ├── Middleware.php
│   │   ├── PsrClient.php
│   │   ├── Responses
│   │   │   ├── Response.php
│   │   │   └── StreamResponse.php
│   │   └── Support
│   │       ├── Collection.php
│   │       └── XML.php
│   ├── HttpClient.php
│   ├── HttpQuery.php
│   ├── IdCard.php
│   ├── Inflector.php
│   ├── Macros
│   │   ├── BlueprintMacro.php
│   │   ├── CollectionMacro.php
│   │   ├── CommandMacro.php
│   │   ├── GrammarMacro.php
│   │   ├── MySqlGrammarMacro.php
│   │   ├── QueryBuilder
│   │   │   ├── OrderByWithQueryBuilderMacro.php
│   │   │   ├── QueryBuilderMacro.php
│   │   │   ├── WhereEndsWithQueryBuilderMacro.php
│   │   │   ├── WhereFindInSetQueryBuilderMacro.php
│   │   │   ├── WhereFullTextQueryBuilderMacro.php
│   │   │   ├── WhereInsQueryBuilderMacro.php
│   │   │   ├── WhereLikeQueryBuilderMacro.php
│   │   │   ├── WhereNotQueryBuilderMacro.php
│   │   │   └── WhereStartsWithQueryBuilderMacro.php
│   │   ├── RequestMacro.php
│   │   ├── ResponseFactoryMacro.php
│   │   ├── SchedulingEventMacro.php
│   │   ├── StrMacro.php
│   │   └── StringableMacro.php
│   ├── Monolog
│   │   ├── AnsiLineFormatter.php
│   │   ├── AppendExtraDataProcessor.php
│   │   └── EcsFormatterTapper.php
│   ├── OS.php
│   ├── OpenAI.php
│   ├── PureModel.php
│   ├── PushDeer.php
│   ├── Rectors
│   │   └── RenameToPsrNameRector.php
│   ├── SqlFormatter.php
│   ├── Sse
│   │   ├── CloseServerSentEventException.php
│   │   └── ServerSentEvent.php
│   ├── System.php
│   ├── Traits
│   │   ├── Cacheable.php
│   │   ├── Castable.php
│   │   ├── ControllerCrudable.php
│   │   ├── Copyable.php
│   │   ├── CreateStaticable.php
│   │   ├── Disenchant.php
│   │   ├── Immutable.php
│   │   ├── ModelCrudable.php
│   │   ├── Sanitizerable.php
│   │   ├── Singletonable.php
│   │   ├── Uncloneable.php
│   │   ├── Unconstructable.php
│   │   ├── ValidateStrictAll.php
│   │   ├── ValidatesData.php
│   │   └── Value.php
│   └── helpers.php
└── View
    ├── Components
    │   ├── AlertComponent.php
    │   └── vendor
    │       └── health
    │           ├── Logo.php
    │           └── StatusIndicator.php
    ├── Composers
    │   └── RequestComposer.php
    └── Creators
        └── RequestCreator.php

52 directories, 237 files
```
