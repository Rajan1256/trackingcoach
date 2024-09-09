<?php

namespace App\Models;

use App\Modules\Team\Contracts\NotTeamAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class QuestionHistory extends Model implements NotTeamAware
{
    use HasFactory;
    use HasTranslations;

    public array $translatable = ['name', 'description'];

    protected $casts = ['options' => 'collection', 'starts_at' => 'timestamp'];

    protected $fillable = ['question_id', 'author_id', 'name', 'description', 'type', 'options', 'starts_at'];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * @return BelongsTo
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class)->withTrashed();
    }

}
