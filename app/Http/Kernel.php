<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'check.roles' => \App\Http\Middleware\CheckRoles::class,
        'content.store' => \App\Http\Middleware\Auth\StoreContentMiddleware::class, 
        'books.store' => \App\Http\Middleware\Auth\BooksStoreMiddleware::class, 
        'library.store' => \App\Http\Middleware\Auth\LibraryStoreMiddleware::class, 
        'role' => \App\Http\Middleware\Auth\RoleMiddleware::class, 
        'author.id.control' =>\App\Http\Middleware\Auth\AuthorIdControlMiddleware::class,
        'publisher.id.control' =>\App\Http\Middleware\Auth\PublisherIdControlMiddleware::class,
        'category.id.control' =>\App\Http\Middleware\Auth\CategoryIdControlMiddleware::class,
        'books.id.control' =>\App\Http\Middleware\Auth\BooksIdControlMiddleware::class,
        'user.validation' =>\App\Http\Middleware\User\ValidationMiddleware::class,
        'user.update' =>\App\Http\Middleware\User\UpdateMiddleware::class,
        'edit.profile' =>\App\Http\Middleware\User\EditProfileMiddleware::class,
        'user.id.control' =>\App\Http\Middleware\User\UserIdConrolMiddleware::class,
    ];
}
