<?php

namespace App\Providers;

use App\Events\DailyScoresWereUpdated;
use App\Events\DeleteTeamUser;
use App\Events\EntryWasSubmitted;
use App\Events\WeeklyScoresWereUpdated;
use App\Listeners\DipMailInBrandingSauce;
use App\Listeners\ForceUserOutOfTeam;
use App\Listeners\RegenerateDailyScores;
use App\Listeners\RegenerateMonthlyScores;
use App\Listeners\RegenerateWeeklyScores;
use App\Modules\Team\Events\SwitchedCurrentTeam;
use App\Modules\Team\Listeners\SetTeamRootUrl;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Mail\Events\MessageSending;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class              => [
            SendEmailVerificationNotification::class,
        ],
        EntryWasSubmitted::class       => [
            RegenerateDailyScores::class,
        ],
        DailyScoresWereUpdated::class  => [
            RegenerateWeeklyScores::class,
        ],
        WeeklyScoresWereUpdated::class => [
            RegenerateMonthlyScores::class,
        ],
        MessageSending::class          => [
            DipMailInBrandingSauce::class,
        ],
        SwitchedCurrentTeam::class     => [
            SetTeamRootUrl::class,
        ],
        DeleteTeamUser::class          => [
            ForceUserOutOfTeam::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
