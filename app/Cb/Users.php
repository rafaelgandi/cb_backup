<?php 
namespace App\Cb;

use App;
use App\Cb;
use Exception;
use DB; // See: http://laravel.com/docs/5.1/database
use PubSub; // See: http://baylorrae.com/php-pubsub/

class Users extends App\Cb\Base {
	public static function instance() { return parent::getInstance(); }
	
	protected function getDetailsById($_user_id) {
		$uid = intval($_user_id);
		if ($uid < 1) { return false; }
		$res = DB::table('users')->where('id', $uid)->first();
		if ($res) { return $res; }
		return false;
	}
	
	protected function emailExists($_email) {
		$e = trim($_email);
		$res = DB::table('users')->where('email', $e)->first();
		return !! $res;
	}
}