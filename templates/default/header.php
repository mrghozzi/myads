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
<title><?php title_site(''); ?></title>
<!-- for-mobile-apps -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Exchange,ads,free,Publish,your site,share,traffic,تبادل إعلاني,إعلانات,مجانية,تبادل زيرات,إعلانات نصية,أنشر موقعكمجانا" />
<meta name="generator" content="MYads" />
<link rel="apple-touch-icon" sizes="57x57" href="<?php url_site();  ?>/bnr/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="<?php url_site();  ?>/bnr/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="<?php url_site();  ?>/bnr/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="<?php url_site();  ?>/bnr/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="<?php url_site();  ?>/bnr/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="<?php url_site();  ?>/bnr/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="<?php url_site();  ?>/bnr/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="<?php url_site();  ?>/bnr/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="<?php url_site();  ?>/bnr/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192"  href="<?php url_site();  ?>/bnr/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?php url_site();  ?>/bnr/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="<?php url_site();  ?>/bnr/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?php url_site();  ?>/bnr/favicon-16x16.png">
<link rel="manifest" href="<?php url_site();  ?>/bnr/manifest.json">
<meta name="msapplication-TileColor" content="#9900CC">
<meta name="msapplication-TileImage" content="<?php url_site();  ?>/bnr/ms-icon-144x144.png">
<meta name="theme-color" content="#9900CC">
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
	
<body <?php if($c_lang=="ar"){  ?> style="direction: rtl;" <?php } ?> >
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
<!-- banner -->
	<div class="banner" style="background-color: white;">
		<div class="w3l_banner_info">
			<div class="container">
				<section class="slider">
					<div class="flexslider">
						<ul class="slides">
							<li>
								<div class="w3l_banner_info_grid">

									<p><?php  descr_site(); ?></p>
								</div>
							</li>


						</ul>
					</div>
				</section>
						<!-- flexSlider -->

						<!-- //flexSlider -->
				<div class="w3ls_banner_scroll">
					<a href="#log" class="scroll"></a>
				</div>
			</div>
		</div>
	</div>
<!-- //banner -->
<?php }else{ echo"404"; }  ?>