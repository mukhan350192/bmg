<?php

use App\Http\Controllers\UrlController;
use App\Http\Controllers\UserController;
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
Route::post('/test',[UserController::class,'test']);
Route::post('/takeCode',[UserController::class,'takeCode']);
Route::post('/sendSMS',[UserController::class,'sendSMS']);
Route::post('/confirmSMS',[UserController::class,'confirmSMS']);
Route::post('/secondStep',[UserController::class,'secondStep']);
Route::post('/thirdStep',[UserController::class,'thirdStep']);
Route::get('/newShortUrl',[UrlController::class,'getShortUrl']);
