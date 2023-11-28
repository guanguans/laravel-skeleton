<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## App directory structure

```shell
.
├── Casts
│   ├── Base64Cast.php
│   ├── CallbackGetCast.php
│   ├── CallbackSetCast.php
│   ├── CommaSeparatedToArrayCast.php
│   ├── CommaSeparatedToIntegerArrayCast.php
│   └── CurrencyCast.php
├── Console
│   ├── Commands
│   │   ├── ClearAllCommand.php
│   │   ├── FindDumpStatementCommand.php
│   │   ├── GenerateTestsCommand.php
│   │   ├── HealthCheckCommand.php
│   │   ├── InflectorCommand.php
│   │   ├── OpenAIHelpCommand.php
│   │   └── OptimizeAllCommand.php
│   └── Kernel.php
├── Contracts
│   ├── BitEncoderContract.php
│   └── SignerContract.php
├── Enums
│   ├── BooleanEnum.php
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
│   │   ├── LogHttp.php
│   │   ├── PreventRequestsDuringMaintenance.php
│   │   ├── RedirectIfAuthenticated.php
│   │   ├── SetAcceptHeader.php
│   │   ├── SetJsonResponseEncodingOptions.php
│   │   ├── TrimStrings.php
│   │   ├── TrustHosts.php
│   │   ├── TrustProxies.php
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
│   └── CollectGarbageListener.php
├── Macros
│   ├── BlueprintMacro.php
│   ├── CollectionMacro.php
│   ├── CommandMacro.php
│   ├── GrammarMacro.php
│   ├── MySqlGrammarMacro.php
│   ├── QueryBuilder
│   │   ├── OrderByWithQueryBuilderMacro.php
│   │   ├── QueryBuilderMacro.php
│   │   ├── WhereEndsWithQueryBuilderMacro.php
│   │   ├── WhereFindInSetQueryBuilderMacro.php
│   │   ├── WhereFullTextQueryBuilderMacro.php
│   │   ├── WhereInsQueryBuilderMacro.php
│   │   ├── WhereLikeQueryBuilderMacro.php
│   │   ├── WhereNotQueryBuilderMacro.php
│   │   └── WhereStartsWithQueryBuilderMacro.php
│   ├── RequestMacro.php
│   ├── ResponseFactoryMacro.php
│   ├── StrMacro.php
│   └── StringableMacro.php
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
│   │   ├── Observable.php
│   │   ├── Pipeable.php
│   │   ├── SerializeDate.php
│   │   ├── Sortable.php
│   │   └── UsingUuidAsPrimaryKey.php
│   ├── HttpLog.php
│   ├── JWTUser.php
│   ├── Model.php
│   └── User.php
├── Notifications
│   └── WelcomeNotification.php
├── Observers
│   └── UserObserver.php
├── Pivots
│   ├── MorphPivotWithCreatorPivot.php
│   └── PivotWithCreatorPivot.php
├── Policies
│   ├── Policy.php
│   └── UserPolicy.php
├── Providers
│   ├── AppServiceProvider.php
│   ├── AuthServiceProvider.php
│   ├── BroadcastServiceProvider.php
│   ├── EventServiceProvider.php
│   ├── ExtendServiceProvider.php
│   └── RouteServiceProvider.php
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
│   ├── StrongPassword.php
│   ├── TimezoneRule.php
│   ├── UlidRule.php
│   ├── UrlRule.php
│   ├── UuidRule.php
│   └── WithoutWhitespaceRule.php
├── Scopes
├── Services
├── Support
│   ├── AbstractRepository.php
│   ├── BitEncoder.php
│   ├── ConsoleWriter.php
│   ├── Discover.php
│   ├── Facades
│   │   ├── OpenAI.php
│   │   └── PushDeer.php
│   ├── FluentAssert.php
│   ├── FoundationSdk.php
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
│   ├── Inflector.php
│   ├── Monolog
│   │   ├── AnsiLineFormatter.php
│   │   └── AppendExtraDataProcessor.php
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
│   └── helpers.php
├── Traits
│   ├── Cacheable.php
│   ├── Castable.php
│   ├── ControllerCrudable.php
│   ├── Copyable.php
│   ├── CreateStaticable.php
│   ├── ModelCrudable.php
│   ├── Sanitizerable.php
│   ├── Singletonable.php
│   ├── ValidateStrictAll.php
│   └── ValidatesData.php
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

50 directories, 210 files
```

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 1500 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[CMS Max](https://www.cmsmax.com/)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
