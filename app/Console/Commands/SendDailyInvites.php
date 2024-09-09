<?php

namespace App\Console\Commands;

use App\Models\Invite;
use App\Models\Team;
use App\Notifications\SendPersonalInvite;
use Auth;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDailyInvites extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invites:send {--t|time= : Force a time (in HH:MM)} {--dry-run : Disable sending the actual invite}';

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
        if ($time = $this->option('time')) {
            $exploded = explode(':', $time);
            $date->setTime($exploded[0], $exploded[1] ?? 0);
        }
        $date->second(0);

        Team::whereHas('users')->each(function (Team $team) use ($date) {
            $team->makeCurrent();
            $customers = $team->users()->isCustomer()->where('auto_invite_time', $date->format('H:i:s'))
                ->get();

            foreach ($customers as $customer) {
                Auth::onceUsingId($customer->id);

                $settings = $customer->getSettings($team);

                $dateForCustomer = (new Carbon($date))->addDays($settings->timezone_offset_days ?? 0);

                $existingInvite = Invite::whereJsonContains('options->date', $date->format('Y-m-d'),)->where([
                    'delivery_status' => 'sent',
                    'team_id'         => $team->id,
                    'user_id'         => $customer->id,
                ])->first();

                if ($existingInvite) {
                    $this->warn($customer->name.' already received an invite');
                    continue;
                }

                if ($dateForCustomer->isWeekend() && $settings->days_per_week != 7) {
                    continue;
                }

                if ($customer->onTimeout()) {
                    $this->warn($customer->name.' is on a timeout');
                    continue;
                }

                $questionCount = $customer->questions()->tracklist()->get()->filter(function ($question) use (
                    $dateForCustomer
                ) {
                    if ($dateForCustomer->isWeekend() && $question->options->get('excludeWeekends', 0) == 1) {
                        return false;
                    }

                    return true;
                })->count();

                if ($questionCount === 0) {
                    $this->warn($customer->name.' does not have questions');
                    continue;
                }

                $this->line('Sending to '.$customer->name);

                if ($this->option('dry-run')) {
                    $this->line('Able to send the invite, not doing so because we\'re running a dry-run');
                } else {
                    $invite = Invite::newTracklistInvite($customer, $dateForCustomer, $team);

                    if ($invite) {
                        $this->line("=== Sending message to {$customer->name} ===");
                        rescue(function () use ($customer, $date, $invite, $team) {
                            $customer->notify(new SendPersonalInvite($date, $invite, $team));
                            $invite->updateQuietly(['delivery_status' => 'sent']);
                            $this->line("Sent");
                        }, function () use ($invite) {
                            $this->line("Failed");
                            $invite->updateQuietly(['delivery_status' => 'failed']);
                        });
                    }
                }
            }
        });
    }
}
