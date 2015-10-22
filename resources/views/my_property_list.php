<?php echo View::make('_header-loggedin')->render(); ?>
<?php cb_message_markup();?>

<a href="<?php echo route('add_property');?>">Add more properties</a>
<br>
<a href="<?php echo route('logout');?>">Logout</a>
<?php echo View::make('_footer')->render(); ?>

