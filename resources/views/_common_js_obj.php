<script>
(function (window) {
	window.CB_JS_TRANSPORT = JSON.parse('<?php echo App\Json::encode([
		'paths' => [
			'baseUri' =>  url(),
			'mods' => url('/js/mods'),
			'thirdParty' => url('/js/third_party'),
			'js' => url('/js'),
			'css' => url('/css')
		]		
	]);?>');
})(this);
</script>