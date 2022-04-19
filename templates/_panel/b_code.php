<?php if($s_st=="buyfgeufb"){
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
            <img class="achievement-box-image" src="<?php url_site();  ?>/templates/_panel/img/banner/banner_ads.png" alt="badge-caffeinated-b">
            <!-- /ACHIEVEMENT BOX IMAGE -->

            <!-- ACHIEVEMENT BOX INFO -->
            <div class="achievement-box-info">
              <!-- ACHIEVEMENT BOX TITLE -->
              <p class="achievement-box-title"><?php lang('codes'); ?>&nbsp;<?php lang('bannads'); ?></p>
              <!-- /ACHIEVEMENT BOX TITLE -->

              <!-- ACHIEVEMENT BOX TEXT -->
              <p class="achievement-box-text"><b><?php lang('yhtierbpyaci'); ?></b></p>
              <!-- /ACHIEVEMENT BOX TEXT -->
            </div>
            <!-- /ACHIEVEMENT BOX INFO -->
          </div>
          <!-- /ACHIEVEMENT BOX INFO WRAP -->

          <!-- BUTTON -->
          <a class="button white-solid" href="<?php url_site();  ?>/b_list">
          <?php lang('list'); ?>&nbsp;<?php lang('bannads'); ?>
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
                <svg class="social-link-icon icon-facebook">
                  <use xlink:href="#svg-facebook"></use>
                </svg>
                <!-- /SOCIAL LINK ICON -->
              </a>
              <!-- /SOCIAL LINK -->

              <!-- SOCIAL LINK -->
              <a class="social-link small twitter" href="https://twitter.com/home?status=<?php title_site(''); echo"&nbsp;"; ref_url(); ?>" target="_blank" >
                <!-- SOCIAL LINK ICON -->
                <svg class="social-link-icon icon-twitter">
                  <use xlink:href="#svg-twitter"></use>
                </svg>
                <!-- /SOCIAL LINK ICON -->
              </a>
              <!-- /SOCIAL LINK -->

              <!-- SOCIAL LINK -->
              <a class="social-link small youtube" href="https://www.wasp.gq/sharer?url=<?php title_site(''); echo"&nbsp;"; ref_url(); ?>" target="_blank" >
                <!-- SOCIAL LINK ICON -->
                <i class="fa fa-wikipedia-w" aria-hidden="true" style="color: #fff;" ></i>
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
            <div class="tab-box-option active">
              <!-- TAB BOX OPTION TITLE -->
              <p class="tab-box-option-title">728x90</p>
              <!-- /TAB BOX OPTION TITLE -->
            </div>
            <!-- /TAB BOX OPTION -->

            <!-- TAB BOX OPTION -->
            <div class="tab-box-option">
              <!-- TAB BOX OPTION TITLE -->
              <p class="tab-box-option-title">300x250</p>
              <!-- /TAB BOX OPTION TITLE -->
            </div>
            <!-- /TAB BOX OPTION -->

            <!-- TAB BOX OPTION -->
            <div class="tab-box-option">
              <!-- TAB BOX OPTION TITLE -->
              <p class="tab-box-option-title">160x600</p>
              <!-- /TAB BOX OPTION TITLE -->
            </div>
            <!-- /TAB BOX OPTION -->

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
            <div class="tab-box-item" style="display: block;">
              <!-- TAB BOX ITEM CONTENT -->
              <div class="tab-box-item-content">
                <!-- TAB BOX ITEM TITLE -->
                <p class="tab-box-item-title">Your promotion tags 728x90  (1 point)</p>
                <hr />
                <!-- /TAB BOX ITEM TITLE -->
                <div class="well" style="color: black;" >
                <textarea class="form-control" type="text"  readonly >
                <?php bnr_mine('728','90');  ?>
                <?php echo htmlspecialchars($extensions_code);  ?>
                </textarea>
                </div>
                <!-- TAB BOX ITEM PARAGRAPH -->
                <p class="tab-box-item-paragraph">
                <center><?php cc_bnr('728','90');  ?></center>
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
                <p class="tab-box-item-title">Your promotion tags 300x250  (1 point)</p>
                <hr />
                <!-- /TAB BOX ITEM TITLE -->
                <div class="well" style="color: black;" >
                <textarea class="form-control" type="text"  readonly >
                <?php bnr_mine('300','250');  ?>
                <?php echo htmlspecialchars($extensions_code);  ?>
                </textarea>
                </div>
                <!-- TAB BOX ITEM PARAGRAPH -->
                <p class="tab-box-item-paragraph">
                <center><?php cc_bnr('300','250');  ?></center>
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
                <p class="tab-box-item-title">Your promotion tags 160x600 (1 point)</p>
                <hr />
                <!-- /TAB BOX ITEM TITLE -->
                <div class="well" style="color: black;" >
                <textarea class="form-control" type="text"  readonly >
                <?php bnr_mine('160','600');  ?>
                <?php echo htmlspecialchars($extensions_code);  ?>
                </textarea>
                </div>
                <!-- TAB BOX ITEM PARAGRAPH -->
                <p class="tab-box-item-paragraph">
                <center><?php cc_bnr('160','600');  ?></center>
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
                <p class="tab-box-item-title">Your promotion tags 468x60  (1 point)</p>
                <hr />
                <!-- /TAB BOX ITEM TITLE -->
                <div class="well" style="color: black;" >
                <textarea class="form-control" type="text"  readonly >
                <?php bnr_mine('468','60');  ?>
                <?php echo htmlspecialchars($extensions_code);  ?>
                </textarea>
                </div>
                <!-- TAB BOX ITEM PARAGRAPH -->
                <p class="tab-box-item-paragraph">
                <center><?php cc_bnr('468','60');  ?></center>
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
                <?php bnr_mine('responsive','60');  ?>
                <?php echo htmlspecialchars($extensions_code);  ?>
                </textarea>
                </div>
                <!-- TAB BOX ITEM PARAGRAPH -->
                <p class="tab-box-item-paragraph">
                <center><?php cc_bnr('responsive','60');  ?></center>
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