<?php

namespace App\Models;

use App\Modules\Team\Contracts\TeamAware;
use App\Modules\Team\Traits\TeamAwareTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Supporter extends Model implements TeamAware
{
    use HasFactory, SoftDeletes;
    use TeamAwareTrait;
    use Notifiable;

    protected $fillable = [
        'team_id', 'first_name', 'last_name', 'relationship', 'email', 'phone', 'locale', 'notification_method',
        'personal_note',
    ];

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

    public function getNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = encrypt($value);
    }

    public function getPhoneAttribute($value)
    {
        return decrypt($value);
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = encrypt($value);
    }

    public function getEmailAttribute($value)
    {
        return decrypt($value);
    }

    public function canReceiveReminderForReview(Review $review)
    {
        if (!$review->canSendReminders()) {
            return false;
        }

        if ($review->answers()->where('supporter_id', '=', $this->id)->count()) {
            return false;
        }

        $reviewInvitations = $this->reviewInvitations()->where('review_id', '=', $review->id)->orderBy('created_at',
            'asc')->get();

        // Wanneer er geen uitnodigingen zijn, dan kan er ook geen reminder gestuurd worden
        if ($reviewInvitations->count() == 0) {
            return false;
        }

        if ($reviewInvitations->count() > 2) {
            return false;
        }

        // wanneer de laatst gestuurde invite minder dan 3 dagen geleden is
        if ($reviewInvitations->last()->created_at > Carbon::now()->subDays(3)) {
            return false;
        }

        return true;
    }

    public function reviewInvitations()
    {
        return $this->hasMany(ReviewInvitation::class);
    }
}
