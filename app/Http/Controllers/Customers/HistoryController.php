<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Models\User;
use DB;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request, User $customer)
    {
        $dates = $customer
            ->answers()
            ->tracklist()
            ->addSelect([DB::raw('count(id) as count, user_id, min(created_at) as created_at'), 'date'])
            ->groupBy(['date', 'user_id', 'scope'])
            ->orderByDesc('date')
            ->paginate(25, ['count', 'date', 'created_at']);

        $dates->map(function ($model) use ($customer) {
            $model->score = round($customer->scores_daily()
                ->where('date', $model->date)
                ->avg('score'));

            return $model;
        });

        return view('customers.history.index', [
            'customer' => $customer,
            'dates'    => $dates,
        ]);
    }

    public function show(Request $request, User $customer, $date)
    {
        $answers = $customer->answers()
            ->tracklist()
            ->where('date', $date)
            ->with('questionHistory')
            ->get()
            ->map(function ($model) {
                $className = '\\App\\Questions\\'.$model->question->type;
                $model->helper = (new $className($model->questionHistory));

                return $model;
            });

        return view('customers.history.show', [
            'date'     => $date,
            'customer' => $customer,
            'answers'  => $answers,
        ]);
    }
}
