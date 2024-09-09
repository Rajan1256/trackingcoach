<?php

namespace App\Console\Commands;

use App\Models\Answer;
use App\Models\Supporter;
use App\Models\Team;
use App\Models\User;
use Illuminate\Console\Command;
use Str;

class PostCoSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'co:sync:finished';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to run after co:sync';

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
        if (app()->environment() !== 'local') {
            return;
        }

        Team::each(function ($team) {
            $fqdn = Str::of($team->fqdn);
            $newFqdn = null;
            $domains = ['trackingcoach.creativeorange.dev', 'trackingcoach.com'];
            foreach ($domains as $domain) {
                if ($fqdn->endsWith($domain)) {
                    $newFqdn = $fqdn->replace($domain, config('app.domain'));
                }
            }

            if ($newFqdn === null) {
                return;
            }

            $team->timestamps = false;
            $team->fqdn = $newFqdn;
            $team->save();
        });

        Supporter::withoutGlobalScopes()->get()->each(function (Supporter $supporter) {
            $supporter->phone = fake()->unique()->phoneNumber;
            $supporter->email = fake()->unique()->safeEmail;
            $supporter->first_name = fake()->firstName;
            $supporter->last_name = fake()->lastName;
            $supporter->timestamps = false;
            $supporter->save();
        });

        // anonimize all users
        User::all()->each(function (User $user) {
            if (\Illuminate\Support\Str::of($user->email)->endsWith(['creativeorange.nl', 'trackingcoach.com', 'topmind.com'])) {
                return;
            }

            $user->timestamps = false;
            $user->email = fake()->unique()->safeEmail;
            $user->first_name = fake()->firstName;
            $user->last_name = fake()->lastName;
            $user->save();
        });

        Answer::withoutGlobalScopes()->whereNotNull('answer_text')->get()->each(function (Answer $answer) {
            $answer->answer_text = fake()->sentence(\Illuminate\Support\Str::of($answer->answer_text)->wordCount());
            $answer->timestamps = false;
            $answer->save();
        });


        return 0;
    }
}
