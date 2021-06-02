<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\MockController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/check/eligibility', [MockController::class, 'checkEligibility']);
Route::post('/check/status', [CustomerController::class, 'checkCustomerStatus']);
Route::post('/customer-offer/{msisdn}', [CustomerController::class, 'saveCustomerOffer']);
Route::post('/update-kyc/{msisdn}', [CustomerController::class, 'updateCustomerKyc']);
Route::get('/customers/{msisdn}', [CustomerController::class, 'getCustomerDetails']);
Route::post('/installment/initial/{msisdn}', [InstallmentController::class, 'initialDeposit']);
Route::post('/installment/pay/{msisdn}', [InstallmentController::class, 'addInstallment']);
// Not found
Route::fallback(function (){
    abort(404, 'Oops!!, You seem to have been lost');
});
