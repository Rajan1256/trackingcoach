<?php

namespace App\Jobs;

use App\Modules\Team\Contracts\QueueTeamAware;
use App\Notifications\SendBigDataExportMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Storage;

class FinishBigDataExport implements ShouldQueue, QueueTeamAware
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public $team, public $export, public $mail, public $user)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        while (!Storage::disk('s3')->has('exports/'.$this->team->id.'/'.$this->export->id.'/big_data.xlsx')) {
        }

        $this->export->addMediaFromDisk('exports/'.$this->team->id.'/'.$this->export->id.'/big_data.xlsx', 's3')
            ->preservingOriginal()
            ->toMediaCollection('exports');

        $this->export->status = 1;
        $this->export->save();

        if ($this->mail) {
            $this->user->notify(new SendBigDataExportMail($this->export, $this->team));
        }
    }
}
