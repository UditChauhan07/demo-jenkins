<?php

use App\http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\BasicmoneyController;
use App\Http\Controllers\BasicparentingController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChildrenController;
use App\Http\Controllers\CommunController;
use App\Http\Controllers\Compatibility_descriptionController;
use App\Http\Controllers\Compatibility_percentageController;
use App\Http\Controllers\Compatible_partnerController;
use App\Http\Controllers\CosmiccellenderController;
use App\Http\Controllers\Daily_predictionController;
use App\Http\Controllers\DestinynoController;
use App\Http\Controllers\DetailedmoneyController;
use App\Http\Controllers\DetailparentingController;
use App\Http\Controllers\DobreadingController;
use App\Http\Controllers\ElementalnoController;
use App\Http\Controllers\FavparameterController;
use App\Http\Controllers\GeneralsettingController;
use App\Http\Controllers\HealthcycleController;
use App\Http\Controllers\HealthprecautionController;
use App\Http\Controllers\HealthreadingController;
use App\Http\Controllers\HealthsuggestionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\Life_changeController;
use App\Http\Controllers\Life_cycleController;
use App\Http\Controllers\LifecoachController;
use App\Http\Controllers\Luckiest_parameterController;
use App\Http\Controllers\MagicboxController;
use App\Http\Controllers\Master_numberController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\NamereadingController;
use App\Http\Controllers\Partner_relationshipController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\PersonaldayController;
use App\Http\Controllers\PersonalmonthController;
use App\Http\Controllers\PersonalweekController;
use App\Http\Controllers\PersonalyearController;
use App\Http\Controllers\Planet_numberController;
use App\Http\Controllers\PossesionController;
use App\Http\Controllers\Primaryno_typeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Subscription_prizeController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\UnfavparameterController;
use App\Http\Controllers\UniversaldayController;
use App\Http\Controllers\UniversalmonthController;
use App\Http\Controllers\UniversalyearController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\Zodic_signController;
use App\Models\Compatibility_description;
use App\Models\Compatibility_percentage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();
Route::get('resetpassword', [HomeController::class, 'changepassword'])->name('reset_password');
Route::post('update-password', [HomeController::class, 'updatepassword']);
Route::get('forget-password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');
Route::post('forget-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post');
Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
Route::post('reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');


Route::get('home', [HomeController::class, 'index'])->name('home');
Route::get('profile', [HomeController::class, 'profile'])->name('profile');
Route::post('update-profile-image', [HomeController::class, 'updateprofilepic'])->name('update.profilepic');
Route::post('update-profile/{id}', [HomeController::class, 'updateprofile'])->name('profile.update');

Route::group(['middleware' => ['auth']], function() {
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('systems', SystemController::class);
    Route::resource('modules', ModuleController::class);
    Route::resource('namereading', NamereadingController::class);
    Route::resource('dobreading', DobreadingController::class);
    Route::resource('magicbox', MagicboxController::class);
    Route::resource('elementalno', ElementalnoController::class);
    Route::resource('healthcycle', HealthcycleController::class);
    Route::resource('healthreading', HealthreadingController::class);
    Route::resource('healthprecaution', HealthprecautionController::class);
    Route::resource('healthsuggestion', HealthsuggestionController::class);
    Route::resource('personalyear', PersonalyearController::class);
    Route::resource('personalmonth', PersonalmonthController::class);
    Route::resource('personalweek', PersonalweekController::class);
    Route::resource('personalday', PersonaldayController::class);
    Route::resource('basicparenting', BasicparentingController::class);
    Route::resource('detailparenting', DetailparentingController::class);
    Route::resource('basicmoney', BasicmoneyController::class);
    Route::resource('detailedmoney', DetailedmoneyController::class);
    Route::resource('destinyno', DestinynoController::class);
    Route::resource('commons', CommunController::class);
    Route::resource('master_numbers', Master_numberController::class);
    Route::resource('luckiest_parameters', Luckiest_parameterController::class);
    Route::resource('life_cycles', Life_cycleController::class);
    Route::resource('life_changes', Life_changeController::class);
    Route::resource('zodic_signs', Zodic_signController::class);
    Route::resource('primaryno_types', Primaryno_typeController::class);
    Route::resource('fav_parameters', FavparameterController::class);
    Route::resource('unfav_parameters', UnfavparameterController::class);
    Route::resource('partner_relationships', Partner_relationshipController::class);
    Route::resource('compatible_partners', Compatible_partnerController::class);
    Route::resource('planet_numbers', Planet_numberController::class);
    Route::resource('universalyear', UniversalyearController::class);
    Route::resource('universalmonth', UniversalmonthController::class);
    Route::resource('universalday', UniversaldayController::class);
    Route::resource('possesions', PossesionController::class);
    Route::resource('compatibility_description', Compatibility_descriptionController::class);
    Route::resource('compatibility_percentage', Compatibility_percentageController::class);

    Route::resource('videos', VideoController::class);
    Route::resource('lifecoach_descriptions', LifecoachController::class);
    Route::resource('dailyprediction', Daily_predictionController::class);
    Route::resource('chat', ChatController::class);
    Route::resource('childrens', ChildrenController::class);
    Route::resource('subscription_prize', Subscription_prizeController::class);
});
Route::post('dailyprediction/{id}/publish', [Daily_predictionController::class, 'publish'])->name('dailyprediction.publish');
Route::post('dailyprediction/{id}/cancel', [Daily_predictionController::class, 'cancelpublish'])->name('dailyprediction.cancel');
Route::get('prediction/{id}/report', [Daily_predictionController::class, 'report'])->name('prediction.report');
Route::post('chatlist', [ChatController::class, 'chat'])->name('chat.chat');
Route::post('askquestion', [ChatController::class, 'questionanswer']);
Route::post('searchuser', [ChatController::class, 'searchuser']);
Route::post('filteruser', [ChatController::class, 'filteruser']);
Route::post('favunfavfilter', [HomeController::class, 'favunfavdatafilter']);
Route::post('compfilter', [HomeController::class, 'compatibilityfilter']);

//
Route::post('predictiondate', [Daily_predictionController::class, 'predictiondate']);
Route::post('type_description', [Compatibility_descriptionController::class, 'typedescription']);
//
Route::post('userfilter', [UserController::class, 'userfilter']);
Route::get('user_show/{id}', [UserController::class, 'show']);
Route::get('user_edit/{id}', [UserController::class, 'edit']);
Route::get('users_destroy/{id}', [UserController::class, 'destroy']);
Route::get('user_unblock/{id}', [UserController::class, 'unblock']);

//subscription
Route::get('subscribe/{id}', [UserController::class, 'subscribe'])->name('users.subscribe');
Route::get('unsubscribe/{id}', [UserController::class, 'unsubscribe'])->name('users.unsubscribe');
Route::get('stripe_mode', [Subscription_prizeController::class, 'stripe_mode'])->name('stripe.mode');
Route::post('stripemode_status/{id}', [Subscription_prizeController::class, 'stripemode_status'])->name('stripe.status');
Route::get('threemonthsubscribes/{id}', [UserController::class, 'threemonthsubscribes'])->name('users.threemonthsubscribes');

//General settings
Route::get('general-settings', [GeneralsettingController::class, 'index'])->name('general.index');
Route::post('free_subcheck', [GeneralsettingController::class, 'free_subcheck'])->name('free_subcheck');
Route::post('general-update-settings', [GeneralsettingController::class, 'store'])->name('general.store');

// Block and unblock routes
Route::post('module/{id}/unblock', [ModuleController::class, 'unblock'])->name('module.unblock');
Route::post('user/{id}/unblock', [UserController::class, 'unblock'])->name('user.unblock');
Route::post('role/{id}/unblock', [RoleController::class, 'unblock'])->name('role.unblock');
Route::post('namereading/{id}/unblock', [NamereadingController::class, 'unblock'])->name('namereading.unblock');
Route::post('dobreading/{id}/unblock', [DobreadingController::class, 'unblock'])->name('dobreading.unblock');
Route::post('magicbox/{id}/unblock', [MagicboxController::class, 'unblock'])->name('magicbox.unblock');
Route::post('elementalno/{id}/unblock', [ElementalnoController::class, 'unblock'])->name('elementalno.unblock');
Route::post('healthcycle/{id}/unblock', [HealthcycleController::class, 'unblock'])->name('healthcycle.unblock');
Route::post('healthreading/{id}/unblock', [HealthreadingController::class, 'unblock'])->name('healthreading.unblock');
Route::post('healthprecaution/{id}/unblock', [HealthprecautionController::class, 'unblock'])->name('precaution.unblock');
Route::post('healthsuggestion/{id}/unblock', [HealthsuggestionController::class, 'unblock'])->name('suggestion.unblock');
Route::post('personalyear/{id}/unblock', [PersonalyearController::class, 'unblock'])->name('personalyear.unblock');
Route::post('personalmonth/{id}/unblock', [PersonalmonthController::class, 'unblock'])->name('personalmonth.unblock');
Route::post('personalweek/{id}/unblock', [PersonalweekController::class, 'unblock'])->name('personalweek.unblock');
Route::post('personalday/{id}/unblock', [PersonaldayController::class, 'unblock'])->name('personalday.unblock');
Route::post('basicparenting/{id}/unblock', [BasicparentingController::class, 'unblock'])->name('basicparent.unblock');
Route::post('detailedparent/{id}/unblock', [DetailparentingController::class, 'unblock'])->name('detailparent.unblock');
Route::post('basicmoney/{id}/unblock', [BasicmoneyController::class, 'unblock'])->name('basicmoney.unblock');
Route::post('detailedmoney/{id}/unblock', [DetailedmoneyController::class, 'unblock'])->name('detailmoney.unblock');
Route::post('destinyno/{id}/unblock', [DestinynoController::class, 'unblock'])->name('destinyno.unblock');
Route::post('masterno/{id}/unblock', [Master_numberController::class, 'unblock'])->name('masterno.unblock');
Route::post('luckyno/{id}/unblock', [Luckiest_parameterController::class, 'unblock'])->name('luckyno.unblock');
Route::post('lifecycle/{id}/unblock', [Life_cycleController::class, 'unblock'])->name('lifecycle.unblock');
Route::post('lifechange/{id}/unblock', [Life_changeController::class, 'unblock'])->name('lifechange.unblock');
Route::post('zodicsign/{id}/unblock', [Zodic_signController::class, 'unblock'])->name('zodicsign.unblock');
Route::post('primaryno/{id}/unblock', [Primaryno_typeController::class, 'unblock'])->name('primaryno.unblock');
Route::post('partner_relation/{id}/unblock', [Partner_relationshipController::class, 'unblock'])->name('relation.unblock');
Route::post('compatible/{id}/unblock', [Compatible_partnerController::class, 'unblock'])->name('compatible.unblock');
Route::post('planetno/{id}/unblock', [Planet_numberController::class, 'unblock'])->name('planetno.unblock');
Route::post('zodicsign/{id}/unblock', [Zodic_signController::class, 'unblock'])->name('zodicsign.unblock');
Route::post('favparameter/{id}/unblock', [FavparameterController::class, 'unblock'])->name('fav.unblock');
Route::post('unfavparameters/{id}/unblock', [UnfavparameterController::class, 'unblock'])->name('unfav.unblock');
Route::post('universalyear/{id}/unblock', [UniversalyearController::class, 'unblock'])->name('universalyear.unblock');
Route::post('universalmonth/{id}/unblock', [UniversalmonthController::class, 'unblock'])->name('universalmonth.unblock');
Route::post('universalday/{id}/unblock', [UniversaldayController::class, 'unblock'])->name('universalday.unblock');

Route::post('children/{id}/unblock', [ChildrenController::class, 'unblock'])->name('childrens.unblock');

//cosmic celender
Route::get('celender', [CosmiccellenderController::class, 'cosmicstars'])->name('cosmiccelender');
// Route::get('getCalender',function(){
//     return view('public');
// });
// Route::post('public/celender', [CosmiccellenderController::class, 'cosmicPublic'])->name('cosmic.public');

Route::get('create', [HomeController::class, 'see']);
// erport userlist
Route::post('filetype', [UserController::class, 'userexport']);
// import data
Route::get('import', [ImportController::class, 'index']);
Route::post('file-import', [ImportController::class, 'fileImport'])->name('file-import');
// invoice 
Route::get('invoice', [PdfController::class, 'index']);
Route::get('export-pdf/{id}', [PdfController::class, 'downloadPdf'])->name('exportpdf');
