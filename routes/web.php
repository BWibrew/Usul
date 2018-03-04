<?php

Auth::routes();

Route::get('/', 'HomeController')->name('home');

Route::resource('sites', 'SiteController');
