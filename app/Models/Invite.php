<?php

namespace App\Models;

use App\Modules\Team\Contracts\TeamAware;
use App\Modules\Team\Traits\TeamAwareTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

use function current_team;

class Invite extends Model implements TeamAware
{
    use HasFactory;
    use TeamAwareTrait;

    protected $casts = ['options' => 'collection'];

    protected $dates = ['expires_at'];

    protected $fillable = [
        'team_id', 'user_id', 'code', 'type', 'options', 'delivery_status', 'expires_at',
    ];

    public static function newMonthlyReportInvite(User $customer, $year, $month)
    {
        $count = $customer->scores_monthly()->where('year', $year)->where('month', $month)->count();

        if ($count == 0) {
            return;
        }

        $monthlyReportInvites = (new static)->where('user_id', $customer->id)->where('type', 'monthlyreport')->get();
        $existing = $monthlyReportInvites->filter(function (self $invite) use ($year, $month) {
            return intval($invite->options->get('year')) == intval($year)
                && intval($invite->options->get('month')) == intval($month);
        });

        $count = count($existing);

        if ($count > 0) {
            return $existing->first();
        }

        return $customer->invites()->create([
            'team_id'    => current_team()->id,
            'code'       => (new static)->generateRand(),
            'options'    => collect([
                'year'  => intval($year),
                'month' => intval($month),
            ]),
            'type'       => 'monthlyreport',
            'expires_at' => Carbon::now()->addMonth()->endOfDay(),
        ]);
    }

    private function generateRand()
    {
        $rand = Str::random(6);

        $find = (new static)->where('code', $rand)
            ->where('expires_at', '>', Carbon::now()->subMonths(3))
            ->count();

        if ($find) {
            return $this->generateRand();
        }

        return $rand;
    }

    public static function newReviewInvite(User $customer, Review $review, Supporter $supporter)
    {
        $invite = $customer->invites()->create([
            'team_id'    => current_team()->id,
            'code'       => (new static)->generateRand(),
            'options'    => collect([
                'review'    => $review->id,
                'supporter' => $supporter->id,
            ]),
            'type'       => 'supporter_review_invite',
            'expires_at' => $review->closes_at->endOfDay(),
        ]);

        return ReviewInvitation::create([
            'user_id'      => $customer->id,
            'review_id'    => $review->id,
            'supporter_id' => $supporter->id,
            'invite_id'    => $invite->id,
        ]);
    }

    public static function newReviewReminder(User $user, Review $review, Supporter $supporter)
    {
        /** @var ReviewInvitation $original */
        $original = $supporter->reviewInvitations()->where('review_id', '=', $review->id)->first();
        $invite = $original->invite;

        // extend invite period
        $invite->expires_at = $review->closes_at;
        $invite->save();

        return ReviewInvitation::create([
            'user_id'      => $user->id,
            'review_id'    => $review->id,
            'supporter_id' => $supporter->id,
            'invite_id'    => $original->invite_id,
        ]);
    }

    public static function newTracklistInvite(User $customer, Carbon $date = null, $team = null)
    {
        if (!$date) {
            $date = Carbon::now()->startOfDay();
        }

        if ($date->isWeekend() && !$customer->receiveInvitesDuringWeekends($team)) {
            return;
        }

        if ($customer->answers()->tracklist()->where('date', $date->format('Y-m-d'))->count() > 0) {
            return;
        }

        if (!count($customer->questions()->tracklist()->get())) {
            return;
        }

        // check if exists
        $tracklistInvites = (new static)->notExpired()->where('user_id', $customer->id)->where('type',
            'tracklist')->get();
        $count = $tracklistInvites->filter(function (self $invite) use ($date) {
            return $invite->options->get('date') == $date->format('Y-m-d') && $invite->delivery_status === 'sent';
        })->count();
        
        if ($count > 0) {
            return;
        }

        return $customer->invites()->create([
            'team_id'         => current_team()->id,
            'code'            => (new static)->generateRand(),
            'options'         => collect([
                'date' => $date->format('Y-m-d'),
            ]),
            'type'            => 'tracklist',
            'delivery_status' => 'new',
            'expires_at'      => Carbon::now()->addDay(),
        ]);
    }

    public static function newWeeklyReportInvite(User $customer, $year, $week)
    {
        $count = $customer->scores_weekly()->where('year', $year)->where('week', $week)->count();

        if ($count == 0) {
            return;
        }

        $weeklyReportInvites = (new static)->where('user_id', $customer->id)->where('type', 'weeklyreport')->get();
        $existing = $weeklyReportInvites->filter(function (self $invite) use ($year, $week) {
            return intval($invite->options->get('year')) == intval($year)
                && intval($invite->options->get('week')) == intval($week);
        });
        $count = count($existing);

        if ($count > 0) {
            return;
        }

        return $customer->invites()->create([
            'team_id'    => current_team()->id,
            'code'       => (new static)->generateRand(),
            'options'    => collect([
                'year' => intval($year),
                'week' => intval($week),
            ]),
            'type'       => 'weeklyreport',
            'expires_at' => Carbon::now()->addWeek()->endOfDay(),
        ]);
    }

    public function scopeNotExpired($query)
    {
        return $query->where('expires_at', '>', Carbon::now());
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
