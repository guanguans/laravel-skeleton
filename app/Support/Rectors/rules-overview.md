# 1 Rules Overview

## ClassHandleMethodRector

Add noinspections doc comment to declare

- class: [`App\Support\Rectors\ClassHandleMethodRector`](ClassHandleMethodRector.php)

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
