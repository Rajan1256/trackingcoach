<?php

namespace App\Modules\Team\Exceptions;

use Exception;
use Illuminate\Queue\Events\JobProcessing;

class TeamAwarenessException extends Exception
{
    /**
     * @param  JobProcessing  $event
     * @return static
     */
    public static function noIdSet(JobProcessing $event): self
    {
        return new static("The current team could not be determined in a job named `".$event->job->getName()."`. No `teamId` was set in the payload.");
    }

    /**
     * @param  JobProcessing  $event
     * @return static
     */
    public static function noTeamFound(JobProcessing $event): self
    {
        return new static("The current team could not be determined in a job named `".$event->job->getName()."`. The team finder could not find a team.");
    }

    /**
     * @param  mixed  $model
     * @return static
     */
    public static function noInterface(mixed $model): static
    {
        return new static("The current model `".get_class($model)."` does not implement a Team Awareness interface (either TeamAware or NotTeamAware).");
    }
}
