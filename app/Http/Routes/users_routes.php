<?php 

Route::any('/signup', [
	'as' => 'signup',
	'uses' => 'UserController@signup'
]);

Route::any('/my-account/{uid}', [
	'as' => 'my_account',
	'uses' => 'UserController@myAccount'
]);