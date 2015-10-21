<?php 
namespace App\Cb;

use App;
use App\Cb;
use Exception;
use DB; // See: http://laravel.com/docs/5.1/database
use PubSub; // See: http://baylorrae.com/php-pubsub/
use Auth;

class Devices extends App\Cb\Base {
	public static function instance() { return parent::getInstance(); }
	
	protected function add($_user_id, $_token, $_os='android') {
		$uid = intval($_user_id);
		$token = trim($_token);
		$os = trim(strtolower($_os));
		if ($uid < 1) { return false; }
		if ($token === '') {
			xplog('Empty token was passed', __METHOD__);
			return false;
		}
		$device_id = DB::table('device_tokens')->insertGetId([
			'users_id' => $uid,
			'token' => $token,
			'os' => $os
		]);
		if (! $device_id) {
			xplog('Unable to add token for user "'.$uid.'"', __METHOD__);
			return false;
		}
		return $device_id;
	}
	
	protected function removeByUserId($_user_id, $_os=false) {
		$uid = intval($_user_id);
		if ($uid < 1) { return false; }
		$query = DB::table('device_tokens')
		->where('users_id', $uid);
		if (!! $_os) {
			$query->where('os', strtolower(trim($_os)));
		}
		$row = $query->delete();
		return is_numeric($row);
	}
}