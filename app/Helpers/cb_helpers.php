<?php 
/* 
	Clevebons site wide helper functions
 */

function cb_message_markup($_message=false, $_type=2, $_display_time=8e3) {
	if ($_message !== false) {
		?>
		<script>
		(function ($) {
			jQuery(function () {
				var msgDetails = <?php echo App\Json::encode([
					'message' => $_message,
					'type' =>  intval($_type),
					'displayTime' => $_display_time
				])?>;
				navigator.mod('Cb\Notify', function (mod) {
					mod.notify(msgDetails.message, msgDetails.type);
				});
			});		
		})(jQuery);
		</script>
		<?php
	}
	if (session()->has('cb_msg')) {
		$msg_details = 	session()->get('cb_msg');
		?>
		<script>
		(function ($) {
			jQuery(function () {
				var msgDetails = <?php echo App\Json::encode([
					'message' => $msg_details['message'],
					'type' =>  intval($msg_details['type']),
					'displayTime' => (isset($msg_details['display_time'])) ? $msg_details['display_time'] : 8e3
 				])?>;
				navigator.mod('Cb\Notify', function (mod) {
					mod.notify(msgDetails.message, msgDetails.type, msgDetails.displayTime);
				});
			});		
		})(jQuery);
		</script>
		<?php
	}
	else if (isset($_GET['cb_msg']) && trim($_GET['cb_msg']) !== '') {
		$type = (isset($_GET['ink_msg_type'])) ? intval($_GET['ink_msg_type']) : $_type;
		?>
		<script>
		(function ($) {
			jQuery(function () {
				var msgDetails = <?php echo App\Json::encode([
					'message' => urldecode($_GET['cb_msg']),
					'type' => urldecode($_GET['cb_msg_type'])
				])?>;
				navigator.mod('Cb\Notify', function (mod) {
					mod.notify(msgDetails.message, msgDetails.type);
				});
			});		
		})(jQuery);
		</script>
		<?php
	}
	else {
		echo '';
	}
	// Remove the session if there are any //
	session()->forget('cb_msg');
}

function cb_set_message($_msg, $_type=2, $_display_time=8e3) {
	return session()->flash('cb_msg', [
		'message' => trim($_msg),
		'type' =>  $_type,
		'display_time' => $_display_time
	]);
}
