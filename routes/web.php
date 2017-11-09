<?php

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', function () {
    return redirect(route('sites.index'));
})->name('home');

Route::resource('sites', 'SiteController');
