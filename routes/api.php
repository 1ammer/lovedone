<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RegisterController;

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

Route::controller(RegisterController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
});


//Route::post('/login', function (Request $request) {
//  return 'hi';
//});
Route::post('social/fb/login', [Controller::class, 'socialFBLogin']);
//Route::middleware('auth:api')->get('user', [Controller::class, 'user']);
Route::post('social/G/login', [Controller::class, 'socialGoogleLogin']);



