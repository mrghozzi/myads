<?php if (isset($s_st) AND ($s_st == "buyfgeufb")) { 
    dinstall_d();  
?>
<div class="account-hub-content">
    <div class="section-header">
        <!-- SECTION HEADER INFO -->
        <div class="section-header-info">
            <!-- SECTION PRETITLE -->
            <p class="section-pretitle"><?php echo $lang['my_profile']; ?></p>
            <!-- /SECTION PRETITLE -->

            <!-- SECTION TITLE -->
            <h2 class="section-title"><?php echo $lang['notifications']; ?></h2>
            <!-- /SECTION TITLE -->
        </div>
        <!-- /SECTION HEADER INFO -->
    </div>
    <div class="notification-box-list">
        <?php ntf_list(); ?>
    </div>
</div>
<?php } else { 
    echo "404"; 
} ?>