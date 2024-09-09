<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Notifications\TestNotification;
use Illuminate\Console\Command;

class SendEveryoneATestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notification';

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
     * @return int
     */
    public function handle()
    {
        Team::all()->each(function (Team $team) {
            // This is very important!
            $team->makeCurrent();
            
            $team->allUsers()->each->notify(new TestNotification());
        });

        return 0;
    }
}
