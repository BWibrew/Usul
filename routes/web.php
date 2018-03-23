<?php

Auth::routes();

Route::get('/', 'HomeController')->name('home');

Route::resource('sites', 'SiteController');
Route::get('sites/{site}/auth', 'SiteAuthController@showAuthSettings')->name('sites.auth');
