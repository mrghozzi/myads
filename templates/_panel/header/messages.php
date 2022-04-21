<?php if($s_st=="buyfgeufb"){  ?>
<!-- ACTION LIST ITEM WRAP -->
        <div class="action-list-item-wrap">
          <!-- ACTION LIST ITEM -->
          <div class="action-list-item header-dropdown-trigger">
            <!-- ACTION LIST ITEM ICON -->
            <svg class="action-list-item-icon icon-messages">
              <use xlink:href="#svg-messages"></use>
            </svg>
            <!-- /ACTION LIST ITEM ICON -->
            <div id="count">
            <?php msg_nbr('span');  ?>
            </div>
          </div>
          <!-- /ACTION LIST ITEM -->

          <!-- DROPDOWN BOX -->
          <div class="dropdown-box header-dropdown">
            <!-- DROPDOWN BOX HEADER -->
            <div class="dropdown-box-header">
              <!-- DROPDOWN BOX HEADER TITLE -->
              <p class="dropdown-box-header-title">Messages</p>
              <!-- /DROPDOWN BOX HEADER TITLE -->

            </div>
            <!-- /DROPDOWN BOX HEADER -->

            <!-- DROPDOWN BOX LIST -->
            <div class="dropdown-box-list medium" data-simplebar>
             <?php msg_nbr('list'); ?>
            </div>
            <!-- /DROPDOWN BOX LIST -->

            <!-- DROPDOWN BOX BUTTON -->
            <a class="dropdown-box-button primary" href="<?php url_site();  ?>/messages"><?php lang('all_msg'); ?></a>
            <!-- /DROPDOWN BOX BUTTON -->
          </div>
          <!-- /DROPDOWN BOX -->
        </div>
        <!-- /ACTION LIST ITEM WRAP -->
<?php }else{ echo"404"; }  ?>