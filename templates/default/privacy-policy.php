<?php if($s_st=="buyfgeufb"){  ?>
<!--
author: Kariya host
author URL: http://www.kariya-host.com
Design: W3layouts
License: Creative Commons Attribution 3.0 Unported
License URL: http://creativecommons.org/licenses/by/3.0/
-->
<!DOCTYPE html>
<html>
<head>
<title><?php title_site('Privacy Policy'); ?></title>
<!-- for-mobile-apps -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Exchange,ads,free,Publish,your site,share,traffic,تبادل إعلاني,إعلانات,مجانية,تبادل زيرات,إعلانات نصية,أنشر موقعكمجانا" />
<meta name="generator" content="MYads" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false);
		function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- //for-mobile-apps -->
<link href="<?php url_site(); echo "/templates/".$template ;  ?>/css/bootstrap.css" rel="stylesheet" type="text/css" media="all" />
<link href="<?php url_site(); echo "/templates/".$template ;  ?>/css/style.css" rel="stylesheet" type="text/css" media="all" />
<!-- js -->
<script type="text/javascript" src="<?php url_site(); echo "/templates/".$template ;  ?>/js/jquery-3.0.0.min.js"></script>
<!-- //js -->
<link href='//fonts.googleapis.com/css?family=Sanchez:400,400italic' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic' rel='stylesheet' type='text/css'>
<!-- start-smoth-scrolling -->
<script type="text/javascript" src="<?php url_site(); echo "/templates/".$template ;  ?>/js/move-top.js"></script>
<script type="text/javascript" src="<?php url_site(); echo "/templates/".$template ;  ?>/js/easing.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$(".scroll").click(function(event){
			event.preventDefault();
			$('html,body').animate({scrollTop:$(this.hash).offset().top},1000);
		});
	});
</script>

<!-- start-smoth-scrolling -->
</head>
	
<body>
<!--navigation-->
 <div class="header"  >
    <div class="sticky-header header-section ">
       <div class="container">
	       <nav class="navbar navbar-default">
				<div class="navbar-header navbar-left">

					<h1><a class="navbar-brand" href="index"><?php title_site(''); ?></a></h1>
				</div>
				<!-- Collect the nav links, forms, and other content for toggling -->
				<div class=" navbar-right" id="bs">
					<nav class="menu menu-shylock">
						<ul class="nav navbar-nav link-effect-7" id="link-effect-7">
							<li class="active"><a href="index" ><?php lang('home'); ?></a></li>
							<?php nev_menu();  ?>
						</ul>
					</nav>
				</div>
			</nav>
		    <div class="clear"></div>
	   </div>
	 </div>
   </div>
<!--navigation-->
<!-- banner1 -->
	<div class="banner1">
		<div class="container">
			<h3>Privacy Policy</h3>
		</div>
	</div>
<!-- //banner1 -->
<!-- single -->
	<div class="single" style="background-color: white;">
      <div class="container">
       	  <p>
             <h2> Privacy Policy</h2>

<ul >
  <li>In common with other websites, log files are stored on the web server saving details such as the visitor's IP address, browser type, referring page and time of visit.</li>
  <li>Cookies may be used to remember visitor preferences when interacting with the website.</li>
  <li>Where registration is required, the visitor's email and a username will be stored on the server.</li>
</ul>

<h2>How the Information is used</h2>

<ul>
  <li>The information is used to enhance the vistor's experience when using the website to display personalised content and possibly advertising.</li>
  <li>E-mail addresses will not be sold, rented or leased to 3rd parties.</li>
  <li>E-mail may be sent to inform you of news of our services or offers by us or our affiliates.</li>
</ul>

<h2>Visitor Options</h2>

<ul>
  <li>If you have subscribed to one of our services, you may unsubscribe by following the instructions which are included in e-mail that you receive.</li>
  <li>You may be able to block cookies via your browser settings but this may prevent you from access to certain features of the website.</li>
</ul>

<h2>Cookies</h2>

<ul>
  <li>Cookies are small digital signature files that are stored by your web browser that allow your preferences to be recorded when visiting the website. Also they may be used to track your return visits to the website.</li>
  <li>3rd party advertising companies may also use cookies for tracking purposes.</li>
</ul>
              </p>
			   <div class="clearfix"> </div>
      </div>
	</div>
<!-- //single -->
<?php }else{ echo"404"; }  ?>