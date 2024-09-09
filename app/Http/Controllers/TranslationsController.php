<?php

namespace App\Http\Controllers;

use App\Enum\Roles;
use App\Http\Requests\Translations\UpdateTranslationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\TranslationLoader\LanguageLine;

class TranslationsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!current_team()->isRoot(), 403);
        abort_if(!Auth::user()->hasCurrentTeamRole([Roles::ADMIN]), 403);

        $lines = LanguageLine::orderBy('key')
            ->paginate(25);

        return view('translations.index', [
            'lines' => $lines,
        ]);
    }

    public function edit(Request $request, LanguageLine $line)
    {
        abort_if(!current_team()->isRoot(), 403);
        abort_if(!Auth::user()->hasCurrentTeamRole([Roles::ADMIN]), 403);

        return view('translations.edit', [
            'line' => $line,
        ]);
    }

    public function update(UpdateTranslationRequest $request, LanguageLine $line)
    {
        abort_if(!current_team()->isRoot(), 403);
        abort_if(!Auth::user()->hasCurrentTeamRole([Roles::ADMIN]), 403);

        $line->update([
            'text' => $request->get('text'),
        ]);

        session()->flash('message', __('Successfully updated translation'));

        return redirect()->to(route('translations.index'));
    }
}
