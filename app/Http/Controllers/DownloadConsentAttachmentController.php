<?php

namespace App\Http\Controllers;

use App\Enum\Roles;
use App\Models\Consent;
use App\Modules\Team\Scopes\TeamAwareScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DownloadConsentAttachmentController extends Controller
{
    public function __invoke(Request $request, $consent, Media $media)
    {
        $consents = Consent::active()
            ->withoutGlobalScope(TeamAwareScope::class);

        if (current_team()->owner->id === Auth::user()->id ||
            Auth::user()->hasCurrentTeamRole(Roles::ADMIN)) {
            $consents->where(function (Builder $query) {
                $query->where('team_id', current_team()?->id)
                    ->orWhereNull('team_id');
            });
        } else {
            $consents->where('team_id', current_team()?->id);
        }
        $consent = $consents->where('id', $consent)->firstOrFail();

        if ($consent->getFirstMedia('pdf')->id !== $media->id) {
            abort(403);
        }
        return Storage::disk($media->getDiskDriverName())
            ->download($media->getPath(), $media->file_name);
    }
}
