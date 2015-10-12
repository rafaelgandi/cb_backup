<?php 
namespace App\Cb\Users;

use App;
use App\Cb\Users;
use Exception;
use DB; // See: http://laravel.com/docs/5.1/database
use PubSub; // See: http://baylorrae.com/php-pubsub/

class Admin extends App\Cb\Base {
	public static function instance() { return parent::getInstance(); }
	
}