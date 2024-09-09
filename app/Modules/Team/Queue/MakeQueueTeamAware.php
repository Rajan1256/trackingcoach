<?php

namespace App\Modules\Team\Queue;

use App\Models\Team;
use App\Modules\Team\Contracts\QueueNotTeamAware;
use App\Modules\Team\Contracts\QueueTeamAware;
use App\Modules\Team\Exceptions\TeamAwarenessException;
use Illuminate\Broadcasting\BroadcastEvent;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Mail\SendQueuedMailable;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobRetryRequested;
use Log;
use Queue;
use ReflectionClass;
use ReflectionException;

class MakeQueueTeamAware
{
    /**
     * @throws ReflectionException
     */
    public function execute()
    {
        $this
            ->listenForJobsBeingQueued()
            ->listenForJobsBeingProcessed()
            ->listenForJobsRetryRequested();
    }

    private function listenForJobsRetryRequested()
    {
        app('events')->listen(JobRetryRequested::class, function (JobRetryRequested $event) {
            if (!array_key_exists('teamId', $event->payload())) {
                return;
            }

            $this->findTeam($event)->makeCurrent();
        });

        return $this;
    }

    /**
     * @param  JobProcessing  $event
     * @return Team
     * @throws TeamAwarenessException
     */
    private function findTeam(JobProcessing $event): Team
    {
        $teamId = $event->job->payload()['teamId'];

        if (!$teamId) {
            $event->job->delete();

            throw TeamAwarenessException::noIdSet($event);
        }


        /** @var Team $team */
        if (!$team = Team::find($teamId)) {
            $event->job->delete();

            throw TeamAwarenessException::noTeamFound($event);
        }

        return $team;
    }

    /**
     * @return $this
     */
    private function listenForJobsBeingProcessed(): self
    {
        Queue::before(function (JobProcessing $event) {
            if (!array_key_exists('teamId', $event->job->payload())) {
                Log::info("team ID does not exist");
                return;
            }

            $this->findTeam($event)->makeCurrent();
        });

        return $this;
    }

    /**
     * @return $this
     * @throws ReflectionException
     */
    private function listenForJobsBeingQueued(): self
    {
        app('queue')->createPayloadUsing(function ($connectionName, $queue, $payload) {
            $queueable = $payload['data']['command'];

            if (!$this->isTeamAware($queueable)) {
                return [];
            }

            return ['teamId' => Team::current()?->id];
        });

        return $this;
    }

    /**
     * @param  mixed  $queueable
     * @return bool
     * @throws ReflectionException
     */
    private function isTeamAware(mixed $queueable): bool
    {
        $reflection = new ReflectionClass($this->getJobFromQueueable($queueable));

        if ($reflection->implementsInterface(QueueTeamAware::class)) {
            return true;
        }

        if ($reflection->implementsInterface(QueueNotTeamAware::class)) {
            return false;
        }

        return false;
    }

    /**
     * @param  mixed  $queueable
     * @return mixed
     */
    private function getJobFromQueueable(mixed $queueable)
    {
        $job = [
                SendQueuedMailable::class      => 'mailable',
                SendQueuedNotifications::class => 'notification',
                CallQueuedListener::class      => 'class',
                BroadcastEvent::class          => 'event',
            ][$queueable::class] ?? null;

        if (!$job) {
            return $queueable;
        }

        if (method_exists($queueable, $job)) {
            return $queueable->{$job}();
        }

        return $queueable->$job;
    }
}
