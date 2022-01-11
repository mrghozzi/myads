<?php if($s_st=="buyfgeufb"){  ?>
<!--
author: Kariya host
author URL: http://www.krhost.ga
Design: W3layouts
License: Creative Commons Attribution 3.0 Unported
License URL: http://creativecommons.org/licenses/by/3.0/
-->
<!DOCTYPE HTML>
<html>
<head>
<title><?php title_site($title_page); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="generator" content="Myads" />
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
<?php if(isset($_GET['t'])){ ?>
	<meta name="description" content="<?php title_site($title_page); ?>" />
    <meta name="author" content="<?php echo $username_topic; ?>">
    <meta property="og:type" content="article" />
    <meta property="og:title" content="<?php title_site($title_page); ?>" />
	<meta property="og:description" content="<?php title_site($title_page); ?>" />
	<meta property="og:url" content="<?php url_page('/t'.$_GET['t']);  ?>" />
	<meta property="og:site_name" content="<?php echo $title_s;  ?>" />
	<meta property="og:image" content="<?php url_page('/bnr/logo.png');  ?>" />
	<meta property="og:image:width" content="420" />
	<meta property="og:image:height" content="280" />
	<meta name="twitter:label1" content="كُتب بواسطة">
	<meta name="twitter:data1" content="<?php echo $username_topic; ?>">
<?php } ?>
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>

<!-- Bootstrap Core CSS -->
<link href="<?php url_site();  ?>/templates/_panel/css/bootstrap.css" rel='stylesheet' type='text/css' />
<!-- Custom CSS -->
<link href="<?php url_site();  ?>/templates/_panel/css/style.css" rel='stylesheet' type='text/css' />
<link href="<?php url_site();  ?>/templates/_panel/css/prestyle.css" rel='stylesheet' type='text/css' />
<!-- font CSS -->
<!-- font-awesome icons -->
<link href="<?php url_site();  ?>/templates/_panel/css/font-awesome.css" rel="stylesheet">
<link href="<?php url_site();  ?>/templates/_panel/css/font-awesome.min.css" rel="stylesheet">
<!-- //font-awesome icons -->
<!--skycons-icons-->
<script src="<?php url_site();  ?>/templates/_panel/js/skycons.js"></script>
<!--//skycons-icons-->
  <!-- js-->

<script src="<?php url_site();  ?>/templates/_panel/js/bootstrap.js"></script>
<script src="<?php url_site();  ?>/templates/_panel/js/jquery.js"></script>
<script src="<?php url_site();  ?>/templates/_panel/js/jquery-3.0.0.min.js"></script>


<script async src="https://imgbb.com/upload.js" data-palette="purple" data-auto-insert="html-embed-thumbnail"></script>

<!--webfonts-->
<link href='//fonts.googleapis.com/css?family=Comfortaa:400,700,300' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Muli:400,300,300italic,400italic' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Open+Sans:100,200,300,400,500,600,700,800,900' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Sanchez:400,400italic' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic' rel='stylesheet' type='text/css'>
<!--//webfonts-->
<!-- Metis Menu -->
<link href="<?php url_site();  ?>/templates/_panel/css/custom.css" rel="stylesheet">
<!--//Metis Menu -->
<link href="<?php url_site();  ?>/templates/_panel/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
<script src="<?php url_site();  ?>/templates/_panel/js/jquery.sparkline.min.js"></script>
<script type="text/javascript">
    /* <![CDATA[ */
    $(function() {
        /** This code runs when everything has been loaded on the page */
        /* Inline sparklines take their values from the contents of the tag */
        $('.inlinesparkline').sparkline();

        /* Sparklines can also take their values from the first argument passed to the sparkline() function */
        var myvalues = [10,8,5,7,4,4,1];
        $('.dynamicsparkline').sparkline(myvalues);

        /* The second argument gives options such as specifying you want a bar chart11 */
        $('.dynamicbar').sparkline(myvalues, {type: 'bar', barColor: '#fff'} );

        /* Use 'html' instead of an array of values to pass options to a sparkline with data in the tag */
        $('.inlinebar').sparkline('html', {type: 'bar', barColor: '#fff'} );

    });
    /* ]]> */
    </script>
	<script src="<?php url_site();  ?>/templates/_panel/js/Chart.js"></script>

<!--pie-chart--->
<script src="<?php url_site();  ?>/templates/_panel/js/pie-chart.js" type="text/javascript"></script>
 <script type="text/javascript">

        $(document).ready(function () {
            $('#demo-pie-1').pieChart({
                barColor: '#68b828',
                trackColor: '#eee',
                lineCap: 'round',
                lineWidth: 10,
                onStep: function (from, to, percent) {
                    $(this.element).find('.pie-value').text(Math.round(percent) + '%');
                }
            });

            $('#demo-pie-2').pieChart({
                barColor: '#7c38bc',
                trackColor: '#eee',
                lineCap: 'butt',
                lineWidth: 10,
                onStep: function (from, to, percent) {
                    $(this.element).find('.pie-value').text(Math.round(percent) + '%');
                }
            });

            $('#demo-pie-3').pieChart({
                barColor: '#0e62c7',
                trackColor: '#eee',
                lineCap: 'square',
                lineWidth: 10,
                onStep: function (from, to, percent) {
                    $(this.element).find('.pie-value').text(Math.round(percent) + '%');
                }
            });


        });

    </script>
	<!--Calender-->
<link rel="stylesheet" href="<?php url_site();  ?>/templates/_panel/css/clndr.css" type="text/css" />

<!-- End Calender -->
<style>
.imgu-bordered-sm {
    border-radius: 50%;
    border: 2px solid #adb5bd;
    padding: 2px;
    height: 35px;
    width: 35px;
}

</style>
<!-- extensions -->

<?php act_extensions("head");  ?>
<!-- End extensions -->
</head>
<body class="cbp-spmenu-push" <?php if($c_lang=="ar"){  ?> style="direction: rtl;" <?php } ?> >
	<div class="main-content">

        <?php template_mine('nav');  ?>
      <!-- header-starts -->
		<div class="sticky-header header-section ">

             <?php if(isset($_COOKIE['user'])){ ?>
			<div class="profile_medile"><!--notifications of menu start -->
                <ul class="nofitications-dropdown">
					<li class="dropdown head-dpdn">
                    <a href="<?php url_site();  ?>/home" class="dropdown-toggle"  ><i class="fa fa-tachometer "></i></a>
				  </li>
                </ul>
				<ul class="nofitications-dropdown">
                       <li class="dropdown head-dpdn">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-envelope"></i><?php msg_nbr('span');  ?></a>
							<ul class="dropdown-menu anti-dropdown-menu">
							<li>
								<div class="notification_header">
									<h3>You have <?php msg_nbr('vu');  ?> new messages</h3>
								</div>
							</li>
                            <?php msg_nbr('list'); ?>
                            <li>
									<div class="notification_bottom">
										<a href="<?php url_site();  ?>/messages"><?php lang('all_msg'); ?></a>
									</div>
								</li>
							</ul>
					</li>
					<li class="dropdown head-dpdn">
						<a href="#" class="dropdown-toggle listnotif" id="count" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-bell"></i><?php ntf_nbr('span');  ?></a>
						<ul class="dropdown-menu anti-dropdown-menu">
                            <li>
								<div class="notification_header">
									<h3>You have <?php ntf_nbr('vu');  ?> new notification</h3>
								</div>
							</li>
                              <?php ntf_nbr('list'); ?>
								<li>
									<div class="notification_bottom">
										<a href="<?php url_site();  ?>/notification">See all notifications</a>
									</div>
								</li>
						</ul>
					</li>
                    <li class="dropdown head-dpdn" >
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-user"></i></a>
				   <ul class="dropdown-menu drp-mnu" id="menu_user">
                                        <li> <a href="<?php url_site();  ?>/u/<?php echo $_COOKIE['user']; ?> " title="<?php user_row('username');  ?>" ><i class="fa fa-user"></i> Profile</a> </li>
                                        <li> <a href="<?php url_site();  ?>/e<?php echo $_COOKIE['user']; ?>"><i class="fa fa-cog"></i> Settings</a> </li>
                                        <?php if($_COOKIE['user']=="1" ){
                if(!(isset($_COOKIE['admin'])==isset($hachadmin)) ){ ?>
						<li><a href="<?php url_site();  ?>/admincp?cont" ><i class="fa fa-tachometer"></i>Mode Admin cP</a></li>
                <?php }
                 if(isset($_COOKIE['admin'])==isset($hachadmin)){ ?>
						<li><a href="<?php url_site();  ?>/admincp?dcont" ><i class="fa fa-home"></i>Mode User</a></li>
                <?php } } ?>
                <?php act_extensions("menu_user");  ?>
                                       <li> <a href="<?php url_site();  ?>/logout?logout" title="<?php lang('logout'); ?>"><i class="fa fa-sign-out"></i> <?php lang('logout'); ?></a> </li>
                </ul>
								</li>
                        <?php act_extensions("dropdown_nofitications");  ?>
            </ul>
              
              <button id="showLeftPush"><i class="fa fa-bars"></i></button>
			</div>
            <?php }else{  ?>

             <div class="profile_medile"><!--notifications of menu start -->

				<ul class="nofitications-dropdown">
					<li class="dropdown head-dpdn">
                    <a href="<?php url_site();  ?>/index" class="dropdown-toggle"  ><i class="fa fa-home"></i></a>
					<a href="<?php url_site();  ?>/login" class="dropdown-toggle"  ><i class="fa fa-sign-in"></i></a>
                    <a href="<?php url_site();  ?>/register" class="dropdown-toggle"  ><i class="fa fa-user-plus"></i></a>

					</li>

				</ul><button id="showLeftPush"><i class="fa fa-bars"></i></button>
			</div>
            <?php }  ?>
            <div class="header-right">
                    <!--toggle button start-->
                 <div class="clearfix"> </div>
				<!--toggle button end-->
			</div>
			<div class="clearfix"> </div>
		</div>
            <!-- Classie -->
				<script src="<?php url_site();  ?>/templates/_panel/js/classie.js"></script>
				<script>
					var menuLeft = document.getElementById( 'cbp-spmenu-s1' ),
						showLeftPush = document.getElementById( 'showLeftPush' ),
						body = document.body;

					showLeftPush.onclick = function() {
						classie.toggle( this, 'active' );
						classie.toggle( body, 'cbp-spmenu-push-toright' );
						classie.toggle( menuLeft, 'cbp-spmenu-open' );
						disableOther( 'showLeftPush' );
					};


					function disableOther( button ) {
						if( button !== 'showLeftPush' ) {
							classie.toggle( showLeftPush, 'disabled' );
						}
					}
				</script>
<script>
$(document).ready(function(){

 function load_unseen_notification(view = '')
 {
  $.ajax({
   url:"<?php url_site();  ?>/requests/fetch.php",
   method:"POST",
   data:{view:view},
   dataType:"json"

  });

 }
 $(document).on('click', '.listnotif', function(){
  $('#count').html('<i class="fa fa-bell"></i>');
  load_unseen_notification('yes');
 });


});
</script>



				<!--//scrolling js-->
<?php }else{ echo"404"; }  ?>