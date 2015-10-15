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

class AuthController extends WasabiBaseController {
	
    public function __construct() {
		
    }
	
	public function login(Request $request) {
		if (Auth::check()) { // If the user is already logged in then redirect to landing page. 	
			return redirect($this->landingPage());
		}
		$p = ['email' => '', 'password' => ''];
		$data = [];
		view()->share([
			'title' => 'Log In',
			'CB_PAGE_JS' => [
				url('/js/mods/Cb.Notify.js')
			]
		]);
		if ($request->isMethod('post') && $request->has('submit')) {
			$p = $request->all();
			// See: https://github.com/Respect/Validation/blob/master/docs/README.md
			$checks = [];
			$checks['email'] = Valid::email()->notEmpty()->validate($p['email']);
			$checks['password'] = Valid::string()->notEmpty()->validate($p['password']);
			try {
				if (in_array(false, $checks)) {
					throw new Exception('Some required field have invalid values');
				}
				$auth_response = App\Cb\Users::authenticate($p['email'], $p['password']);
				if (! is_object($auth_response)) {
					if (is_numeric($auth_response)) {
						// $auth_response <-- is user id in this context
						$resend_link = route('resend_signup_confirmation', [
							'uid' => App\Crypt::urlencode($auth_response)
						]);
						throw new Exception('Please verify your account. Click <a href="'.$resend_link.'">here</a> to resend the confirmation email');
					}
					throw new Exception('Invalid email or password');
				}
				// Successfully authenticated, save some details to session for faster access //
				$request->session()->put('current_user', $auth_response);
				$request->session()->put('current_user_type', $auth_response->type);
				return redirect($this->landingPage($auth_response->type));
			}
			catch (Exception $err) {
				cb_set_message($err->getMessage(), 0);
			}
		}
		$data['post'] = $p;
		return View::make('user_login', $data)->render();
	}
	
	public function resendSignUpConfirmation(Request $request, $uid) {
		if (Auth::check()) { return redirect($this->landingPage()); }
		$uid = intval(App\Crypt::urldecode($uid));
		if ($uid < 1) { abort(404); } // Redirect to 404 page if id is unknown
		$user_details = App\Cb\Users::getDetailsById($uid);
		if (! $user_details) { abort(404); }
		
		// Send confimation email here //
		$confirmation_sent = App\Cb\Notifications\Email::signUpConfirmation([
			'uid' => $user_details->id,
			'fname' => $user_details->fname,
			'email' => $user_details->email
		]);
		if (! $confirmation_sent) {
			xplog('Unable to send confirmation email for user "'.$user_details->id.'"');
			return redirect(url().'?00');
		}
		// Send success message //
		$request->session()->flash('sys_message', [
			'message' => 'A verification email has been sent to '.$user_details->email,
			'redirect' => ['Sign In' => route('login')]
		]);
		return redirect(route('sys_message'));	
	}
	
	public function signUpConfirmation(Request $request, $uid) {
		if (Auth::check()) { return redirect($this->landingPage()); } // If already logged then redirect to landing page
		$data = [];
		$uid = intval(App\Crypt::urldecode($uid));
		if ($uid < 1) { abort(404); } // Redirect to 404 page if id is unknown
		$user_details = App\Cb\Users::getDetailsById($uid);
		if (intval($user_details->status) === 1) {
			// If already confirmed then redirect to landing page //
			return redirect(url()); 
		}
		App\Cb\Users::confirmAccount($uid); // Confirm account here 
		// Send success message //
		$request->session()->flash('sys_message', [
			'message' => 'Successfully activated your account. You may now login by clicking the link below.',
			'redirect' => ['Sign In' => route('login')]
		]);
		return redirect(route('sys_message'));	
	}
	
	public function logout(Request $request) {
		$request->session()->flush(); // Remove all session data. See: http://laravel.com/docs/5.1/session
		Auth::logout();
		return redirect(url());
	}
	
}
