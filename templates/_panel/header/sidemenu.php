<?php if($s_st=="buyfgeufb"){  ?>
   <!-- HEADER -->
  <header class="header">
    <!-- HEADER ACTIONS -->
    <div class="header-actions">
      <!-- HEADER BRAND -->
      <div class="header-brand">
        <!-- LOGO -->
        <div class="logo">
          <!-- ICON LOGO VIKINGER -->
          <a href="<?php url_site();  ?>"><img src="<?php url_site();  ?>/bnr/logo_w.png" width="40"  /></a>
          <!-- /ICON LOGO VIKINGER -->
        </div>
        <!-- /LOGO -->

        <!-- HEADER BRAND TEXT -->
        <h1 class="header-brand-text"><?php echo $title_s;  ?></h1>
        <!-- /HEADER BRAND TEXT -->
      </div>
      <!-- /HEADER BRAND -->
    </div>
    <!-- /HEADER ACTIONS -->

    <!-- HEADER ACTIONS -->
    <div class="header-actions">
      <!-- SIDEMENU TRIGGER -->
      <div class="sidemenu-trigger navigation-widget-trigger">
        <!-- ICON GRID -->
        <svg class="icon-grid">
          <use xlink:href="#svg-grid"></use>
        </svg>
        <!-- /ICON GRID -->
      </div>
      <!-- /SIDEMENU TRIGGER -->

      <!-- MOBILEMENU TRIGGER -->
      <div class="mobilemenu-trigger navigation-widget-mobile-trigger">
        <!-- BURGER ICON -->
        <div class="burger-icon inverted">
          <!-- BURGER ICON BAR -->
          <div class="burger-icon-bar"></div>
          <!-- /BURGER ICON BAR -->

          <!-- BURGER ICON BAR -->
          <div class="burger-icon-bar"></div>
          <!-- /BURGER ICON BAR -->

          <!-- BURGER ICON BAR -->
          <div class="burger-icon-bar"></div>
          <!-- /BURGER ICON BAR -->
        </div>
        <!-- /BURGER ICON -->
      </div>
      <!-- /MOBILEMENU TRIGGER -->

      <!-- NAVIGATION -->
      <nav class="navigation">
        <!-- MENU MAIN -->
        <ul class="menu-main">
          <?php sid_menu();  ?>
        </ul>
        <!-- /MENU MAIN -->
      </nav>
      <!-- /NAVIGATION -->
    </div>
    <!-- /HEADER ACTIONS -->

    <!-- HEADER ACTIONS -->
    <form class="header-actions search-bar" action="<?php echo $url_site;  ?>/portal" method="GET" >
      <!-- INTERACTIVE INPUT -->
      <div class="interactive-input dark">

        <input type="text" id="search-main" name="search" placeholder="Search">

        <!-- INTERACTIVE INPUT ICON WRAP -->
        <div class="interactive-input-icon-wrap">
          <!-- INTERACTIVE INPUT ICON -->
          <svg class="interactive-input-icon icon-magnifying-glass">
            <use xlink:href="#svg-magnifying-glass"></use>
          </svg>
          <!-- /INTERACTIVE INPUT ICON -->
        </div>
        <!-- /INTERACTIVE INPUT ICON WRAP -->

        <!-- INTERACTIVE INPUT ACTION -->
        <div class="interactive-input-action">
          <!-- INTERACTIVE INPUT ACTION ICON -->
          <svg class="interactive-input-action-icon icon-cross-thin">
            <use xlink:href="#svg-cross-thin"></use>
          </svg>
          <!-- /INTERACTIVE INPUT ACTION ICON -->
        </div>
        <!-- /INTERACTIVE INPUT ACTION -->
      </div>
      <!-- /INTERACTIVE INPUT -->

    </form>
    <!-- /HEADER ACTIONS -->

    <!-- HEADER ACTIONS -->
    <div class="header-actions">
      <!-- PROGRESS STAT -->
      <?php   if(isset($_COOKIE['user']))  { ?>
      <div class="progress-stat">
        <!-- BAR PROGRESS WRAP -->
        <div class="bar-progress-wrap">
          <!-- BAR PROGRESS INFO -->
          <a class="bar-progress-info" href="<?php url_site();  ?>/history"><?php user_row('pts'); ?>&nbsp;PTS</a>
          <!-- /BAR PROGRESS INFO -->
        </div>
        <!-- /BAR PROGRESS WRAP -->
      </div>
      <?php } ?>
      <!-- /PROGRESS STAT -->
      <?php if(isset($_COOKIE['user']) && isset($_COOKIE['admin']) && ($_COOKIE['admin']==$hachadmin)  ){ ?>
      <?php template_mine('header/admin_board');  ?>
      <?php } ?>
    </div>
    <!-- /HEADER ACTIONS -->
<?php if(isset($_COOKIE['user'])){ ?>
    <!-- HEADER ACTIONS -->
    <div class="header-actions">
      <!-- ACTION LIST -->
      <div class="action-list dark">
<?php template_mine('header/messages');  ?>
<?php template_mine('header/notification');  ?>
      </div>
      <!-- /ACTION LIST -->
 <?php template_mine('header/settings');  ?>
    </div>
    <?php }else{ ?>
    <div class="header-actions">
      <!-- REGISTER BUTTON -->
      <a class="register-button button no-shadow" href="<?php url_site();  ?>/login"><?php lang('login'); ?></a>
      <a class="register-button button no-shadow" href="<?php url_site();  ?>/register"><?php lang('sign_up'); ?></a>
      <!-- /REGISTER BUTTON -->

    </div>
    <?php } ?>
    <!-- /HEADER ACTIONS -->
  </header>
  <!-- /HEADER -->
<?php }else{ echo"404"; }  ?>