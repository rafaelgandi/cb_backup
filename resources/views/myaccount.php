<?php echo View::make('_header-loggedin')->render(); ?>

My account page <a href="<?php echo route('logout');?>">logout</a>


<?php echo View::make('_footer')->render(); ?>