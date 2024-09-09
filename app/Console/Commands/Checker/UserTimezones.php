<?php

namespace App\Console\Commands\Checker;

use App\Models\Team;
use App\Models\User;
use Illuminate\Console\Command;

class UserTimezones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checker:timezones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Team::all()->each(function (Team $team) {
            $team->makeCurrent();
            $team->users->each(function (User $user) use ($team) {
                if ($user->getSettings($team)->use_own_timezone) {
                    $user->save();
                }
            });
        });
    }
}
