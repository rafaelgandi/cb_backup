<?php 
namespace App\Cb\Properties;

use App;
use App\Cb\Properties;
use Exception;
use DB; // See: http://laravel.com/docs/5.1/database
use PubSub; // See: http://baylorrae.com/php-pubsub/

class Files extends App\Cb\Base {
	public static function instance() { return parent::getInstance(); }
	private $docs_dir = '';
	private $images_dir = '';
	private $allowed_docs = [];
	private $allowed_images = [];
	private $num_max_file_per_type = 10;
	protected function __construct() {
		$properties_dir = App\Files::makeDirIfNotExists(public_path('uploads/properties'));
		$this->docs_dir = App\Files::makeDirIfNotExists($properties_dir.'/docs');
		$this->images_dir = App\Files::makeDirIfNotExists($properties_dir.'/images');
		$this->allowed_docs = config('cleverbons.files.allowed_docs');
		$this->allowed_images = config('cleverbons.files.allowed_images');	
		$this->num_max_file_per_type = config('cleverbons.properties.max_num_file_per_type');				
	}
	
	protected function isAllowed($_files_array=[], $_type='doc') {
		$check = true;
		$type = trim(strtolower($_type));
		foreach ($_files_array as $file) {
			$file = (array) $file;
			$name = (isset($file['filename'])) ? $file['filename'] : $file['name'];
			if (trim($name) === '') { continue; }
			$ext = App\Upload::getExtensionFromFilename($name);
			if ($ext === '') { return false; }
			if ($type === 'doc') {
				if (! in_array($ext, $this->allowed_docs) && ! in_array($ext, $this->allowed_images)) { return false; }
			}
			else {
				if (! in_array($ext, $this->allowed_images)) { return false; }
			}
			
		}
		return true;
	}
	
	protected function save($_property_id, $_files_array=[], $_type='doc') {
		$pid = intval($_property_id);
		$type = trim(strtolower($_type));
		if ($pid <= 0) {
			xplog('Invalid property id given', __METHOD__);
			return false;
		}
		$insert_data = [];
		$uploaded_file_details = [];
		$destination_dir = ($type === 'doc') ? $this->docs_dir.'/' : $this->images_dir.'/';
		foreach ($_files_array as $file) {
			$file = (array) $file;
			$name = (isset($file['filename'])) ? $file['filename'] : $file['name'];
			if (trim($name) === '') { continue; }
			if (! isset($file['base64'])) { // Web
				$uploaded_file_details = App\Upload::save($file, [
					'destination' => $destination_dir
				]);	
			}
			else { // Mobile
				$uploaded_file_details = App\Upload::saveBase64($file['base64'], [
					'destination' => $destination_dir,
					'extension' => $file['extension']
				]);
			}
			if (! is_object($uploaded_file_details)) {
				xplog('Unable to save file "'.$name.'" for property "'.$pid.'"', __METHOD__);
				continue;
			}
			// Gather details for the database //
			$insert_data[] = [
				'properties_id' => $pid,
				'type' => ($type === 'doc') ? 'doc' : 'image',
				'codedname' => $uploaded_file_details->coded_name,
				'filename' => $uploaded_file_details->filename,
				'extension' => $uploaded_file_details->extension
			];
		}
		if (count($insert_data)) { // Make sure that their are data to save.
			return is_numeric(DB::table('property_files')->insert($insert_data)); // Insert the file data to the database
		}
		return true;
	}
	
	protected function isMaxExceeded($_property_id, $_type='doc') {
		$pid = intval($_property_id);
		$type = trim(strtolower($_type));
		if ($pid <= 0) {
			xplog('Invalid property id given', __METHOD__);
			return true;
		}
		$count = DB::table('property_files')
		->where('properties_id', $pid)
		->where('type', $type)
		->count();
		return ($count >= $this->num_max_file_per_type);
	}
	
	
	
	
	
}