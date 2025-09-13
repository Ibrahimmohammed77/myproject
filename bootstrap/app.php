<?php

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {})
    ->withExceptions(function (Exceptions $exceptions) {

        //  اجعل الاستجابات JSON حصراً لمسارات /api/*
        $exceptions->shouldRenderJsonWhen(fn($request) => $request->is('api/*'));

        //  مُنشئ سريع للاستجابة الموحّدة
        $api = fn() => new class {
            use App\Traits\ApiResponse;
        };

        // 422: أخطاء التحقق
        $exceptions->render(function (ValidationException $e, $request) use ($api) {
            if (! $request->is('api/*')) return null; // اترك الويب يعيد redirect مع أخطاء الجلسة
            return $api()->error(
                'حدث خطأ أثناء إدخال البيانات',
                'VALIDATION_ERROR',
                422,
                [
                    'message'   => $e->getMessage(),
                    'exception' => class_basename($e),
                    'errors'    => $e->errors(),
                ]
            );
        });

        // 404: رابط غير موجود
        $exceptions->render(function (NotFoundHttpException $e, $request) use ($api) {
            if (! $request->is('api/*')) return null;
            return $api()->error(
                'الرابط المطلوب غير موجود',
                'ROUTE_NOT_FOUND',
                404,
                [
                    'message'   => $e->getMessage(),
                    'exception' => class_basename($e),
                ]
            );
        });

        // 500: خطأ قاعدة البيانات
        $exceptions->render(function (QueryException $e, $request) use ($api) {
            if (! $request->is('api/*')) return null;
            $payload = [
                'message'   => $e->getMessage(),
                'exception' => class_basename($e),
            ];
            if (! app()->isLocal()) {
                // في بيئة الإنتاج لا تُظهر التفاصيل
                unset($payload['message']);
            }
            return $api()->error(
                'خطأ في قاعدة البيانات',
                'DATABASE_ERROR',
                500,
                $payload
            );
        });

        // 500: أخطاء عامة
        $exceptions->render(function (\Throwable $e, $request) use ($api) {
            if (! $request->is('api/*')) return null;
            $payload = [
                'message'   => $e->getMessage(),
                'exception' => class_basename($e),
            ];
            if (! app()->isLocal()) {
                unset($payload['message']);
            }
            return $api()->error(
                'خطأ غير متوقع',
                'UNDEFINED_ERROR',
                500,
                $payload
            );
        });
    })->create();
