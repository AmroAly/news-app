<?php



// Auth Routes
Route::get('/', 'PostController@index');
Route::post('/check', 'AuthController@checkApiToken');
Route::post('/register', 'AuthController@register');
Route::get('/register/confirm/{token}', 'AuthController@confirmEmail');
Route::post('/login', 'AuthController@login');
Route::post('/logout', 'AuthController@logout');

// News Routes
Route::get('/news', 'PostController@index');
Route::get('/news/{id}', 'PostController@show');
Route::get('/users/{user_id}/news', 'PostController@postsByUser');
Route::post('/news/create', 'PostController@store');
Route::delete('/news/{id}', 'PostController@destroy');
Route::get('/pdf/{id}', 'PostController@generatePdf');
