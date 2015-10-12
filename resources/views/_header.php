<!DOCTYPE html>
<html data-csrf="<?php echo csrf_token();?>">
    <head>
        
        <title><?php echo (isset($title) ? $title : 'Auction App'); ?></title>  
        <style type="text/css">
			label { display: block; }
			
		</style>
    </head>
    <body>