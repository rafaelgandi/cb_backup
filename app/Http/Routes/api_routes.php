<?php 
/* 
	API sepcific routes only
 */
Route::group(['prefix' => 'api'], function () {	
	Route::any('/v1/ae605b5ab5a60d46a5a7a30409dabb72.json', [
		'as' => 'api',
		'uses' => 'ApiController@serve'
	]);
	
	Route::any('/instructions', function () {
		return response(App\Files::get(app_path('Http/api_instructions.txt')))->header('Content-Type', 'text/plain');
	});
});
