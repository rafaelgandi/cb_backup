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
			'title' => 'AuctionApp',
			'CB_PAGE_JS' => [],
			'CB_PAGE_CSS' => []
		]);
    }
	
	public function signup(Request $request) {
		if (Auth::check()) { // If the user is already logged in then redirect to landing page. 	
			return redirect($this->landingPage());
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
		view()->share([
			'title' => 'Sign Up',
			'CB_PAGE_JS' => [
				url('/js/mods/Cb.Notify.js')
			]
		]);
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
					if ($checks['cpassword'] === false && trim($p['password']) !== '') { $err_msg = 'Password mismatch.'; }	
					throw new Exception($err_msg);
				}
				if (App\Cb\Users::emailExists($p['email'])) { throw new Exception('Sorry the email address your provided is already registered in our system.'); }
				if (isset($p['is_agent']) && intval($p['is_agent']) === 1) {
					if (isset($_FILES['company_logo']['name']) && trim($_FILES['company_logo']['name']) !== '') {
						$uploaded_image_ext = App\Upload::getExtension($_FILES['company_logo']);
						// Check if file is a valid image //
						if (! in_array($uploaded_image_ext, config('cleverbons.files.allowed_images'))) {
							throw new Exception('Please upload a valid logo');
						}
						$has_uploaded_a_logo = true;	
					}				
				}	
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
						'logo' => '', // To be added later in App\Cb\Users\Company::saveLogo()
						'primary_color' => $p['company_color']
					]);
					if (! $company_details ) {
						throw new Exception('Unable to save your company details. Please check your connection and try again.');
					}
					if (isset($has_uploaded_a_logo)) {
						// Save the uploaded logo for his/her company //		
						if(! App\Cb\Users\Company::saveLogo($user_id, $_FILES['company_logo'])) {
							xplog('Unable to save logo file for user "'.$user_id.'"', __METHOD__);
						}
					}					
				}
				// Send confimation email here //
				$confirmation_sent = App\Cb\Notifications\Email::signUpConfirmation([
					'uid' => $user_id,
					'fname' => $p['fname'],
					'email' => $p['email']
				]);
				if (! $confirmation_sent) {
					xplog('Unable to send confirmation email for user "'.$user_id.'"');
					throw new Exception('Unable to send your confirmation email.');
				}
				// Send success message //
				$request->session()->flash('sys_message', [
					'message' => 'A verification email has been sent to '.$p['email'],
					'redirect' => ['Sign In' => route('login')]
				]);
				return redirect(route('sys_message'));				
			}
			catch (Exception $err) {
				cb_set_message($err->getMessage(), 0);
			}		
		}
		$data['post'] = $p;
		return View::make('user_signup', $data)->render();
	}
	
	public function myAccount(Request $request, $uid) {
		if (! Auth::check()) { return redirect(route('logout')); } // Make sure user is already logged in
		$uid = intval(App\Crypt::urldecode($uid));
		if ($uid < 1) { abort(404); } // Redirect to 404 page if user id is unknown
		$user_details = App\Cb\Users::getDetailsById($uid);
		if (! $user_details) { abort(404); } // Make sure user details is available
		$p = [
			'fname' => $user_details->fname, 
			'lname' => $user_details->lname,
			'email' => $user_details->email,
			'phone' => $user_details->phone,
			'cell' => $user_details->cellphone,
			'company_name' => '',
			'company_street' => '',
			'company_state' => '',
			'company_phone' => '',
			'company_abn' => '',
			'company_city' => '',
			'company_postcode' => '',
			'company_color' => ''
		];
		$company_details = App\Cb\Users\Company::getDetailsByUserId($user_details->id);
		if ($company_details) {
			$company_info = [
				'company_name' => $company_details->name,
				'company_street' => $company_details->street,
				'company_state' => $company_details->state,
				'company_phone' => $company_details->phone,
				'company_abn' => $company_details->abn,
				'company_city' => $company_details->city,
				'company_postcode' => $company_details->postcode,
				'company_color' => $company_details->primary_color,
				'company_logo_filename' => $company_details->logo
			];
			$p = array_merge($p, $company_info);
		}
		//_pr($company_details);
		$data = [];
		view()->share([
			'title' => 'My Account',
			'CB_PAGE_JS' => [
				url('/js/mods/Cb.Notify.js')
			],
			'CB_JS_TRANSPORT' => [
				'testing' => [1,2,3]
			]
		]);
		$data['aus_states'] = config('cleverbons.aus_states');
		if ($request->isMethod('post') && $request->has('submit')) {
			$p = $request->all();
			// See: https://github.com/Respect/Validation/blob/master/docs/VALIDATORS.md
			$checks = [];
			$checks['fname'] = Valid::string()->notEmpty()->validate($p['fname']);
			$checks['lname'] = Valid::string()->notEmpty()->validate($p['lname']);
			//$checks['email'] = Valid::email()->notEmpty()->validate($p['email']);			
			$checks['phone'] = Valid::string()->notEmpty()->validate($p['phone']);
			$checks['cell'] = Valid::string()->notEmpty()->validate($p['cell']);
			if (isset($p['company_name']) && trim($p['company_name']) !== '') {
				$checks['company_name'] = Valid::string()->notEmpty()->validate($p['company_name']);
				$checks['company_street'] = Valid::string()->notEmpty()->validate($p['company_street']);
				$checks['company_state'] = Valid::string()->notEmpty()->validate($p['company_state']);
				$checks['company_phone'] = Valid::string()->notEmpty()->validate($p['company_phone']);	
				$checks['company_abn'] = Valid::string()->notEmpty()->validate($p['company_abn']);	
				$checks['company_city'] = Valid::string()->notEmpty()->validate($p['company_city']);	
				$checks['company_postcode'] = Valid::string()->notEmpty()->validate($p['company_postcode']);	
				$checks['company_color'] = Valid::string()->notEmpty()->validate($p['company_color']);
			}
			try {
				if (in_array(false, $checks)) {
					throw new Exception('Some required field(s) have invalid values.');
				}
				if (trim($p['email']) !== $user_details->email) {
					if (App\Cb\Users::emailExists($p['email'])) { 
						throw new Exception('Sorry the email address your provided is already registered in our system.'); 
					}
				}
				if (isset($_FILES['company_logo']['name']) && trim($_FILES['company_logo']['name']) !== '') {
					$uploaded_image_ext = App\Upload::getExtension($_FILES['company_logo']);
					// Check if file is a valid image //
					if (! in_array($uploaded_image_ext, config('cleverbons.files.allowed_images'))) {
						throw new Exception('Please upload a valid logo.');
					}
					$has_uploaded_a_logo = true;	
				}
				// Update user details //
				$updated_user_details = App\Cb\Users::update($user_details->id, [
					'fname' => $p['fname'],
					'lname' => $p['lname'],
					'phone' => $p['phone'],
					'cellphone' => $p['cell']
				]);
				if (! $updated_user_details) {
					throw new Exception('Unable to save your details. Please reload your page and try again.');
				}
				// Update user company details //
				$updated_company_details = App\Cb\Users\Company::update($user_details->id, [
					'name' => $p['company_name'],
					'abn' => $p['company_abn'],
					'street' => $p['company_street'],
					'city' => $p['company_city'],
					'state' => $p['company_state'],
					'postcode' => $p['company_postcode'],
					'phone' => $p['company_phone'],
					'primary_color' => $p['company_color']
				]);
				if (! $updated_company_details) {
					throw new Exception('Unable to save your company details. Please reload your page and try again.');
				}
				// Update the user's logo file here //
				if (isset($has_uploaded_a_logo)) {
					// Save the uploaded logo for his/her company //
					$logo_filename = App\Cb\Users\Company::saveLogo($user_details->id, $_FILES['company_logo']);	
					if(! $logo_filename) {
						xplog('Unable to save logo file for user "'.$user_details->id.'"', __METHOD__);
					}
					$p['company_logo_filename'] = $logo_filename;
				}			
				// Successfully updated everything //
				cb_set_message('Successfully updated your details', 1);
			}
			catch (Exception $err) {
				cb_set_message($err->getMessage(), 0);
			}
			
		}	
		$data['logo_dir'] = App\Cb\Users\Company::getLogoDirBaseUri();
		$data['post'] = $p;
		return View::make('myaccount', $data)->render();
	}
}
