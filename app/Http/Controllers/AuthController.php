<?php
namespace App\Http\Controllers;

use App;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Respect\Validation\Validator as Valid;
use View;
use Auth;

class AuthController extends WasabiBaseController {
	
    public function __construct() {
		
    }
	
	public function login(Request $request) {
		
	}
	
	public function logout(Request $request) {
		$request->session()->flush(); // Remove all session data. See: http://laravel.com/docs/5.1/session
		Auth::logout();
		return redirect(url());
	}
}
