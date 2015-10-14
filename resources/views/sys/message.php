<?php echo View::make('_header')->render(); ?>
<div style="text-align:center;padding:50px;">
	<?php echo $sys_message; ?>
	<br>
	<br>
	<?php if (isset($sys_message_link)):?>
		<a href="<?php echo $sys_message_link; ?>"><?php echo $sys_message_label; ?></a>
	<?php endif;?>	
</div>
<?php echo View::make('_footer')->render(); ?>