<?php

namespace App\Models;

use App\Modules\Team\Contracts\NotTeamAware;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model implements NotTeamAware
{
    use HasFactory;

    protected $fillable = [
        'question', 'answer',
    ];

    public function scopeSearch(Builder $query, $search): Builder
    {
        return $query
            ->where('question', 'like', '%'.$search.'%')
            ->orWhere('answer', 'like', '%'.$search.'%');
    }
}
