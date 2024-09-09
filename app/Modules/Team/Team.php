<?php

namespace App\Modules\Team;

use App\Modules\Team\Contracts\NotTeamAware;
use App\Modules\Team\Contracts\TeamAware;
use App\Modules\Team\Exceptions\TeamAwarenessException;
use App\Modules\Team\Queue\MakeQueueTeamAware;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;
use ReflectionException;

use function get_class;

class Team
{
    public function __construct(public Application $app)
    {
    }

    public function start()
    {
        $this
            ->configureModels()
            ->configureRequests()
            ->configureQueue();
    }

    /**
     * @throws ReflectionException
     */
    private function configureQueue(): self
    {
        app(MakeQueueTeamAware::class)->execute();

        return $this;
    }

    private function configureRequests()
    {
        if (!$this->app->runningInConsole()) {
            $this->determineCurrentTeam();
        }

        return $this;
    }

    private function determineCurrentTeam()
    {
        $tenantFinder = $this->app[Finder::class];

        $tenant = $tenantFinder->findForRequest($this->app['request']);

        $tenant?->makeCurrent();
    }

    private function configureModels()
    {
        app('events')->listen('eloquent.booting:*', function ($event, $models) {
            foreach ($models as $model) {
                if (Str::startsWith(get_class($model),
                        'App\\Models\\') && !$model instanceof TeamAware && !$model instanceof NotTeamAware) {
                    throw TeamAwarenessException::noInterface($model);
                }
            }
        });

        return $this;
    }
}
