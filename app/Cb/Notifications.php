<?php 
namespace App\Cb;

use App;
use App\Cb;
use Exception;
use DB; // See: http://laravel.com/docs/5.1/database
use PubSub; // See: http://baylorrae.com/php-pubsub/
use Auth;

class Notifications extends App\Cb\Base {
	public static function instance() { return parent::getInstance(); }
	
	
}