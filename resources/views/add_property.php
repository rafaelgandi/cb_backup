<?php echo View::make('_header-loggedin')->render(); ?>
<?php cb_message_markup();?>


<style type="text/css">
	label { display: block; }
</style>

<form action="" method="post" enctype="multipart/form-data">
	
	<label>Street</label>
	<input type="text" name="property_street" value="<?php echo $post['property_street'];?>">
	<label>State</label>
	<select name="property_state">
		<option value="">Please Select</option>
		<?php if (isset($aus_states) && is_array($aus_states)):?>
			<?php foreach ($aus_states as $state):?>
				<option value="<?php echo $state; ?>" <?php echo (
					(strtoupper($state) === strtoupper($post['property_state']))
						? 'selected="selected"'
						: ''
				)?>><?php echo strtoupper($state);?></option>
			<?php endforeach;?>
		<?php endif;?>
	</select>
	<label>City</label>
	<input type="text" name="property_city" value="<?php echo $post['property_city'];?>">
	<label>Postcode</label>
	<input type="text" name="property_postcode" value="<?php echo $post['property_postcode'];?>">
	<label>Phone</label>
	<input type="text" name="property_phone" value="<?php echo $post['property_phone'];?>">
	<label>Short Description</label>
	<textarea name="property_short_desc" placeholder="Short Description"><?php echo $post['property_short_desc'];?></textarea>
	<label>Description</label>
	<textarea name="property_description" placeholder="Detailed Description"><?php echo $post['property_description'];?></textarea>
	
	<hr>
	<h5>General Features</h5>
	<label>Type</label>
	<select name="property_type">
		<option value="">Please Select</option>
		<?php if (isset($property_types) && is_array($property_types)):?>
			<?php foreach ($property_types as $ptype):?>
				<option value="<?php echo $ptype; ?>" <?php echo (
					(strtoupper($ptype) === strtoupper($post['property_type']))
						? 'selected="selected"'
						: ''
				)?>><?php echo ucwords($ptype);?></option>
			<?php endforeach;?>
		<?php endif;?>
	</select>
	<label>Bedrooms</label>
	<input type="text" name="property_bedrooms" value="<?php echo $post['property_bedrooms'];?>">
	<label>Bathrooms</label>
	<input type="text" name="property_bathrooms" value="<?php echo $post['property_bathrooms'];?>">
	
	
	<hr>
	<h5>Floor Plans</h5>
	<label>Land Area</label>
	<input type="text" name="property_landarea" value="<?php echo $post['property_landarea'];?>">
	<label>Floor Area</label>
	<input type="text" name="property_floorarea" value="<?php echo $post['property_floorarea'];?>">
	<label>Floor Plan Documents</label>
	<ul>
		<li><input type="file" name="property_floorplan_files[]"></li>
		<li><input type="file" name="property_floorplan_files[]"></li>
		<li><input type="file" name="property_floorplan_files[]"></li>
		<li><input type="file" name="property_floorplan_files[]"></li>
		<li><input type="file" name="property_floorplan_files[]"></li>
	</ul>
	
	<label>Property Images</label>
	<ul>
		<li><input type="file" name="property_images[]"></li>
		<li><input type="file" name="property_images[]"></li>
		<li><input type="file" name="property_images[]"></li>
		<li><input type="file" name="property_images[]"></li>
		<li><input type="file" name="property_images[]"></li>
	</ul>
	
	
	<hr>
	<h5>Outdoor Features</h5>
	<label>Garage</label>
	<input type="text" name="property_garage" value="<?php echo $post['property_garage'];?>">
	
	<label>Latitude</label>
	<input type="text" name="property_lat" value="<?php echo $post['property_lat'];?>">
	<label>Longitude</label>
	<input type="text" name="property_lng" value="<?php echo $post['property_lng'];?>">

	
	
	<label>Terms and Conditions</label>
	<input type="checkbox" checked name="property_terms" <?php echo (isset($post['property_terms']) && intval($post['property_terms']) === 1) ? 'checked' : '';?> value="1">
	<br>
	<br>
	<input type="hidden" name="_token" id="token" value="<?php echo csrf_token(); ?>">
	<input type="submit" name="submit" value="Add Property">
</form>
<?php echo View::make('_footer')->render(); ?>

