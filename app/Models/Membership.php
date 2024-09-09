<?php

namespace App\Models;

use App\Modules\Team\Contracts\NotTeamAware;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Membership extends Pivot implements NotTeamAware
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    protected $table = 'team_user';

    protected $casts = [
        'data'  => 'encrypted:collection',
        'roles' => 'collection',
    ];

    protected $fillable = [
        'data', 'company_name', 'team_id', 'user_id', 'auto_invite_time', 'roles', 'paired_app_token', 'days_per_week',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
