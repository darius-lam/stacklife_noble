<?php
  require_once('../../etc/sl_ini.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>StackLife</title>

<?php
include_once('includes/includes.php');
global $TYPEKIT_CODE;
echo <<<EOF
  <link rel="author" href="$www_root/humans.txt" />
  <link rel="icon" href="$www_root/images/favicon.ico" type="image/x-icon" />
  <link rel="stylesheet" href="$www_root/css/shelflife.theme.css" type="text/css" />
  <link rel="stylesheet" href="$www_root/css/template.css" type="text/css" />
  <link rel="stylesheet" href="$www_root/stackview/jquery.stackview.css" type="text/css" />
  
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
  <script type="text/javascript" src="$www_root/stackview/jquery.stackview.min.js"></script>
  <script type="text/javascript" src="$www_root/js/landing_page.js"></script>
  $TYPEKIT_CODE
EOF;
?>

</head>

<body>

    <div class="container group row">
		
		<div class="group span2">
			
			 <?php require_once('includes/logo.php');?>
			<div class="about-button">
				<a href="index.php" class="about">Home</a>
			</div>
		</div><!--end logo include-->
		
		<div class="span10">
			<br/><br/><br/><br/>
      		
      		<div class="center">
				<iframe src="http://player.vimeo.com/video/55894472?title=0&amp;byline=0&amp;portrait=0&amp;badge=0" width="500" height="334" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe> 
    			<img src="images/how-to.png"/>
    		</div><!--end center-->
    	</div><!--end main-->
		<div class="row span12">
		<img src="images/how-to.png"/>
		</div>	
	
</div><!--end container-->

</body>
</html>
