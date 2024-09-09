<?php

namespace App\Models;

use App\Modules\Team\Contracts\TeamAware;
use App\Modules\Team\Traits\TeamAwareTrait;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Rutorika\Sortable\SortableTrait;

/**
 * @property string $scope
 * @property Collection $options
 */
class Question extends Model implements TeamAware
{
    use HasFactory, SoftDeletes, SortableTrait;
    use TeamAwareTrait;

    public $versions = [];

    protected $fillable = [
        'team_id', 'scope', 'name', 'description', 'type', 'options', 'author_id', 'starts_at', 'parent_id', 'position',
        'user_id',
    ];

    protected $historyAttributes = [
        'name', 'description', 'type', 'options', 'author_id', 'starts_at',
    ];

    /**
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeTracklist($query)
    {
        return $query->where('scope', 'tracklist');
    }

    public function scopeTracklistTemplate($query)
    {
        return $query->where('scope', 'tracklistTemplate');
    }

    public function getAttribute($key)
    {
        if (in_array($key, $this->historyAttributes)) {
            return $this->getLatestVersion() ? $this->getLatestVersion()->$key : null;
        }

        return parent::getAttribute($key);
    }

    public function getLatestVersion()
    {
        if (!$this->latest) {
            if ($this->relationLoaded('histories')) {
                $latest = $this->histories->sortByDesc('created_at')->first();
            } else {
                $latest = $this->histories()->where('question_id', $this->id)->orderBy('created_at', 'desc')->first();
            }
            $this->latest = $latest->id;
            $this->versions[$latest->id] = $latest;
        }

        return $this->versions[$this->latest];
    }

    /**
     * @return HasMany
     */
    public function histories(): HasMany
    {
        return $this->hasMany(QuestionHistory::class);
    }

    public function scopeReview($query)
    {
        return $query->where('scope', 'review');
    }

    public function scopeReviewTemplate($query)
    {
        return $query->where('scope', 'reviewTemplate');
    }

    public function present()
    {
        if (!$this->getLatestVersion()) {
            throw new Exception("No version found for question with id: {$this->id}");
        }
        $type = $this->getLatestVersion()->type;
        $className = '\\App\\Questions\\'.$type;

        if (!class_exists($className)) {
            throw new Exception("Class with name {$className} does not exist on question: {$this->id}");
        }

        return new $className($this);
    }
}
