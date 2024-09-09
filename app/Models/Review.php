<?php

namespace App\Models;

use App\Modules\Team\Contracts\TeamAware;
use App\Modules\Team\Traits\TeamAwareTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model implements TeamAware
{
    use HasFactory;
    use TeamAwareTrait;

    protected $casts = [
        'opens_at'   => 'date',
        'closes_at'  => 'date',
        'visible_at' => 'date',
    ];

    protected $fillable = [
        'team_id', 'name', 'user_id', 'closes_at', 'opens_at', 'visible_at',
    ];

    protected static function booted()
    {
        static::updated(function (Review $review) {
            $review->reviewInvitations()->each(function (ReviewInvitation $reviewInvitation) use ($review) {
                /** @var Supporter $supporter */
                $supporter = $reviewInvitation->supporter;
                if ($supporter && $review->answers->where('supporter_id', $supporter->id)->count() === 0) {
                    /** @var Invite $invite */
                    $invite = $reviewInvitation->invite;
                    $invite->expires_at = $review->closes_at->endOfDay();
                    $invite->save();
                }
            });
        });
    }

    public function reviewInvitations()
    {
        return $this->hasMany(ReviewInvitation::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class)
            ->review();
    }

    public function canSendReminders()
    {
        return $this->canSendInvites();
    }

    public function canSendInvites()
    {
        return $this->opens_at < Carbon::now() && $this->closes_at > Carbon::now()->addDay();
    }

    public function scopeRemindable($query)
    {
        return $this->scopeInvitable($query);
    }

    public function scopeInvitable($query)
    {
        return $query->where('opens_at', '<', Carbon::now())
            ->where('closes_at', '>', Carbon::now()->addDay());
    }

    public function scopeVisible($query)
    {
        return $query->where('visible_at', '<', Carbon::now());
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
