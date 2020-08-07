<?php

Route::get('/', function () {
    return redirect('/blog');
});


Route::get('/blog', 'BlogController@index')->name('blog.home');
Route::get('/blog/{slug}', 'BlogController@showPost')->name('blog.detail');


Route::get('/admin', function(){
    return redirect('/admin/post');
});

Route::middleware('auth')->namespace('Admin')->group(function(){
    Route::resource('admin/index', 'PostController');
    Route::resource('admin/post', 'PostController');
    Route::resource('admin/tag', 'TagController');
    Route::get('admin/upload', 'UploadController@index');
});

Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
