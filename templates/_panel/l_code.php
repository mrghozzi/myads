<?php if(isset($s_st) AND ($s_st=="buyfgeufb")){ dinstall_d();
   $o_type =  "extensions_code";
 $bnextensions = $db_con->prepare("SELECT  * FROM `options` WHERE o_type=:o_type " );
$bnextensions->bindParam(":o_type", $o_type);
$bnextensions->execute();
$abextensions=$bnextensions->fetch(PDO::FETCH_ASSOC);
$extensions_code = $abextensions['o_valuer'];
?>
<div class="grid grid change-on-desktop" >
       <div class="achievement-box secondary" style="background: url(<?php url_site();  ?>/templates/_panel/img/banner/03.jpg) no-repeat 50%; background-size: cover " >
          <!-- ACHIEVEMENT BOX INFO WRAP -->
          <div class="achievement-box-info-wrap">
            <!-- ACHIEVEMENT BOX IMAGE -->
            <img class="achievement-box-image" src="<?php url_site();  ?>/templates/_panel/img/banner/link_ads.png" alt="badge-caffeinated-b">
            <!-- /ACHIEVEMENT BOX IMAGE -->

            <!-- ACHIEVEMENT BOX INFO -->
            <div class="achievement-box-info">
              <!-- ACHIEVEMENT BOX TITLE -->
              <p class="achievement-box-title"><?php lang('codes'); ?>&nbsp;<?php lang('textads'); ?></p>
              <!-- /ACHIEVEMENT BOX TITLE -->

              <!-- ACHIEVEMENT BOX TEXT -->
              <p class="achievement-box-text"><b><?php lang('yhtierbpyaci'); ?></b></p>
              <!-- /ACHIEVEMENT BOX TEXT -->
            </div>
            <!-- /ACHIEVEMENT BOX INFO -->
          </div>
          <!-- /ACHIEVEMENT BOX INFO WRAP -->

          <!-- BUTTON -->
          <a class="button white-solid" href="<?php url_site();  ?>/l_list">
          <?php lang('list'); ?>&nbsp;<?php lang('textads'); ?>
          </a>
          <!-- /BUTTON -->
       </div>
</div>
<div class="grid grid" >
  <div class="grid-column" >
    <div class="widget-box" >
         <!-- WIDGET BOX TITLE -->
         <p class="widget-box-title">Your referral link</p>
         <br />
         <blockquote class="widget-box" >
         <center><kbd><?php ref_url(); ?></kbd></center>
         </blockquote>
         <br />
         <p class="widget-box-title"><i class="fa fa-share"></i>&nbsp;Share your referral link</p>
         <div class="widget-box-content">
            <!-- SOCIAL LINKS -->
            <div class="social-links multiline align-left">
              <!-- SOCIAL LINK -->
              <a class="social-link small facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?php ref_url(); ?>" target="_blank" >
                <!-- SOCIAL LINK ICON -->
                <i class="fa-brands fa-facebook-f" style="color: #ffffff;"></i>
                <!-- /SOCIAL LINK ICON -->
              </a>
              <!-- /SOCIAL LINK -->

              <!-- SOCIAL LINK -->
              <a class="social-link small" href="https://twitter.com/intent/tweet?text=<?php title_site(''); echo"&url="; ref_url(); ?>" style="background-color: #011a24;" target="_blank" >
                <!-- SOCIAL LINK ICON -->
                <i class="fa-brands fa-x-twitter" style="color: #ffffff;"></i>
                <!-- /SOCIAL LINK ICON -->
              </a>
              <!-- /SOCIAL LINK -->

              <!-- SOCIAL LINK -->
              <a class="social-link small" href="https://telegram.me/share/url?url=<?php ref_url(); echo"&text="; title_site(''); ?>" style="background-color: #0088cc;" target="_blank" >
                <!-- SOCIAL LINK ICON -->
                <i class="fa-brands fa-telegram" style="color: #ffffff;"></i>
                <!-- /SOCIAL LINK ICON -->
              </a>
              <!-- /SOCIAL LINK -->

           </div>
         <!-- WIDGET BOX TITLE -->
    </div>
  </div>
  <div class="tab-box">
          <!-- TAB BOX OPTIONS -->
          <div class="tab-box-options">
            <!-- TAB BOX OPTION -->
            <div class="tab-box-option">
              <!-- TAB BOX OPTION TITLE -->
              <p class="tab-box-option-title">468x60</p>
              <!-- /TAB BOX OPTION TITLE -->
            </div>
            <!-- /TAB BOX OPTION -->

            <!-- TAB BOX OPTION -->
            <div class="tab-box-option">
              <!-- TAB BOX OPTION TITLE -->
              <p class="tab-box-option-title"><?php lang('responsive'); ?>&nbsp;<span class="badge badge-info"><font face="Comic Sans MS">beta<br></font></span></p>
              <!-- /TAB BOX OPTION TITLE -->
            </div>
            <!-- /TAB BOX OPTION -->
          </div>
          <!-- /TAB BOX OPTIONS -->

          <!-- TAB BOX ITEMS -->
          <div class="tab-box-items">
            <!-- TAB BOX ITEM -->
            <div class="tab-box-item" style="display: none;">
              <!-- TAB BOX ITEM CONTENT -->
              <div class="tab-box-item-content">
                <!-- TAB BOX ITEM TITLE -->
                <p class="tab-box-item-title">Your promotion tags 468x60  (1 point)</p>
                <hr />
                <!-- /TAB BOX ITEM TITLE -->
                <div class="well" style="color: black;" >
                <textarea class="form-control" type="text"  readonly >
                <?php lnk_mine('468','60');  ?>
                <?php echo htmlspecialchars($extensions_code);  ?>
                </textarea>
                </div>
                <!-- TAB BOX ITEM PARAGRAPH -->
                <p class="tab-box-item-paragraph">
                <center><?php cc_lnk('468','60');  ?></center>
                </p>
                <!-- /TAB BOX ITEM PARAGRAPH -->
              </div>
              <!-- /TAB BOX ITEM CONTENT -->
            </div>
            <!-- /TAB BOX ITEM -->
            <!-- TAB BOX ITEM -->
            <div class="tab-box-item" style="display: none;">
              <!-- TAB BOX ITEM CONTENT -->
              <div class="tab-box-item-content">
                <!-- TAB BOX ITEM TITLE -->
                <p class="tab-box-item-title">Your promotion tags Responsive  (1 point)</p>
                <hr />
                <!-- /TAB BOX ITEM TITLE -->
                <div class="well" style="color: black;" >
                <textarea class="form-control" type="text"  readonly >
                <?php lnk_mine('510','320');  ?>
                <?php echo htmlspecialchars($extensions_code);  ?>
                </textarea>
                </div>
                <!-- TAB BOX ITEM PARAGRAPH -->
                <p class="tab-box-item-paragraph">
                <center><?php cc_lnk('510','320');  ?></center>
                </p>
                <!-- /TAB BOX ITEM PARAGRAPH -->
              </div>
              <!-- /TAB BOX ITEM CONTENT -->
            </div>
            <!-- /TAB BOX ITEM -->

          </div>
          </div>
          <!-- /TAB BOX ITEMS -->
  </div>
</div>
<?php }else{ echo"404"; }  ?>