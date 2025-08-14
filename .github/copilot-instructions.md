# Laravel PHP 8.3+ Package Copilot Instructions

[//]: # (https://github.com/github/awesome-copilot)

## 项目背景

- 技术栈：PHP (>=8.3), Laravel 12, Composer, NPM, JavaScript, Go, SQL
- 代码风格：PSR-12，使用 Laravel Pint / PHP-CS-Fixer (仓库含自定义 Fixer)
- 测试：Pest 优先；必要时 PHPUnit
- 目标：可维护、类型明确、易测试、高性能

## PHP 代码规范

- 严格遵守 PSR-12；必须使用 `declare(strict_types=1);`
- 使用 PHP 8.3+ 新特性：readonly 类、枚举、联合类型、属性、泛型注解
- 方法参数和返回值必须有类型声明，包括 nullable 类型使用 `?Type`
- 优先使用命名参数和简短闭包语法 `fn() =>`
- 使用新的 `match` 表达式替代简单的 `switch`
- 类常量使用 `final` 修饰符防止重写
- 优先使用 `readonly` 类和属性提升不可变性

## JavaScript/前端规范

- 使用 ES6+ 语法，优先 `const`/`let`，避免 `var`
- 函数优先使用箭头函数 `() => {}`
- 使用解构赋值和扩展运算符
- 异步操作使用 `async/await`，避免 Promise 链
- 模块化导入/导出，使用 `import/export`
- 遵循 ESLint 配置规则

## Go 代码规范

- 严格遵循 `go fmt` 和 `gofmt` 格式化标准
- 错误处理：必须检查每个 `error` 返回值，不忽略
- 接口设计：保持简小，遵循单一职责原则
- 包命名：简短、小写、无下划线
- 函数命名：驼峰式，公共函数首字母大写
- 使用 `context.Context` 处理超时和取消操作
- 并发安全：使用 `sync` 包的原语，避免数据竞争

## SQL 规范

- 关键字使用大写：`SELECT`, `FROM`, `WHERE`, `ORDER BY`
- 表名和字段名使用 snake_case
- 复杂查询使用适当的缩进和换行
- 避免 `SELECT *`，明确指定需要的字段
- 使用参数化查询防止 SQL 注入
- 索引命名：`idx_表名_字段名`，唯一索引：`uk_表名_字段名`
- 外键命名：`fk_当前表_引用表_字段名`

## Laravel 架构约定

### 控制器 (Controllers)

- 保持精简，单一方法不超过 20 行
- 业务逻辑委托给 Service/Action 类
- 使用资源控制器方法命名：`index`, `show`, `store`, `update`, `destroy`
- 返回统一的 JSON 响应格式
- 使用类型提示注入依赖
- 控制器方法使用 `__invoke()` 实现单一职责行为类

### 服务层 (Services)

- 处理复杂业务逻辑
- 方法命名动词开头：`create`, `update`, `delete`, `process`
- 单一职责，每个服务类专注一个领域
- 返回 DTO 或领域对象，不直接返回 Eloquent 模型
- 使用数据库事务处理复杂操作

### Actions (单一职责行为类)

- 每个 Action 只处理一个具体业务操作
- 使用 `__invoke()` 方法作为入口
- 命名使用动词 + 名词格式：`CreateUser`, `SendNotification`
- 返回明确的值对象或 DTO

### 仓储模式 (Repositories)

- 处理数据访问逻辑
- 定义接口，使用依赖注入
- 方法命名：`findById`, `findByEmail`, `getActive`
- 返回 Collection 或 Model 实例
- 使用查询构建器优化性能

### 数据传输对象 (DTOs)

- 使用 readonly 类定义 DTO
- 构造函数参数使用属性提升
- 提供 `fromArray` 和 `toArray` 方法
- 验证数据完整性

### 值对象 (Value Objects)

- 使用 readonly 类定义不可变值对象
- 实现 `equals()` 方法进行值比较
- 提供工厂方法创建实例
- 封装业务规则和验证逻辑

### 请求验证 (Form Requests)

- 每个创建/更新操作单独的 Request 类
- 规则方法返回数组，使用 Laravel 验证规则
- 自定义错误消息使用 `messages()` 方法
- 授权逻辑放在 `authorize()` 方法
- 使用 `prepareForValidation()` 预处理数据
- 自定义验证规则放在 `app/Rules` 目录

### API 资源 (Resources)

- 使用 API Resource 转换模型数据
- 避免在 Resource 中执行查询
- 条件字段使用 `when()` 方法
- 嵌套资源使用 `Resource::collection()`
- 使用 `whenLoaded()` 处理关联关系
- 分页资源使用 `ResourceCollection`

### Eloquent 模型 (Models)

- 使用 `$fillable` 属性明确可填充字段
- 定义关联关系并指定返回类型
- 使用访问器和修改器处理数据转换
- 实现模型事件和观察者
- 使用枚举定义状态字段
- 使用 `$casts` 属性自动类型转换
- 定义查询作用域方法 `scope*`
- 软删除使用 `SoftDeletes` trait

### 模型观察者 (Observers)

- 创建独立的观察者类处理模型事件
- 观察者方法命名对应模型事件：`created`, `updated`, `deleted`
- 在服务提供者中注册观察者
- 避免在观察者中执行复杂业务逻辑

### 策略类 (Policies)

- 为每个模型创建对应的策略类
- 策略方法命名：`view`, `create`, `update`, `delete`
- 使用 `Gate::allows()` 或 `@can` 指令检查权限
- 在控制器中使用 `authorize()` 方法

### 枚举 (Enums)

- 使用 PHP 8.1+ 枚举定义常量值
- 实现方法提供额外功能
- 使用 backed enums 存储到数据库
- 枚举方法提供标签、颜色等业务属性

### 集合 (Collections)

- 使用 Laravel Collection 处理数组数据
- 优先使用 Collection 方法而非原生 PHP 数组函数
- 自定义 Collection 类继承 `Illuminate\Support\Collection`
- 使用管道 `pipe()` 处理复杂数据转换

## 依赖管理

### 依赖注入

- 优先使用构造函数注入
- 接口注入，不直接依赖具体实现
- 使用 Laravel 服务容器管理依赖
- 避免服务定位器模式

### 服务提供者 (Service Providers)

- 按功能模块组织服务提供者
- `register()` 方法注册服务
- `boot()` 方法执行初始化逻辑
- 使用延迟加载提升性能
- 条件绑定使用 `when()` 方法

### 门面 (Facades)

- 谨慎使用门面，优先依赖注入
- 自定义门面继承 `Illuminate\Support\Facades\Facade`
- 提供 `getFacadeAccessor()` 方法
- 在测试中使用 `shouldReceive()` 模拟门面

## 测试约定

### Pest 测试规范

- 测试方法命名：`it('should return user when valid id provided')`
- 使用描述性的测试组织：`describe('UserService')`
- 测试数据使用 Factory 生成
- 断言使用 Pest 提供的 `expect()` 语法

### 测试覆盖

- 单元测试：Service、Repository、DTO 类
- 功能测试：API 端点、完整业务流程
- 集成测试：数据库交互、外部服务调用
- 浏览器测试：使用 Laravel Dusk 进行 E2E 测试
- 测试覆盖率目标：80% 以上

### 模型工厂 (Factories)

- 使用 Factory 生成测试数据
- 定义状态方法便于创建特定状态的数据
- 避免在测试中硬编码数据
- 使用 `sequence()` 方法创建序列数据

### HTTP 测试

- 使用 `actingAs()` 模拟用户认证
- 测试 JSON 响应使用 `assertJson()` 系列方法
- 文件上传测试使用 `UploadedFile::fake()`
- 数据库断言使用 `assertDatabaseHas()`

## 数据库设计

### 迁移文件 (Migrations)

- 文件命名清晰描述变更内容
- 使用 Laravel 迁移方法，避免原生 SQL
- 外键约束使用 `constrained()` 方法
- 添加适当的索引
- 使用 `after()` 方法指定字段位置
- 批量修改使用 `table()` 方法

### 数据库填充 (Seeders)

- 使用 Factory 生成测试数据
- 环境特定的种子数据
- 避免在生产环境运行不必要的 Seeder
- 使用 `DatabaseSeeder` 统一管理

### 查询优化

- 避免 N+1 问题，使用 `with()` 预加载
- 大数据集使用 `chunk()` 或 `lazy()` 处理
- 复杂查询使用 Query Builder 或原生 SQL
- 使用数据库索引优化查询性能
- 子查询使用 `whereExists()` 或 `whereHas()`

### 数据库事务

- 复杂操作使用 `DB::transaction()` 包装
- 嵌套事务使用 `DB::beginTransaction()` 手动控制
- 事务回调中抛出异常自动回滚
- 测试中使用 `RefreshDatabase` trait

## 队列处理 (Queues)

### 队列任务

- 任务类使用 `ShouldQueue` 接口
- 实现 `failed()` 方法处理失败情况
- 使用唯一 ID 防止重复执行
- 设置重试次数和延迟时间
- 批量任务使用 `Bus::batch()`

### 队列监控

- 监控队列长度和处理时间
- 失败任务及时处理
- 使用 Horizon 管理队列状态
- 队列工作进程监控和自动重启

### 任务调度 (Task Scheduling)

- 使用 `Schedule` 定义定时任务
- 任务频率使用 Laravel 提供的方法
- 任务输出重定向到日志
- 使用 `onOneServer()` 避免重复执行

## 事件系统 (Events)

- 事件类使用 `Dispatchable` trait
- 监听器处理具体业务逻辑
- 使用队列处理异步监听器
- 避免在事件中执行重逻辑
- 事件发现使用 `shouldDiscoverEvents()` 方法

## 通知系统 (Notifications)

### 通知类

- 继承 `Illuminate\Notifications\Notification`
- 实现 `via()` 方法定义通知渠道
- 每个渠道实现对应的方法：`toMail()`, `toDatabase()`
- 使用 `Notifiable` trait 添加通知功能

### 通知渠道

- 邮件通知使用 Mailable 类
- 数据库通知存储到 `notifications` 表
- 广播通知使用 WebSocket
- 短信通知集成第三方服务

## 邮件系统 (Mail)

### Mailable 类

- 继承 `Illuminate\Mail\Mailable`
- 使用 `build()` 方法构建邮件
- 模板文件放在 `resources/views/emails` 目录
- 使用 Markdown 模板简化邮件设计

### 邮件队列

- 邮件发送使用 `queue()` 方法异步处理
- 设置邮件发送延迟时间
- 批量邮件使用队列避免阻塞

## 缓存策略

### Redis 缓存

- 缓存键命名：`前缀:模块:标识符`
- 设置合理的过期时间
- 使用标签进行批量清除
- 缓存失效策略：主动失效 + TTL 兜底

### 查询缓存

- 频繁查询使用 `remember()` 缓存
- 缓存时间根据数据更新频率设定
- 使用缓存标签便于管理
- 监控缓存命中率

### 配置缓存

- 生产环境使用 `config:cache` 缓存配置
- 路由缓存使用 `route:cache`
- 视图缓存使用 `view:cache`
- 事件缓存使用 `event:cache`

## 文件存储 (Storage)

### 文件系统

- 使用 Storage facade 操作文件
- 配置多个磁盘支持不同存储需求
- 文件上传验证大小和类型
- 使用 `Storage::url()` 生成公开 URL

### 文件上传

- 表单验证使用 `file` 和 `mimes` 规则
- 大文件上传使用分块上传
- 图片处理使用 Intervention Image
- 文件安全扫描和类型检测

## 本地化 (Localization)

### 语言文件

- 语言文件放在 `resources/lang` 目录
- 使用 `__()` 函数或 `trans()` 助手函数
- 参数化翻译使用占位符语法
- 复数形式使用管道符分隔

### 语言切换

- 使用 `App::setLocale()` 设置当前语言
- 中间件处理语言切换
- URL 路由包含语言前缀
- 用户语言偏好存储到数据库

## 错误处理与日志

### 异常处理

- 领域异常：继承 `DomainException`
- 验证异常：使用 `ValidationException`
- 授权异常：使用 `AuthorizationException`
- 自定义异常提供上下文信息
- 异常处理器统一格式化错误响应

### 日志记录

- 使用结构化日志，包含上下文信息
- 日志级别正确使用：error、warning、info、debug
- 业务关键操作必须记录日志
- 敏感信息不记录到日志中
- 日志轮转和清理策略

### 错误页面

- 自定义错误页面放在 `resources/views/errors` 目录
- 不同 HTTP 状态码对应不同模板
- 错误页面提供友好的用户体验
- 生产环境隐藏详细错误信息

## 安全性 (Security)

### 认证 (Authentication)

- 使用 Laravel Sanctum 进行 API 认证
- 支持多种认证守卫：web、api
- 密码重置功能使用安全令牌
- 记住登录功能使用安全 Cookie

### 授权 (Authorization)

- 实现基于策略的授权控制
- 使用 Gate 定义简单授权逻辑
- 敏感操作需要二次验证
- 角色权限使用第三方包如 Spatie Permission

### 数据保护

- 密码使用 bcrypt 或 argon2 哈希
- 敏感数据加密存储使用 `encrypt()` 函数
- 实现 CSRF 保护，API 使用 Sanctum
- 验证所有用户输入，防止 XSS 攻击
- 使用 HTTPS 加密传输

### SQL 注入防护

- 使用 Eloquent ORM 或参数化查询
- 避免原生 SQL 拼接用户输入
- 使用 `DB::raw()` 时确保数据安全
- 定期更新框架和依赖包

## API 设计 (API Design)

### RESTful API

- 遵循 REST 设计原则
- HTTP 动词对应 CRUD 操作
- 状态码正确使用：200、201、204、400、401、403、404、422、500
- 统一的错误响应格式

### API 版本控制

- URL 路径版本控制：`/api/v1/users`
- 头部版本控制：`Accept: application/vnd.api.v1+json`
- 向后兼容性考虑
- 版本废弃通知机制

### API 限流

- 使用 Laravel Sanctum 的 API 限流中间件
- 基于用户或 IP 的限流策略
- 限流响应头信息返回
- 超限时友好的错误提示

### API 文档

- 使用 OpenAPI 3.0 规范
- 自动生成 API 文档
- 详细描述请求参数和响应格式
- 提供请求示例和错误码说明

## 性能优化

### 代码层面

- 避免在循环中执行数据库查询
- 使用 Eloquent 关联预加载
- 大数据处理使用流式处理
- 适当使用缓存减少计算
- 延迟加载非关键数据

### 数据库层面

- 查询优化，使用 EXPLAIN 分析
- 合理设计索引策略
- 读写分离配置
- 数据库连接池优化
- 查询监控和慢查询分析

### 前端优化

- 资源文件压缩和合并
- 使用 CDN 加速静态资源
- 图片懒加载和格式优化
- 浏览器缓存策略
- 使用 Laravel Mix 构建资源

## 中间件 (Middleware)

- 中间件保持单一职责
- 使用类型提示
- 适当的错误处理
- 性能考虑，避免重复验证
- 全局中间件注册在 `Kernel.php`

## 路由定义 (Routes)

- 使用路由组织织相关路由
- API 路由使用版本前缀
- 合理使用中间件
- 路由参数类型约束
- 路由缓存提升性能

## 配置管理

### 环境配置

- 使用环境变量管理配置
- 不同环境使用不同配置文件
- 敏感信息不提交到版本控制
- 配置缓存提升性能
- 配置验证确保必要参数存在

### 服务配置

- 第三方服务配置集中管理
- 使用配置文件而非硬编码
- 开发和生产环境配置分离
- 配置热更新机制

## 部署与运维

### 环境管理

- 使用 `.env` 文件管理环境变量
- 敏感配置使用环境变量
- 不同环境使用不同配置
- 配置缓存提升性能

### 监控告警

- 应用性能监控 (APM)
- 错误率和响应时间监控
- 资源使用率监控
- 关键业务指标监控
- 日志聚合和分析

### 部署流程

- 使用 Laravel Envoy 自动化部署
- 零停机部署策略
- 数据库迁移自动化
- 回滚机制和备份策略
- 健康检查端点

## 文档与注释

### 代码注释

- 使用中文注释，说明业务逻辑
- 复杂算法添加实现思路
- 外部依赖说明用途
- 临时方案添加 TODO 注释
- PHPDoc 注释标准格式

### 项目文档

- README 文件详细说明项目信息
- CHANGELOG 记录版本变更
- 部署文档和环境配置说明
- 架构设计文档

## 代码审查

### 审查要点

- 代码风格是否符合规范
- 业务逻辑是否正确
- 性能是否存在问题
- 安全漏洞检查
- 测试覆盖是否充分

### 提交规范

- Commit 消息使用约定式提交格式
- 功能开发使用特性分支
- 代码合并前必须审查
- 自动化测试必须通过

## 命名约定

### 文件和目录命名

- 控制器：`UserController.php`
- 模型：`User.php`
- 迁移：`2024_01_01_000000_create_users_table.php`
- 工厂：`UserFactory.php`
- 测试：`UserServiceTest.php`
- 中间件：`EnsureUserIsActive.php`
- 请求：`StoreUserRequest.php`
- 资源：`UserResource.php`
- 策略：`UserPolicy.php`
- 观察者：`UserObserver.php`
- 邮件：`WelcomeUserMail.php`
- 通知：`UserRegisteredNotification.php`
- 任务：`ProcessPaymentJob.php`
- 事件：`UserRegistered.php`
- 监听器：`SendWelcomeEmail.php`

### 类和方法命名

- 类名：PascalCase (`UserService`)
- 方法名：camelCase (`findById`)
- 常量：SCREAMING_SNAKE_CASE (`MAX_RETRY_COUNT`)
- 配置键：snake_case (`database.connections.mysql`)
- 路由名称：点分隔 (`users.show`)
- 视图文件：kebab-case (`user-profile.blade.php`)

## 禁止事项

- 禁止在代码中硬编码敏感信息
- 禁止绕过框架的安全机制
- 禁止使用已废弃的 Laravel 功能
- 禁止在控制器中编写复杂业务逻辑
- 禁止忽略异常不进行处理
- 禁止在生产环境使用 `dd()` 或 `dump()`
- 禁止直接在视图中执行数据库查询
- 禁止使用全局变量传递数据
- 禁止绕过 Pint/PHP-CS-Fixer 规则
- 禁止生成冗余样板代码
- 禁止在模型中编写业务逻辑
- 禁止在 Migration 中使用模型类
- 禁止忽略队列任务的失败处理
- 禁止在循环中调用外部 API

## 代码生成要求

生成代码时必须遵循：

1. 完整的命名空间声明
2. 严格类型声明 `declare(strict_types=1);`
3. 适当的类型提示和返回类型
4. 对应的 Pest 测试用例
5. 中文业务注释
6. 错误处理机制
7. 性能考虑（如预加载、缓存等）
8. 安全性检查
9. 遵循单一职责原则
10. 符合项目架构约定
11. 实现适当的设计模式
12. 考虑可扩展性和可维护性
