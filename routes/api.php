<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\CodeCheckController;
use App\Http\Controllers\ResetPasswordController;

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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route:: middleware('auth:sanctum')->group(function(){
    Route::get('/user', [Controller::class,'user']);
    Route::post('change/password', [RegisterController::class,'changePassword']);
});

Route::controller(RegisterController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('forgot/password', 'forgotPassword');

});

Route::post('password/email',  [ForgotPasswordController::class,'check']);
Route::post('password/code/check', [CodeCheckController::class,'check']);
Route::post('password/reset', [ResetPasswordController::class,'']);


Route::post('social/fb/login', [Controller::class, 'socialFBLogin']);
//Route::middleware('auth:api')->get('user', [Controller::class, 'user']);
Route::post('social/g/login', [Controller::class, 'socialGoogleLogin']);



