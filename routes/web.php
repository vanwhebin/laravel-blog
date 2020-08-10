<?php

Route::get('/', function () {
    // return redirect('/blog');
    return view('welcome');
});



Route::get('/admin', function(){
    return redirect('/admin/post');
});

Route::middleware('auth')->namespace('Admin')->group(function(){
    Route::resource('admin/index', 'PostController');
    Route::resource('admin/post', 'PostController', ['except'=> 'show']);
    Route::resource('admin/tag', 'TagController');
    Route::get('admin/upload', 'UploadController@index');
    Route::post('admin/upload/folder', 'UploadController@createFolder');
    Route::delete('admin/upload/folder', 'UploadController@deleteFolder');
    Route::post('admin/upload/file', 'UploadController@uploadFile');
    Route::delete('admin/upload/file', 'UploadController@deleteFile');
});


Route::get('/blog', 'BlogController@index')->name('blog.home');
Route::get('/blog/{slug}', 'BlogController@showPost')->name('blog.detail');
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
Route::get('contact', 'ContactController@showForm');
Route::post('contact', 'ContactController@sendContactInfo');
// 在如下这行之后
Route::get('rss', 'BlogController@rss');
// 添加新的路由
Route::get('sitemap.xml', 'BlogController@siteMap');
