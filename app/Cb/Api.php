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
	
	/*//////////////////////////////////////////////////////////////////////////////// 
		API: get_user_auth_details
		@param:  email
		@param:  password
		@return: {"success":<array>}
	*/////////////////////////////////////////////////////////////////////////////////
	protected function getUserAuthDetails($_post) {
		return [
			'payload' => $_post['api_name']
		];
	}
}