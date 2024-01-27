<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
 
    return back()->withError(['type' => 'green', 'msg', 'Verification link sent!']);
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::middleware(['xmlrpc'])->group(function () {
    Route::domain('helpers.canadiangrid.ca')->group(function () {
        //Route::post('/', 'XmlrpcController@xmlrpc');
        Route::resource('/', 'XmlrpcController');
    })->name('helpers');
});

Route::middleware(['web'])->group(function () {
    Route::get('img/{uuid?}', 'HomeController@getTexture');
    Route::get('getwelcome', 'HomeController@getWelcome');
    Route::get('join', 'AuthController@join')->name('register');
    Route::get('auth/login', 'AuthController@login')->name('login');
    Route::resources([
        'acc' => 'AccController',
        'auth' => 'AuthController',
        'admin' => 'AdminController',
        'blog' => 'BlogController',
        'tier' => 'TierController',
        'u' => 'ProfilesController',
    ]);
    Route::get('{id?}', 'HomeController@showPage');
    Route::resource('/', 'HomeController');
});