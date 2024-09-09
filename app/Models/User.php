<?php

namespace App\Models;

use App\Enum\Roles;
use App\Modules\Team\Contracts\NotTeamAware;
use App\Settings;
use App\Traits\HasTeams;
use Auth;
use Carbon\Carbon;
use Creativeorange\Gravatar\Facades\Gravatar;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Jenssegers\Date\Date;

use function current_team;
use function is_null;

/**
 * @property int $days_per_week
 */
class User extends Authenticatable implements NotTeamAware
{
    use HasFactory, Notifiable, HasTeams, SoftDeletes;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'name',
        'email',
        'password',
        'language',
        'date_format',
        'timezone',
        'locale',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static function boot()
    {
        parent::boot();

        static::saved(function (self $user) {
            $settings = rescue(function () use ($user) {
                return $user->getSettings();
            }, null, false) ?? null;
            if ($settings && $settings->filled_auto_invite_time && $settings->use_own_timezone) {
                $settings->timezone_offset_days = 0;
                $settings->auto_invite_time = $settings->filled_auto_invite_time;
                $settings->save(true);
            } elseif ($settings && $settings->filled_auto_invite_time) {
                $user->fillTimezoneData();
            }

            if ($user->hasCurrentTeamRole(Roles::COACH) ||
                $user->hasCurrentTeamRole(Roles::ADMIN) ||
                $user->hasCurrentTeamRole(Roles::PHYSIOLOGIST) ||
                $user->hasCurrentTeamRole('tracker')
            ) {
                rescue(function () {
                    if (current_team()->subscribed()) {
                        current_team()->subscription()->updateQuantity(current_team()->users()->isBillableUser()->count());
                    }
                });
            }
        });
    }

    public function getSettings($team = null)
    {
        if (is_null($team)) {
            $team = current_team();
        }

        return Settings::find($this, $team);
    }

    public function fillTimezoneData()
    {
        $settings = $this->getSettings();
        $inviteTime = Carbon::now()->createFromTimeString($settings->filled_auto_invite_time,
            ($settings->timezone ?: 'Europe/Amsterdam'));


        $ignoredTimezone = Carbon::parse($inviteTime, 'Europe/Amsterdam')->shiftTimezone('Europe/Amsterdam');
        $respectedTimezone = (new Carbon($inviteTime, $inviteTime->timezone))->setTimezone('Europe/Amsterdam');

        $ignoredDay = (new Carbon($ignoredTimezone))->startOfDay();
        $respectedDay = (new Carbon($respectedTimezone))->startOfDay();

        $settings->timezone_offset_days = $respectedDay->diffInDays($ignoredDay, false);
        $settings->auto_invite_time = $inviteTime->setTimezone('Europe/Amsterdam')->format('H:i:s');
        $settings->save(true);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function coach($teamId = null): HasOneThrough
    {
        if (is_null($teamId)) {
            $teamId = current_team()->id;
        }

        return $this->hasOneThrough(User::class, CoachUser::class, 'user_id', 'id', 'id', 'coach_id')
            ->where('team_id', $teamId);
    }

    public function consents(): BelongsToMany
    {
        return $this->belongsToMany(Consent::class);
    }

    public function customerSchedules(): HasMany
    {
        return $this->hasMany(CustomerSchedule::class);
    }

    public function dashboardStatistics()
    {
        return [
            $this->getOverallScore(),
            $this->listWeekReports(),
            $this->listMonthReports(),
            $this->getLastMonthsGraphData(),
            $this->getGrowthDetails(),
        ];
    }

    public function getOverallScore()
    {
        return round($this->scores_monthly()->where(DB::raw('concat(year,month)'), '!=',
            Date::now()->format('Yn'))->get()
            ->groupBy(function ($scoreMonthly) {
                return $scoreMonthly->year.$scoreMonthly->month;
            })->map(function ($grouped) {
                return $grouped->average('score');
            })->average());
    }

    public function scores_monthly(): HasMany
    {
        return $this->hasMany(MonthlyScore::class);
    }

    public function listWeekReports()
    {
        return $this->scores_weekly()
            ->distinct()
            ->orderByDesc('year')
            ->orderByDesc('week')
            ->get(['week', 'year'])
            ->map(function ($item) {
                return [
                    'week' => $item->week,
                    'year' => $item->year,
                ];
            });
    }

    public function scores_weekly(): HasMany
    {
        return $this->hasMany(WeeklyScore::class);
    }

    public function listMonthReports()
    {
        return $this->scores_monthly()
            ->distinct()
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get(['month', 'year'])
            ->map(function ($item) {
                return \Date::createFromDate($item->year, $item->month, 1);
            });
    }

    public function listDayReports($customer)
    {
        return $dates = $this->answers()
            ->tracklist()
            ->addSelect([DB::raw('count(id) as count, user_id, min(created_at) as created_at'), 'date'])
            ->groupBy(['date', 'user_id', 'scope'])
            ->orderByDesc('date')
            ->get(['count', 'date', 'created_at']);

        $dates->map(function ($model) use ($customer) {
            $model->score = round($customer->scores_daily()
                ->where('date', $model->date)
                ->avg('score'));

            return $model;
        });
    }

    public function verbatimReports($customer)
    {
        $verbatimIds = $customer->questions()
            ->tracklist()
            ->get()
            ->filter(fn($q) => $q->type === 'Verbatim')
            ->pluck('id');

        return $verbatims = $customer->answers()
            ->whereIn('question_id', $verbatimIds)
            ->orderByDesc('date')
            ->get();

        $verbatims->map(function ($model) use ($customer) {
            $model->score = round($customer->scores_daily()
                ->where('date', $model->date)
                ->avg('score'));

            return $model;
        });
    }
    
    public function getLastMonthsGraphData()
    {
        return $this->scores_monthly()
            ->where(function ($query) {
                foreach (range(1, 12) as $minus) {
                    $query->orWhere(function ($query) use ($minus) {
                        $date = Date::now()->subMonths($minus);
                        $query->where('year', $date->year)->where('month', $date->month);
                    });
                }
            })->get()
            ->groupBy(function ($scoresMonthly) {
                return $scoresMonthly->year.$scoresMonthly->month;
            })->map(function ($grouped) {
                return [
                    'date'  => Date::create($grouped->first()->year, $grouped->first()->month, 1)->startOfDay(),
                    'score' => round($grouped->average('score')),
                ];
            })->sortBy('date');
    }

    public function getGrowthDetails()
    {
        $questions = $this->questions()->tracklist()->get();

        $scores = $this->scores_weekly()->whereIn('question_id', $questions->pluck('id')->toArray())
            ->where('week', '!=', Carbon::now()->format('W'))
            ->addSelect('*', DB::raw('((year * 100) + week) as yw, year, week'))
            ->having('yw', '>=', Carbon::now()->subWeeks(52 / 2)->format('oW'))
            ->orderBy('yw', 'asc')
            ->get()
            ->map(function ($q) {
                $colors = ['#ff4757', '#ffa502', '#2ed573'];
                $ed = $q->extra_data;
                if (!$ed['zero'] || $ed['zero'] < $ed['target']) {
                    if ($ed['value'] >= $ed['target']) {
                        $q->color = $colors[2];
                    } elseif ($ed['value'] > $ed['start']) {
                        $q->color = $colors[1];
                    } else {
                        $q->color = $colors[0];
                    }
                } else {
                    if ($ed['value'] <= $ed['target']) {
                        $q->color = $colors[2];
                    } elseif ($ed['value'] < $ed['start']) {
                        $q->color = $colors[1];
                    } else {
                        $q->color = $colors[0];
                    }
                }

                return $q;
            })
            ->groupBy('question_id')
            ->filter(function ($items) {
                return $items->count() >= 2;
            });

        foreach ($questions as $question) {
            $question->scores = $scores->get($question->id);
        }

        return $questions->filter(function (Question $question) {
            return !empty($question->scores);
        })->values();
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->sorted();
    }

    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class);
    }

    public function getGravatarAttribute()
    {
        return Gravatar::fallback('/img/default-avatar.png')->get($this->email);
    }

    public function getLastWeeksGraphData($maxRecords = 10, $untilWeekString = null, $movingAverageWeeks = 4)
    {
        $weeks = $this->scores_weekly()
            ->where('week', '!=', Carbon::now()->format('W'))
            ->groupBy(['year', 'week'])
            ->selectRaw('((year * 100) + week) as yw, year, week')
            ->orderBy('yw', 'desc');

        if ($untilWeekString) {
            $weeks = $weeks->where(DB::raw('CONCAT(year,week)'), '<=', $untilWeekString);
        }

        if ($movingAverageWeeks) {
            $weeks = $weeks->take($maxRecords + $movingAverageWeeks);
        } else {
            $weeks = $weeks->take($maxRecords);
        }

        $weeks = $weeks->get();

        if (!$weeks->count()) {
            return collect();
        }
        $scoresWeekly = $this->scores_weekly()
            ->where('week', '!=', Carbon::now()->format('W'))
            ->where(DB::raw('((year * 100) + week)'), '>=', $weeks->last()->yw)
            ->where(DB::raw('((year * 100) + week)'), '<=', $weeks->first()->yw)
            ->get()
            ->groupBy(function ($scoresWeekly) {
                return $scoresWeekly->year.$scoresWeekly->week;
            })->map(function ($grouped, $index) use ($movingAverageWeeks) {
                $noWeekResponses = $grouped->max(function ($item) {
                    if ($item) {
                        return count($item->extra_data['answer_ids']);
                    }

                    return 0;
                });

                return collect([
                    'date'            => Date::now()->setISODate($grouped->first()->year, $grouped->first()->week),
                    'score'           => round($grouped->average('score')),
                    'noWeekResponses' => $noWeekResponses,
                ]);
            })
            ->sortBy('date');

        if ($movingAverageWeeks) {
            $scoresWeekly->each(function ($week, $i) use ($scoresWeekly, $movingAverageWeeks) {
                $last4 = $scoresWeekly->filter(function ($score) use ($week) {
                    return $score->get('date') <= $week->get('date');
                })->reverse()->take($movingAverageWeeks)->reverse();

                $week->put('movingAverage', round($last4->avg('score')));
                $week->put('movingAverageComplete', count($last4) == $movingAverageWeeks);
            });
        }

        return $scoresWeekly->reverse()->take($maxRecords)->reverse();
    }

    public function getMaskEmailAttribute()
    {
        $split = Str::of($this->email)->explode('@');
        $before = Str::mask($this->email, '*', -Str::length($this->email) + 3,
            Str::length($split[0]) - 3);

        return Str::mask($before, '*', Str::length($split[0]) + 1, Str::length($split[1]) - 6);
    }

    public function getNameAttribute(): string
    {
        return sprintf("%s %s", $this->first_name, $this->last_name);
    }

    public function getSettingsAttribute()
    {
        if ($currentTeam = current_team()) {
            return Settings::find($this, $currentTeam);
        }
        return null;
    }

    public function getTrackList($date = null): Collection
    {
        if (!$date) {
            $date = now();
        }

        return $this->questions()->tracklist()->get()
            ->filter(function ($question) use ($date) {
                if ($date->isWeekend() && $question->options->get('excludeWeekends', 0) == 1) {
                    return false;
                }

                return true;
            })
            ->map(function (Question $model) {
                return $model->present();
            });
    }

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function invites(): HasMany
    {
        return $this->hasMany(Invite::class);
    }

    public function isVerified()
    {
        return $this->verifyUser()->count() === 0;
    }

    public function verifyUser(): HasOne
    {
        return $this->hasOne(VerifyUser::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function onTimeout()
    {
        return $this->timeouts()->between(Date::now(), Date::now())->count() > 0;
    }

    public function timeouts(): HasMany
    {
        return $this->hasMany(Timeout::class);
    }

    public function physiologist($teamId = null): HasOneThrough
    {
        if (is_null($teamId)) {
            $teamId = current_team()->id;
        }

        return $this->hasOneThrough(User::class, PhysiologistUser::class, 'user_id', 'id', 'id', 'physiologist_id')
            ->where('team_id', $teamId);
    }

    public function programMilestones(): HasMany
    {
        return $this->hasMany(ProgramMilestone::class)
            ->sorted();
    }

    public function receiveInvitesDuringWeekends(Team $team = null)
    {
        return $this->getSettings($team)->days_per_week == 7;
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function scopeIsAdministrator(Builder $query): Builder
    {
        return $this->scopeHavingRole($query, Roles::ADMIN);
    }

    /**
     * @param  Builder  $query
     * @param  array|string  $role
     * @param  string  $boolean
     * @return Builder
     */
    public function scopeHavingRole(Builder $query, $role, string $boolean = 'and'): Builder
    {
        $roles = Arr::wrap($role);

        return $query->whereJsonContains('team_user.roles', $roles, $boolean);
    }

    public function scopeIsArchivedCustomer(Builder $query): Builder
    {
        return $this->scopeHavingRole($query, 'client_archived');
    }

    public function scopeIsBillableUser(Builder $query)
    {
        return $query->where(function (Builder $query) {
            $query->whereJsonContains('team_user.roles', Arr::wrap(Roles::COACH))
                ->orWhereJsonContains('team_user.roles', Arr::wrap(Roles::PHYSIOLOGIST))
                ->orWhereJsonContains('team_user.roles', Arr::wrap(Roles::ADMIN))
                ->orWhereJsonContains('team_user.roles', Arr::wrap('tracker'));
        })
            ->where('exclude_from_billing_count', 0);
    }

    public function scopeIsCoach(Builder $query): Builder
    {
        return $this->scopeHavingRole($query, Roles::COACH);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeIsCustomer(Builder $query): Builder
    {
        return $this->scopeHavingRole($query, Roles::CUSTOMER);
    }

    public function scopeIsPhysiologists(Builder $query): Builder
    {
        return $this->scopeHavingRole($query, Roles::PHYSIOLOGIST);
    }

    public function scopeIsTracker(Builder $query): Builder
    {
        return $this->scopeHavingRole($query, 'tracker');
    }

    /**
     * @param  Builder  $query
     * @param  array|string  $role
     * @param  string  $boolean
     * @return Builder
     */
    public function scopeNotHavingRole(Builder $query, $role, string $boolean = 'and'): Builder
    {
        $roles = Arr::wrap($role);

        return $query->whereHas('teams',
            fn(Builder $subQuery) => $subQuery->whereJsonDoesntContain('roles', $roles, $boolean));
    }

    public function scopePartOfCoach(Builder $query): Builder
    {
        return $query->whereHas('coach', function (Builder $query) {
            $query->where('coach_id', Auth::user()->id);
        });
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopePartOfCurrentTeam(Builder $query): Builder
    {
        return $query->whereHas('teams', function (Builder $query) {
            $query->where('team_id', current_team()?->id);
        });
    }

    public function scopePartOfPhysiologist(Builder $query): Builder
    {
        return $query->whereHas('physiologist', function (Builder $query) {
            $query->where('physiologist_id', Auth::user()->id);
        });
    }

    public function scopeSearch(Builder $query, $search): Builder
    {
        return $query
            ->where(DB::raw('CONCAT(first_name, " ", last_name)'), 'like', '%'.$search.'%')
            ->orWhere('email', 'like', '%'.$search.'%');
    }

    public function scopeWithName(Builder $builder)
    {
        $builder->addSelect(DB::raw('CONCAT(first_name, " ", last_name) as name'));
    }

    public function scores_daily(): HasMany
    {
        return $this->hasMany(DailyScore::class);
    }

    public function supporters(): HasMany
    {
        return $this->hasMany(Supporter::class);
    }

    public function tests(): HasMany
    {
        return $this->hasMany(Test::class);
    }
}
