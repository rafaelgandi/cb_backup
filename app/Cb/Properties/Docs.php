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
	protected function __construct() {
		$properties_dir = App\Files::makeDirIfNotExists(public_path('uploads/properties'));
		$this->docs_dir = App\Files::makeDirIfNotExists($properties_dir.'/docs');
		$this->allowed_docs = config('cleverbons.files.allowed_docs');
		$this->allowed_images = config('cleverbons.files.allowed_images');
		
	}
	
	protected function isAllowed($_files_array=[]) {
		$check = true;
		foreach ($_files_array as $file) {
			$file = (array) $file;
			$name = (isset($file['filename'])) ? $file['filename'] : $file['name'];
			if (trim($name) === '') { continue; }
			$ext = App\Upload::getExtensionFromFilename($name);
			if ($ext === '') { return false; }
			if (! in_array($ext, $this->allowed_docs) && ! in_array($ext, $this->allowed_images)) { return false; }
		}
		return true;
	}
	
	protected function save() {
		
	}
}