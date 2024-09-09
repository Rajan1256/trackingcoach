<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Reports\WeekReport;
use Illuminate\Http\Request;

class WeeklyReportController extends Controller
{
    public function __invoke(Request $request, User $customer, $year, $month)
    {
        $data = (new WeekReport($customer, $year, $month))->getData();
        return view('scores.weekly', [
            'data' => $data,
        ]);
    }
}
