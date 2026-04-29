<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'portal/daftar',
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        $middleware->alias([
            'role'          => \App\Http\Middleware\RoleMiddleware::class,
            'module'        => \App\Http\Middleware\ModuleAccess::class,
            'auth'          => \App\Http\Middleware\Authenticate::class,
            'mobile.auth'   => \App\Http\Middleware\AuthenticateMobileToken::class,
            'mobile.role'   => \App\Http\Middleware\MobileRoleMiddleware::class,
            'mobile.locale' => \App\Http\Middleware\SetApiLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            // Let Laravel handle these natively (redirects, validation, 404, etc.)
            if ($request->expectsJson()
                || $e instanceof \Illuminate\Auth\AuthenticationException
                || $e instanceof \Illuminate\Validation\ValidationException
                || $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException
                || $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException
                || $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
            ) {
                return null;
            }
            return response()->view('errors.500', ['exception' => $e], 500);
        });
    })->create();
