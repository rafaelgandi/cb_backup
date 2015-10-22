<?php 


Route::any('/add-property', [
	'as' => 'add_property',
	'uses' => 'PropertiesController@addProperty'
]);

Route::any('/update-property/{pid}', [
	'as' => 'update_property',
	'uses' => 'PropertiesController@updateProperty'
]);

Route::any('/my-properties/{pid}', [
	'as' => 'my_properties',
	'uses' => 'PropertiesController@listMyProperty'
]);