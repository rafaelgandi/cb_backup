<?php 
Route::group(['prefix' => 'api'], function () {
	
	
	
	// Check API here //
	Route::any('/testing', function () {
		// See: http://davidwalsh.name/curl-post
		//set POST variables
		$fields = array(
			'pass_key' => config('api.passkey'), // required
			'api_name' => 'user_authenticate',
			'email' => 'rafael@sushidigital.com.au',
			'password' => '123456'
		);
		echo '<!DOCTYPE html><html><body><form action="'.url('/api/v1/ae605b5ab5a60d46a5a7a30409dabb72.json').'" method="post" id="f">';	
		foreach ($fields as $key=>$value) {
			if (is_array($value)) {
				foreach ($value as $k=>$v) { echo '<input type="hidden" name="'.$k.'" id="uid" value="'.$v.'">';}
			}
			else { echo '<input type="hidden" name="'.$key.'" id="uid" value="'.$value.'">'; }		
		}	
		echo '<input type="hidden" name="_token" id="token" value="'.csrf_token().'">';	
		echo '<script>document.getElementById("f").submit();</script></form></body></html>';	
	});	
	
	// Signup test
	Route::any('/test/signup', function () {
		// See: http://davidwalsh.name/curl-post
		//set POST variables
		$fields = array(
			'pass_key' => config('api.passkey'), // required
			'api_name' => 'add_user',
			
			'fname' => 'api10', 
			'lname' => 'api10',
			'email' => 'api10@sushidigital.com.au',
			'password' => '123456',
			'cpassword' => '123456',
			'phone' => '123458788979',
			'cell' => '',
			'is_agent' => '0',
			'company_name' => 'api company',
			'company_street' => 'asdfsaf',
			'company_state' => 'WA',
			'company_phone' => '32323232',
			'company_abn' => '65656565',
			'company_city' => '',
			'company_postcode' => '',
			'company_color' => '',
			'terms' => '1',
			// See: http://www.opinionatedgeek.com/dotnet/tools/Base64Encode/
			'company_logo' => App\Json::encode([
				'base64' => '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',
				'extension' => 'jpg'
			])
		);
		echo '<!DOCTYPE html><html><body><form action="'.url('/api/v1/ae605b5ab5a60d46a5a7a30409dabb72.json').'" method="post" id="f">';	
		foreach ($fields as $key=>$value) {
			if (is_array($value)) {
				foreach ($value as $k=>$v) { echo "<input type='hidden' name='".$k."' value='".$v."'>"; }
			}
			else { echo "<input type='hidden' name='".$key."' value='".$value."'>"; }		
		}	
		echo '<input type="hidden" name="_token" id="token" value="'.csrf_token().'">';	
		echo '<script>document.getElementById("f").submit();</script></form></body></html>';	
	});	
	
});
