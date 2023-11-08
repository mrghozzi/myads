<?php if($s_st=="buyfgeufb"){  ?>
<!--
author: Kariya host
author URL: http://www.kariya-host.gq/
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
<meta name="msapplication-TileColor" content="#615dfa">
<meta name="msapplication-TileImage" content="<?php url_site();  ?>/bnr/ms-icon-144x144.png">
<meta name="theme-color" content="#615dfa">
<?php if(isset($_GET['t'])){ ?>
<meta name="description" content="<?php echo $description_page; ?>" />
<meta name="author" content="<?php echo $username_topic; ?>">
<meta property="og:type" content="article" />
<meta property="og:title" content="<?php title_site($title_page); ?>" />
<meta property="og:description" content="<?php echo $description_page; ?>" />
<meta property="og:url" content="<?php url_page('/t'.$_GET['t']);  ?>" />
<meta property="og:site_name" content="<?php echo $title_s;  ?>" />
<?php if(isset($image_page)){ ?>
<meta property="og:image" content="<?php echo $image_page;  ?>" />
<?php }else{ ?>
<meta property="og:image" content="<?php url_page('/bnr/logo.png');  ?>" />
<?php } ?>
<meta property="og:image:width" content="420" />
<meta property="og:image:height" content="280" />
<meta name="twitter:label1" content="كُتب بواسطة">
<meta name="twitter:data1" content="<?php echo $username_topic; ?>">
<?php } ?>
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>

  <!-- bootstrap 4.3.1 -->
<link href="<?php url_site();  ?>/templates/_panel/<?php echo $c_mode; ?>/bootstrap.min.css" rel='stylesheet' type='text/css' />
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel='stylesheet' type='text/css' />
  <!-- styles -->
<link href="<?php url_site();  ?>/templates/_panel/<?php echo $c_mode; ?>/styles.min.css" rel='stylesheet' type='text/css' />
<link href="<?php url_site();  ?>/templates/_panel/<?php echo $c_mode; ?>/prestyle.css" rel='stylesheet' type='text/css' />
  <!-- simplebar styles -->
<link rel="stylesheet" href="<?php url_site();  ?>/templates/_panel/<?php echo $c_mode; ?>/simplebar.css">
  <!-- tiny-slider styles -->
<link rel="stylesheet" href="<?php url_site();  ?>/templates/_panel/<?php echo $c_mode; ?>/tiny-slider.css">
  <!-- dataTables styles -->
<link rel="stylesheet" href="<?php url_site();  ?>/templates/_panel/<?php echo $c_mode; ?>/dataTables.css">

 <!-- font-awesome icons -->
<link href="https://use.fontawesome.com/releases/v6.4.2/css/all.css" rel="stylesheet">

<!--webfonts-->
<link href='//fonts.googleapis.com/css?family=Comfortaa:400,700,300' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Muli:400,300,300italic,400italic' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Open+Sans:100,200,300,400,500,600,700,800,900' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Sanchez:400,400italic' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic' rel='stylesheet' type='text/css'>

<!-- jquery -->
<script type="text/javascript" src="<?php url_site();  ?>/templates/_panel/js/jquery-3.6.0.min.js"></script>
<!-- extensions -->

<?php act_extensions("head");  ?>
<!-- End extensions -->
</head>
<body>

      <?php template_mine('header/page_loader');  ?>
      <?php template_mine('header/nav');  ?>
      <?php template_mine('header/sidemenu');  ?>
      <?php template_mine('header/desktop_sidebar');  ?>
      <?php template_mine('header/mobile_sidebar');  ?>
      <?php template_mine('header/floaty_bar');  ?>

<div class="content-grid">

<?php }else{ echo"404"; }  ?>