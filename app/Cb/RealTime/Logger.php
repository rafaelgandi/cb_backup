<?php 
namespace App\Cb\RealTime;

use App;
use App\Cb\RealTime;
use Exception;
use DB; // See: http://laravel.com/docs/5.1/database
use PubSub; // See: http://baylorrae.com/php-pubsub/

class Logger {
	public function log($_msg) {
		// See: https://github.com/pusher/pusher-http-php#debugging--logging
		xplog('PUSHER LOG: '.$_msg, __METHOD__);
	}
}