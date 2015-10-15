<?php
namespace App\Http\Controllers;

use App;
use App\Ink;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Respect\Validation\Validator as Valid;
use View;
use Auth;
use Exception;

class WasabiBaseController extends Controller {
	
    public function __construct() {
		
    }
	
	protected function landingPage($_user_type=false) {
		if ($_user_type === false) {
			$_user_type = 0;
			// Try to use the current logged in user(if any) details //
			if (session()->has('current_user_type')) {
				$_user_type = session()->get('current_user_type');
			}
		}
		$user_type = intval($_user_type);
		if ($user_type === 1) { // Admin user
			// TODO: add admin landing uri
			return url();
		}
		else if ($user_type === 0) { // Normal user
			// TODO: change this to real landing page
			return route('my_account', [
				'uid' => App\Crypt::urlencode(session()->get('current_user')->id)
			]);
		}
		else { return url(); } // If all else fails.. redirect to home page
	}
}
