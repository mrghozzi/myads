<?php if(isset($s_st) AND ($s_st=="buyfgeufb")){ dinstall_d();

$catdids = $_GET['f'];
$catdids=preg_replace("/\'/", "", $catdids);
if(is_numeric($catdids)){
$rescat =$db_con->prepare("SELECT * FROM `f_cat` WHERE id={$catdids} ORDER BY `id` DESC ");
if($rescat->execute()){
$wtcat=$rescat->fetch(PDO::FETCH_ASSOC);
if(isset($wtcat['id'])){
 ?>

  <!-- SECTION BANNER -->
    <div class="section-banner" style="background: url(<?php url_site();  ?>/templates/_panel/img/banner/Newsfeed.png) no-repeat 50%;" >
      <!-- SECTION BANNER ICON -->
      <img class="section-banner-icon" src="<?php url_site();  ?>/templates/_panel/img/banner/discussion-icon.png"  alt="overview-icon">
      <!-- /SECTION BANNER ICON -->

      <!-- SECTION BANNER TEXT -->
      <p class="section-banner-title">
        <h3 style="color: #fff; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" >
         <?php echo $wtcat['name'];  ?>
        </h3>
      </p>
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
              // Get all forum categories
              $categories = $db_con->prepare("SELECT * FROM `f_cat` ORDER BY `id` DESC");
              $categories->execute();
              
              while($category = $categories->fetch(PDO::FETCH_ASSOC)) {
                $cat_id = $category['id'];
                
                // Count active topics in this category
                $topic_count = $db_con->prepare("SELECT COUNT(id) as count FROM forum WHERE statu=1 AND cat=:cat_id");
                $topic_count->bindParam(':cat_id', $cat_id);
                $topic_count->execute();
                $count_result = $topic_count->fetch(PDO::FETCH_ASSOC);
                
                // Get latest topic in this category
                $latest_topic = $db_con->prepare("SELECT * FROM `forum` WHERE statu=1 AND cat=:cat_id ORDER BY `id` DESC LIMIT 1");
                $latest_topic->bindParam(':cat_id', $cat_id);
                $latest_topic->execute();
                $latest_result = $latest_topic->fetch(PDO::FETCH_ASSOC);
              ?>
              
              <!-- CATEGORY ITEM -->
              <div class="post-peek card">
                <!-- CATEGORY LINK -->
                <h4>
                  <a href="<?php echo "{$url_site}/f{$category['id']}"; ?>">
                    <i class="fa <?php echo $category['icons']; ?>" aria-hidden="true"></i>
                    &nbsp;<?php echo $category['name']; ?>
                  </a>
                </h4>
                <!-- /CATEGORY LINK -->
              </div>
              <!-- /CATEGORY ITEM -->
              
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
<?php 
   }else{ template_mine('404'); }
  }else{ template_mine('404'); }
 }else{ template_mine('404'); }
}else{ echo"404"; }  ?>