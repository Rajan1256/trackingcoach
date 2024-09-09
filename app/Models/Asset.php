<?php

namespace App\Models;

use App\Modules\Team\Contracts\TeamAware;
use App\Modules\Team\Traits\TeamAwareTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Asset extends Model implements HasMedia, TeamAware
{
    use HasFactory;
    use InteractsWithMedia;
    use TeamAwareTrait;

    protected $fillable = [
        'team_id', 'user_id', 'folder_id', 'author_id', 'coach_can_access', 'tracker_can_access',
        'physiologist_can_access', 'user_can_access',
    ];

    protected $casts = [
        'coach_can_access'        => 'boolean',
        'tracker_can_access'      => 'boolean',
        'physiologist_can_access' => 'boolean',
        'client_can_access'       => 'boolean',
    ];

    public function scopeForFolder(Builder $query, ?AssetFolder $folder)
    {
        return $query->where('folder_id', $folder?->id);
    }

    public function scopeForCustomer(Builder $query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeCoachCanAccess(Builder $query)
    {
        return $query->where('coach_can_access', true);
    }

    public function scopeTrackerCanAccess(Builder $query)
    {
        return $query->where('tracker_can_access', true);
    }

    public function scopePhysiologistCanAccess(Builder $query)
    {
        return $query->where('physiologist_can_access', true);
    }

    public function scopeUserCanAccess(Builder $query)
    {
        return $query->where('user_can_access', true);
    }

    public function file()
    {
        return $this->getFirstMedia('assets');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('assets')
            ->singleFile();
    }

    public function getIcon()
    {
        switch (optional($this->getFirstMedia('assets'))->mime_type) {
            case 'video/x-msvideo':
                return asset('img/filemanager/ext/avi.svg');
            case 'image/bmp':
                return asset('img/filemanager/ext/bmp.svg');
            case 'application/msword':
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                return asset('img/filemanager/ext/doc.svg');
            case 'application/postscript':
                return asset('img/filemanager/ext/eps.svg');
            case 'image/gif':
                return asset('img/filemanager/ext/gif.svg');
            case 'image/jpeg':
                return asset('img/filemanager/ext/jpg.svg');
            case 'video/quicktime':
                return asset('img/filemanager/ext/mov.svg');
            case 'video/mpeg':
            case 'video/mp4':
                return asset('img/filemanager/ext/mpg.svg');
            case 'application/pdf':
                return asset('img/filemanager/ext/pdf.svg');
            case 'image/png':
                return asset('img/filemanager/ext/png.svg');
            case 'application/vnd.ms-powerpoint':
            case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
                return asset('img/filemanager/ext/ppt.svg');
            case 'image/svg+xml':
                return asset('img/filemanager/ext/svg.svg');
            case 'text/plain':
                return asset('img/filemanager/ext/txt.svg');
            case 'application/vnd.ms-excel':
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                return asset('img/filemanager/ext/xls.svg');
            case 'application/zip':
                return asset('img/filemanager/ext/zip.svg');
        }

        return asset('img/filemanager/ext/unknown.svg');
    }
}
