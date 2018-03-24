<?php

Auth::routes();

Route::get('/', 'HomeController')->name('home');

Route::resource('sites', 'SiteController');
Route::get('sites/{site}/auth', 'SiteAuthController@showAuthSettings')->name('sites.editAuth');
Route::patch('sites/{site}/auth', 'SiteAuthController@authenticate')->name('sites.auth');
