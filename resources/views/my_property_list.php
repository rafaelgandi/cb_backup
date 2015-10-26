<?php echo View::make('_header-loggedin')->render(); ?>
<?php cb_message_markup();?>

<a href="<?php echo route('add_property');?>">Add more properties</a>
<br>
<a href="<?php echo route('logout');?>">Logout</a>

<?php 

//_pr($property_list);
//var_dump(count($property_list));


if (!! $property_list && count($property_list)):?>
	<ul>
		<?php foreach ($property_list as $property):?>
		<li><span><?php echo $property->short_desc; ?></span></li>
		<?php endforeach;?>
	</ul>
	<div>
		<?php //echo $property_list->render();?>
	</div>
<?php else:?>
	<h4>No prperties yet</h4>
<?php endif;?>




<?php echo View::make('_footer')->render(); ?>

