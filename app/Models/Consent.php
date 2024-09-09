<?php

namespace App\Models;

use App\Modules\Team\Contracts\TeamAware;
use App\Modules\Team\Traits\TeamAwareTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Consent extends Model implements HasMedia, TeamAware
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;
    use TeamAwareTrait;

    protected $fillable = [
        'team_id', 'name', 'description', 'confirmation_text', 'activated_at',
    ];

    protected $casts = [
        'activated_at' => 'date',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function isActive(): bool
    {
        return !!$this->activated_at;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('pdf')
            ->singleFile();
    }

    public function scopeActive(Builder $query)
    {
        return $query->whereNotNull('activated_at');
    }

    public function scopeNotAcceptedBy(Builder $query, User $user)
    {
        return $query->whereDoesntHave('users', function (Builder $query) use ($user) {
            $query->where('user_id', $user->id);
        });
    }
}
