<?php

namespace App\Console\Commands;

use App\Models\Invite;
use App\Models\Review;
use App\Models\Supporter;
use App\Models\Team;
use App\Models\User;
use App\Notifications\SendReviewSupporterReminder;
use Illuminate\Console\Command;
use Log;

use function rescue;

class SendDailyReviewReminders extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily review reminders';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:reviews';

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
     * @return void
     */
    public function handle()
    {
        Team::all()->each(function (Team $team) {
            $team->makeCurrent();

            Review::remindable()->get()->each(function (Review $review) {
                $user = $review->user;
                $this->info("Checking Review with ID {$review->id}");

                /** @var Supporter $supporter */
                foreach ($user->supporters as $supporter) {
                    if (!$supporter->canReceiveReminderForReview($review)) {
                        continue;
                    }

                    $this->line("Send reminder to {$supporter->first_name}");
                    $this->createAndSendReminder($supporter, $review, $user);
                }
            });
        });
    }

    private function createAndSendReminder(Supporter $supporter, Review $review, User $user)
    {
        rescue(function () use ($supporter, $review, $user) {
            $reviewInvitation = Invite::newReviewReminder($user, $review, $supporter);
            $supporter->notify((new SendReviewSupporterReminder($reviewInvitation)));
        }, function () use ($supporter, $review, $user) {
            Log::error("Failed to send message to ".$supporter->name." for review: ".$review->id);
        }, true);
    }
}
