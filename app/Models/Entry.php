<?php

namespace App\Models;

use App\Modules\Team\Contracts\TeamAware;
use App\Modules\Team\Traits\TeamAwareTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entry extends Model implements TeamAware
{
    use HasFactory;
    use TeamAwareTrait;

    protected $fillable = ['team_id', 'scope', 'user_id', 'invite_id', 'date'];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invite(): BelongsTo
    {
        return $this->belongsTo(Invite::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class, 'date', 'date')
            ->where('user_id', $this->user_id)
            ->where('scope', 'tracklist');
    }

    /**
     * Scope a query to only tracklist questions.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeTracklist($query)
    {
        return $query->where('scope', 'tracklist');
    }

    /**
     * Scope a query to only tracklist questions.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeReview($query)
    {
        return $query->where('scope', 'review');
    }
}
