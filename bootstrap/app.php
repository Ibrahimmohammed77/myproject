<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Illuminate\Foundation\Configuration\Middleware $middleware) {
    })

    ->withExceptions(function (Exceptions $exceptions) {
        // خلّي أي طلب يبدأ بـ api/* يرجع JSON
        $exceptions->shouldRenderJsonWhen(
            fn($request) => $request->is('api/*') || $request->expectsJson()
        );

        // استدعاء Trait ApiResponse مباشرة
        $api = fn() => new class {
            use \App\Traits\ApiResponse;
        };

        // 1) أخطاء الفاليديشن
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) use ($api) {
            return $api()->error("البيانات المدخلة غير صحيحة", "VALIDATION_ERROR", 422, $e->errors());
        });

        // 2) أخطاء قاعدة البيانات
        $exceptions->render(function (\Illuminate\Database\QueryException $e, $request) use ($api) {
            return $api()->error("خطأ في قاعدة البيانات", "DB_ERROR", 500, [
                "message"   => $e->getMessage(),
                "exception" => class_basename($e),
            ]);
        });

        // 3) لو الرابط غير موجود
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) use ($api) {
            return $api()->error("الرابط المطلوب غير موجود", "NOT_FOUND", 404, null);
        });

        // 4) أي خطأ آخر
        $exceptions->render(function (\Throwable $e, $request) use ($api) {
            return $api()->error("خطأ غير متوقع", "SERVER_ERROR", 500, [
                "message"   => $e->getMessage(),
                "exception" => class_basename($e),
            ]);
        });
    })
    ->create();
