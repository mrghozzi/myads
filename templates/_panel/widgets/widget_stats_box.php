<?php if(isset($s_st) AND ($s_st=="buyfgeufb")){ ?>
<div class="stats-box-slider">
          <!-- STATS BOX SLIDER CONTROLS -->
          <div class="stats-box-slider-controls">
            <!-- STATS BOX SLIDER CONTROLS TITLE -->
            <p class="stats-box-slider-controls-title"><?php echo $abwidgets['name']; ?></p>
            <!-- /STATS BOX SLIDER CONTROLS TITLE -->

            <!-- SLIDER CONTROLS -->
            <div id="stats-box-slider-controls" class="slider-controls">
              <!-- SLIDER CONTROL -->
              <div class="slider-control negative left">
                <!-- SLIDER CONTROL ICON -->
                <svg class="slider-control-icon icon-small-arrow">
                  <use xlink:href="#svg-small-arrow"></use>
                </svg>
                <!-- /SLIDER CONTROL ICON -->
              </div>
              <!-- /SLIDER CONTROL -->

              <!-- SLIDER CONTROL -->
              <div class="slider-control negative right">
                <!-- SLIDER CONTROL ICON -->
                <svg class="slider-control-icon icon-small-arrow">
                  <use xlink:href="#svg-small-arrow"></use>
                </svg>
                <!-- /SLIDER CONTROL ICON -->
              </div>
              <!-- /SLIDER CONTROL -->
            </div>
            <!-- /SLIDER CONTROLS -->
          </div>
          <!-- /STATS BOX SLIDER CONTROLS -->

          <!-- STATS BOX SLIDER ITEMS -->
          <div id="stats-box-slider-items" class="stats-box-slider-items">
            <!-- STATS BOX -->
            <div class="stats-box stat-profile-views">
              <!-- STATS BOX VALUE WRAP -->
              <div class="stats-box-value-wrap">
                <!-- STATS BOX VALUE -->
                <p class="stats-box-value"><?php nbr_state('banner'); ?></p>
                <!-- /STATS BOX VALUE -->

                <!-- STATS BOX DIFF -->
                <div class="stats-box-diff">
              <!-- banner BOX DIFF ICON -->
              <div>
                <!-- ICON PLUS SMALL -->
                <!-- ICON STATUS -->
                <svg class="icon-status" style="fill: #ff5384;" >
                <use xlink:href="#svg-status"></use>
                </svg>
                <!-- /ICON STATUS -->
                <!-- /ICON PLUS SMALL -->
              </div>
              <!-- /banner BOX DIFF ICON -->

                  <!-- STATS BOX DIFF VALUE -->
                  <p class="stats-box-diff-value"><?php lang('bannads'); ?></p>
                  <!-- /STATS BOX DIFF VALUE -->
                </div>
                <!-- /STATS BOX DIFF -->
              </div>
              <!-- /STATS BOX VALUE WRAP -->

              <!-- STATS BOX TITLE -->
              <p class="stats-box-title"><?php lang('Views'); ?></p>
              <!-- /STATS BOX TITLE -->

              <!-- STATS BOX TEXT -->
              <p class="stats-box-text"><?php admin_state('banner'); ?></p>
              <!-- /STATS BOX TEXT -->
            </div>
            <!-- /STATS BOX -->

            <!-- STATS BOX -->
            <div class="stats-box stat-posts-created">
              <!-- STATS BOX VALUE WRAP -->
              <div class="stats-box-value-wrap">
                <!-- STATS BOX VALUE -->
                <p class="stats-box-value"><?php nbr_state('link'); ?></p>
                <!-- /STATS BOX VALUE -->

                <!-- STATS BOX DIFF -->
                <div class="stats-box-diff">
              <!-- link BOX DIFF ICON -->
              <div>
                <!-- ICON PLUS SMALL -->
                <!-- ICON STATUS -->
                    <!-- ICON EVENTS WEEKLY -->
                    <svg class="icon-events-weekly"  style="fill: #ff5384;" >
                    <use xlink:href="#svg-events-weekly"></use>
                    </svg>
                   <!-- /ICON EVENTS WEEKLY -->
                <!-- /ICON STATUS -->
                <!-- /ICON PLUS SMALL -->
              </div>
              <!-- /link BOX DIFF ICON -->

                  <!-- STATS BOX DIFF VALUE -->
                  <p class="stats-box-diff-value"><?php lang('textads'); ?></p>
                  <!-- /STATS BOX DIFF VALUE -->
                </div>
                <!-- /STATS BOX DIFF -->
              </div>
              <!-- /STATS BOX VALUE WRAP -->

              <!-- STATS BOX TITLE -->
              <p class="stats-box-title"><?php lang('Views'); ?></p>
              <!-- /STATS BOX TITLE -->

              <!-- STATS BOX TEXT -->
              <p class="stats-box-text"><?php admin_state('link'); ?></p>
              <!-- /STATS BOX TEXT -->
            </div>
            <!-- /STATS BOX -->

            <!-- STATS BOX -->
            <div class="stats-box stat-reactions-received">
              <!-- STATS BOX VALUE WRAP -->
              <div class="stats-box-value-wrap">
                <!-- STATS BOX VALUE -->
                <p class="stats-box-value"><?php nbr_state('users');  ?></p>
                <!-- /STATS BOX VALUE -->

                <!-- STATS BOX DIFF -->
                <div class="stats-box-diff">
          <!-- ACCOUNT STAT BOX ICON WRAP -->
          <div class="account-stat-box-icon-wrap">
            <!-- ACCOUNT STAT BOX ICON -->
            <svg class="account-stat-box-icon icon-members" style="fill: #ff5384;">
              <use xlink:href="#svg-members"></use>
            </svg>
            <!-- /ACCOUNT STAT BOX ICON -->
          </div>
          <!-- /ACCOUNT STAT BOX ICON WRAP -->

                  <!-- STATS BOX DIFF VALUE -->
                  <p class="stats-box-diff-value"><?php lang('users'); ?></p>
                  <!-- /STATS BOX DIFF VALUE -->
                </div>
                <!-- /STATS BOX DIFF -->
              </div>
              <!-- /STATS BOX VALUE WRAP -->

              <!-- STATS BOX TITLE -->
              <p class="stats-box-title"><?php nbr_state('forum');  ?> : <?php lang('topics'); ?></p>
              <!-- /STATS BOX TITLE -->

              <!-- STATS BOX TEXT -->
              <p class="stats-box-text"><?php nbr_state('directory');  ?> : <?php lang('directory'); ?></p>
              <!-- /STATS BOX TEXT -->
            </div>
            <!-- /STATS BOX -->

            <!-- STATS BOX -->
            <div class="stats-box stat-comments-received">
              <!-- STATS BOX VALUE WRAP -->
              <div class="stats-box-value-wrap">
                <!-- STATS BOX VALUE -->
                <p class="stats-box-value"><?php nbr_state('visits');  ?></p>
                <!-- /STATS BOX VALUE -->

                <!-- STATS BOX DIFF -->
                <div class="stats-box-diff">
              <!-- visits BOX DIFF ICON -->
              <div>
                <!-- ICON PLUS SMALL -->
                <svg class="icon-timeline"  style="fill: #ff5384;" >
                  <use xlink:href="#svg-timeline"></use>
                </svg>
              </div>
              <!-- /visits BOX DIFF ICON -->

                  <!-- STATS BOX DIFF VALUE -->
                  <p class="stats-box-diff-value"><?php lang('exvisit'); ?></p>
                  <!-- /STATS BOX DIFF VALUE -->
                </div>
                <!-- /STATS BOX DIFF -->
              </div>
              <!-- /STATS BOX VALUE WRAP -->

              <!-- STATS BOX TITLE -->
              <p class="stats-box-title"></p>
              <!-- /STATS BOX TITLE -->

              <!-- STATS BOX TEXT -->
              <p class="stats-box-text"></p>
              <!-- /STATS BOX TEXT -->
            </div>
            <!-- /STATS BOX -->
          </div>
          <!-- /STATS BOX SLIDER ITEMS -->
        </div>
<?php } ?>
