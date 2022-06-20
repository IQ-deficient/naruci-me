<?php

use App\Http\Controllers\Api\EmployeeController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('v1')->group(function () {
    Route::get('employee', [EmployeeController::class, 'index']);
});

//Route::get('/users', [\App\Http\Controllers\UserController::class, 'index']);
//Route::post('/loadOrderData', [\App\Http\Controllers\OrderController::class, 'loadOrderData']);
//Route::get('/loadOrderData', [\App\Http\Controllers\CartController::class, 'getCartForUser'])->name('getCartForUser');
