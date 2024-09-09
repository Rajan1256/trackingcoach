<?php

namespace App\Http\Controllers;

use App\Http\Requests\Consents\StoreConsentRequest;
use App\Http\Requests\Consents\UpdateConsentRequest;
use App\Models\Consent;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use function current_team;
use function redirect;

class ConsentsController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(Request $request)
    {
        return view('consents.create');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request  $request
     * @param  Consent  $consent
     * @return Response
     */
    public function destroy(Request $request, Consent $consent)
    {
        $consent->delete();

        session()->flash('message', __('Consent successfully deleted'));

        return redirect()->to(route('consents.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Request  $request
     * @param  Consent  $consent
     * @return Response
     */
    public function edit(Request $request, Consent $consent)
    {
        return view('consents.edit', [
            'consent' => $consent,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        $consents = Consent::query();

        return view('consents.index', [
            'consents' => $consents->paginate((25)),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  Request  $request
     * @param  Consent  $consent
     * @return Application|Factory|View
     */
    public function show(Request $request, Consent $consent)
    {
        $users = $consent->users()->paginate(25);

        return view('consents.show', [
            'consent' => $consent,
            'users'   => $users,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(StoreConsentRequest $request, Consent $consent)
    {
        $consent = Consent::create($request->merge([
            'team_id' => ($request->get('global')) ? null : current_team()->id,
        ])->toArray());

        if ($request->hasFile('file')) {
            $consent->addMedia($request->allFiles()['file'])
                ->toMediaCollection('pdf');
        }

        session()->flash('message', __('Successfully created the consent'));

        return redirect()->to(route('consents.show', $consent));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Consent  $consent
     * @return Response
     */
    public function update(UpdateConsentRequest $request, Consent $consent)
    {
        $request->merge(['team_id' => ($request->get('global')) ? null : current_team()->id,]);

        $consent->update($request->all());

        if ($request->hasFile('file')) {
            $consent->addMedia($request->allFiles()['file'])
                ->toMediaCollection('pdf');
        }

        session()->flash('message', __('Consent successfully updated'));

        return redirect()->to(route('consents.show', $consent));
    }
}
