<?php 
namespace App\Cb;

use App;
use App\Cb;
use Exception;
use DB; // See: http://laravel.com/docs/5.1/database
use PubSub; // See: http://baylorrae.com/php-pubsub/
use Auth;

class Users extends App\Cb\Base {
	public static function instance() { return parent::getInstance(); }
	
	protected function getDetailsById($_user_id) {
		$uid = intval($_user_id);
		if ($uid < 1) { return false; }
		$res = DB::table('users')	
		->select('users.*', 'user_details.*')
		->join('user_details', function ($join) {
			$join->on('users.id', '=', 'user_details.users_id');
		})
		->where('id', $uid)
		->first();
		if ($res) {
			return $res;
		}
		return false;
	}
	
	protected function emailExists($_email) {
		$e = trim($_email);
		$res = DB::table('users')->where('email', $e)->first();
		return !! $res;
	}
	
	protected function confirmAccount($_user_id) {
		$uid = intval($_user_id);
		if ($uid < 1) { return false; }
		$row = DB::table('users')->where('id', $uid)->update([
			'status' => 1
		]);
		if (! $row) {
			xplog('Unable to confirm account for user "'.$uid.'"', __METHOD__);
		}
		return true;
	}
	
	protected function authenticate($_email, $_password, $_checkonly=false) {
		$e = trim($_email);
		$pw = trim($_password);	
		$auth = Auth::attempt(['email' => $e, 'password' => $pw]);
		if (! $auth) { return false; }
		$curr_user_details = Auth::user();
		if (intval($curr_user_details->type) === 0) { // Normal user
			if (intval($curr_user_details->status) === 0) {
				$user_id = intval($curr_user_details->id);
				session()->flush();
				Auth::logout();
				// Returns the user id if user has not yet activated his/her account //
				return $user_id; 
			}
		}
		if (!! $_checkonly) { // Dont log in the user. Usually used with the api
			session()->flush();
			Auth::logout();
		}
		return $curr_user_details;
	}
	
	protected function add($_params=[]) {
		$p = array_merge([
			'status' => 0,
			'type' => 0, // Defaults to normal user
			'is_loggedin' => 0,
			'phone' => '',
			'cellphone' => '',
			'city' => '',
			'suburb' => '',
			'proof_income' => ''
		], $_params);
		$uid = DB::table('users')->insertGetId([
			'email' => trim($p['email']),
			'password' => bcrypt($p['password']),
			'type' => intval($p['type']),
			'is_loggedin' => intval($p['is_loggedin'])
		]);
		if (! $uid) {
			xplog('Unable to add user with email "'.trim($p['email']).'"', __METHOD__);
			return false;
		}
		$res = DB::table('user_details')->insert([
			'users_id' => $uid,
			'fname' => trim($p['fname']),
			'lname' => trim($p['lname']),
			'phone' => trim($p['phone']),
			'cellphone' => trim($p['cellphone']),
			'city' => trim($p['city']),
			'suburb' => trim($p['suburb']),
			'proof_income' => trim($p['proof_income'])
		]);
		// See: http://stackoverflow.com/questions/18424122/how-to-know-if-a-query-fails-in-laravel-4
		if ($res === false) { 
			xplog('Unable to add user details with id "'.$uid.'"', __METHOD__); 
			return false;
		}
		return $uid;
	}
	
	protected function update($_user_id, $_data=[]) {
		$uid = intval($_user_id);
		if ($uid < 1) { return false; }
		// We only update the user_details table here as the users table should only be
		// updated by the code not the user.
		$row = DB::table('user_details')->where('users_id', $uid)->update($_data);
		if (! is_numeric($row)) { 
			xplog('Unable to update user_details table for user "'.$uid.'"', __METHOD__); 
			return false; 
		}
		return true;
	}
}