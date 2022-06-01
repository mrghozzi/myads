<?php if($s_st=="buyfgeufb"){

    $catdids = $_GET['f'];
$rescat =$db_con->prepare("SELECT * FROM `f_cat` WHERE id={$catdids} ORDER BY `id` DESC ");
$rescat->execute();
$wtcat=$rescat->fetch(PDO::FETCH_ASSOC)
 ?>

  <!-- SECTION BANNER -->
    <div class="section-banner" style="background: url(<?php url_site();  ?>/templates/_panel/img/banner/Newsfeed.png) no-repeat 50%;" >
      <!-- SECTION BANNER ICON -->
      <img class="section-banner-icon" src="<?php url_site();  ?>/templates/_panel/img/banner/discussion-icon.png"  alt="overview-icon">
      <!-- /SECTION BANNER ICON -->

      <!-- SECTION BANNER TITLE -->
      <p class="section-banner-title"><center><h1 style="color: #fff;" ><?php lang('forum'); ?></h1></center></p>
      <!-- /SECTION BANNER TITLE -->

      <!-- SECTION BANNER TEXT -->
      <p class="section-banner-text"><h4 style="color: #fff;" ><center><i class="fa <?php echo $wtcat['icons'];  ?>" aria-hidden="true"></i>&nbsp;<?php echo $wtcat['name'];  ?></h4></center></p>
      <!-- /SECTION BANNER TEXT -->
    </div>
    <!-- /SECTION BANNER -->

<div class="grid grid-3-6-3 mobile-prefer-content" >
<div class="grid-column" >
       <div class="widget-box">

          <!-- WIDGET BOX TITLE -->
          <p class="widget-box-title"><h4><?php lang('board');  ?></h4></p>
          <!-- /WIDGET BOX TITLE -->

          <!-- WIDGET BOX CONTENT -->
          <div class="widget-box-content">
            <!-- POST PEEK LIST -->
            <div class="post-peek-list">
              <a href="<?php url_site();  ?>/forum" class="btn btn-primary" >&nbsp;<i class="fa fa-home" aria-hidden="true"></i>&nbsp;</a>
              <?php   if(isset($_COOKIE['user']))  { ?>
              <a href="<?php url_site();  ?>/post" class="btn btn-success" ><?php lang('w_new_tpc');  ?>&nbsp;<i class="fa fa-plus" aria-hidden="true"></i> </a>
              <?php } ?>
            </div>
            <!-- /POST PEEK LIST -->
          </div>
          <!-- /WIDGET BOX CONTENT -->
       </div>
       <div class="widget-box">

          <!-- WIDGET BOX TITLE -->
          <p class="widget-box-title"><h4><?php lang('cat_s');  ?></h4></p>
          <!-- /WIDGET BOX TITLE -->

          <!-- WIDGET BOX CONTENT -->
          <div class="widget-box-content">
            <!-- POST PEEK LIST -->
            <div class="post-peek-list">
<?php
$statement = "`f_cat` WHERE id ORDER BY `id` DESC";
$results =$db_con->prepare("SELECT * FROM {$statement} ");
$results->execute();
while($wt=$results->fetch(PDO::FETCH_ASSOC)) {
  $catdids = $wt['id'];
$catcount = $db_con->prepare("SELECT  COUNT(id) as nbr FROM forum WHERE statu=1 AND cat={$catdids} " );
$catcount->execute();
$abcat=$catcount->fetch(PDO::FETCH_ASSOC);
$catusz = $db_con->prepare("SELECT *  FROM `forum` WHERE statu=1 AND  cat={$catdids} ORDER BY `id` DESC " );
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);
$catdid = $sucat['id'];
 ?>
              <!-- POST PEEK -->
              <div class="post-peek card">
                <!-- POST PEEK IMAGE -->

                <!-- POST PEEK TITLE -->
                <h4>
                <a href="<?php echo "{$url_site}/f{$wt['id']}"; ?>">
                <i class="fa <?php echo $wt['icons']; ?>" aria-hidden="true"></i>&nbsp;<?php echo $wt['name']; ?></a>
                </h4>
                <!-- /POST PEEK TITLE -->


                <!-- /POST PEEK TEXT -->
              </div>
              <!-- /POST PEEK -->
<?php } ?>
            </div>
            <!-- /POST PEEK LIST -->
          </div>
          <!-- /WIDGET BOX CONTENT -->
       </div>
<?php widgets(3); ?>
</div>
<div class="grid-column" >
<?php   forum_tpc_list();   ?>
</div>
<div class="grid-column" >
<?php widgets(4); ?>
</div>
</div>
<?php }else{ echo"404"; }  ?>