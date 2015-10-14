<html>
    <head>  
	   <meta charset="utf-8">
        <title>Cleverbons</title>      
    </head>
    <body style="font-family: 'Lucida Grande',verdana,arial,helvetica,sans-serif;font-size: 13px;line-height: 1.425em;">
	<div style="padding: 5px;color:#fff;width: 100%;height:100px;background-color:#bada55;">
			
	</div>
	<hr style="border:0;border-bottom: 1px solid #E8E8E8;" />
	<br>
		Dear <?php echo $fname?>, <br></br>
		
		To finish setting up your AuctionApp account, we just need to make sure this email address is yours.
		<br><br><br>
		<a href="<?php echo $confirmation_link;?>">Click here to verify <?php echo $email?></a>
	<br>
	<hr style="border:0;border-bottom: 1px solid #E8E8E8;" />
	<div align="right" style="padding:5px;color:#ccc;font-size:10px;">Delivered by <a href="<?php echo url();?>" style="color:#03CEF1;">AuctionApp</a></div>
    </body>
</html>