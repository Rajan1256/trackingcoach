<?php

namespace App\Console\Commands;

use App\Events\EntryWasSubmitted;
use App\Models\Entry;
use App\Modules\Team\Scopes\TeamAwareScope;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class RegenerateScores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scores:regenerate {--T|--team= : The ID of the team for which scores should be regenerated} {--Y|--year= : The year for which scores should be regenerated}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate all scores';

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
        $team = $this->option('team');
        $year = $this->option('year');

        $entries = Entry::withoutGlobalScopes([TeamAwareScope::class])->with('team')->when($team,
            function (Builder $query, string $team) {
                $query->where('team_id', $team);
            })
            ->when($year, function (Builder $query, string $year) {
                $start = CarbonImmutable::createFromFormat('Y-m-d', "{$year}-01-01");
                $end = $start->addYear();

                $query->where('date', '>=', $start)
                    ->where('date', '<', $end);
            })->get();

        foreach ($entries as $entry) {
            $entry->team->makeCurrent();
            event(new EntryWasSubmitted($entry));
        }
    }
}
