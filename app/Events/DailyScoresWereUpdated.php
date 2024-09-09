<?php

namespace App\Events;

use App\Models\Entry;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DailyScoresWereUpdated
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  Entry  $entry
     */
    public function __construct(public Entry $entry)
    {
    }
}
