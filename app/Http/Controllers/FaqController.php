<?php

namespace App\Http\Controllers;

use App\Http\Requests\Faq\StoreFaqRequest;
use App\Http\Requests\Faq\UpdateFaqRequest;
use App\Models\Faq;
use Illuminate\Http\Request;

use function redirect;

class FaqController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Faq::class, 'faq');
    }

    public function index(Request $request)
    {
        $faq = Faq::all();

        return view('faq.manage.index', [
            'faq' => $faq,
        ]);
    }

    public function create(Request $request)
    {
        return view('faq.manage.create');
    }

    public function store(StoreFaqRequest $request)
    {
        Faq::create([
            'question' => $request->get('question'),
            'answer'   => $request->get('answer'),
        ]);

        session()->flash('message', __('FAQ successfully created'));

        return redirect()->to(route('faq.manage.index'));
    }

    public function edit(Request $request, Faq $faq)
    {
        return view('faq.manage.edit', [
            'faq' => $faq,
        ]);
    }

    public function update(UpdateFaqRequest $request, Faq $faq)
    {
        $faq->update([
            'question' => $request->get('question'),
            'answer'   => $request->get('answer'),
        ]);

        session()->flash('message', __('FAQ successfully updated'));

        return redirect()->back();
    }

    public function destroy(Request $request, Faq $faq)
    {
        $faq->delete();

        session()->flash('message', __('FAQ successfully deleted'));

        return redirect()->to(route('faq.manage.index'));
    }
}
