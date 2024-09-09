<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Models\User;
use App\Notifications\SendMonthlyReportToUser;
use App\Reports\MonthReport;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendMonthlyReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:monthly';

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
        $now = Carbon::now();
        if ($now->day != 1) {
            $this->error('Hey, it isn\'t the first of the month!');
            return;
        }

        $lastMonth = Carbon::now()->subMonth();
        $month = $lastMonth->month;
        $year = $lastMonth->year;

        $this->line($month, $year);

        Team::all()->each(function ($team) use ($year, $month) {
            $team->makeCurrent();

            $team->users()
                ->isCustomer()
                ->get()
                ->each(function (User $user) use ($year, $month, $team) {
                    Auth::onceUsingId($user->id);

                    if ($user->onTimeout()) {
                        $this->warn("{$user->name} is on timeout");
                        return;
                    }

                    if ($user->scores_monthly()->where('month', $month)->where('year', $year)->count() === 0) {
                        $this->warn("{$user->name} has no scores");
                        return;
                    }

                    $this->line("Sending to $user->name");

                    $monthReport = (new MonthReport($user, $year, $month));

                    try {
                        $user->notify(new SendMonthlyReportToUser($monthReport, $team));
                        $this->info('Invite sent!');
                    } catch (Exception $exception) {
                        $this->warn("Could not send monthly report to $user->id");
                        Log::error("Could not send monthly report to $user->id");
                    }
                });
        });
    }
}
