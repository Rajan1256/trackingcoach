<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notes\StoreNoteRequest;
use App\Http\Requests\Notes\UpdateNoteRequest;
use App\Models\Note;
use App\Models\User;
use Auth;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotesController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Note::class, 'note');
    }

    public function index(Request $request, User $customer)
    {
        $notes = $customer->notes()
            ->forUser()
            ->with('author')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customers.notes.index', [
            'customer' => $customer,
            'notes'    => $notes,
        ]);
    }

    public function create(Request $request, User $customer)
    {
        return view('customers.notes.create', [
            'customer' => $customer,
        ]);
    }

    public function store(StoreNoteRequest $request, User $customer)
    {
        Note::create([
            'body'          => $request->get('note'),
            'team_id'       => current_team()->id,
            'user_id'       => $customer->id,
            'author_id'     => Auth::user()->id,
            'authorization' => $request->get('authorization'),
        ]);

        session()->flash('message', 'Note succesfully saved');

        return redirect()->to(route('customers.notes.index', [
            'customer' => $customer,
        ]));
    }

    /**
     * Show the form for editing a specified note.
     *
     * @param  Request  $request
     * @param  User  $customer
     * @param  Note  $note
     * @return Factory|View
     */
    public function edit(Request $request, User $customer, Note $note)
    {
        return view('customers.notes.edit', [
            'customer' => $customer,
            'note'     => $note,
        ]);
    }

    /**
     * Update a note and return to the edit view.
     *
     * @param  Request  $request
     * @param  User  $customer
     * @param  Note  $note
     * @return RedirectResponse
     */
    public function update(UpdateNoteRequest $request, User $customer, Note $note)
    {
        $note->update([
            'body'          => $request->get('note'),
            'authorization' => $request->get('authorization'),
        ]);

        session()->flash('message', __('Note successfully updated.'));

        return redirect()->route('customers.notes.index', [
            'customer' => $customer,
            'note'     => $note,
        ]);
    }

    public function destroy(Request $request, User $customer, Note $note)
    {
        $note->delete();

        session()->flash('message', __('Note successfully deleted'));

        return redirect()->back();
    }
}
