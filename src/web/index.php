<?php
    require_once(dirname(__FILE__) . '/../../etc/sl_ini.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>StackLife &#64; NOBLE</title>
<?php
include_once('includes/includes.php');
echo <<<EOF
  <script type="text/javascript" src="$www_root/js/landing_page.js"></script>
EOF;
?>

<script>
   
data = "&key=school&value=NOBLE";
$.ajax({
    url: "<?php echo $www_root ?>/sl_funcs.php?func=set_session_var",
    type: "get",
    data: data,
    success: function(data){
    }
});
    
</script>

</head>

<body>

    <div class="container group row">
		<div class="group span2 middle-position">

			 <?php require_once('includes/logo.php');?>

		</div><!--end logo include-->

		<div class="main span8">
      		<div id="landing-stack"></div>
    	</div><!--end main-->


		<div class="span4-negative middle-position-search">
		<div class="dive-in">
			<span class="text cyan"> &larr; Click a book to dive into the stacks</span></p>
		</div>
			<form id="search2" method="get" action="<?php echo $www_root?>/search">
            	<input type="hidden" style="display:none" name="search_type" value="keyword"/>
            	<input type="text" autofocus="autofocus" name="q" placeholder="Search"/>
            	<input type="submit" name="submit_search" id="itemsearch" value="Go!"/>
			</form>
			<a id="inline" href="#advanced" style="display:none">Advanced Search</a>
			<a href="<?php echo $www_root?>/search?advanced=true" class="button advanced-search2">Advanced Search</a>
			<br/>

			<p class="text">Welcome to StackLife &#64; NOBLE, a new way to browse the library resource of the North of Boston Library Exchange.</p>
			<p class="text"><span class="cyan">This is a prototype.</span> We’re eager to hear from you. Please email us at <span class="cyan">owhlibrary@andover.edu</span> with any feedback you have!</p>
			<br/>


			<div class="about-button">
				<a href="<?php echo $www_root ?>/explainer.php" class="heading">How it works</a>
			</div>
			<br/>
			<div class="about-button">
				<a href="<?php echo $www_root ?>/about" class="heading">About</a>
			</div>
			
		</div><!--end-span4-negative-->

</div><!--end container-->

</body>
</html>
