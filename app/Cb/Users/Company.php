<?php 
namespace App\Cb\Users;

use App;
use App\Cb\Users;
use Exception;
use DB; // See: http://laravel.com/docs/5.1/database
use PubSub; // See: http://baylorrae.com/php-pubsub/

class Company extends App\Cb\Base {
	public static function instance() { return parent::getInstance(); }
	private $logo_dir = '';
	private $logo_dir_baseuri = '';
	
	protected function __construct() {
		$dir = 'uploads/company_logos';
		$this->logo_dir = App\Files::makeDirIfNotExists(public_path($dir));
		$this->logo_dir_baseuri = url('/'.$dir);
	}
	
	protected function getLogoDirBaseUri() {
		return $this->logo_dir_baseuri;
	}

	protected function add($_user_id, $_params=[]) {
		$uid = intval($_user_id);
		if ($uid <= 0) {
			xplog('Invalid user id given', __METHOD__);
			return false;
		}
		$p = array_merge([
			'name' => '',
			'abn' => '',
			'street' => '',
			'city' => '',
			'state' => '',
			'postcode' => '',
			'phone' => '',
			'logo' => '',
			'primary_color' => ''
		], $_params);
		$res = DB::table('user_company_details')->insert([
			'users_id' => $uid,
			'name' => trim($p['name']),
			'abn' => trim($p['abn']),
			'street' => trim($p['street']),
			'city' => trim($p['city']),
			'state' => trim($p['state']),
			'postcode' => trim($p['postcode']),
			'phone' => trim($p['phone']),
			'logo' => trim($p['logo']),
			'primary_color' => trim($p['primary_color'])
		]);
		// See: http://stackoverflow.com/questions/18424122/how-to-know-if-a-query-fails-in-laravel-4
		if ($res === false) { 
			xplog('Unable to add company details for user "'.$uid.'"', __METHOD__); 
			return false;
		}
		return true;
	}
	
	protected function saveLogo($_user_id, $_file, $_base64_data=false) {
		$uid = intval($_user_id);
		if ($uid <= 0) {
			xplog('Invalid user id given', __METHOD__);
			return false;
		}
		$user_company_details = $this->getDetailsByUserId($uid);
		// If logo already exists then delete the file first //
		if (!! $user_company_details && trim($user_company_details->logo) !== '') {
			if (App\Files::isFile($this->logo_dir.'/'.$user_company_details->logo)) {
				App\Cb\Files::deleteAllInstance($this->logo_dir.'/'.$user_company_details->logo);
			}
		}
		if (! $_base64_data) {
			$uploaded_image_details = App\Upload::save($_file, [
				'destination' => $this->logo_dir.'/'
			]);			
		}
		else {
			$uploaded_image_details = App\Upload::saveBase64($_file->base64, [
				'destination' => $this->logo_dir.'/',
				'extension' => $_file->extension
			]);
		}
		if (! is_object($uploaded_image_details)) {
			xplog('Unable to save logo', __METHOD__);
			return false;
		}
		// Save in the database //
		$res = DB::table('user_company_details')->where('users_id', $uid)->update([
			'logo' => $uploaded_image_details->coded_name
		]);
		if ($res === false) {
			xplog('Unable to save logo in the database', __METHOD__);
			return false;
		}
		return $uploaded_image_details->coded_name;
	}
	
	protected function getDetailsByUserId($_user_id) {
		$uid = intval($_user_id);
		if ($uid <= 0) {
			xplog('Invalid user id given', __METHOD__);
			return false;
		}
		$res = DB::table('user_company_details')
		->where('users_id', $uid)
		->first();
		if (! $res) { return false; }
		return $res;
	}
	
	protected function update($_user_id, $_data=[]) {
		$uid = intval($_user_id);
		if ($uid < 1) { return false; }
		$row = DB::table('user_company_details')->where('users_id', $uid)->update($_data);
		if (! is_numeric($row)) { 
			xplog('Unable to update user_details table for user "'.$uid.'"', __METHOD__); 
			return false; 
		}
		return true;
	}
}