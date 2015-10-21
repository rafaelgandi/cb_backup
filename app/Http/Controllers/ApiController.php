<?php
namespace App\Http\Controllers;

use App;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Respect\Validation\Validator as Valid;
use View;
use Auth;
use Exception;

class ApiController extends WasabiBaseController {	
    public function __construct() {}
	public function serve(Request $request) {
		$p = [];
		$response = [];
		try { // See: http://www.w3schools.com/php/php_exception.asp
			if ($request->isMethod('post')) {
				$p = $request->all();
				App\Cb\Api::setPost($p)->authenticate(); // Make sure required data is passed to the api
				$api_map = [
					// Add the necessary api maps here //
					'check' => 'App\Cb\Api::check',
					'user_authenticate' => 'App\Cb\Api::userAuthenticate',
					'logout' => 'App\Cb\Api::logout',
					'add_user' => 'App\Cb\Api\Users::addUser',
					'update_user' => 'App\Cb\Api\Users::updateUser',
				];
				if (! isset($api_map[$p['api_name']])) {
					throw new App\Cb\Api\Exceptyon('API "'.$p['api_name'].'" is unknown');
				}
				$response = App\Cb\Api::run($api_map[$p['api_name']]);
				return response()->json($response);
			}
			else { throw new App\Cb\Api\Exceptyon('Please use POST method in accessing the api'); }
		}
		catch (App\Cb\Api\Exceptyon $err) {
			$response['error'] = $err->getMessage();
			if (isset($p['api_name'])) {
				$response['api_name'] = $p['api_name'];
			}
			return response()->json($response);
		}		
	}
}
