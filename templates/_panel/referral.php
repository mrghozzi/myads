<?php if(isset($s_st) AND ($s_st=="buyfgeufb")){ dinstall_d(); ?>
<div class="grid grid change-on-desktop" >
       <div class="achievement-box secondary" style="background: url(<?php url_site();  ?>/templates/_panel/img/banner/03.jpg) no-repeat 50%; background-size: cover " >
          <!-- ACHIEVEMENT BOX INFO WRAP -->
          <div class="achievement-box-info-wrap">
            <!-- ACHIEVEMENT BOX IMAGE -->
            <img class="achievement-box-image" src="<?php url_site();  ?>/templates/_panel/img/banner/referral.png" alt="badge-caffeinated-b">
            <!-- /ACHIEVEMENT BOX IMAGE -->

            <!-- ACHIEVEMENT BOX INFO -->
            <div class="achievement-box-info">
              <!-- ACHIEVEMENT BOX TITLE -->
              <p class="achievement-box-title"><?php lang('list'); ?>&nbsp;<?php lang('referal'); ?></p>
              <!-- /ACHIEVEMENT BOX TITLE -->

              <!-- ACHIEVEMENT BOX TEXT -->
              <p class="achievement-box-text"><b><?php lang('ryffyrly'); ?></b></p>
              <!-- /ACHIEVEMENT BOX TEXT -->
            </div>
            <!-- /ACHIEVEMENT BOX INFO -->
          </div>
          <!-- /ACHIEVEMENT BOX INFO WRAP -->

          <!-- BUTTON -->
          <a class="button white-solid" href="<?php url_site();  ?>/r_code">
          <i class="fa fa-code" aria-hidden="true"></i>&nbsp;<?php lang('codes'); ?>&nbsp;<?php lang('referal'); ?>
          </a>
          <!-- /BUTTON -->
       </div>
</div>
<div class="grid grid" >
  <div class="grid-column" >
    <div class="widget-box" >
        <table class="table table-borderless table-hover" id="tablepagination">
			  <thead>
			   <tr>
                <th>#ID</th>
				<th>Username</th>
                <th>Start Date</th>
				<th>PTS</th>
               </tr>
			  </thead>
			  <tbody>
               <?php include "include/referral.php";  ?>
              </tbody>
		</table>
    </div>
  </div>
</div>
<?php }else{ echo"404"; }  ?> 