<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?php
  require_once ('../../sl_ini.php');
  include_once('book.inc.php');
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php
include_once('includes.php');
echo <<<EOF

	<title></title>

	<script type="text/javascript" src="$www_root/js/book.js"></script>
	<script type="text/javascript" src="$www_root/js/jquery.history.js"></script>
	<script type="text/javascript" src="http://www.google.com/jsapi"></script>
EOF;
?>

<script type="text/javascript">

var 
hollis = '<?php echo $hollis ?>',
worldcatnum = '',
loc_sort_order = '';
anchor_subject = '',
uniform_id = '',
uniform_count = '',
uid = '<?php echo $uid ?>',
title = '';

var slurl = '<?php echo $www_root ?>/src/web/sl_funcs.php';
var www_root = '<?php echo $www_root ?>';

var recentlyviewed = '';
var alsoviewed = new Array();

var GBSArray = ['ISBN:<?php echo $isbn_trim ?>', 'OCLC:<?php echo $oclcnum ?>'];

google.load("books", "0");

var History = window.History;

$(document).ready(function() {

	<?php
	foreach(array_reverse($_SESSION['books']) as $id => $past_book){
		if($id != $uid) {
	?>
		recentlyviewed += ('&recently[]=<?php echo $id ?>');
		alsoviewed.push('<?php echo $id ?>');
	<?php }
	} ?>

	$(window).scroll(function (event) {
		// what the y position of the scroll is
		var y = $(this).scrollTop();
		// whether that's below the form
		// HEIGHT OF HEADER
		if (y >= $('.header').height()) {
		// if so, ad the fixed class
			$('#fixedstack').addClass('fixed');
			$('#overlaynav').addClass('fixed');
			stackheight = $(window).height();
			$('.container').css('height', stackheight);
			$('#viewerCanvas').css('height', stackheight*.9).css('width', stackheight*.75);
			$('#fixedclear').css('clear', 'both');
		} else {
			// otherwise remove it
			$('#fixedstack').removeClass('fixed');
			$('#overlaynav').removeClass('fixed');
			stackheight = $(window).height() - $('.header').height();
			$('.container').css('height', stackheight);
			$('#viewerCanvas').css('height', stackheight*.9).css('width', stackheight*.75);
			$('#fixedclear').css('clear', '');
		}

	});

}); //End document ready
</script>
</head>

<!-- /////////////////// BODY ////////////////////////// -->
<body>
  <div id="wrapper">
    <div style="display:none;">
    	<div id="viewerCanvas" style="width: 610px; height: 725px"></div>
    </div>
    
    <?php require_once('header.php');?>

    <div class="container group">
      <div class="container-content">
      	<div class="main">
				  <div id="fixedstack"></div>
        </div><!-- end main-->
			<div class="itemData-container scrollmarg">
				<div id="itemData">
          <h1 class="home-stack"></h1>
          <div id="creator_container"></div>

          <img class="cover-image ol-cover-image" src="" />

					<a href="" target="_blank" class="button" id="hollis_button">HOLLIS</a>
					<div id="online">
            <a class="button-google-disabled" href="#viewerCanvas"><img src="<?php echo $www_root ?>/images/gbs_preview_disabled.png" /></a>
            <a id="gviewer" class="button-google" href="#viewerCanvas" style="display:none;"><img src="<?php echo $www_root ?>/images/gbs_preview.png" border="0" /></a>
          </div>
          <div class="wikipedia-icon"> 
						<div class="wikipedia_link">
						  <a href="" target="_blank" >
						    <img src="<?php echo $www_root ?>/images/wikipedia.png" />
						  </a>
					  </div>
					</div><!--end wikipedia-icon-->	
					<div class="buy" style="display:none;">	 
            <a id="amzn" href="" target="_blank"><img class="buy" src="<?php echo $www_root ?>/images/amazon.png" alt="Amazon"/></a><span class="author-divider">|</span>                 	
            <a id="abes" href="" target="_blank"><img class="buy" src="<?php echo $www_root ?>/images/abeBooks.png" alt="AbeBooks"/> </a><span class="author-divider">|</span>
            <a id="bandn" href="" target="_blank"><img class="buy" src="<?php echo $www_root ?>/images/barnesAndNoble.png" alt="Barnes&amp;Noble"/></a><span class="author-divider">|</span>
            <a id="hrvbs" href="" target="_blank"><img class="buy" src="<?php echo $www_root ?>/images/harvardBookStore.png" alt="Harvard Book Store"/></a>
          </div>  <!--end buy-->	
          <span class="button-availability available-button slide-more" style="display:none;"><span class="icon"></span>Availability<span class="arrow"></span></span>
					<div id="availability" class="slide-content" style="display:none;"></div>
          
          <h3 class="imprint"></h3>  
                  
          <h3 class="clickable advanced-data slide-more">Advanced Bibliographic Data<span class="arrow"></span></h3>
        	
        	<div class="advanced-data-box slide-content" style="display:none;">
        	  <ul>
        	    <li class="advanced-isbn"><p>ISBN: </p></li>
        	    <li class="advanced-oclc"><p>OCLC: </p></li>
        	    <li class="advanced-language"><p>Language: </p></li>
        	  </ul>
        	</div>
        </div><!--end itemData-->              
                
        <div id="all-rank" class="slide-more">	            
        	<div id="shelfRankCalc" class="button-shelfRank">
        		<span class="unpack">ShelfRank</span>
        		<span class="shelfRank"></span>
        	</div><!--end shelfRankCalc-->
        </div><!--end all-rank-->
        <div id="rank-math" class="slide-content" style="display:none;"></div><!--end rank-math-->
      </div><!--end itemDataContainer-->   
                
			<div id="contextData" class="group">
        <div id="overlay-buttons">
            <div class="subjects">
							<span class="heading">Library Shelves</span>
             	<ul>
                <li id="callview" class="button button-selected stack-button"><span class="reload"><span class="reload-text">Infinite bookshelf</span></span></li>
           		</ul>
           	</div><!--end subjects-->
            <div class="neighborhoods">
            	<span class="heading">Community Shelves</span>
             		<ul>
             			<li id="alsoviewed" class="button stack-button"><span class="reload">People who viewed this also viewed these</span></li>
        					<li id="recentlyviewed" class="button stack-button"><span class="reload">You recently viewed these</span></li>
         				</ul>
         			</div><!--end neighborhoods-->
         			<div class="wikipedia"></div><!--end wikipedia-->

           			<div id="tagGraph"></div>
				</div><!--end overlay-buttons-->
                <form id="book-tags-form" method="post">
                	<input type="text" id="bookTags" name="bookTags" class="required" onfocus="if (this.value=='tag it') this.value = ''" type="text" value="tag it"/>
				   	<input type="submit" name="submit_tags"  id="submit_tags" value="Go!"/>
				</form>
				<div class="book-tag-success"><p><span style="display:none;"></span></p></div>

 		       
           	</div> <!-- end contextData -->
           	<div id="fixedclear"></div>
           	<div class="text-description group"> 
           		<div id="toc"></div>
        	</div> <!-- end text-description -->
        </div> <!--end container-content-->
    </div><!--end container-->

  <script id="gbscript" type="text/javascript" src="http://books.google.com/books?jscmd=viewapi&bibkeys=OCLC:<?php echo $oclcnum ?>,ISBN:<?php echo $isbn_trim ?>&callback=ProcessGBSBookInfo"></script>
   </div> <!--end wrapper-->
</body>
</html>
