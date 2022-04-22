<?php if($s_st=="buyfgeufb"){  ?>
<!-- ACTION LIST ITEM WRAP -->
        <div class="action-list-item-wrap">
          <!-- ACTION LIST ITEM -->
          <div class="action-list-item header-dropdown-trigger listnotif" >
            <!-- ACTION LIST ITEM ICON -->
            <svg class="action-list-item-icon icon-notification">
              <use xlink:href="#svg-notification"></use>
            </svg>
            <div id="count">
            <?php ntf_nbr('span');  ?>
            </div>
            <!-- /ACTION LIST ITEM ICON -->
          </div>
          <!-- /ACTION LIST ITEM -->

          <!-- DROPDOWN BOX -->
          <div class="dropdown-box header-dropdown">
            <!-- DROPDOWN BOX HEADER -->
            <div class="dropdown-box-header">
              <!-- DROPDOWN BOX HEADER TITLE -->
              <p class="dropdown-box-header-title">Notifications</p>
              <!-- /DROPDOWN BOX HEADER TITLE -->

             </div>
            <!-- /DROPDOWN BOX HEADER -->
            <!-- DROPDOWN BOX LIST -->
            <div class="dropdown-box-list" data-simplebar>
            <?php ntf_nbr('list'); ?>
            </div>
            <!-- DROPDOWN BOX BUTTON -->
            <a class="dropdown-box-button secondary" href="<?php url_site();  ?>/notification">View all Notifications</a>
            <!-- /DROPDOWN BOX BUTTON -->
          </div>
          <!-- /DROPDOWN BOX -->
        </div>
        <!-- /ACTION LIST ITEM WRAP -->
<script>
$(document).ready(function(){

 function load_unseen_notification(view = '')
 {
  $.ajax({
   url:"<?php url_site();  ?>/requests/fetch.php",
   method:"POST",
   data:{view:view},
   dataType:"json"

  });

 }
 $(document).on('click', '.listnotif', function(){
  $('#count').html('');
  load_unseen_notification('yes');
 });


});
</script>
<?php }else{ echo"404"; }  ?>