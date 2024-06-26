<?php

use App\Http\Controllers\Api\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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
// test 
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();

});
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [AuthController::class, 'login'])->name('api.login');
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});

Route::group([
    'middleware' => 'api'
], function ($router) {
    Route::post('create', [PaymentController::class, 'create'])->name('api.create');
    Route::post('status', [PaymentController::class, 'status'])->name('api.status');
    // Route::post('testWebhook', [PaymentController::class, 'testWebhook'])->name('api.testWebhook');
    // Route::get('getPayment/{token}', [PaymentController::class, 'getPayment'])->name('api.getPayment');
    Route::post('filter', [PaymentController::class, 'filter'])->name('api.filter');
});
    
