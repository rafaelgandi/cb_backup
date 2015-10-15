<!DOCTYPE html>
<html data-csrf="<?php echo csrf_token();?>">
    <head>		
        <title><?php echo (isset($title) ? $title : 'Auction App'); ?></title>  
        <?php if (isset($CB_PAGE_CSS) && is_array($CB_PAGE_CSS) && count($CB_PAGE_CSS)):
			echo App\Assets::embed($CB_PAGE_CSS, 'css');
		?><?php endif;?>
        <script src="<?php echo url('/js/jquery.js');?>"></script>
		<?php echo View::make('_common_js_obj')->render(); ?>
		<?php echo View::make('_extend_js_obj')->render(); ?>
        <script src="<?php echo url('/js/third_party/famd.js');?>"></script>
		<?php if (isset($CB_PAGE_JS) && is_array($CB_PAGE_JS) && count($CB_PAGE_JS)):
			echo App\Assets::embed($CB_PAGE_JS);
		?><?php endif;?>
		
    </head>
    <body>