<?php 
namespace App\Cb\Notifications;

use App;
use App\Cb\Notifications;
use Exception;
use DB; // See: http://laravel.com/docs/5.1/database
use PubSub; // See: http://baylorrae.com/php-pubsub/

class Mail extends App\Cb\Base {
	public static function instance() { return parent::getInstance(); }
	
}