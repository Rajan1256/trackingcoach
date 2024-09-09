<?php

namespace App\Models;

use App\Modules\Team\Contracts\TeamAware;
use App\Modules\Team\Traits\TeamAwareTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetFolder extends Model implements TeamAware
{
    use HasFactory;
    use TeamAwareTrait;

    protected $fillable = [
        'parent_id', 'user_id', 'team_id', 'name',
    ];

    public function scopeForCustomer(Builder $query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeForFolder(Builder $query, ?AssetFolder $folder)
    {
        return $query->where('parent_id', $folder?->id);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function deleteWithChildren()
    {
        foreach ($this->children as $child) {
            $child->deleteWithChildren();
        }

        $this->assets()->delete();
        $this->delete();
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'folder_id');
    }
}
