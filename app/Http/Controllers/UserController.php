<?php
namespace App\Http\Controllers;

use App;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Respect\Validation\Validator as Valid;
use Exception;
use View;
use Auth;

class UserController extends WasabiBaseController {
	
    public function __construct() {
		//$current_user_type = (session()->has('current_user_type')) ? session('current_user_type') : false;
		view()->share([
			'title' => 'Ink In Time',
			'CB_PAGE_JS' => [],
			'CB_PAGE_CSS' => []
		]);
    }
	
	public function signup(Request $request) {
		if (Auth::check()) { // If the user is already logged in then redirect to landing page. 	
			//return redirect();
		}
		$p = [
			'fname' => '', 
			'lname' => '',
			'email' => '',
			'password' => '',
			'cpassword' => '',
			'phone' => '',
			'cell' => '',
			'is_agent' => '0',
			'company_name' => '',
			'company_street' => '',
			'company_state' => 'ACT',
			'company_phone' => '',
			'company_abn' => '',
			'company_city' => '',
			'company_postcode' => '',
			'company_color' => '',
			'terms' => '1'
		];
		$data = [];
		view()->share(['title' => 'Sign Up']);
		$data['aus_states'] = config('cleverbons.aus_states');
		if ($request->isMethod('post') && $request->has('submit')) {
			$p = $request->all();
			// See: https://github.com/Respect/Validation/blob/master/docs/README.md
			// See: https://github.com/Respect/Validation/blob/master/docs/VALIDATORS.md
			$checks = [];
			$checks['fname'] = Valid::string()->notEmpty()->validate($p['fname']);
			$checks['lname'] = Valid::string()->notEmpty()->validate($p['lname']);
			$checks['email'] = Valid::email()->notEmpty()->validate($p['email']);
			$checks['password'] = Valid::string()->notEmpty()->validate($p['password']);
			$checks['cpassword'] = Valid::string()->notEmpty()->equals($p['password'])->validate($p['cpassword']); // Check password confirmation			
			$checks['phone'] = Valid::string()->notEmpty()->validate($p['phone']);
			$checks['cell'] = Valid::string()->notEmpty()->validate($p['cell']);
			if (isset($p['is_agent']) && intval($p['is_agent']) === 1) {
				$checks['company_name'] = Valid::string()->notEmpty()->validate($p['company_name']);
				$checks['company_street'] = Valid::string()->notEmpty()->validate($p['company_street']);
				$checks['company_state'] = Valid::string()->notEmpty()->validate($p['company_state']);
				$checks['company_phone'] = Valid::string()->notEmpty()->validate($p['company_phone']);	
				$checks['company_abn'] = Valid::string()->notEmpty()->validate($p['company_abn']);	
				$checks['company_city'] = Valid::string()->notEmpty()->validate($p['company_city']);	
				$checks['company_postcode'] = Valid::string()->notEmpty()->validate($p['company_postcode']);	
				$checks['company_color'] = Valid::string()->notEmpty()->validate($p['company_color']);		
			}
			$checks['terms'] = isset($p['terms']);
			try {
				if (in_array(false, $checks)) {
					$err_msg = 'Some required field(s) have invalid values.';
					if ($checks['terms'] === false) { $err_msg = 'You did not agree to the terms and conditions.'; }
					if ($checks['cpassword'] === false) { $err_msg = 'Password mismatch.'; }	
					throw new Exception($err_msg);
				}
				if (App\Cb\Users::emailExists($p['email'])) { throw new Exception('Sorry the email address your provided is already registered in our system.'); }
				$user_id = App\Cb\Users::add([
					'email' => $p['email'],
					'password' => $p['password'],
					'fname' => $p['fname'],
					'lname' => $p['lname'],
					'phone' => $p['phone'],
					'cellphone' => $p['cell']
				]);
				if (! $user_id ) {
					throw new Exception('Unable to save your details. Please check your connection and try again.');
				}
				if (isset($p['is_agent']) && intval($p['is_agent']) === 1) {
					// Save the company details first //
					$company_details = App\Cb\Users\Company::add($user_id, [
						'name' => $p['company_name'],
						'abn' => $p['company_abn'],
						'street' => $p['company_street'],
						'city' => $p['company_city'],
						'state' => $p['company_state'],
						'postcode' => $p['company_postcode'],
						'phone' => $p['company_phone'],
						'logo' => '', /* @BOOKMARK: TODO add the logo filename here */
						'primary_color' => $p['company_color']
					]);
					if (! $user_id ) {
						throw new Exception('Unable to save your company details. Please check your connection and try again.');
					}
					$uploaded_image_ext = App\Upload::getExtension($_FILES['company_logo']);
					// Checko if file is a valid image //
					if (! isset($_FILES['company_logo']) || ! in_array($uploaded_image_ext, config('cleverbons.files.allowed_images'))) {
						throw new Exception('Please upload a valid logo');
					}				
					$logo_saved = App\Cb\Users\Company::saveLogo($user_id, $_FILES['company_logo']);
				}
				/* @BOOKMARK: TODO more stuff here */
				_pr($p);
				
				
				
			}
			catch (Exception $err) {
				$data['cb_err_msg'] = $err->getMessage();
			}		
		}
		$data['post'] = $p;
		return View::make('user_signup', $data)->render();
	}
}
