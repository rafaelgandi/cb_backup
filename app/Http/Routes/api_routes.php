<?php 
/* 
	API sepcific routes only
 */
Route::group(['prefix' => 'api'], function () {
	
	Route::any('/v1/ae605b5ab5a60d46a5a7a30409dabb72.json', [
		'as' => 'api',
		'uses' => 'ApiController@serve'
	]);
	
	// Check API here //
	Route::any('/testing', function () {
		// See: http://davidwalsh.name/curl-post
		//set POST variables
		$fields = array(
			//'pass_key' => config('api.passkey'), // required
			'api_name' => 'check',
			'data' => [
				'foo' => 'bar'
			]
		);
		echo '<!DOCTYPE html><html><body><form action="'.url('/api/v1/ae605b5ab5a60d46a5a7a30409dabb72.json').'" method="post" id="f">';	
		foreach ($fields as $key=>$value) {
			if (is_array($value)) {
				foreach ($value as $k=>$v) { echo '<input type="hidden" name="'.$k.'" id="uid" value="'.$v.'">';}
			}
			else { echo '<input type="hidden" name="'.$key.'" id="uid" value="'.$value.'">'; }
			echo '<input type="hidden" name="_token" id="token" value="'.csrf_token().'">';	
		}	
		echo '<script>document.getElementById("f").submit();</script></form></body></html>';	
	});
	
	Route::any('/instructions', function () {
		return response(App\Files::get(app_path('Http/api_instructions.txt')))->header('Content-Type', 'text/plain');
	});
});
