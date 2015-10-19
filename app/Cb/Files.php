<?php 
namespace App\Cb;

use App;
use App\Cb;
use Exception;
use DB; // See: http://laravel.com/docs/5.1/database
use PubSub; // See: http://baylorrae.com/php-pubsub/
use Auth;

class Files extends App\Cb\Base {
	public static function instance() { return parent::getInstance(); }
	protected function __construct() {}
	
	protected function deleteAllInstance($_path) {
		if (! App\Files::isFile($_path)) { 
			xplog('File "'.$_path.'" cannot be found while trying to delete', __METHOD__);
			return false;
		}
		App\Files::delete($_path);
		$filename = basename($_path);
		
		// TODO:  delete on other directories code goes here //
		
	}
}