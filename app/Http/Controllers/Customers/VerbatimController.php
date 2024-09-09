<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VerbatimController extends Controller
{
    public function index(Request $request, User $customer)
    {
        $verbatimIds = $customer->questions()
            ->tracklist()
            ->get()
            ->filter(fn($q) => $q->type === 'Verbatim')
            ->pluck('id');

        $verbatims = $customer->answers()
            ->whereIn('question_id', $verbatimIds)
            ->orderByDesc('date')
            ->paginate(50);

        $verbatims->map(function ($model) use ($customer) {
            $model->score = round($customer->scores_daily()
                ->where('date', $model->date)
                ->avg('score'));

            return $model;
        });

        return view('customers.verbatim.index', [
            'customer'  => $customer,
            'verbatims' => $verbatims,
        ]);
    }
}
