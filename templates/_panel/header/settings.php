<?php if($s_st=="buyfgeufb"){  ?>
<!-- ACTION ITEM WRAP -->
 <div class="action-item-wrap">
        <!-- ACTION ITEM -->
        <div class="action-item dark header-settings-dropdown-trigger">
          <!-- ACTION ITEM ICON -->
          <svg class="action-item-icon icon-settings">
            <use xlink:href="#svg-settings"></use>
          </svg>
          <!-- /ACTION ITEM ICON -->
        </div>
        <!-- /ACTION ITEM -->
     <!-- DROPDOWN NAVIGATION -->
        <div class="dropdown-navigation header-settings-dropdown">
          <!-- DROPDOWN NAVIGATION HEADER -->
          <div class="dropdown-navigation-header">
            <!-- USER STATUS -->
            <div class="user-status">
              <!-- USER STATUS AVATAR -->
              <a class="user-status-avatar" href="profile-timeline.html">
                <!-- USER AVATAR -->
                <div class="user-avatar small no-outline">
                  <!-- USER AVATAR CONTENT -->
                  <div class="user-avatar-content">
                    <!-- HEXAGON -->
                    <div class="hexagon-image-30-32" data-src="img/avatar/01.jpg"></div>
                    <!-- /HEXAGON -->
                  </div>
                  <!-- /USER AVATAR CONTENT -->

                  <!-- USER AVATAR PROGRESS -->
                  <div class="user-avatar-progress">
                    <!-- HEXAGON -->
                    <div class="hexagon-progress-40-44"></div>
                    <!-- /HEXAGON -->
                  </div>
                  <!-- /USER AVATAR PROGRESS -->

                  <!-- USER AVATAR PROGRESS BORDER -->
                  <div class="user-avatar-progress-border">
                    <!-- HEXAGON -->
                    <div class="hexagon-border-40-44"></div>
                    <!-- /HEXAGON -->
                  </div>
                  <!-- /USER AVATAR PROGRESS BORDER -->

                  <!-- USER AVATAR BADGE -->
                  <div class="user-avatar-badge">
                    <!-- USER AVATAR BADGE BORDER -->
                    <div class="user-avatar-badge-border">
                      <!-- HEXAGON -->
                      <div class="hexagon-22-24"></div>
                      <!-- /HEXAGON -->
                    </div>
                    <!-- /USER AVATAR BADGE BORDER -->

                    <!-- USER AVATAR BADGE CONTENT -->
                    <div class="user-avatar-badge-content">
                      <!-- HEXAGON -->
                      <div class="hexagon-dark-16-18"></div>
                      <!-- /HEXAGON -->
                    </div>
                    <!-- /USER AVATAR BADGE CONTENT -->

                    <!-- USER AVATAR BADGE TEXT -->
                    <p class="user-avatar-badge-text">24</p>
                    <!-- /USER AVATAR BADGE TEXT -->
                  </div>
                  <!-- /USER AVATAR BADGE -->
                </div>
                <!-- /USER AVATAR -->
              </a>
              <!-- /USER STATUS AVATAR -->

              <!-- USER STATUS TITLE -->
              <p class="user-status-title"><span class="bold">Hi Marina!</span></p>
              <!-- /USER STATUS TITLE -->

              <!-- USER STATUS TEXT -->
              <p class="user-status-text small"><a href="profile-timeline.html">@marinavalentine</a></p>
              <!-- /USER STATUS TEXT -->
            </div>
            <!-- /USER STATUS -->
          </div>
          <!-- /DROPDOWN NAVIGATION HEADER -->

          <!-- DROPDOWN NAVIGATION CATEGORY -->
          <p class="dropdown-navigation-category">My Profile</p>
          <!-- /DROPDOWN NAVIGATION CATEGORY -->

          <!-- DROPDOWN NAVIGATION LINK -->
          <a class="dropdown-navigation-link" href="<?php url_site();  ?>/e<?php echo $_COOKIE['user']; ?>">Profile Info</a>
          <!-- /DROPDOWN NAVIGATION LINK -->

          <!-- DROPDOWN NAVIGATION LINK -->
          <a class="dropdown-navigation-link" href="hub-profile-social.html">Social &amp; Stream</a>
          <!-- /DROPDOWN NAVIGATION LINK -->

          <!-- DROPDOWN NAVIGATION LINK -->
          <a class="dropdown-navigation-link" href="hub-profile-notifications.html">Notifications</a>
          <!-- /DROPDOWN NAVIGATION LINK -->

          <!-- DROPDOWN NAVIGATION LINK -->
          <a class="dropdown-navigation-link" href="hub-profile-messages.html">Messages</a>
          <!-- /DROPDOWN NAVIGATION LINK -->

          <!-- DROPDOWN NAVIGATION LINK -->
          <a class="dropdown-navigation-link" href="hub-profile-requests.html">Friend Requests</a>
          <!-- /DROPDOWN NAVIGATION LINK -->

          <!-- DROPDOWN NAVIGATION CATEGORY -->
          <p class="dropdown-navigation-category">Account</p>
          <!-- /DROPDOWN NAVIGATION CATEGORY -->

          <!-- DROPDOWN NAVIGATION LINK -->
          <a class="dropdown-navigation-link" href="hub-account-info.html">Account Info</a>
          <!-- /DROPDOWN NAVIGATION LINK -->

          <!-- DROPDOWN NAVIGATION LINK -->
          <a class="dropdown-navigation-link" href="hub-account-password.html">Change Password</a>
          <!-- /DROPDOWN NAVIGATION LINK -->

          <!-- DROPDOWN NAVIGATION LINK -->
          <a class="dropdown-navigation-link" href="hub-account-settings.html">General Settings</a>
          <!-- /DROPDOWN NAVIGATION LINK -->

          <!-- DROPDOWN NAVIGATION CATEGORY -->
          <p class="dropdown-navigation-category">Groups</p>
          <!-- /DROPDOWN NAVIGATION CATEGORY -->

          <!-- DROPDOWN NAVIGATION LINK -->
          <a class="dropdown-navigation-link" href="hub-group-management.html">Manage Groups</a>
          <!-- /DROPDOWN NAVIGATION LINK -->

          <!-- DROPDOWN NAVIGATION LINK -->
          <a class="dropdown-navigation-link" href="hub-group-invitations.html">Invitations</a>
          <!-- /DROPDOWN NAVIGATION LINK -->
          <?php if($_COOKIE['user']=="1" ){ ?>

          <!-- DROPDOWN NAVIGATION CATEGORY -->
          <p class="dropdown-navigation-category"><?php lang('mode_admin'); ?></p>
          <!-- /DROPDOWN NAVIGATION CATEGORY -->

          <?php if(!(isset($_COOKIE['admin'])==isset($hachadmin)) ){ ?>
          <!-- DROPDOWN NAVIGATION LINK -->
          <a class="dropdown-navigation-link" href="<?php url_site();  ?>/admincp?cont"><?php lang('activate'); ?></a>
          <!-- /DROPDOWN NAVIGATION LINK -->
           <?php }else   if(isset($_COOKIE['admin'])==isset($hachadmin)){ ?>
           <!-- DROPDOWN NAVIGATION LINK -->
          <a class="dropdown-navigation-link" href="<?php url_site();  ?>/admincp?dcont"><?php lang('close'); ?></a>
          <!-- /DROPDOWN NAVIGATION LINK -->
          <?php  } ?>

          <?php  } ?>
          <!-- DROPDOWN NAVIGATION BUTTON -->
          <a href="<?php url_site();  ?>/logout?logout" class="dropdown-navigation-button button small secondary"><?php lang('logout'); ?></a>
          <!-- /DROPDOWN NAVIGATION BUTTON -->
        </div>
        <!-- /DROPDOWN NAVIGATION -->
 </div>
<!-- /ACTION ITEM WRAP -->
<?php }else{ echo"404"; }  ?>