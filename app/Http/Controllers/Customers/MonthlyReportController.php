<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Reports\MonthReport;
use Illuminate\Http\Request;

class MonthlyReportController extends Controller
{
    public function __invoke(Request $request, User $customer, $year, $month)
    {
        $data = (new MonthReport($customer, $year, $month))->getData();
        return view('scores.monthly', [
            'data' => $data,
        ]);
    }
}
