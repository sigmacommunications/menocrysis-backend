<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/task', [App\Console\Commands\RecurringPayment::class, 'handle'])->name('handle');


Route::get('/', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login');
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('home_login');
Route::get('/admin', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('admin');
Route::post('/adminpost', [App\Http\Controllers\Auth\LoginController::class, 'admin'])->name('admin_post');


Route::get('/verify-email', function () {
    return view('verify_email');
});

Route::get('/delete-account', function () {
    return view('verify_email');
});

Route::get('/delete-user', function () {
    $user = \App\Models\User::where('email',request('email'))->first();
	if($user)
	{
		$user->delete();
		return response()->json(['success'=> true,'message' =>'user deleted successfully']);
	}
	return response()->json(['success'=> false, 'message'=>'user not found']);
	
});

Route::post('/send-otp', [App\Http\Controllers\Auth\LoginController::class, 'sendOtpToVerifyEmail']);
Route::post('/verify-otp-delete', [App\Http\Controllers\Auth\LoginController::class, 'verifyOtpAndDeleteUser']);

Route::group(['middleware' => ['auth']], function () { 
    
    Route::get('/helper', [App\Http\Controllers\NotificationController::class, 'helper']);
    Route::resource('/notification',App\Http\Controllers\NotificationController::class);
    Route::resource('/orders',App\Http\Controllers\OrdersController::class);
 	Route::resource('trophy',App\Http\Controllers\TrophyController::class);
    Route::get('/home', [App\Http\Controllers\DashboardController::class, 'dashboard'])->name('home');
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/admin_info', [App\Http\Controllers\DashboardController::class, 'admin_info'])->name('admin_info');
    Route::post('/admin_info_post', [App\Http\Controllers\DashboardController::class, 'admin_info_post'])->name('admin_info_post');    
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
    Route::resource('guides',App\Http\Controllers\GuideController::class);
    Route::get('/terms/conditions', [App\Http\Controllers\TermAndConditionController::class, 'index'])->name('terms_conditions');
    Route::post('/terms-conditions', [App\Http\Controllers\TermAndConditionController::class, 'termandcontionpost'])->name('terms_conditions_post');
    Route::get('/transaction', [App\Http\Controllers\TranasactionController::class, 'transaction_list'])->name('transaction');
    Route::get('/transaction_status/{id}', [App\Http\Controllers\TranasactionController::class, 'transaction_status'])->name('transaction_status');
    
    Route::resource('category',App\Http\Controllers\CategoryController::class);
    Route::resource('brand',App\Http\Controllers\BrandController::class);
    Route::resource('product',App\Http\Controllers\ProductController::class);
    Route::resource('shipping',App\Http\Controllers\ShippingController::class);

    // ==================== NEW ADMIN PANEL ROUTES ====================
    
    // Users Management
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::get('/users/{id}/status', [App\Http\Controllers\Admin\UserController::class, 'updateStatus'])->name('users.status');
    
    // Services Management
    // Categories Routes
    Route::get('categories', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/create', [App\Http\Controllers\Admin\CategoryController::class, 'create'])->name('categories.create');
    Route::post('categories', [App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
    Route::get('categories/{id}/edit', [App\Http\Controllers\Admin\CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('categories/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('categories/{id}/toggle-status', [App\Http\Controllers\Admin\CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
    Route::get('categories/get-categories', [App\Http\Controllers\Admin\CategoryController::class, 'getCategories'])->name('categories.get-categories');
    
    // Amenities Management
    Route::resource('amenities', App\Http\Controllers\Admin\AmenityController::class);
    
    // Locations Management
    Route::resource('locations', App\Http\Controllers\Admin\LocationController::class);
	Route::get('/location/map-data', [App\Http\Controllers\Admin\LocationController::class, 'getMapData'])->name('locations.map-data');
	Route::get('/location/revenue', [App\Http\Controllers\Admin\LocationController::class, 'getLocationRevenue'])->name('locations.revenue');
	Route::get('/location/barbers/{location}', [App\Http\Controllers\Admin\LocationController::class, 'getBarbersByLocation'])->name('locations.barbers');
    
    // Charges Management
    Route::resource('charges', App\Http\Controllers\Admin\ChargeController::class);
    Route::post('/charges/update-rates', [App\Http\Controllers\Admin\ChargeController::class, 'updateRates'])->name('charges.update-rates');
    
    // Payments Overview
    Route::get('/payments', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/stats', [App\Http\Controllers\Admin\PaymentController::class, 'getStats'])->name('payments.stats');
    Route::get('/payments/{id}', [App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('payments.show');
	Route::get('/payments/export', [App\Http\Controllers\Admin\PaymentController::class, 'export'])->name('payments.export');
    
    // Dynamic Splitting
    Route::get('/splitting', [App\Http\Controllers\Admin\SplittingController::class, 'index'])->name('splitting.index');
	Route::get('/splitting/analytics', [App\Http\Controllers\Admin\SplittingController::class, 'getAnalytics'])->name('splitting.analytics');
	Route::get('/splitting/barber/{barberId}', [App\Http\Controllers\Admin\SplittingController::class, 'getBarberEarnings'])->name('splitting.barber-earnings');

    
    // Bookings Management
    Route::resource('bookings', App\Http\Controllers\Admin\BookingController::class);
    Route::post('/bookings/{id}/status', [App\Http\Controllers\Admin\BookingController::class, 'updateStatus'])->name('bookings.status');
    
    // Reviews Management
    Route::resource('reviews', App\Http\Controllers\Admin\ReviewController::class);
    Route::post('/reviews/{id}/status', [App\Http\Controllers\Admin\ReviewController::class, 'updateStatus'])->name('reviews.status');
    
    // Support Tickets
    Route::resource('support', App\Http\Controllers\Admin\SupportController::class);
    Route::post('/support/{id}/reply', [App\Http\Controllers\Admin\SupportController::class, 'sendReply'])->name('support.reply');
    Route::post('/support/{id}/status', [App\Http\Controllers\Admin\SupportController::class, 'updateStatus'])->name('support.status');

    // Notification
    // Route::get('/notification/{id}',[\App\Http\Controllers\NotificationController::class,'show'])->name('admin.notification');
    
});