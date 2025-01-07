<?php if(isset($s_st) AND ($s_st=="buyfgeufb")){ dinstall_d(); ?>

  <!-- SECTION BANNER -->
    <div class="section-banner" style="background: url(<?php url_site();  ?>/templates/_panel/img/banner/Newsfeed.png) no-repeat 50%;" >
      <!-- SECTION BANNER ICON -->
      <img class="section-banner-icon" src="<?php url_site();  ?>/templates/_panel/img/banner/newsfeed-icon.png"  alt="overview-icon">
      <!-- /SECTION BANNER ICON -->

      <!-- SECTION BANNER TITLE -->
      <p class="section-banner-title"><?php lang('directory'); ?></p>
      <!-- /SECTION BANNER TITLE -->

      <!-- SECTION BANNER TEXT -->
      <p class="section-banner-text"><?php lang('latest_sites');  ?></p>
      <!-- /SECTION BANNER TEXT -->
    </div>
    <!-- /SECTION BANNER -->

<div class="grid grid-3-6-3" >
<div class="grid-column" >
       <div class="widget-box">

          <!-- WIDGET BOX TITLE -->
          <p class="widget-box-title"><h4><?php lang('board');  ?></h4></p>
          <!-- /WIDGET BOX TITLE -->

          <!-- WIDGET BOX CONTENT -->
          <div class="widget-box-content">
            <!-- POST PEEK LIST -->
            <div class="post-peek-list">
              <a href="<?php url_site();  ?>/directory" class="btn btn-primary" >&nbsp;<i class="fa fa-home" aria-hidden="true"></i>&nbsp;</a>
              <a href="<?php url_site();  ?>/add-site.html" class="btn btn-success" ><?php lang('addWebsite');  ?>&nbsp;<i class="fa fa-plus" aria-hidden="true"></i> </a>
            </div>
            <!-- /POST PEEK LIST -->
          </div>
          <!-- /WIDGET BOX CONTENT -->
       </div>
<?php 
      if(isset($_GET['cat'])){ $get_cat = $_GET['cat']; }else{ $get_cat = "0"; }
      $wcatcount = $db_con->prepare("SELECT  COUNT(id) as nbr FROM cat_dir WHERE sub={$get_cat} AND statu=1");
      $wcatcount->execute();
      $wabcat=$wcatcount->fetch(PDO::FETCH_ASSOC);
      $wcat_nbr = $wabcat['nbr'];
      if(isset($wcat_nbr) AND ($wcat_nbr>=1)){
      ?>     
       <div class="widget-box">

          <!-- WIDGET BOX TITLE -->
          <p class="widget-box-title"><h4><?php lang('cat_s'); ?></h4></p>
          <!-- /WIDGET BOX TITLE -->

          <!-- WIDGET BOX CONTENT -->
          <div class="widget-box-content">
            <!-- POST PEEK LIST -->
            <div class="post-peek-list">
<?php
$catsum = $db_con->prepare("SELECT  * FROM cat_dir WHERE sub={$get_cat} AND statu=1 ORDER BY `ordercat`  ASC");
$catsum->execute();
while($sucats=$catsum->fetch(PDO::FETCH_ASSOC))
{
$catdids=$sucats['id'];
$catcount = $db_con->prepare("SELECT  COUNT(id) as nbr FROM directory WHERE cat=$catdids AND statu=1");
$catcount->execute();
$abcat=$catcount->fetch(PDO::FETCH_ASSOC);

$cat_nbr = $abcat['nbr'];
$get_subcat = $sucats['id'];
$sub_cat = "";
$catsums = $db_con->prepare("SELECT  * FROM cat_dir WHERE sub={$get_subcat} AND statu=1 ORDER BY `ordercat`   DESC");
$catsums->execute();
while($sucatsu=$catsums->fetch(PDO::FETCH_ASSOC))
{
   $catdidsu=$sucatsu['id'];
  $catcounts = $db_con->prepare("SELECT  COUNT(id) as nbr FROM directory WHERE cat=$catdidsu AND statu=1");
  $catcounts->execute();
  $abcats=$catcounts->fetch(PDO::FETCH_ASSOC);

  $cat_nbr = $cat_nbr+$abcats['nbr'];

  $sub_cat = "<p class=\"post-peek-title\"><i class=\"fa-solid fa-circle-chevron-right\" style=\"color: #615dfa;\"></i>&nbsp;&nbsp;<a href=\"{$url_site}/cat/{$catdids}\">{$sucatsu['name']}
  <span class=\"badge badge-info\">{$abcats['nbr']}</span></a></p><br/>".$sub_cat;
}
 ?>
              <!-- POST PEEK -->
              <div class="post-peek">
                <!-- POST PEEK IMAGE -->
                <a class="post-peek-image" href="<?php url_site();  ?>/cat/<?php echo $catdids; ?>">

                <svg xmlns="http://www.w3.org/2000/svg" height="16" width="18" viewBox="0 0 576 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#615dfa" d="M88.7 223.8L0 375.8V96C0 60.7 28.7 32 64 32H181.5c17 0 33.3 6.7 45.3 18.7l26.5 26.5c12 12 28.3 18.7 45.3 18.7H416c35.3 0 64 28.7 64 64v32H144c-22.8 0-43.8 12.1-55.3 31.8zm27.6 16.1C122.1 230 132.6 224 144 224H544c11.5 0 22 6.1 27.7 16.1s5.7 22.2-.1 32.1l-112 192C453.9 474 443.4 480 432 480H32c-11.5 0-22-6.1-27.7-16.1s-5.7-22.2 .1-32.1l112-192z"/></svg>
                </a>
                <!-- /POST PEEK IMAGE -->

                <!-- POST PEEK TITLE -->
                <p class="post-peek-title"><a href="<?php url_site();  ?>/cat/<?php echo $catdids; ?>"><?php echo $sucats['name']; ?>
                <span class="badge badge-info"><?php echo $cat_nbr; ?></span></a></p>
                <!-- /POST PEEK TITLE -->

                <!-- POST PEEK TEXT -->
                <p class="post-peek-text"><?php echo $sub_cat;  ?></p>
                <!-- /POST PEEK TEXT -->
              </div>
              <!-- /POST PEEK -->
<?php } ?>
            </div>
            <!-- /POST PEEK LIST -->
          </div>
          <!-- /WIDGET BOX CONTENT -->
       </div>
<?php } ?>      
<?php widgets(5); ?>       
</div>
<div class="grid-column" >
<?php   dir_cat_list();   ?>
</div>
<div class="grid-column" >
<?php widgets(6); ?>
</div>
</div>

<?php }else{ echo"404"; }  ?>