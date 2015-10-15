<?php if (isset($CB_JS_TRANSPORT) && is_array($CB_JS_TRANSPORT)):?>
<script>
(function (window) {
	if (!! window.CB_JS_TRANSPORT) {
		var moreVars = JSON.parse('<?php echo App\Json::encode($CB_JS_TRANSPORT);?>');
		window.CB_JS_TRANSPORT = jQuery.extend(window.CB_JS_TRANSPORT, moreVars);
	}
})(this);
</script>
<?php endif;?>