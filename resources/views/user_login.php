<?php echo View::make('_header')->render(); ?>
<?php cb_message_markup();?>


<style type="text/css">
	label { display: block; }
</style>

<form action="" method="post">
	<label>Email</label>
	<input type="email" name="email" value="<?php echo $post['email'];?>">
	<label>Password</label>
	<input type="password" name="password" value="<?php echo $post['password'];?>">
	<br>
	<br>
	<input type="hidden" name="_token" id="token" value="<?php echo csrf_token(); ?>">
	<input type="submit" name="submit" value="Login">
</form>
<?php echo View::make('_footer')->render(); ?>

