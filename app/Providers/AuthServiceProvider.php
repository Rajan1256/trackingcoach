<?php

namespace App\Providers;

use App\Enum\Roles;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use function in_array;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {

        Gate::before(function (User $user, string $ability) {
            if($user->id === 1) {
                return true;
            }
        });

        Gate::after(function ($user, $ability, $result, $arguments) {
            if ($user->hasCurrentTeamRole([Roles::ADMIN]) && (!empty($arguments[0]) &&
                    !in_array($arguments[0], [
                        'App\Models\Team',
                    ]) && !$arguments[0] instanceof Team)) {
                return true;
            }
        });

        // Team member policy
        Gate::define('viewAny-teamMember', 'App\Policies\TeamMemberPolicy@viewAny');
        Gate::define('view-teamMember', 'App\Policies\TeamMemberPolicy@view');
        Gate::define('create-teamMember', 'App\Policies\TeamMemberPolicy@create');
        Gate::define('update-teamMember', 'App\Policies\TeamMemberPolicy@update');
        Gate::define('delete-teamMember', 'App\Policies\TeamMemberPolicy@delete');
        Gate::define('restore-teamMember', 'App\Policies\TeamMemberPolicy@restore');
        Gate::define('forceDelete-teamMember', 'App\Policies\TeamMemberPolicy@forceDelete');
        Gate::define('promote-teamMember', 'App\Policies\TeamMemberPolicy@promote');

        // Global timeout policy
        Gate::define('viewAny-globalTimeout', 'App\Policies\GlobalTimeoutPolicy@viewAny');
        Gate::define('view-globalTimeout', 'App\Policies\GlobalTimeoutPolicy@view');
        Gate::define('create-globalTimeout', 'App\Policies\GlobalTimeoutPolicy@create');
        Gate::define('update-globalTimeout', 'App\Policies\GlobalTimeoutPolicy@update');
        Gate::define('delete-globalTimeout', 'App\Policies\GlobalTimeoutPolicy@delete');
        Gate::define('restore-globalTimeout', 'App\Policies\GlobalTimeoutPolicy@restore');
        Gate::define('forceDelete-globalTimeout', 'App\Policies\GlobalTimeoutPolicy@forceDelete');

        $this->registerPolicies();
    }
}
