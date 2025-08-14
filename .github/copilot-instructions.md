# Laravel PHP 8.3+ Package Copilot Instructions

[//]: # (https://github.com/github/awesome-copilot)

## 项目背景

- 技术栈：PHP \(>=8.3\), Laravel 12, Composer, NPM。
- 代码风格：PSR-12，使用 Laravel Pint / PHP-CS-Fixer \(仓库含自定义 Fixer\)。
- 测试：Pest 优先；必要时 PHPUnit。
- 目标：可维护、类型明确、易测试。

## 约定与输出

- 严格遵守 PSR-12；尽量使用 `declare\(strict_types=1\);`。
- Controller 精简，业务放到 Service/Action；复用 Form Request 校验。
- Eloquent: 使用资源集合/Resource 转换响应；避免 N\+1，必要时 `with\(\)`。
- 依赖注入优先，使用构造注入；不可隐式 new 复杂对象。
- 错误处理：抛出领域异常或使用 Laravel 异常处理器；避免静默失败。
- 注释与提交信息使用中文简洁描述；代码内注释简短说明“为何”，少写“做了什么”。

## 安全与合规

- 不要输出或伪造任何密钥、令牌、证书、真实个人信息。
- 生成示例时使用占位符，例如 `YOUR\_KEY`、`example.com`。

## 文件与目录

- 应用代码：`app/`；测试：`tests/`；配置：`config/`。
- 迁移与填充：`database/migrations`、`database/seeders`。
- 若需新类，放在合理命名空间下并附上最小可用示例与对应测试。

## 代码示例约束

- PHP 示例包含命名空间与类型声明；方法/类尽量短小。
- 返回 JSON 的接口示例包含路由、请求验证、Resource 转换与测试样例。
- 生成单测使用 Pest，命名 `it\('描述'\)`；覆盖正常与异常路径。

## 禁止事项

- 禁止引入与现有栈不一致的框架或风格。
- 禁止绕过现有 Fixer/CI 规则；禁止生成过度样板。
