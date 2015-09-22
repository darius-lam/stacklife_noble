<?php
global $TYPEKIT_KEY;
global $GOOGLE_ANALYTICS;
global $GOOGLE_ANALYTICS_DOMAIN;
$tracker = $GOOGLE_ANALYTICS[0];

echo <<<EOF
  <link rel="author" href="$www_root/humans.txt" />
  <link rel="icon" href="$www_root/images/favicon.ico" type="image/x-icon" />
  
  <link rel="stylesheet" href="$www_root/css/jquery.fancybox-1.3.4.css" type="text/css" />
  <link rel="stylesheet" href="$www_root/css/shelflife.theme.css" type="text/css" />

  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js"></script>
  <script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.min.js"></script>

<!-- Typekit Code goes here -->
<script src="//use.typekit.net/izr1jbf.js"></script>
    <script>try{Typekit.load({ async: true });}catch(e){}</script>
    
  <script type="text/javascript" src="$www_root/stackview/jquery.stackview.min.js"></script>
  <script type="text/javascript" src="$www_root/js/handlebars.js"></script>
  <script type="text/javascript" src="$www_root/js/jquery.fancybox-1.3.4.pack.js"></script>
    <link rel="stylesheet" href="$www_root/css/bootstrap.css" type="text/css" />	
  <link rel="stylesheet" href="$www_root/css/template.css" type="text/css" />
  <link rel="stylesheet" href="$www_root/stackview/jquery.stackview.css" type="text/css" />


    <script type="text/javascript">
    if (document.location.hostname.search("noblenet.org") !== -1) {
          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
          })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

          ga('create', 'UA-52252320-5', 'auto');
          ga('send', 'pageview');
    }
    </script>

  <!--[if IE]>
        <link rel="stylesheet" href="$www_root/stackview/ie.stackview.css" type="text/css" />
        <link rel="stylesheet" href="$www_root/css/ie.template.css" type="text/css" />
  <![endif]-->
EOF;
?>
