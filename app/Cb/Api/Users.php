<?php 
namespace App\Cb\Api;
use App;
use App\Cb\Api;
use Exception;
use DB; // See: http://laravel.com/docs/5.1/database
use PubSub; // See: http://baylorrae.com/php-pubsub/
use Respect\Validation\Validator as Valid;

class Users extends App\Cb\Base {
	public static function instance() { return parent::getInstance(); }
	
	/*//////////////////////////////////////////////////////////////////////////////// 
		API: add_user
		@param: fname <required>
		@param: lname <required>
		@param: email <required>
		@param: password <required>
		@param: cpassword <required>
		@param: phone <required>
		@param: cell
		@param: is_agent <defaults to '0'> <required>
		@param: company_name <required>
		@param: company_street
		@param: company_state
		@param: company_phone <required>
		@param: company_abn <required>
		@param: company_city
		@param: company_postcode
		@param: company_color
		@param: company_logo
		@param: terms <defaults to '0'> <required>
		@return: payload <object> - User details
 	*/////////////////////////////////////////////////////////////////////////////////
	protected function addUser($_post) {
		$p = $_post;
		$defaults = [
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
			'terms' => '0'
		];
		// Required user details //
		App\Cb\Api::req($p, ['email', 'password', 'cpassword', 'fname', 'lname', 'phone', 'terms']);
		$has_company = ((isset($p['company_name']) && trim($p['company_name']) !== '') || (isset($p['is_agent']) && intval($p['is_agent'])));
		if ($has_company) {
			// Required company details //
			App\Cb\Api::req($p, ['company_name','company_abn','company_phone']);
		}
		$p = array_merge($defaults, $p);
		// See: https://github.com/Respect/Validation/blob/master/docs/VALIDATORS.md
		$checks = [];
		$checks['fname'] = Valid::string()->notEmpty()->validate($p['fname']);
		$checks['lname'] = Valid::string()->notEmpty()->validate($p['lname']);
		$checks['email'] = Valid::email()->notEmpty()->validate($p['email']);
		$checks['password'] = Valid::string()->notEmpty()->validate($p['password']);
		$checks['cpassword'] = Valid::string()->notEmpty()->equals($p['password'])->validate($p['cpassword']); // Check password confirmation			
		$checks['phone'] = Valid::string()->notEmpty()->validate($p['phone']);
		$checks['terms'] = isset($p['terms']) && intval($p['terms']);
		if (in_array(false, $checks)) {
			$err_msg = 'Some required field(s) have invalid values.';
			if ($checks['terms'] === false) { $err_msg = 'Terms and conditions need to be set'; }
			if ($checks['cpassword'] === false && trim($p['password']) !== '') { $err_msg = 'Password mismatch.'; }	
			App\Cb\Api::error($err_msg);
		}
		if (App\Cb\Users::emailExists($p['email'])) { App\Cb\Api::error('Sorry the email address your provided is already registered in our system.'); }
		// Check the logo file passed //
		if (isset($p['company_logo'])) {
			if (App\Json::isValid($p['company_logo'])) {
				$logo_details = (object) App\Json::decode($p['company_logo']);
				if (isset($logo_details->base64)) {
					// Check if file is a valid image //
					if (! in_array($logo_details->extension, config('cleverbons.files.allowed_images'))) {
						App\Cb\Api::error('Please upload a valid logo');
					}
					$has_uploaded_a_logo = true;	
				}
			}
			else {
				xplog('Invalid JSON string passed |'.$p['company_logo'].'|', __METHOD__);
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
		if (! $user_id ) { App\Cb\Api::error('Unable to save details.'); }
		if ($has_company) {
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
			if (! $company_details ) { App\Cb\Api::error('Unable to save company details'); }
			if (isset($has_uploaded_a_logo)) {		
				// Save the uploaded logo for his/her company //
				// See: http://www.opinionatedgeek.com/dotnet/tools/Base64Encode/	
				if(! App\Cb\Users\Company::saveLogo($user_id, $logo_details, true)) {
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
			App\Cb\Api::error('Unable to send your confirmation email.');
		}
		$user_details = App\Cb\Users::getDetailsById($user_id);
		$res = [
			'api_name' => $_post['api_name'],
			'payload' => [
				'user_details' => $user_details
			]
		];
		if ($has_company) {
			$company_details = App\Cb\Users\Company::getDetailsByUserId($user_id);
			if (! $company_details) {
				xplog('Unable to find company details for user "'.$user_id.'"', __METHOD__);
			}
			$res['payload']['company_details'] = $company_details;
		}
		return $res;
	}
	
	/*//////////////////////////////////////////////////////////////////////////////// 
		API: update_user
		@param: user_id <required>
		@param: fname <required>
		@param: lname <required>
		@param: email <required>
		@param: phone <required>
		@param: cell <required>
		@param: company_name <required>
		@param: company_street <required>
		@param: company_state <required>
		@param: company_phone <required>
		@param: company_abn <required>
		@param: company_city <required>
		@param: company_postcode <required>
		@param: company_color <required>
		@param: company_logo
		@return: payload <object> - User details
 	*/////////////////////////////////////////////////////////////////////////////////
	protected function updateUser($_post) {
		$p = $_post;
		// Fields that must be set //
		App\Cb\Api::mustSet($p, [
			'user_id', 'fname', 'lname', 'phone', 'cell', 'company_name', 
			'company_street', 'company_state', 'company_phone', 'company_abn', 'company_city', 
			'company_postcode', 'company_color'
		]);
		$uid = intval($p['user_id']);
		if ($uid < 1) { App\Cb\Api::error('Invalid user id sent'); }
		$user_details = App\Cb\Users::getDetailsById($uid);
		if (! $user_details) { App\Cb\Api::error('Unable to find user details.'); }
		// See: https://github.com/Respect/Validation/blob/master/docs/VALIDATORS.md
		$checks = [];
		$checks['fname'] = Valid::string()->notEmpty()->validate($p['fname']);
		$checks['lname'] = Valid::string()->notEmpty()->validate($p['lname']);				
		if (in_array(false, $checks)) { App\Cb\Api::error('Some required field(s) have invalid values.'); }
		// Check the logo file passed //
		if (isset($p['company_logo'])) {
			if (App\Json::isValid($p['company_logo'])) {
				// See: http://www.opinionatedgeek.com/dotnet/tools/Base64Encode/	
				$logo_details = (object) App\Json::decode($p['company_logo']);
				if (isset($logo_details->base64)) {
					// Check if file is a valid image //
					if (! in_array($logo_details->extension, config('cleverbons.files.allowed_images'))) {
						App\Cb\Api::error('Please upload a valid logo');
					}
					$has_uploaded_a_logo = true;	
				}
			}
			else {
				xplog('Invalid JSON string passed |'.$p['company_logo'].'|', __METHOD__);
			}
		}
		// Update user details //
		$updated_user_details = App\Cb\Users::update($user_details->id, [
			'fname' => $p['fname'],
			'lname' => $p['lname'],
			'phone' => $p['phone'],
			'cellphone' => $p['cell']
		]);
		if (! $updated_user_details) { App\Cb\Api::error('Unable to save user details'); }		
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
		if (! $updated_company_details) { App\Cb\Api::error('Unable to save company details'); }
		if (isset($has_uploaded_a_logo)) {
			// Save the uploaded logo for his/her company //
			// See: http://www.opinionatedgeek.com/dotnet/tools/Base64Encode/	
			if(! App\Cb\Users\Company::saveLogo($user_details->id, $logo_details, true)) {
				xplog('Unable to save logo file for user "'.$uid.'"', __METHOD__);
			}
		}
		// Requery the newly updated user details //
		$user_details = App\Cb\Users::getDetailsById($user_details->id);
		$res = [
			'api_name' => $_post['api_name'],
			'payload' => [
				'user_details' => $user_details
			]
		];
		$user_company_details = App\Cb\Users\Company::getDetailsByUserId($user_details->id);
		if (!! $user_company_details) {
			$res['payload']['company_details'] = $user_company_details;
		}
		return $res;
	}
}