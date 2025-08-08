<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'client' => \App\Http\Middleware\ClientMiddleware::class,
            'refresh.csrf' => \App\Http\Middleware\RefreshCsrfToken::class,
        ]);
        
        // Use custom CSRF middleware with register route exception
        $middleware->replace(
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\VerifyCsrfToken::class
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'CSRF token mismatch'], 419);
            }
            
            // For registration form, redirect back with error
            if ($request->is('register')) {
                return redirect()->back()
                    ->withInput($request->except('password', 'password_confirmation'))
                    ->withErrors(['csrf' => 'Votre session a expiré. Veuillez réessayer.']);
            }
            
            return redirect()->route('login')->withErrors(['csrf' => 'Votre session a expiré. Veuillez vous reconnecter.']);
        });
    })->withProviders([
        Spatie\Permission\PermissionServiceProvider::class,

    ])->create();
