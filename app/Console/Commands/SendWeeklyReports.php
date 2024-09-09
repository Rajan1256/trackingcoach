<?php

namespace App\Console\Commands;

use App\Models\Invite;
use App\Models\Team;
use App\Models\User;
use App\Notifications\SendWeeklyReportToUser;
use App\Reports\WeekReport;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Log;

class SendWeeklyReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:weekly';

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
        $today = Carbon::now();

        if ($today->isSaturday()) {
            $week = $today->format('W');
            $year = $today->format('o');
            $daysPerWeek = 5;
        } elseif ($today->isMonday()) {
            $lastWeek = (new Carbon($today))->subWeek();
            $week = $lastWeek->format('W');
            $year = $lastWeek->format('o');
            $daysPerWeek = 7;
        } else {
            $this->warn('Today is not the day');
            return;
        }

        Team::all()->each(function (Team $team) use ($week, $year, $daysPerWeek) {
            $team->makeCurrent();
            $this->info("===== SENDING INVITES FOR {$daysPerWeek} DAYS PER WEEK, {$year}W{$week} for team {$team->id} ======");

            $team->users()
                ->isCustomer()
                ->where('days_per_week', $daysPerWeek)
                ->get()
                ->each(function (User $customer) use ($week, $year, $team) {
                    Auth::onceUsingId($customer->id);

                    if ($customer->onTimeout()) {
                        $this->warn("{$customer->name} is on timeout");
                        return;
                    }

                    if ($customer->scores_weekly()->where('week', $week)->where('year', $year)->count() === 0) {
                        $this->warn("No scores for {$customer->name}");
                        return;
                    }

                    $this->line("Sending to {$customer->name}");

                    $weekReport = new WeekReport($customer, $year, $week, $team);
                    $invite = Invite::newWeeklyReportInvite($customer, $year, $week);

                    if (!$invite instanceof Invite) {
                        $this->warn('Failed to generate a new invite, perhaps the client already received one.');
                        return;
                    }

                    try {
                        $customer->notify(new SendWeeklyReportToUser($weekReport, $invite, $team));
                        $this->info('Invite sent!');
                    } catch (Exception $exception) {
                        $this->warn("Could not send weekly report to {$customer->name}");
                        Log::error("Could not send weekly report to client $customer->id");
                    }
                });
        });
    }
}
