<?php if($s_st=="buyfgeufb"){  ?>
  <!-- SECTION BANNER -->
<div class="section-banner" style="background: url(<?php url_site();  ?>/templates/_panel/img/banner/Newsfeed.png) no-repeat 50%;" >
      <!-- SECTION BANNER ICON -->
      <img class="section-banner-icon" src="<?php url_site();  ?>/templates/_panel/img/banner/discussion-icon.png" >
      <!-- /SECTION BANNER ICON -->

      <!-- SECTION BANNER TITLE -->
      <p class="section-banner-title"><?php lang('forum'); ?></p>
      <!-- /SECTION BANNER TITLE -->

      <!-- SECTION BANNER TEXT -->
      <p class="section-banner-text"></p>
      <!-- /SECTION BANNER TEXT -->
</div>
    <!-- /SECTION BANNER -->
     <?php ads_site(4); ?>
<div class="section-filters-bar v6">
      <!-- SECTION FILTERS BAR ACTIONS -->
      <div class="section-filters-bar-actions">
      <?php if(isset($elnk_site) AND ($elnk_site==1)){ ?>
      <a href="https://www.adstn.gq/kb/myads:post" class="button primary " target="_blank">&nbsp;<b><i class="fa fa-question-circle" aria-hidden="true"></i></b></a>
      <?php } ?>
      </div>
      <?php   if(isset($_COOKIE['user']))  { ?>
      <div class="section-filters-bar-actions">
        <!-- BUTTON -->
        <a href="<?php url_site();  ?>/post" class="button secondary" style="color: #fff;">
        <i class="fa fa-plus nav_icon"></i>&nbsp;
        <?php lang('add'); ?></a>
        <!-- /BUTTON -->
      </div>
      <?php } ?>
      <!-- /SECTION FILTERS BAR ACTIONS -->
</div>
<div class="table table-forum">
      <!-- TABLE HEADER -->
      <div class="table-header">
        <!-- TABLE HEADER COLUMN -->
        <div class="table-header-column">
          <!-- TABLE HEADER TITLE -->
          <p class="table-header-title"><?php lang('cat_s'); ?></p>
          <!-- /TABLE HEADER TITLE -->
        </div>
        <!-- /TABLE HEADER COLUMN -->

        <!-- TABLE HEADER COLUMN -->
        <div class="table-header-column centered padded-medium">
          <!-- TABLE HEADER TITLE -->
          <p class="table-header-title"><?php lang('topics'); ?></p>
          <!-- /TABLE HEADER TITLE -->
        </div>
        <!-- /TABLE HEADER COLUMN -->

        <!-- TABLE HEADER COLUMN -->
        <div class="table-header-column padded-big-left">
          <!-- TABLE HEADER TITLE -->
          <p class="table-header-title"><?php lang('latest_post'); ?></p>
          <!-- /TABLE HEADER TITLE -->
        </div>
        <!-- /TABLE HEADER COLUMN -->
      </div>
      <!-- /TABLE HEADER -->

      <!-- TABLE BODY -->
      <div class="table-body">
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
if(isset($catdid)){
$catdnb = $db_con->prepare("SELECT  * FROM status WHERE tp_id='{$catdid}' AND s_type=2 " );
$catdnb->execute();
$abdnb=$catdnb->fetch(PDO::FETCH_ASSOC);
$time_stt= convertTime($abdnb['date']);
}else{

  $time_stt = "";

}
?>
        <!-- TABLE ROW -->
        <div class="table-row big">
          <!-- TABLE COLUMN -->
          <div class="table-column">
            <!-- FORUM CATEGORY -->
            <div class="forum-category">
              <!-- FORUM CATEGORY IMAGE -->
              <h1><a href="<?php echo "{$url_site}/f{$wt['id']}"; ?>">
                <i class="fa <?php echo $wt['icons']; ?>" aria-hidden="true"></i>
              </a></h1>
              <!-- /FORUM CATEGORY IMAGE -->

              <!-- FORUM CATEGORY INFO -->
              <div class="forum-category-info">
                <!-- FORUM CATEGORY TITLE -->
                <p class="forum-category-title"><a href="<?php echo "{$url_site}/f{$wt['id']}"; ?>"><?php echo $wt['name']; ?></a></p>
                <!-- /FORUM CATEGORY TITLE -->

              </div>
              <!-- /FORUM CATEGORY INFO -->
            </div>
            <!-- /FORUM CATEGORY -->
          </div>
          <!-- /TABLE COLUMN -->

          <!-- TABLE COLUMN -->
          <div class="table-column centered padded-medium">
            <!-- TABLE TITLE -->
            <p class="table-title"><?php echo $abcat['nbr']; ?></p>
            <!-- /TABLE TITLE -->
          </div>
          <!-- /TABLE COLUMN -->

          <!-- TABLE COLUMN -->
          <div class="table-column padded-big-left">
            <!-- TABLE LINK -->
            <a class="table-link" href="<?php echo "{$url_site}/t{$sucat['id']}"; ?>"><?php echo $sucat['name']; ?></a>
            <!-- /TABLE LINK -->

            <!-- TABLE LINK -->
            <a class="table-link" href="<?php echo "{$url_site}/t{$sucat['id']}"; ?>"><?php echo "<i class=\"fa fa-clock-o\" aria-hidden=\"true\"></i> منذ {$time_stt}"; ?></a>
            <!-- /TABLE LINK -->
            <!-- /TABLE LINK -->
          </div>
          <!-- /TABLE COLUMN -->
        </div>
        <!-- /TABLE ROW -->
<?php } ?>
      </div>
      <!-- /TABLE BODY -->
    </div>
<?php }else{ echo"404"; }  ?>