<?php

Route::middleware('guest')->prefix('v1')->group(function () {
    Route::post('register', 'AuthController@register')->name('register');
    Route::post('login', 'AuthController@login')->name('login');
    Route::post('refresh-token', 'AuthController@refreshToken')->name('refreshToken');

    Route::get('status', 'AuthController@check')->name('check  guest');
});

Route::middleware('auth:api')->prefix('v1')->group(function () {
    Route::post('logout', 'AuthController@logout')->name('logout');
    
    Route::post('upload', 'UploadController@upload');
    Route::get('authenticated', 'AuthController@check')->name('check status');
});
