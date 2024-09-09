<?php

namespace App\Models;

use App\Modules\Team\Contracts\TeamAware;
use App\Modules\Team\Traits\TeamAwareTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Export extends Model implements TeamAware, HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use TeamAwareTrait;

    protected $casts = [
        'data' => 'array',
    ];

    protected $fillable = [
        'created_by', 'user_id', 'team_id', 'type', 'year', 'data', 'status',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFileAttribute()
    {
        return $this->getFirstMedia('exports');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('exports')
            ->singleFile();
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
