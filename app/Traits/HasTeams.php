<?php

namespace App\Traits;

use App\Models\Membership;
use App\Models\Team;
use Illuminate\Collections\Collection;
use Illuminate\Support\Str;
use Laravel\Jetstream\Role;

use function collect;

trait HasTeams
{
    /**
     * Determine if the given team is the current team.
     *
     * @param  mixed  $team
     * @return bool
     */
    public function isCurrentTeam($team)
    {
        return $team->id === $this->currentTeam->id;
    }

    /**
     * Get the current team of the user's context.
     */
    public function currentTeam()
    {
        if (is_null($this->current_team_id)) {
            $this->forceFill([
                'current_team_id' => $this->teams()->first()->id ?? null,
            ])->save();
        }

        return $this->belongsTo(Team::class, 'current_team_id');
    }

    /**
     * Get all of the teams the user belongs to.
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class)
            ->withPivot(['roles', 'company_name', 'paired_app_token', 'data'])
            ->withTimestamps()
            ->using(Membership::class)
            ->as('membership');
    }

    /**
     * Get all of the teams the user owns or belongs to.
     *
     * @return Collection
     */
    public function allTeams()
    {
        return $this->ownedTeams->merge($this->teams)->sortBy('name');
    }

    /**
     * Get all of the teams the user belongs to.
     */
    public function ownedTeams()
    {
        return $this->hasMany(Team::class);
    }

    /**
     * Determine if the user has the given permission on the given team.
     *
     * @param  mixed  $team
     * @param  string  $permission
     * @return bool
     */
    public function hasTeamPermission($team, string $permission)
    {
        if ($this->ownsTeam($team)) {
            return true;
        }

        if (!$this->belongsToTeam($team)) {
            return false;
        }

        $permissions = $this->teamPermissions($team);

        return in_array($permission, $permissions) ||
            in_array('*', $permissions) ||
            (Str::endsWith($permission, ':create') && in_array('*:create', $permissions)) ||
            (Str::endsWith($permission, ':update') && in_array('*:update', $permissions));
    }

    /**
     * Determine if the user owns the given team.
     *
     * @param  mixed  $team
     * @return bool
     */
    public function ownsTeam($team)
    {
        return $this->id == $team->user_id;
    }

    /**
     * Determine if the user belongs to the given team.
     *
     * @param  mixed  $team
     * @return bool
     */
    public function belongsToTeam($team)
    {
        return $this->teams->contains(function ($t) use ($team) {
                return $t->id === $team->id;
            }) || $this->ownsTeam($team);
    }

    /**
     * Get the user's permissions for the given team.
     *
     * @param  mixed  $team
     * @return array
     */
    public function teamPermissions($team)
    {
        if ($this->ownsTeam($team)) {
            return ['*'];
        }

        if (!$this->belongsToTeam($team)) {
            return [];
        }

        return $this->teamRole($team)->permissions;
    }

    /**
     * Get the role that the user has on the team.
     *
     * @param  mixed  $team
     * @return Role
     */
    public function teamRole($team)
    {
        if ($this->ownsTeam($team)) {
            return new OwnerRole;
        }

        if (!$this->belongsToTeam($team)) {
            return;
        }

        return Jetstream::findRole($team->users->where(
            'id', $this->id
        )->first()->membership->role);
    }

    public function hasCurrentTeamRole(array|string $roles = [])
    {
        if (!is_array($roles)) {
            $roles = [$roles];
        }

        $hasRole = false;
        foreach ($roles as $role) {
            $hasRole = $this->currentRoles()->contains($role);

            if ($hasRole) {
                break;
            }
        }

        return $hasRole;
    }

    public function currentRoles()
    {
        $teamId = current_team()?->id ?? $this->current_team_id;

        if (!$teamId) {
            return collect();
        }

        return $this->teams()->where('teams.id', $teamId)->first()?->membership?->roles ?? collect();
    }
}
