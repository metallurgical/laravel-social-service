<?php

Route::group(['namespace' => 'Api'], function() {
    Route::post('facebook/login', 'FacebookController@login')->name('facebook.login');
});
