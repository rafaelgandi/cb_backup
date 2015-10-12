<?php 
namespace App\Cb;

use App;
use App\Nkie12;
use App\Cb;
use Exception;
use DB; // See: https://github.com/usmanhalalit/pixie
use PubSub; // See: http://baylorrae.com/php-pubsub/

class Base extends App\Nkie12\NinDo {
	public static function instance() { return parent::getInstance(); }
}