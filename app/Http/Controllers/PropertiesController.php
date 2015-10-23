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

class PropertiesController extends WasabiBaseController {
	
    public function __construct() {
		//$current_user_type = (session()->has('current_user_type')) ? session('current_user_type') : false;
		view()->share([
			'title' => 'AuctionApp',
			'CB_PAGE_JS' => [],
			'CB_PAGE_CSS' => []
		]);
    }
	
	public function addProperty(Request $request) {
		if (! Auth::check()) { return redirect(route('logout')); }
		if (! $request->session()->has('current_user')) { return redirect(route('logout')); }
		$current_user =  $request->session()->get('current_user');
		$data = [];
		view()->share([
			'title' => 'Add Property',
			'CB_PAGE_JS' => [
				url('/js/mods/Cb.Notify.js')
			]
		]);
		$p = [
			'property_street' => '',
			'property_state' => 'ACT',
			'property_city' => '',
			'property_postcode' => '',
			'property_phone' => '',
			'property_short_desc' => '',
			'property_description' => '',
			'property_type' => '',
			'property_bedrooms' => '0',
			'property_bathrooms' => '0',
			'property_landarea' => '',
			'property_floorarea' => '',
			'property_garage' => '0',
			'property_lat' => '00000',
			'property_lng' => '00000',
			'property_terms' => '1',
		];	
			
		$data['aus_states'] = config('cleverbons.aus_states');
		$data['property_types'] = App\Cb\Properties::getTypes();
		if ($request->isMethod('post') && $request->has('submit')) {
			$p = $request->all();
			// See: https://github.com/Respect/Validation/blob/master/docs/VALIDATORS.md
			$checks = [];
			$checks['property_street'] = Valid::string()->notEmpty()->validate($p['property_street']);
			$checks['property_state'] = Valid::string()->notEmpty()->validate($p['property_state']);
			$checks['property_city'] = Valid::string()->notEmpty()->validate($p['property_city']);
			$checks['property_postcode'] = Valid::string()->notEmpty()->validate($p['property_postcode']);
			$checks['property_phone'] = Valid::string()->notEmpty()->validate($p['property_phone']);
			$checks['property_short_desc'] = Valid::string()->notEmpty()->validate($p['property_short_desc']);
			$checks['property_description'] = Valid::string()->notEmpty()->validate($p['property_description']);
			$checks['property_type'] = Valid::string()->notEmpty()->validate($p['property_type']);
			$checks['property_bedrooms'] = Valid::int()->notEmpty()->validate($p['property_bedrooms']);
			$checks['property_bathrooms'] = Valid::int()->notEmpty()->validate($p['property_bathrooms']);
			$checks['property_landarea'] = Valid::string()->notEmpty()->validate($p['property_landarea']);
			$checks['property_floorarea'] = Valid::string()->notEmpty()->validate($p['property_floorarea']);
			$checks['property_garage'] = Valid::int()->notEmpty()->validate($p['property_garage']);
			$checks['property_lat'] = Valid::string()->notEmpty()->validate($p['property_lat']);
			$checks['property_lng'] = Valid::string()->notEmpty()->validate($p['property_lng']);
			$checks['property_terms'] = isset($p['property_terms']);
			try {
				if (in_array(false, $checks)) { throw new Exception('Some required field(s) have invalid values.'); }
				// Floorplan Files //
				if (isset($_FILES['property_floorplan_files']['name'])) {
					$floorplan_file_arr = App\Upload::reArrayFiles($_FILES['property_floorplan_files']);
					if (! App\Cb\Properties\Docs::isAllowed($floorplan_file_arr)) {
						throw new Exception('One or more of the floor plan files are supported');
					}	
				}
				// Property Images //
				if (isset($_FILES['property_images']['name'])) {
					$images_file_arr = App\Upload::reArrayFiles($_FILES['property_images']);
					if (! App\Cb\Properties\Images::isAllowed($images_file_arr)) {
						throw new Exception('One or more of the images is not supported');
					}		
				}
				$property_id = App\Cb\Properties::add($current_user->id, [
					'short_desc' => $p['property_short_desc'],
					'description' => $p['property_description'],
					'street' => $p['property_street'],			
					'city' => $p['property_city'],
					'state' => $p['property_state'],
					'postcode' => $p['property_postcode'],
					'lat' => $p['property_lat'],
					'lng' => $p['property_lng'],
					'num_bedrooms' => $p['property_bedrooms'],
					'num_bathrooms' => $p['property_bathrooms'],
					'num_garage' => $p['property_garage'],
					'landarea' => $p['property_landarea'],
					'floorarea' => $p['property_floorarea'],
					'type' => $p['property_type']
				]);
				if (! $property_id ) {
					throw new Exception('Unable to add property. Please check your connection and try again.');
				}
				// Save the floorplan docs //
				if (isset($floorplan_file_arr) && ! App\Cb\Properties\Docs::save($property_id, $floorplan_file_arr)) {
					xplog('Unable to save some floor plan files for property "'.$property_id.'"', __METHOD__);
				}
				// Save the images //
				if (isset($images_file_arr) && ! App\Cb\Properties\Images::save($property_id, $images_file_arr)) {
					xplog('Unable to save some images for property "'.$property_id.'"', __METHOD__);
				}
				
				cb_set_message('Successfully added property to your account', 1);
				return redirect(route('my_properties'));				
			}
			catch (Exception $err) {
				cb_set_message($err->getMessage(), 0);
			}
		}
		
		$data['post'] = $p;
		return View::make('add_property', $data)->render();
	}
	
	public function updateProperty(Request $request) {
		if (! Auth::check()) { return redirect(route('logout')); }
		
	}
	
	public function listMyProperty(Request $request) {
		if (! Auth::check()) { return redirect(route('logout')); }
		if (! $request->session()->has('current_user')) { return redirect(route('logout')); }
		$current_user =  $request->session()->get('current_user');
		$data = [];
		view()->share([
			'title' => 'My Property List',
			'CB_PAGE_JS' => [
				url('/js/mods/Cb.Notify.js')
			]
		]);
		$data['porperty_list'] = App\Cb\Properties::getListByUserId($current_user->id); 
		
		return View::make('my_property_list', $data)->render();
	}
	
}
