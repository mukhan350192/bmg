<?php

use App\Http\Controllers\CabinetController;
use App\Http\Controllers\PayboxController;
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
Route::post('/newShortUrl',[UrlController::class,'getShortUrl']);
Route::get('/newGetData',[UrlController::class,'newGetData']);
Route::get('/agreementNew',[UrlController::class,'agreementNew']);
Route::post('/testData',[UserController::class,'testData']);
//PersonalCabinet
Route::post('/login',[CabinetController::class,'login']);
Route::post('/loginTest',[CabinetController::class,'loginTest']);
Route::post('/getUserInfo',[CabinetController::class,'getUserInfo']);
Route::post('/history',[CabinetController::class,'getUserHistory']);
Route::post('/getUserProfileFromBitrix',[CabinetController::class,'getUserProfileFromBitrix']);
Route::post('/notFull',[CabinetController::class,'notFull']);
Route::post('/repeatUser',[CabinetController::class,'getRepeatRequest']);
Route::post('/repeatRequest',[CabinetController::class,'repeatRequest']);
//Payment
Route::post('make_payment123', [PayboxController::class,'payment']);
Route::post('payment-result', [PayboxController::class,'paymentResult'])->name('payment-result');
