<?php

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


Route::post('register_send_otp',[\App\Http\Controllers\Api\AuthController::class,'send_otp_register']);
Route::post('login_send_otp',[\App\Http\Controllers\Api\AuthController::class,'send_login_otp']);

Route::post('otp_check',[\App\Http\Controllers\Api\AuthController::class,'otp_check']);
Route::post('pin_set',[\App\Http\Controllers\Api\AuthController::class,'pin_set']);
Route::post('login',[\App\Http\Controllers\Api\AuthController::class,'login']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


