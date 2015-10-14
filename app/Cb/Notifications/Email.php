<?php 
namespace App\Cb\Notifications;

use App;
use App\Cb\Notifications;
use Exception;
use DB; // See: http://laravel.com/docs/5.1/database
use PubSub; // See: http://baylorrae.com/php-pubsub/
use Mail; // See: http://laravel.com/docs/5.1/mail

class Email extends App\Cb\Base {
	public static function instance() { return parent::getInstance(); }
	protected function __construct() {
		set_time_limit(0); // Some emails takes a long time
	}
	
	protected function signUpConfirmation($_params=[]) {
		$uid = intval($_params['uid']);
		if ($uid < 1) {
			xplog('Invalid user id "'.$uid.'"', __METHOD__);
			return false;
		}
		$data = [
			'fname' => $_params['fname'],
			'email' => $_params['email'],
			'confirmation_link' => route('signup_confirmation', [
				'uid' => App\Crypt::urlencode($uid)
			])
		];
		return Mail::send('emails.signup_confirmation', $data, function ($m) use ($_params) {
			$m->to($_params['email'], $_params['fname'])->subject('AuctionApp - verify your email address');
			$m->from('auctionapp@noreply.com.au', 'AcutionApp');
		});
	}
	
}