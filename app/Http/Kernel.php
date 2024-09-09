<?php

namespace App\Http;

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CheckForUnacceptedConsents;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\IsVerified;
use App\Http\Middleware\PreventRequestsDuringMaintenance;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\RedirectToOtherWebsiteMiddleware;
use App\Http\Middleware\TrackLastActivityMiddleware;
use App\Http\Middleware\TrimStrings;
use App\Http\Middleware\TrustProxies;
use App\Http\Middleware\VerifyCsrfToken;
use App\Modules\Team\Http\Middleware\EnsureValidTeamSession;
use App\Modules\Team\Http\Middleware\HasMembershipForTeam;
use App\Modules\Team\Http\Middleware\HasNoMembershipForTeam;
use App\Modules\Team\Http\Middleware\NeedsTeam;
use Fruitcake\Cors\HandleCors;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        TrustProxies::class,
        HandleCors::class,
        PreventRequestsDuringMaintenance::class,
        ValidatePostSize::class,
        TrimStrings::class,
        ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            RedirectToOtherWebsiteMiddleware::class,
        ],

        'team' => [
            NeedsTeam::class,
            EnsureValidTeamSession::class,
//            NeedActiveSubscription::class,
            TrackLastActivityMiddleware::class,
        ],

        'api' => [
            'throttle:api',
            SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'             => Authenticate::class,
        'auth.basic'       => AuthenticateWithBasicAuth::class,
        'cache.headers'    => SetCacheHeaders::class,
        'can'              => Authorize::class,
        'guest'            => RedirectIfAuthenticated::class,
        'password.confirm' => RequirePassword::class,
        'signed'           => ValidateSignature::class,
        'team-access'      => HasMembershipForTeam::class,
        'throttle'         => ThrottleRequests::class,
        'verified'         => EnsureEmailIsVerified::class,
        'check-consents'   => CheckForUnacceptedConsents::class,
        'no-team-access'   => HasNoMembershipForTeam::class,
        'is-verified'      => IsVerified::class,
    ];
}
