<?php 

Route::any('/signup', [
	'as' => 'signup',
	'uses' => 'UserController@signup'
]);