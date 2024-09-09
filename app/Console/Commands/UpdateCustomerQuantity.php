<?php

namespace App\Console\Commands;

use App\Models\Team;
use Illuminate\Console\Command;

class UpdateCustomerQuantity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:quantity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all customer quantities';

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
     * @return int
     */
    public function handle()
    {
        Team::all()->each(function (Team $team) {
            $team->makeCurrent();

            if ($team->subscribed()) {
                $quantity = $team->users()->isBillableUser()->count();
                if ($team->hasPlanOption('unlimited')) {
                    $quantity = 1;
                }

                $team->subscription()->updateQuantity($quantity);
            }
        });
    }
}
