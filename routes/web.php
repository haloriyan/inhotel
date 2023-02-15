<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return bcrypt('inikatasandi');
});
Route::get('test', "UserController@testPayout");
Route::get('getpo', "UserController@getPayout");

Route::get('payment/done', "VisitorController@paymentDone");

Route::group(['prefix' => "{username}"], function () {
    Route::get('cart', "UserController@cart")->name('user.cart');
    Route::get('/product/{id}', "UserController@product")->name('user.product');
    Route::get('{category?}', "UserController@homepage");
});