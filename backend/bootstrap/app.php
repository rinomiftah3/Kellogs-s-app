<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Illuminate\Http\Request;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;

use Illuminate\Validation\ValidationException;

use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Session\TokenMismatchException;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use Symfony\Component\HttpFoundation\File\Exception\FileException;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(
    basePath: dirname(__DIR__)
)

    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (
        Middleware $middleware
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Prevent Redirect To Login Route
        |--------------------------------------------------------------------------
        */

        $middleware->redirectGuestsTo(
            fn () => null
        );

        /*
        |--------------------------------------------------------------------------
        | Middleware Alias
        |--------------------------------------------------------------------------
        */

        $middleware->alias([

            'role' =>
                RoleMiddleware::class,

            'permission' =>
                PermissionMiddleware::class,

            'role_or_permission' =>
                RoleOrPermissionMiddleware::class,

        ]);
    })

    ->withExceptions(function (
        Exceptions $exceptions
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Force JSON Response For API
        |--------------------------------------------------------------------------
        */

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) =>

            $request->expectsJson()
            || $request->is('api/*')
        );

        /*
        |--------------------------------------------------------------------------
        | Global API Exception Handler
        |--------------------------------------------------------------------------
        */

        $exceptions->render(
            function (
                Throwable $e,
                Request $request
            ) {

                if (
                    !$request->expectsJson()
                    && !$request->is('api/*')
                ) {
                    return null;
                }

                /*
                |--------------------------------------------------------------------------
                | Validation Exception
                |--------------------------------------------------------------------------
                */

                if (
                    $e instanceof ValidationException
                ) {

                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi gagal',
                        'errors' => $e->errors(),
                    ], 422);
                }

                /*
                |--------------------------------------------------------------------------
                | Authentication Exception
                |--------------------------------------------------------------------------
                */

                if (
                    $e instanceof AuthenticationException
                ) {

                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized',
                    ], 401);
                }

                /*
                |--------------------------------------------------------------------------
                | Authorization Exception
                |--------------------------------------------------------------------------
                */

                if (
                    $e instanceof AuthorizationException
                ) {

                    return response()->json([
                        'success' => false,
                        'message' => 'Forbidden',
                    ], 403);
                }

                /*
                |--------------------------------------------------------------------------
                | CSRF Exception
                |--------------------------------------------------------------------------
                */

                if (
                    $e instanceof TokenMismatchException
                ) {

                    return response()->json([
                        'success' => false,
                        'message' => 'Session expired',
                    ], 419);
                }

                /*
                |--------------------------------------------------------------------------
                | Maintenance Mode
                |--------------------------------------------------------------------------
                */

                if (
                    $e instanceof ServiceUnavailableHttpException
                ) {

                    return response()->json([
                        'success' => false,
                        'message' =>
                            'Application sedang maintenance',
                    ], 503);
                }

                /*
                |--------------------------------------------------------------------------
                | Spatie Unauthorized
                |--------------------------------------------------------------------------
                */

                if (
                    $e instanceof UnauthorizedException
                ) {

                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                    ], $e->getStatusCode());
                }

                /*
                |--------------------------------------------------------------------------
                | Permission Not Found
                |--------------------------------------------------------------------------
                */

                if (
                    $e instanceof PermissionDoesNotExist
                ) {

                    return response()->json([
                        'success' => false,
                        'message' =>
                            'Permission tidak ditemukan',
                    ], 404);
                }

                /*
                |--------------------------------------------------------------------------
                | Model Not Found
                |--------------------------------------------------------------------------
                */

                if (
                    $e instanceof ModelNotFoundException
                ) {

                    return response()->json([
                        'success' => false,
                        'message' =>
                            'Data tidak ditemukan',
                    ], 404);
                }

                /*
                |--------------------------------------------------------------------------
                | Route Not Found
                |--------------------------------------------------------------------------
                */

                if (
                    $e instanceof NotFoundHttpException
                ) {

                    return response()->json([
                        'success' => false,
                        'message' =>
                            'Endpoint tidak ditemukan',
                    ], 404);
                }

                /*
                |--------------------------------------------------------------------------
                | Method Not Allowed
                |--------------------------------------------------------------------------
                */

                if (
                    $e instanceof MethodNotAllowedHttpException
                ) {

                    return response()->json([
                        'success' => false,
                        'message' =>
                            'Method tidak diizinkan',
                    ], 405);
                }

                /*
                |--------------------------------------------------------------------------
                | Too Many Requests
                |--------------------------------------------------------------------------
                */

                if (
                    $e instanceof TooManyRequestsHttpException
                ) {

                    return response()->json([
                        'success' => false,
                        'message' =>
                            'Terlalu banyak request',
                    ], 429);
                }

                /*
                |--------------------------------------------------------------------------
                | Database Exception
                |--------------------------------------------------------------------------
                */

                if (
                    $e instanceof QueryException
                ) {

                    return response()->json([
                        'success' => false,
                        'message' => app()->isProduction()
                            ? 'Terjadi kesalahan database'
                            : $e->getMessage(),
                    ], 500);
                }

                /*
                |--------------------------------------------------------------------------
                | File Upload Exception
                |--------------------------------------------------------------------------
                */

                if (
                    $e instanceof FileException
                ) {

                    return response()->json([
                        'success' => false,
                        'message' =>
                            'Gagal memproses file upload',
                    ], 422);
                }

                /*
                |--------------------------------------------------------------------------
                | Request ID
                |--------------------------------------------------------------------------
                */

                $requestId =
                    (string) Str::uuid();

                /*
                |--------------------------------------------------------------------------
                | Error Logging
                |--------------------------------------------------------------------------
                */

                Log::error(
                    $e->getMessage(),
                    [

                        'request_id' =>
                            $requestId,

                        'exception' =>
                            get_class($e),

                        'file' =>
                            $e->getFile(),

                        'line' =>
                            $e->getLine(),

                        'url' =>
                            $request->fullUrl(),

                        'method' =>
                            $request->method(),

                        'user_id' =>
                            optional(
                                $request->user()
                            )->id,

                        'ip' =>
                            $request->ip(),

                        'user_agent' =>
                            $request->userAgent(),
                    ]
                );

                /*
                |--------------------------------------------------------------------------
                | Internal Server Error
                |--------------------------------------------------------------------------
                */

                return response()->json([

                    'success' => false,

                    'message' => app()->isProduction()
                        ? 'Terjadi kesalahan pada server'
                        : $e->getMessage(),

                    'request_id' =>
                        $requestId,

                    'debug' => app()->isProduction()
                        ? null
                        : [

                            'exception' =>
                                get_class($e),

                            'file' =>
                                $e->getFile(),

                            'line' =>
                                $e->getLine(),
                        ],

                ], 500);
            }
        );
    })

    ->create();