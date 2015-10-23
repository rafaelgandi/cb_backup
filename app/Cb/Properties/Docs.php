<?php 
namespace App\Cb\Properties;

use App;
use App\Cb\Properties;
use Exception;
use DB; // See: http://laravel.com/docs/5.1/database
use PubSub; // See: http://baylorrae.com/php-pubsub/

class Docs extends App\Cb\Base {
	public static function instance() { return parent::getInstance(); }
	private $docs_dir = '';
	private $allowed_docs = [];
	private $allowed_images = [];
	protected function __construct() {}
	
	protected function isAllowed($_files_array=[]) {
		return App\Cb\Properties\Files::isAllowed($_files_array, 'doc');
	}
	
	protected function save($_property_id, $_files_array=[]) {
		$pid = intval($_property_id);
		if ($pid <= 0) {
			xplog('Invalid property id given', __METHOD__);
			return false;
		}
		return App\Cb\Properties\Files::save($pid, $_files_array, 'doc');
	}
	
	protected function isMaxExceeded($_property_id) {
		return App\Cb\Properties\Files::isMaxExceeded($_property_id, 'doc');
	}
	
}