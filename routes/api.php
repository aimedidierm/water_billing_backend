<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\MeterController;
use App\Http\Controllers\MeterReadingsController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout']);
Route::get('/profile', [UserController::class, 'edit']);
Route::put('/profile', [UserController::class, 'updateApi']);

Route::group(["prefix" => "admin", "middleware" => ["auth:api", "adminCheck"], "as" => "admin."], function () {
    Route::apiResource('/users', UserController::class)->only('index', 'store', 'update', 'destroy');
    Route::get('/settings', [UserController::class, 'show']);
    Route::apiResource('/meters', MeterController::class)->only('index', 'store');
    Route::apiResource('/billing', BillingController::class)->only('index');
});

Route::group(["prefix" => "client", "middleware" => ["auth:api", "clientCheck"], "as" => "client."], function () {
    Route::get('/settings', [UserController::class, 'show']);
    Route::apiResource('/readings', MeterReadingsController::class)->only('index', 'store');
    Route::get('/billing', [BillingController::class, 'clientListing']);
});
