<?php

namespace App\Console\Commands;

use App\Models\Invite;
use App\Models\Team;
use App\Notifications\SendPersonalReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendDailyReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invites:reminders';

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
        $date = Carbon::now();

        Team::all()->each(function (Team $team) use ($date) {
            $team->makeCurrent();

            Invite::where('type', 'tracklist')
                ->notExpired()
                ->get()
                ->each(function (Invite $invite) use ($date, $team) {
                    $user = $invite->user;
                    $reminder = 0;

                    if ($invite->created_at >= (new Carbon($date))->subHours(2)->startOfMinute() && $invite->created_at < (new Carbon($date))->subHours(2)->startOfMinute()->addMinute()) {
                        $reminder = 1;
                    } elseif ($invite->created_at >= (new Carbon($date))->subHours(12)->startOfMinute() && $invite->created_at < (new Carbon($date))->subHours(12)->startOfMinute()->addMinute()) {
                        $reminder = 2;
                    }

                    if ($reminder > 0) {
                        try {
                            Log::info("=== Sending reminder '{$reminder}' to {$user->name} on {$team->fqdn} (Team ID: {$team->id})");
                            $user->notify(new SendPersonalReminder($reminder, $invite, $team));
                            Log::notice("Sent");
                        } catch (Throwable $exception) {
                            \Log::error("Failed {$invite->id} (Team ID: {$team->id})");
                        }
                    }
                });
        });
    }
}
