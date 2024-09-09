<?php

namespace App\Jobs;

use App\Exports\BigDataExport;
use App\Modules\Team\Contracts\QueueTeamAware;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StartBigDataExport implements ShouldQueue, QueueTeamAware
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public $team, public $year, public $user, public $export)
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
        (new BigDataExport($this->year,
            $this->user))->queue('exports/'.$this->team->id.'/'.$this->export->id.'/big_data.xlsx', 's3');
    }
}
