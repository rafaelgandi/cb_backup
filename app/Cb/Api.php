<?php 
namespace App\Cb;

use App;
use App\Cb;
use Exception;
use DB; // See: http://laravel.com/docs/5.1/database
use PubSub; // See: http://baylorrae.com/php-pubsub/
use Respect\Validation\Validator as Valid;

class Api extends App\Cb\Base {
	public static function instance() { return parent::getInstance(); }
	private $post = [];
	private $pass_key;
	
	protected function __construct() {
		$this->pass_key = config('api.passkey');
	}
	
	protected function setPost($_post) {
		$this->post = $_post;
		return $this;
	}
	
	protected function run($_method) {
		// See: http://stackoverflow.com/questions/273169/how-do-i-dynamically-invoke-a-class-method-in-php
		return call_user_func($_method, $this->post); // (As of PHP 5.2.3)
	}
	
	protected function error($_msg) {
		throw new App\Cb\Api\Exceptyon($_msg);
	}
	
	protected function authenticate() {
		// Make sure the pass_key value is given //
		if (! isset($this->post['pass_key'])) { $this->error('No pass key given'); }
		$pass_key = trim($this->post['pass_key']);
		if ($pass_key !== $this->pass_key) { $this->error('Invalid pass key given'); }
		// Make sure the api_name value is given //
		if (! isset($this->post['api_name'])) { $this->error('No api name given'); }
		if (trim($this->post['api_name']) === '') { $this->error('No api name given'); }
		return true;
	}
	
	protected function req($_post=[], $_requried_keys=[], $_msg='Missing some required field(s).') {
		foreach ($_requried_keys as $key) {
			if (! isset($_post[$key]) || trim($_post[$key]) === '') {
				$this->error($_msg);
				return false;
			}
		}
		return $this;
	}
	
	protected function mustSet($_post=[], $_requried_keys=[], $_msg='Missing some required field(s).') {
		foreach ($_requried_keys as $key) {
			if (! isset($_post[$key])) {
				$this->error($_msg);
				return false;
			}
		}
		return $this;
	}
	
	/*//////////////////////////////////////////////////////////////////////////////// 
		API: user_authenticate
		@param:  email
		@param:  password
		@param:  token
		@param:  os (android | ios)
		@return: payload <object> - User details
 	*/////////////////////////////////////////////////////////////////////////////////
	protected function userAuthenticate($_post) {
		$p = $_post;
		$this->req($p, ['email', 'password', 'token', 'os']);
		$auth_response = App\Cb\Users::authenticate($p['email'], $p['password'], true);
		$uid;
		if (! is_object($auth_response)) {
			if (is_numeric($auth_response)) {
				// $auth_response <-- is user id in this context
				$resend_link = route('resend_signup_confirmation', [
					'uid' => App\Crypt::urlencode($auth_response)
				]);
				$uid = $auth_response;
			}
			else {
				$this->error('Invalid email or password');
			}	
		}
		else {
			$uid = $auth_response->id;
			App\Cb\Users\Presence::setOnline($uid); // Set presence as online
		}	
		// Save the token for this user //
		App\Cb\Devices::add($uid, $p['token'], $p['os']);
		xplog('Registered device token "'.$p['token'].'" for user "'.$uid.'" for os "'.$p['os'].'"', __METHOD__);
		$user_details = App\Cb\Users::getDetailsById($uid);
		if (! $user_details) { $this->error('Unable to find user details.'); }
		if (isset($resend_link)) { $user_details->resend_link = $resend_link; }
		return [
			'api_name' => $_post['api_name'],
			'payload' => $user_details
		];
	}
	
	/*//////////////////////////////////////////////////////////////////////////////// 
		API: logout
		@param:  user_id
		@param:  os (android | ios)
		@return: payload <number> (1=success)
 	*/////////////////////////////////////////////////////////////////////////////////
	protected function logout($_post) {
		$p = $_post;
		$this->req($p, ['user_id', 'os']);
		$uid = intval($p['user_id']);
		if ($uid < 1) { $this->error('Invalid user id sent'); }
		$user_details = App\Cb\Users::getDetailsById($uid);
		if (! $user_details) { $this->error('Unable to find user details.'); }
		if (! App\Cb\Devices::removeByUserId($uid, $p['os'])) {
			xplog('Unable to remove device token for user "'.$uid.'" for os "'.$p['os'].'"', __METHOD__);
		}
		// Set user presence to offline //
		App\Cb\Users\Presence::setOffline($uid); 
		return [
			'api_name' => $_post['api_name'],
			'payload' => 1
		];
	}
	
	
}