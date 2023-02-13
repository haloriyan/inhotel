<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return bcrypt('inikatasandi');
});

Route::group(['prefix' => "{username}"], function () {
    Route::get('cart', "UserController@cart")->name('user.cart');
    Route::get('/product/{id}', "UserController@product")->name('user.product');
    Route::get('{category?}', "UserController@homepage");
});