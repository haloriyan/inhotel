<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('config/{key}', "UserController@getConfig");

Route::group(['prefix' => "visitor"], function () {
    Route::post('login', "VisitorController@login")->name('visitor.login');
    Route::post('/', "VisitorController@load");
    Route::post('cart', "VisitorController@cart");
    Route::post('cart-quantity', "VisitorController@cartQuantity");
    Route::post('checkout', "VisitorController@checkout");

    Route::group(['prefix' => "order"], function () {
        Route::post('add', "OrderController@add");
    });
});

Route::group(['prefix' => "report"], function () {
    Route::post('/', "ReportController@summary");
    Route::post('payment', "ReportController@payment");
    Route::post('payment/detail', "ReportController@paymentDetail");
    Route::post('revenue-withdraw', "ReportController@revenueToWithdraw");
});

Route::group(['prefix' => "payment"], function () {
    Route::post('callback', "OrderController@paymentCallback");
    Route::post('withdraw', "UserController@withdraw");
    Route::post('withdraw/check', "UserController@checkWithdraw");
});

Route::group(['prefix' => "user"], function () {
    Route::post('login', "UserController@login");
    Route::post('register', "UserController@register");
    Route::post('logout', "UserController@logout");
    Route::post('home', "UserController@home");
    Route::post('update', "UserController@update");
    Route::post('profile', "UserController@profile");
    Route::post('premium', "UserController@getPremium");
    Route::post('otp', "UserController@otpAuth");
    Route::post('otp-resend', "UserController@resendOtp");

    Route::post('forget-password', "UserController@forgetPassword");

    Route::group(['prefix' => "order"], function () {
        Route::post('/', 'UserController@order');
    });

    Route::group(['prefix' => "banner"], function () {
        Route::post('/', 'BannerController@get');
        Route::post('store', 'BannerController@store');
        Route::post('delete', 'BannerController@delete');
        Route::post('priority', 'BannerController@priority');
    });
});

Route::group(['prefix' => "social"], function () {
    Route::post('store', "SocialController@store");
    Route::post('delete', "SocialController@delete");
});

Route::group(['prefix' => "bank"], function () {
    Route::post('store', "BankController@store");
    Route::post('delete', "BankController@delete");
    Route::post('/', "BankController@load");
});

Route::group(['prefix' => "product"], function () {
    Route::post('store', "ProductController@store");
    Route::post('delete', "ProductController@delete");
    Route::post('visibility', "ProductController@visibility");
    Route::get('{id}', "ProductController@get");
});

Route::group(['prefix' => "gallery"], function () {
    Route::post('create', "GalleryController@create");
    Route::post('/', "GalleryController@all");

    Route::group(['prefix' => "image"], function () {
        Route::post('/', "GalleryController@images");
        Route::post('upload', "GalleryController@upload");
        Route::post('remove', "GalleryController@remove");
    });
});

Route::group(['prefix' => "export"], function () {
    Route::post('visitor', "ExportController@visitor");
    Route::post('revenue', "ExportController@revenue");
});