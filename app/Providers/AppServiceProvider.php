<?php

namespace App\Providers;

use App\Models\Team;
use App\Models\User;
use App\Modules\Team\Exceptions\NoMembershipForThisTeamException;
use Flare;
use Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Cashier\Cashier;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Password::defaults(function () {
            return Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised();
        });
        Cashier::useCustomerModel(Team::class);
        Cashier::calculateTaxes();
        Flare::filterExceptionsUsing(
            fn(Throwable $throwable) => !$throwable instanceof NoMembershipForThisTeamException
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
