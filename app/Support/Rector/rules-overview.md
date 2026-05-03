# 2 Rules Overview

## ClassHandleMethodRector

Add noinspections doc comment to declare

- class: [`App\Support\Rector\ClassHandleMethodRector`](ClassHandleMethodRector.php)

```diff
 <?php

 namespace App\Http\Middleware;

 use Illuminate\Http\Request;
+use Symfony\Component\HttpFoundation\Response;

 class VerifySignature
 {
-    public function handle(Request $request, \Closure $next): mixed
+    public function handle(Request $request, \Closure $next): Response
     {
         return $next($request);
     }
 }
```

<br>

## MixinStaticRector

Mixin static

- class: [`App\Support\Rector\MixinStaticRector`](MixinStaticRector.php)

```diff
 namespace App\Support\Mixin;

 use App\Support\Attribute\Mixin;
 use Illuminate\Support\Str;
 use Mtownsend\ReadTime\ReadTime;

 #[Mixin(Str::class)]
 final class StrMixin
 {
-    public function readTime(): \Closure
+    public static function readTime(): \Closure
     {
         /**
          * @param list<string>|string $content
          */
         return static fn (
             array|string $content,
             bool $omitSeconds = true,
             bool $abbreviated = false,
             int $wordsPerMinute = 230
         ): string => new ReadTime($content, $omitSeconds, $abbreviated, $wordsPerMinute)->get();
     }
 }
```

<br>

```diff
 namespace App\Support\Mixin;

 use App\Support\Attribute\Mixin;
 use Illuminate\Support\Str;
 use Illuminate\Support\Stringable;

 #[Mixin(Stringable::class)]
 final class StringableMixin
 {
-    public static function readTime(): \Closure
+    public function readTime(): \Closure
     {
         return fn (
             bool $omitSeconds = true,
             bool $abbreviated = false,
             int $wordsPerMinute = 230
         ): Stringable => new Stringable(
             Str::readTime($this->value, $omitSeconds, $abbreviated, $wordsPerMinute)
         );
     }
 }
```

<br>
