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

// Route::get("/test",function(){
//         return "<h1>asdasd</h1>";
// });


Route::get('cron', [\App\Http\Controllers\Api\RegisterController::class, 'cron'])->name('cron');
Route::get('cron/plane', [\App\Http\Controllers\Api\RegisterController::class, 'cron_plane'])->name('cron_plane');

Route::post('register', [\App\Http\Controllers\Api\RegisterController::class, 'register']);
Route::get('noauth', [\App\Http\Controllers\Api\RegisterController::class, 'noauth'])->name('noauth');


Route::any('login', [\App\Http\Controllers\Api\RegisterController::class, 'login'])->name('login');
Route::any('verify', [\App\Http\Controllers\Api\RegisterController::class, 'verify']);
Route::post('password/email',  [\App\Http\Controllers\Api\ForgotPasswordController::class,'forget']);
Route::any('password/reset', [\App\Http\Controllers\Api\CodeCheckController::class,'index']);
Route::post('password/code/check', [\App\Http\Controllers\Api\CodeCheckController::class,'code_verify']);
// Route::get('guide', [\App\Http\Controllers\Api\CMSController::class, 'guide']);
// Route::get('term/conditions', [\App\Http\Controllers\Api\CMSController::class, 'termanscondition']);


Route::post('/send-otp', [\App\Http\Controllers\Api\UserController::class, 'sendOtp']);
Route::post('/verify-otp-delete', [\App\Http\Controllers\Api\UserController::class, 'verifyOtpAndDelete']);


Route::group(['middleware' => ['api','auth:api'], 'prefix' => 'auth'], function () {
    // Route::middleware('auth:api')->group( function () {
    Route::post('health/assessment', [\App\Http\Controllers\Api\HealthAssessmentController::class, 'store']);
	
    
    Route::post('change_password', [\App\Http\Controllers\Api\RegisterController::class, 'change_password']);
    Route::post('profile', [\App\Http\Controllers\Api\UserController::class, 'profile']);
	Route::get('admin/info', [\App\Http\Controllers\Api\UserController::class, 'admininfo']);
    Route::get('logout', [\App\Http\Controllers\Api\RegisterController::class, 'logout']);
});
