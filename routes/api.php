<?php

Route::middleware('guest')->prefix('v1')->group(function () {
    Route::post('register', 'AuthController@register')->name('register');
    Route::post('login', 'AuthController@login')->name('login');
    Route::post('refresh-token', 'AuthController@refreshToken')->name('refreshToken');

    Route::get('status', 'AuthController@check')->name('check  guest');
});

Route::middleware('auth:api')->prefix('v1')->group(function () {
    Route::post('logout', 'AuthController@logout')->name('logout');
    Route::post('_internal/register', 'AuthController@register')->name('register user');

    Route::get('users', 'UserController@index')->name('list of user');
    Route::put('user/{id}', 'UserController@update')->name('update user');
    Route::delete('user/{id}', 'UserController@delete')->name('delete user');
    
    Route::post('upload', 'UploadController@upload');
    Route::get('authenticated', 'AuthController@check')->name('check status');
});
