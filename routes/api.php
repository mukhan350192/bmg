<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BioController;
use App\Http\Controllers\CabinetController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PayboxController;
use App\Http\Controllers\ScoringController;
use App\Http\Controllers\TestController;
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
Route::post('/confirmSMSTest',[UserController::class,'confirmSMSTest']);
Route::post('/secondStep',[UserController::class,'secondStep']);
Route::post('/thirdStep',[UserController::class,'thirdStep']);
Route::post('/testData',[UserController::class,'testData']);

//shortUrl
Route::post('/newShortUrl',[UrlController::class,'getShortUrl']);
Route::get('/newGetData',[UrlController::class,'newGetData']);
Route::get('/agreementNew',[UrlController::class,'agreementNew']);
Route::get('/prolongation',[UrlController::class,'prolongation']);
Route::post('/prolongationData',[UrlController::class,'prolongationData']);
Route::get('/getProlongationData',[UrlController::class,'getProlongationData']);
Route::get('/getData',[UrlController::class,'getData']);
Route::get('/agreement',[UrlController::class,'agreement']);

//PersonalCabinet
Route::post('/login',[CabinetController::class,'login']);
Route::post('/loginTest',[CabinetController::class,'loginTest']);
Route::post('/getUserInfo',[CabinetController::class,'getUserInfo']);
Route::post('/history',[CabinetController::class,'getUserHistory']);
Route::post('/getUserProfileFromBitrix',[CabinetController::class,'getUserProfileFromBitrix']);
Route::post('/notFull',[CabinetController::class,'notFull']);
Route::post('/repeatUser',[CabinetController::class,'getRepeatRequest']);
Route::post('/repeatRequest',[CabinetController::class,'repeatRequest']);
Route::post('/repeatRequestTest',[CabinetController::class,'repeatRequestTest']);
//Payment
Route::post('make_payment123', [PayboxController::class,'payment']);
Route::post('payment-result', [PayboxController::class,'paymentResult'])->name('payment-result');
//password
Route::get('/checkPerson',[PasswordController::class,'checkPerson']);
Route::get('/checkUrl',[PasswordController::class,'checkUrl']);
Route::get('/resetPassword',[PasswordController::class,'resetPassword']);
//Test
Route::prefix('test')->group(function (){
    Route::post('/test',[BioController::class,'test']);
    Route::post('/takeCode',[BioController::class,'takeCode']);
    Route::post('/sendSMS',[BioController::class,'sendSMS']);
    Route::post('/confirmSMS',[BioController::class,'confirmSMS']);
    Route::post('/secondStep',[BioController::class,'secondStep']);
    Route::post('/thirdStep',[BioController::class,'thirdStep']);
    Route::post('/deleteUser',[BioController::class,'deleteUser']);
});
//Scoring
Route::get('/scoringResult',[ScoringController::class,'scoringResult']);
Route::get('/getScore',[ScoringController::class,'getScore']);
Route::get('/getDocumentData',[ScoringController::class,'getDocumentData']);
//admin
Route::post('/authAdmin',[AdminController::class,'authAdmin']);
Route::post('/searchUser',[AdminController::class,'searchUser']);
Route::post('/deleteUser',[AdminController::class,'deleteUser']);
//
Route::post('/createDeletedUsers',[UserController::class,'createDeletedUsers']);
