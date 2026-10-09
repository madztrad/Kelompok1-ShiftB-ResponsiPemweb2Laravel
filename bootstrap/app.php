<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Exceptions\MissingAbilityException;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Sanctum token ability: dipakai sebagai "abilities:admin" / "abilities:user"
        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        /*
         * Format error API yang konsisten:
         * { "message": "...", "errors": null | { field: [..] } }
         */
        $error = fn (string $message, int $status, ?array $errors = null) => response()->json([
            'message' => $message,
            'errors' => $errors,
        ], $status);

        $exceptions->render(function (ValidationException $e, Request $request) use ($error) {
            if ($request->is('api/*')) {
                return $error('Data yang dikirim tidak valid.', 422, $e->errors());
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) use ($error) {
            if ($request->is('api/*')) {
                return $error('Anda belum login atau token tidak valid.', 401);
            }
        });

        // ability token tidak cukup (mis. token user mengakses /api/admin/*)
        $exceptions->render(function (MissingAbilityException $e, Request $request) use ($error) {
            if ($request->is('api/*')) {
                return $error('Anda tidak memiliki akses untuk melakukan aksi ini.', 403);
            }
        });

        // Policy/Gate yang menolak (AuthorizationException sudah dikonversi Laravel)
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) use ($error) {
            if ($request->is('api/*')) {
                return $error('Anda tidak memiliki akses untuk melakukan aksi ini.', 403);
            }
        });

        // Route tidak ada & ModelNotFoundException (sudah dikonversi Laravel)
        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($error) {
            if ($request->is('api/*')) {
                return $error('Data tidak ditemukan.', 404);
            }
        });

        // abort(409, '...'), throttle 429, method not allowed 405, dll.
        $exceptions->render(function (HttpExceptionInterface $e, Request $request) use ($error) {
            if ($request->is('api/*')) {
                $status = $e->getStatusCode();

                return $error($e->getMessage() ?: (Response::$statusTexts[$status] ?? 'Terjadi kesalahan.'), $status);
            }
        });
    })->create();
