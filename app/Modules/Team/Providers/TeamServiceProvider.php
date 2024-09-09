<?php

namespace App\Modules\Team\Providers;

use App\Modules\Team\Team;
use Illuminate\Support\ServiceProvider;

class TeamServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->bind(Team::class, fn($app) => new Team($app));

        app(Team::class)->start();
    }

}
