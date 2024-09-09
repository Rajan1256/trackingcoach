<?php

namespace App\Listeners;

use App\Events\DeleteTeamUser;
use App\Models\CoachUser;
use App\Models\PhysiologistUser;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ForceUserOutOfTeam
{
    public function handle(DeleteTeamUser $event)
    {
        /** @var User $user */
        $user = $event->user;
        /** @var Team $team */
        $team = $event->team;

        $team->makeCurrent();
        $user->answers()->withoutGlobalScope(SoftDeletingScope::class)->toBase()->delete();
        $user->consents()->withoutGlobalScope(SoftDeletingScope::class)->detach();
        $user->customerSchedules()->withoutGlobalScope(SoftDeletingScope::class)->toBase()->delete();
        $user->scores_daily()->withoutGlobalScope(SoftDeletingScope::class)->toBase()->delete();
        $user->scores_weekly()->withoutGlobalScope(SoftDeletingScope::class)->toBase()->delete();
        $user->scores_monthly()->withoutGlobalScope(SoftDeletingScope::class)->toBase()->delete();
        $user->entries()->withoutGlobalScope(SoftDeletingScope::class)->toBase()->delete();
        $user->questions()->withoutGlobalScope(SoftDeletingScope::class)->toBase()->delete();
        $user->goals()->withoutGlobalScope(SoftDeletingScope::class)->toBase()->delete();
        $user->interviews()->withoutGlobalScope(SoftDeletingScope::class)->toBase()->delete();
        $user->invites()->withoutGlobalScope(SoftDeletingScope::class)->toBase()->delete();
        $user->notes()->withoutGlobalScope(SoftDeletingScope::class)->toBase()->delete();
        $user->timeouts()->withoutGlobalScope(SoftDeletingScope::class)->toBase()->delete();
        $user->programMilestones()->withoutGlobalScope(SoftDeletingScope::class)->toBase()->delete();
        $user->reviews()->withoutGlobalScope(SoftDeletingScope::class)->toBase()->delete();
        $user->supporters()->withoutGlobalScope(SoftDeletingScope::class)->toBase()->delete();
        $user->tests()->withoutGlobalScope(SoftDeletingScope::class)->toBase()->delete();

        PhysiologistUser::where('team_id', $team->id)->where('user_id', $user->id)->delete();
        CoachUser::where('team_id', $team->id)->where('user_id', $user->id)->delete();

        $team->users()->detach($user->id);

        if ($user->teams()->count() === 0) {
            $user->forceDelete();
        }
    }
}
