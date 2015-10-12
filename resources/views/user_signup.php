<?php echo View::make('_header')->render(); ?>

<?php echo (isset($cb_err_msg)) ? '<span style="color:red;">'.$cb_err_msg.'</span>' : '';?>
<form action="" method="post" enctype="multipart/form-data">
	
	<label>First Name</label>
	<input type="text" name="fname" value="<?php echo $post['fname'];?>">
	<label>Last Name</label>
	<input type="text" name="lname" value="<?php echo $post['lname'];?>">
	<label>Email</label>
	<input type="email" name="email" value="<?php echo $post['email'];?>">
	<label>Password</label>
	<input type="password" name="password" value="<?php echo $post['password'];?>">
	<label>Confirm Password</label>
	<input type="password" name="cpassword" value="<?php echo $post['cpassword'];?>">
	<label>Phone</label>
	<input type="text" name="phone" value="<?php echo $post['phone'];?>">
	<label>Mobile</label>
	<input type="text" name="cell" value="<?php echo $post['cell'];?>">
	
	<label>Is Agent?</label>
	<input type="checkbox" name="is_agent" <?php echo (isset($post['is_agent']) && intval($post['is_agent']) === 1) ? 'checked' : '';?> value="1">
	
	<label>Company Name</label>
	<input type="text" name="company_name" value="<?php echo $post['company_name'];?>">
	<label>Company Street</label>
	<input type="text" name="company_street" value="<?php echo $post['company_street'];?>">
	<label>Company State</label>
	<select name="company_state">
		<option value="">Please Select</option>
		<?php if (isset($aus_states) && is_array($aus_states)):?>
			<?php foreach ($aus_states as $state):?>
				<option value="<?php echo $state; ?>" <?php echo (
					(strtoupper($state) === strtoupper($post['company_state']))
						? 'selected="selected"'
						: ''
				)?>><?php echo strtoupper($state);?></option>
			<?php endforeach;?>
		<?php endif;?>
	</select>
	<label>Company Phone</label>
	<input type="text" name="company_phone" value="<?php echo $post['company_phone'];?>">
	<label>Company ABN</label>
	<input type="text" name="company_abn" value="<?php echo $post['company_abn'];?>">
	<label>Company City</label>
	<input type="text" name="company_city" value="<?php echo $post['company_city'];?>">
	<label>Company Postcode</label>
	<input type="text" name="company_postcode" value="<?php echo $post['company_postcode'];?>">
	<label>Company Color</label>
	<input type="text" name="company_color" value="<?php echo $post['company_color'];?>">
	<label>Terms and Conditions</label>
	<input type="checkbox" checked name="terms" <?php echo (isset($post['terms']) && intval($post['terms']) === 1) ? 'checked' : '';?> value="1">
	<br>
	<br>
	<input type="hidden" name="_token" id="token" value="<?php echo csrf_token(); ?>">
	<input type="submit" name="submit" value="Sign Up">
</form>

<?php echo View::make('_footer')->render(); ?>

