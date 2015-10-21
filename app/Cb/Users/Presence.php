<?php 
namespace App\Cb\Users;

use App;
use App\Cb\Users;
use Exception;
use DB; // See: http://laravel.com/docs/5.1/database
use PubSub; // See: http://baylorrae.com/php-pubsub/

class Presence extends App\Cb\Base {
	public static function instance() { return parent::getInstance(); }
	protected function __construct() {}
	
	protected function setOnline($_user_id) {
		$uid = intval($_user_id);
		if ($uid < 1) { return false; }
		$row = DB::table('users')->where('id', $uid)->update([
			'is_loggedin' => 1
		]);
		return is_numeric($row);
	}
	
	protected function setOffline($_user_id) {
		$uid = intval($_user_id);
		if ($uid < 1) { return false; }
		$row = DB::table('users')->where('id', $uid)->update([
			'is_loggedin' => 0
		]);
		return is_numeric($row);
	}	
}