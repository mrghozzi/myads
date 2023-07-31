<?php if($s_st=="buyfgeufb"){ global $admin_page; ?>

<?php if(isset($admin_page) AND ($admin_page==1)){ ?>
<?php template_mine('admin/admin_header');  ?>
<div class="grid grid-3-6-3 medium-space" >
<div class="grid-column" >
<?php template_mine('admin/admin_nav');  ?>
</div>
<?php }else{ ?>
<div class="grid grid change-on-desktop" >
       <div class="achievement-box secondary" style="background: url(<?php url_site();  ?>/templates/_panel/img/banner/03.jpg) no-repeat 50%; background-size: cover " >
          <!-- ACHIEVEMENT BOX INFO WRAP -->
          <div class="achievement-box-info-wrap">
            <!-- ACHIEVEMENT BOX IMAGE -->
            <img class="achievement-box-image" src="<?php url_site();  ?>/templates/_panel/img/banner/banner_ads.png" alt="badge-caffeinated-b">
            <!-- /ACHIEVEMENT BOX IMAGE -->

            <!-- ACHIEVEMENT BOX INFO -->
            <div class="achievement-box-info">
              <!-- ACHIEVEMENT BOX TITLE -->
              <p class="achievement-box-title"><?php lang('list'); ?>&nbsp;<?php lang('bannads'); ?></p>
              <!-- /ACHIEVEMENT BOX TITLE -->

              <!-- ACHIEVEMENT BOX TEXT -->
              <p class="achievement-box-text"><b><?php lang('yhtierbpyaci'); ?></b></p>
              <!-- /ACHIEVEMENT BOX TEXT -->
            </div>
            <!-- /ACHIEVEMENT BOX INFO -->
          </div>
          <!-- /ACHIEVEMENT BOX INFO WRAP -->

          <!-- BUTTON -->
          <a class="button white-solid" href="<?php url_site();  ?>/b_code">
          <i class="fa fa-code" aria-hidden="true"></i>&nbsp;<?php lang('codes'); ?>&nbsp;<?php lang('bannads'); ?>
          </a>
          <!-- /BUTTON -->
       </div>
</div>
<div class="section-filters-bar v6">
      <!-- SECTION FILTERS BAR ACTIONS -->
      <div class="section-filters-bar-actions" >
      <?php if(isset($elnk_site) AND ($elnk_site==1)){ ?>
      <a href="https://www.adstn.gq/kb/myads:<?php lang('list'); ?>&nbsp;<?php lang('bannads'); ?>" class="button primary " target="_blank">&nbsp;<b><i class="fa fa-question-circle" aria-hidden="true"></i></b></a>
      &nbsp;
      <?php } ?>
      <a class="button tertiary " href="<?php url_site();  ?>/state?ty=banner&st=vu">&nbsp;<i class="fa fa-line-chart" aria-hidden="true"></i>&nbsp;</a>
      </div>
      <p class="text-sticker">
          <!-- TEXT STICKER ICON -->
          <svg class="text-sticker-icon icon-info">
            <use xlink:href="#svg-info"></use>
          </svg>
          <!-- TEXT STICKER ICON -->
          <?php echo $lang['you_have']."&nbsp;"; user_row('nvu'); echo "&nbsp;".$lang['ptvyba']; ?>
      </p>
      <div class="section-filters-bar-actions">
        <!-- BUTTON -->
        <a href="<?php url_site();  ?>/promote?p=banners" class="button secondary" style="color: #fff;" >
        <i class="fa fa-plus nav_icon"></i>&nbsp;
        <?php lang('add'); ?>
        </a>
        <!-- /BUTTON -->
      </div>
      <!-- /SECTION FILTERS BAR ACTIONS -->
</div>
<div class="grid grid" >
<?php } ?>
  <div class="grid-column" >
    <div class="widget-box" >
					<table id="tablepagination" class="table table table-hover">
						<thead>
							<tr>
                              <th>#ID</th>
							  <th>Name</th>
                              <th>Vu</th>
							  <th>Clik</th>
                              <th>Size</th>
                              <th>Statu</th>
                            </tr>
						</thead>
						<tbody>
                        <?php bnr_list();  ?>
               </tbody>
					</table>
    </div>
  </div>
</div>
<?php }else{ echo"404"; }  ?>