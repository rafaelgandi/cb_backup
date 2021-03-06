<?php 
namespace App\Cb;

use App;
use App\Cb;
use Exception;
use DB; // See: http://laravel.com/docs/5.1/database
use PubSub; // See: http://baylorrae.com/php-pubsub/

class Properties extends App\Cb\Base {
	public static function instance() { return parent::getInstance(); }
	
	protected function getTypes() {
		return ['Town House', 'Building', 'Apartment', 'Condo', 'Payag'];
	}
	
	protected function add($_user_id, $_params=[]) {
		$uid = intval($_user_id);
		if ($uid < 1) { return false; }
		$p = array_merge([
			'users_id' => $uid,
			'short_desc' => '',
			'description' => '',
			'street' => '',			
			'city' => '',
			'state' => '',
			'postcode' => '',
			'lat' => '',
			'lng' => '',
			'num_bedrooms' => '0',
			'num_bathrooms' => '0',
			'num_garage' => '0',
			'landarea' => '',
			'floorarea' => '',
			'type' => ''
		], $_params);
		$property_id = DB::table('properties')->insertGetId([
			'users_id' => $uid,
			'short_desc' => strip_tags(trim($p['short_desc'])),
			'description' => strip_tags(trim($p['description'])),
			'street' => $p['street'],			
			'city' => $p['city'],
			'state' => $p['state'],
			'postcode' => $p['postcode'],
			'lat' => $p['lat'],
			'lng' => $p['lng'],
			'num_bedrooms' => intval($p['num_bedrooms']),
			'num_bathrooms' => intval($p['num_bathrooms']),
			'num_garage' => intval($p['num_garage']),
			'landarea' => $p['landarea'],
			'floorarea' => $p['floorarea'],
			'type' => $p['type']
		]);
		if (! $property_id) {
			xplog('Unable to add property for user"'.$uid.'"', __METHOD__);
			return false;
		}
		return $property_id;
	}
	
	protected function getListByUserId($_user_id, $_filters=[]) {
		$uid = intval($_user_id);
		if ($uid < 1) { return false; }	
		$num_per_page = config('cleverbons.num_per_page');
		$query = DB::table('properties')
		->where('users_id', $uid)
		->orderBy('id', 'desc');
		if (isset($_filters['start_at'])) { // Usually used for pagination on API calls
			// See: http://laravel.com/docs/5.1/queries#ordering-grouping-limit-and-offset
			$query
			->skip($_filters['start_at'])
			->take($num_per_page);
			$res = $query->get();
		}
		else {
			// See: http://laravel.com/docs/5.1/pagination
			$res = $query->paginate($num_per_page);
		}	
		if (!! $res) { return $res; }
		return false;
	}
}