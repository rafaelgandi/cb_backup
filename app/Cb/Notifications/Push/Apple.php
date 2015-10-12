<?php 
namespace App\Cb\Notifications\Push;

use App;
use App\Cb\Notifications\Push;
use Exception;
use DB; // See: http://laravel.com/docs/5.1/database
use PubSub; // See: http://baylorrae.com/php-pubsub/

class Apple extends App\Cb\Base {
	public static function instance() { return parent::getInstance(); }
	
}