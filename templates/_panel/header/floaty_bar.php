<?php if(isset($s_st) AND ($s_st=="buyfgeufb")){  ?>
<?php if(isset($_COOKIE['user'])){ ?>
   <!-- FLOATY BAR -->
  <aside class="floaty-bar">
  <?php   if(isset($_COOKIE['user']))  { ?>
    <!-- BAR ACTIONS -->
    <div class="bar-actions">
      <!-- PROGRESS STAT -->
      <div class="progress-stat">
        <!-- BAR PROGRESS WRAP -->
        <div class="bar-progress-wrap">
          <!-- BAR PROGRESS INFO -->
          <a class="bar-progress-info" href="<?php url_site();  ?>/history"><?php user_row('pts'); ?>&nbsp;PTS</a>
          <!-- /BAR PROGRESS INFO -->
        </div>
      </div>
      <!-- /PROGRESS STAT -->
    </div>
    <!-- /BAR ACTIONS -->
    <?php } ?>
    <!-- BAR ACTIONS -->
    <div class="bar-actions">
      <!-- ACTION LIST -->
      <div class="action-list dark">
        <!-- ACTION LIST ITEM -->
        <a class="action-list-item" href="<?php url_site();  ?>/portal">
          <!-- ACTION LIST ITEM ICON -->
          <svg class="action-list-item-icon icon-newsfeed">
            <use xlink:href="#svg-newsfeed"></use>
          </svg>
          <!-- /ACTION LIST ITEM ICON -->
        </a>
        <!-- /ACTION LIST ITEM -->
        <!-- ACTION LIST ITEM -->
        <a class="action-list-item" href="<?php url_site();  ?>/messages">
          <!-- ACTION LIST ITEM ICON -->
          <svg class="action-list-item-icon icon-messages">
            <use xlink:href="#svg-messages"></use>
          </svg>
          <!-- /ACTION LIST ITEM ICON -->
          <?php msg_nbr('span');  ?>
        </a>
        <!-- /ACTION LIST ITEM -->

        <!-- ACTION LIST ITEM -->
        <a class="action-list-item listnotif" href="<?php url_site();  ?>/notification">
          <!-- ACTION LIST ITEM ICON -->
          <svg class="action-list-item-icon icon-notification">
            <use xlink:href="#svg-notification"></use>
          </svg>
          <div id="count">
            <?php ntf_nbr('span');  ?>
            </div>
          <!-- /ACTION LIST ITEM ICON -->
        </a>
        <!-- /ACTION LIST ITEM -->
      </div>
      <!-- /ACTION LIST -->

      <!-- ACTION ITEM WRAP -->
      <a class="action-item-wrap" href="<?php url_site();  ?>/e<?php echo $_COOKIE['user']; ?>">
        <!-- ACTION ITEM -->
        <div class="action-item dark">
          <!-- ACTION ITEM ICON -->
          <svg class="action-item-icon icon-settings">
            <use xlink:href="#svg-settings"></use>
          </svg>
          <!-- /ACTION ITEM ICON -->
        </div>
        <!-- /ACTION ITEM -->
      </a>
      <?php if(isset($_COOKIE['user']) && isset($_COOKIE['admin']) && ($_COOKIE['admin']==$hachadmin)  ){ ?>
      <a class="action-item-wrap" href="<?php url_site();  ?>/admincp?home">
        <!-- ACTION ITEM -->
        <div class="action-item dark">
          <!-- ACTION ITEM ICON -->
          <svg class="action-item-icon icon-private">
            <use xlink:href="#svg-private"></use>
          </svg>
          <!-- /ACTION ITEM ICON -->
        </div>
        <!-- /ACTION ITEM -->
      </a>
      <?php } ?>
      <!-- /ACTION ITEM WRAP -->
    </div>
    <!-- /BAR ACTIONS -->
  </aside>
  <!-- /FLOATY BAR -->
  <?php }else{ ?>
  <aside class="floaty-bar logged-out">
    <!-- LOGIN BUTTON -->
    <a class="login-button button small primary" href="<?php url_site();  ?>/login"><?php lang('login'); ?></a>&nbsp;
    <a class="login-button button small primary" href="<?php url_site();  ?>/register"><?php lang('sign_up'); ?></a>
    <!-- /LOGIN BUTTON -->
  </aside>
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
  <?php }?>
<?php }else{ echo"404"; }  ?>