<?php if($s_st=="buyfgeufb"){ global $admin_page;  ?>
<div class="grid grid change-on-desktop" >
       <div class="achievement-box secondary" style="background: url(<?php url_site();  ?>/templates/_panel/img/banner/state_banner.png) no-repeat 50%; background-size: cover " >
          <!-- ACHIEVEMENT BOX INFO WRAP -->
          <div class="achievement-box-info-wrap">
            <!-- ACHIEVEMENT BOX IMAGE -->
            <img class="achievement-box-image" src="<?php url_site();  ?>/templates/_panel/img/banner/statistics.png" alt="badge-caffeinated-b">
            <!-- /ACHIEVEMENT BOX IMAGE -->

            <!-- ACHIEVEMENT BOX INFO -->
            <div class="achievement-box-info">
              <!-- ACHIEVEMENT BOX TITLE -->
              <p class="achievement-box-title">
<?php
if(isset($_GET['ty']) AND ($_GET['ty']=="banner")){ lang('bannads'); }
else if(isset($_GET['ty']) AND ($_GET['ty']=="link")){ lang('textads'); }
else if(isset($_GET['ty']) AND ($_GET['ty']=="vu")){ lang('bannads'); echo "<br />"; lang('hits'); }
else if(isset($_GET['ty']) AND ($_GET['ty']=="clik")){ lang('textads'); echo "<br />"; lang('hits'); }
?>
              </p>
              <!-- /ACHIEVEMENT BOX TITLE -->

              <!-- ACHIEVEMENT BOX TEXT -->
              <p class="achievement-box-text"><b>
<?php
if(isset($_GET['id'])){  echo "N&deg;".$_GET['id']; }
else if(isset($_GET['st'])){ echo "@"; get_user($_GET['st'],"username"); }
?>
              </b></p>
              <!-- /ACHIEVEMENT BOX TEXT -->
            </div>
            <!-- /ACHIEVEMENT BOX INFO -->
          </div>
          <!-- /ACHIEVEMENT BOX INFO WRAP -->

          <!-- BUTTON -->
          <a class="button white-solid" href="<?php echo $_SERVER['HTTP_REFERER'];  ?>">
          <i class="fa fa-angle-double-left" aria-hidden="true"></i>&nbsp;<?php lang('go_back'); ?>
          </a>
          <!-- /BUTTON -->
       </div>
</div>
<?php if(isset($admin_page) AND ($admin_page==1)){ ?>
<div class="grid grid-3-6-3 medium-space" >
<div class="grid-column" >
<?php template_mine('admin/admin_nav');  ?>
</div>
<?php }else{ ?>
<div class="grid grid" >
<?php } ?>
  <div class="grid-column" >
    <div class="widget-box" >
			 <table id="tablepagination" class="table table-borderless table-hover">
				 <thead>
				  <tr>
                   <th>#ID</th>
				   <th>Url</th>
                   <th>Time</th>
				   <th>Browser</th>
                   <th>platform</th>
                   <th>Ip</th>
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