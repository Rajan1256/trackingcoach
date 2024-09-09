<?php

namespace App\Models;

use App\Modules\Team\Contracts\TeamAware;
use App\Modules\Team\Traits\TeamAwareTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

use function array_key_exists;
use function unserialize;

class Test extends Model implements TeamAware, HasMedia
{
    use HasFactory;
    use TeamAwareTrait;
    use InteractsWithMedia;

    protected $unserialized;

    protected $fillable = [
        'user_id', 'team_id', 'author_id', 'type', 'data', 'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function getAttribute($key)
    {
        if (!$this->unserialized && array_key_exists('data', $this->attributes)) {
            $this->unserialized = unserialize($this->attributes['data']);
        }

        if ($this->unserialized && property_exists($this->unserialized, $key)) {
            return $this->unserialized->$key;
        }

        return parent::getAttribute($key);
    }

    public function setDataAttribute($value)
    {
        $this->attributes['data'] = serialize($value);
    }

    public function getHelperAttribute()
    {
        return new $this->attributes['type']();
    }

    public function getDataAttribute($value)
    {
        return unserialize($value);
    }

    public function registerMediaConversions(Media $media = null): void
    {
    }

    public function registerMediaCollections(): void
    {
    }

    public function registerAllMediaConversions(): void
    {
    }
}
