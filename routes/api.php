<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarCategoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\CarBookingController;
use App\Http\Controllers\CarSeatController;
use Illuminate\Support\Facades\Route;


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
//// Auth routes
// Route::group(['middleware' => 'cors'], function () {
Route::middleware('auth:sanctum')->group(function () {
    Route::resource('/booking', CarBookingController::class)->only('store');
    Route::post('/payment/confirm',[CarBookingController::class, 'confirmPayment']);

});
// admin route
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::resource('/category', CarCategoryController::class)->except(['index', 'show',  'edit',]);
    Route::resource('/cars', CarController::class)->except(['index', 'show',  'edit',]);
    Route::resource('/seat', CarSeatController::class)->except(['index', 'show',  'edit',]);
    Route::resource('/booking', CarBookingController::class)->only('index');
});
// normal route
Route::resource('/category', CarCategoryController::class)->only(['index', 'show',]);
Route::resource('/cars', CarController::class)->only(['index', 'show',]);
Route::resource('/seat', CarSeatController::class)->only(['index', 'show',]);
Route::get('/car/seats/{carid}', [CarController::class, 'allSeats']);


Route::group(['prefix' => 'auth'], function () {
    Route::get('google', [AuthController::class, 'redirectToGoogle']);
    Route::post('/google/callback', [AuthController::class, 'handleGoogleCallback']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});
// });
