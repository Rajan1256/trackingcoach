<?php

namespace App\Models;

use App\Modules\Team\Contracts\TeamAware;
use App\Modules\Team\Traits\TeamAwareTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Goal extends Model implements TeamAware
{
    use HasFactory;
    use TeamAwareTrait;
    use HasTranslations;

    public $translatable = ['name'];

    protected $fillable = [
        'scope', 'user_id', 'team_id', 'name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
