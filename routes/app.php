<?php

use App\Http\Controllers\App\AttemptAuthenticateController;
use App\Http\Controllers\App\DailyTracklistController;
use App\Http\Controllers\App\DashboardController;
use App\Http\Controllers\App\HistoryController;
use App\Http\Controllers\App\ManualAttemptAuthenticateController;
use App\Http\Controllers\App\MonthReportController;
use App\Http\Controllers\App\RegisterDeviceController;
use App\Http\Controllers\App\WeekReportController;
use Illuminate\Support\Facades\Route;


Route::post('authenticate/manual', ManualAttemptAuthenticateController::class);
Route::post('authenticate/{token}', AttemptAuthenticateController::class);
Route::get('{token}/dashboard', DashboardController::class);
Route::get('{token}/history', HistoryController::class);
Route::get('{token}/week_report/{year}/{week}', WeekReportController::class);
Route::get('{token}/month_report/{year}/{month}', MonthReportController::class);
Route::get('{token}/questions', [DailyTracklistController::class, 'show']);
Route::post('{token}/questions', [DailyTracklistController::class, 'store']);
Route::post('{token}/register-device', RegisterDeviceController::class);
