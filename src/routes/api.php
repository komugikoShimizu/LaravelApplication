<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CategolyController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SummaryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('categories',[CategolyController::class, 'getCategolies']);
Route::post('transactions', [TransactionController::class, 'createTransactions']);
Route::get('transactions', [TransactionController::class, 'getMonthTransactions']);
Route::get('summaries', [SummaryController::class, 'monthlySummary']);
