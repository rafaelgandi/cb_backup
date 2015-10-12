<?php 

Route::any('/login', [
	'as' => 'login',
	'uses' => 'AuthController@login'
]);

Route::get('/logout', [
	'as' => 'logout',
	'uses' => 'AuthController@logout'
]);