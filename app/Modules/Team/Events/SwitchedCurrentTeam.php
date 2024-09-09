<?php

namespace App\Modules\Team\Events;

use App\Models\Team;
use Auth;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SwitchedCurrentTeam
{
    use Dispatchable, SerializesModels;

    public function __construct(public Team $team)
    {
        if (Auth::user()?->belongsToTeam($this->team)) {
            $user = Auth::user();
            $user->timestamps = false;
            $user->forceFill(['current_team_id' => $this->team->id]);
            $user->save();
        }
    }
}
