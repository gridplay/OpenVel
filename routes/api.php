<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Route::middleware(['api'])->group(function () {
	// https://canadiangrid.ca/api/paypal
	Route::post('paypal', 'TierController@paypal');
	Route::post('siminfo', 'HomeController@siminfo');
	Route::post('abusereport', 'HomeController@AbuseReport');
});
