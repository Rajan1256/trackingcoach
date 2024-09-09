<?php

namespace App\Models;

use App\Modules\Team\Contracts\TeamAware;
use App\Modules\Team\Traits\TeamAwareTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interview extends Model implements TeamAware
{
    use HasFactory;
    use TeamAwareTrait;

    protected $casts = [
        'date'     => 'date',
        'continue' => 'array',
        'start'    => 'array',
        'stop'     => 'array',
        'best'     => 'array',
        'worst'    => 'array',
    ];

    protected $fillable = [
        'date', 'continue', 'start', 'stop', 'best', 'worst', 'team_id', 'user_id', 'author_id'
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
}
