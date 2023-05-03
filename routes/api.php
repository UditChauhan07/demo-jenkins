<?php

use App\Http\Controllers\Api\OfflineApiController;
use App\Http\Controllers\Api\PaidOfflineApiController;
use App\Http\Controllers\Api\PaidUserApiController;
use App\Http\Controllers\Api\UserApiController;
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

Route::post('onboardingstepone', [UserApiController::class, 'onboardingstepone']);
Route::post('onboardingsteptwo', [UserApiController::class, 'onboardingsteptwo']);
Route::post('onboardingstepthree', [UserApiController::class, 'onboardingstepthree']);
Route::post('onboardingstepfour', [UserApiController::class, 'onboardingstepfour']);
Route::post('otheruserdetail', [UserApiController::class, 'otheruserdetail']);
Route::post('loginwithtoken', [UserApiController::class, 'loginwithtoken']);
Route::post('userlogin', [UserApiController::class, 'userlogin']);
Route::post('verifyloginotp', [UserApiController::class, 'verifyloginotp']);
Route::post('offlineapi', [OfflineApiController::class, 'offlineapi']);
Route::post('otheruserreading', [UserApiController::class, 'otheruserreading']);
Route::post('sharepersonreading', [UserApiController::class, 'sharepersonreading']);
Route::post('uploadprofilepic', [UserApiController::class, 'uploadprofilepic']);
Route::post('namereadingcompatibilitycheck', [UserApiController::class, 'namereadingcompatibilitycheck']);
Route::post('editanothernamecompatibilitycheck', [UserApiController::class, 'editanothernamecompatibilitycheck']);
Route::post('updateanothernameverification', [UserApiController::class, 'updateanothernameverification']);
Route::post('compatibilitycheckhistory', [UserApiController::class, 'compatibilitycheckhistory']);
Route::post('checkcarcompatibility', [UserApiController::class, 'checkcarcompatibility']);
Route::post('checkbusinesscompatibility', [UserApiController::class, 'checkbusinesscompatibility']);
Route::post('checkpropertycompatibility', [UserApiController::class, 'checkpropertycompatibility']);
Route::post('checknamereadingcompatibility', [UserApiController::class, 'checknamereadingcompatibility']);


// By Summi Ma'am Logic
Route::post('onetoonecompatibilitycheck', [UserApiController::class, 'onetoonecompatibilitycheck']);
Route::post('typecompatibilitycheck', [UserApiController::class, 'typecompatibilitycheck']);

Route::post('businesscompatibilitycheck', [UserApiController::class, 'businesscompatibilitycheck']);
Route::post('usernamecompatibilitycheck', [UserApiController::class, 'usernamecompatibilitycheck']);
Route::post('carcompatibilitycheck', [UserApiController::class, 'carcompatibilitycheck']);
Route::post('propertycompatibilitycheck', [UserApiController::class, 'propertycompatibilitycheck']);



//create 24-11-2022 start here
Route::post('editanotherusernamecompatibilitycheck', [UserApiController::class, 'editanotherusernamecompatibilitycheck']);
Route::post('userlifecoach', [UserApiController::class, 'userlifecoach']);
//update end here

//create at 25-11-2022 start here
Route::post('userlifecoachcosmiccalender', [UserApiController::class, 'userlifecoachcosmiccalender']);
//end here

//created at 28-11-2022
Route::post('resendotpverify', [UserApiController::class, 'resendotpverify']);
Route::post('resentverifyloginotp', [UserApiController::class, 'resentverifyloginotp']);
Route::post('editprofile', [UserApiController::class, 'editprofile']);
//endhere


//created at 29-11-2022
Route::post('usercosmiccelender', [UserApiController::class, 'usercosmiccelender']);
//endhere

// By Kulbir Sir Logic API
Route::post('onetoothercompatibilitycheck', [UserApiController::class, 'onetoothercompatibilitycheck']);  
Route::post('othermodulecompatibilitycheck', [UserApiController::class, 'othermodulecompatibilitycheck']);

//common
Route::post('userPossesionData', [UserApiController::class, 'userPossesionData']);
Route::post('userelementalData', [UserApiController::class, 'userelementalData']);
Route::post('lifecoach', [UserApiController::class, 'lifecoach']);
Route::post('usertraveldata', [UserApiController::class, 'usertraveldata']);
Route::post('cosmiccelender', [UserApiController::class, 'cosmiccelender']);
Route::post('userdailyprediction', [UserApiController::class, 'userdailyprediction']);
Route::post('askquesstion', [UserApiController::class, 'askquesstion']);
Route::post('likedislikemessage', [UserApiController::class, 'likedislikemessage']);
Route::post('seenunseenmessage', [UserApiController::class, 'seenunseenmessage']);
Route::post('messagelist', [UserApiController::class, 'messagelist']);
Route::post('videolist', [UserApiController::class, 'videolist']);
Route::post('usertravelpossibility', [UserApiController::class, 'usertravelpossibility']);
Route::post('lifecoachcosmiccalender', [UserApiController::class, 'lifecoachcosmiccalender']);

Route::post('newothercompatibilitycheck', [UserApiController::class, 'newothercompatibilitycheck']);
Route::post('childcompatibilitycheck', [UserApiController::class, 'childcompatibilitycheck']);
Route::post('subscriptionprize', [UserApiController::class, 'subscriptionprize']);

Route::post('user_payments', [UserApiController::class, 'user_payments']);
Route::post('user_payment_list', [UserApiController::class, 'user_payment_list']);
Route::post('cancel_user_subscription', [UserApiController::class, 'cancel_user_subscription']);
Route::post('paused_user_subscription', [UserApiController::class, 'paused_user_subscription']);
Route::post('resume_user_subscription', [UserApiController::class, 'resume_user_subscription']);
Route::post('stripemode', [UserApiController::class, 'stripemode']);
Route::post('cancelspecialbscription', [UserApiController::class, 'cancelspecialbscription']);

Route::post('deleteuser', [UserApiController::class, 'deleteuser']);
Route::post('newuser_payments', [UserApiController::class, 'newuser_payments']);

//Paid test api
Route::group(['prefix' => 'paid'], function() {
Route::post('offlineapi', [PaidOfflineApiController::class, 'offlineapi']);

Route::post('onboardingstepone', [PaidUserApiController::class, 'onboardingstepone']);
Route::post('onboardingsteptwo', [PaidUserApiController::class, 'onboardingsteptwo']);
Route::post('onboardingstepthree', [PaidUserApiController::class, 'onboardingstepthree']);
Route::post('onboardingstepfour', [PaidUserApiController::class, 'onboardingstepfour']);
Route::post('otheruserdetail', [PaidUserApiController::class, 'otheruserdetail']);
Route::post('loginwithtoken', [PaidUserApiController::class, 'loginwithtoken']);
Route::post('userlogin', [PaidUserApiController::class, 'userlogin']);
Route::post('verifyloginotp', [PaidUserApiController::class, 'verifyloginotp']);
Route::post('otheruserreading', [PaidUserApiController::class, 'otheruserreading']);
Route::post('sharepersonreading', [PaidUserApiController::class, 'sharepersonreading']);
Route::post('uploadprofilepic', [PaidUserApiController::class, 'uploadprofilepic']);
Route::post('namereadingcompatibilitycheck', [PaidUserApiController::class, 'namereadingcompatibilitycheck']);
Route::post('editanothernamecompatibilitycheck', [PaidUserApiController::class, 'editanothernamecompatibilitycheck']);
Route::post('updateanothernameverification', [PaidUserApiController::class, 'updateanothernameverification']);
Route::post('compatibilitycheckhistory', [PaidUserApiController::class, 'compatibilitycheckhistory']);
Route::post('checkcarcompatibility', [PaidUserApiController::class, 'checkcarcompatibility']);
Route::post('checkbusinesscompatibility', [PaidUserApiController::class, 'checkbusinesscompatibility']);
Route::post('checkpropertycompatibility', [PaidUserApiController::class, 'checkpropertycompatibility']);
Route::post('checknamereadingcompatibility', [PaidUserApiController::class, 'checknamereadingcompatibility']);


// By Summi Ma'am Logic
Route::post('onetoonecompatibilitycheck', [PaidUserApiController::class, 'onetoonecompatibilitycheck']);
Route::post('typecompatibilitycheck', [PaidUserApiController::class, 'typecompatibilitycheck']);
Route::post('businesscompatibilitycheck', [PaidUserApiController::class, 'businesscompatibilitycheck']);
Route::post('usernamecompatibilitycheck', [PaidUserApiController::class, 'usernamecompatibilitycheck']);
Route::post('carcompatibilitycheck', [PaidUserApiController::class, 'carcompatibilitycheck']);
Route::post('propertycompatibilitycheck', [PaidUserApiController::class, 'propertycompatibilitycheck']);


//create 24-11-2022 start here
Route::post('editanotherusernamecompatibilitycheck', [PaidUserApiController::class, 'editanotherusernamecompatibilitycheck']);
Route::post('userlifecoach', [PaidUserApiController::class, 'userlifecoach']);
//update end here

//create at 25-11-2022 start here
Route::post('userlifecoachcosmiccalender', [PaidUserApiController::class, 'userlifecoachcosmiccalender']);
//end here

//created at 28-11-2022
Route::post('resendotpverify', [PaidUserApiController::class, 'resendotpverify']);
Route::post('resentverifyloginotp', [PaidUserApiController::class, 'resentverifyloginotp']);
Route::post('editprofile', [PaidUserApiController::class, 'editprofile']);
//endhere

//created at 29-11-2022
Route::post('usercosmiccelender', [PaidUserApiController::class, 'usercosmiccelender']);
//endhere

// By Kulbir Sir Logic API
Route::post('onetoothercompatibilitycheck', [PaidUserApiController::class, 'onetoothercompatibilitycheck']);  
Route::post('othermodulecompatibilitycheck', [PaidUserApiController::class, 'othermodulecompatibilitycheck']);

//common
Route::post('userPossesionData', [PaidUserApiController::class, 'userPossesionData']);
Route::post('userelementalData', [PaidUserApiController::class, 'userelementalData']);
Route::post('lifecoach', [PaidUserApiController::class, 'lifecoach']);
Route::post('usertraveldata', [PaidUserApiController::class, 'usertraveldata']);
Route::post('cosmiccelender', [PaidUserApiController::class, 'cosmiccelender']);
Route::post('userdailyprediction', [PaidUserApiController::class, 'userdailyprediction']);
Route::post('askquesstion', [PaidUserApiController::class, 'askquesstion']);
Route::post('likedislikemessage', [PaidUserApiController::class, 'likedislikemessage']);
Route::post('seenunseenmessage', [PaidUserApiController::class, 'seenunseenmessage']);
Route::post('messagelist', [PaidUserApiController::class, 'messagelist']);
Route::post('videolist', [PaidUserApiController::class, 'videolist']);
Route::post('usertravelpossibility', [PaidUserApiController::class, 'usertravelpossibility']);
Route::post('lifecoachcosmiccalender', [PaidUserApiController::class, 'lifecoachcosmiccalender']);
Route::post('newothercompatibilitycheck', [PaidUserApiController::class, 'newothercompatibilitycheck']);

});