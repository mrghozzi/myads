<?php if(isset($s_st) AND ($s_st=="buyfgeufb")){ dinstall_d(); ?>
<div class="content">
      <!-- FORM BOX -->
      <div class="widget-box" >
      <?php if(isset($_GET['bnerrMSG'])){  ?>
      <div class="alert alert-danger" role="alert"><?php echo $_GET['bnerrMSG'];  ?></div>
      <?php }  ?>
        <!-- FORM BOX TITLE -->
        <center><h2><?php lang('sign_in'); ?></h2></center><br />
        <!-- /FORM BOX TITLE -->

        <!-- FORM -->
        <form class="form" method="post"  action="login.php">
          <!-- FORM ROW -->
          <div class="form-row">
            <!-- FORM ITEM -->
            <div class="form-item">
              <!-- FORM INPUT -->
              <div class="form-input">
                <label for="login-username"><?php lang('usermail'); ?></label>
                <input type="text" id="login-username" name="username">
              </div>
              <!-- /FORM INPUT -->
            </div>
            <!-- /FORM ITEM -->
          </div>
          <!-- /FORM ROW -->

          <!-- FORM ROW -->
          <div class="form-row">
            <!-- FORM ITEM -->
            <div class="form-item">
              <!-- FORM INPUT -->
              <div class="form-input">
                <label for="login-password"><?php lang('password'); ?></label>
                <input type="password" id="login-password" name="pass">
              </div>
              <!-- /FORM INPUT -->
            </div>
            <!-- /FORM ITEM -->
          </div>
          <!-- /FORM ROW -->

          <!-- FORM ROW -->
          <div class="form-row space-between">
            <!-- FORM ITEM -->
            <div class="form-item">
              <!-- CHECKBOX WRAP -->
              <div class="checkbox-wrap">
                <input type="checkbox" id="login-remember" name="login_remember" checked="">
                <!-- CHECKBOX BOX -->
                <div class="checkbox-box">
                  <!-- ICON CROSS -->
                  <svg class="icon-cross">
                    <use xlink:href="#svg-cross"></use>
                  </svg>
                  <!-- /ICON CROSS -->
                </div>
                <!-- /CHECKBOX BOX -->
                <label for="login-remember">Remember Me</label>
              </div>
              <!-- /CHECKBOX WRAP -->
            </div>
            <!-- /FORM ITEM -->

            <!-- FORM ITEM -->
            <div class="form-item">
              <!-- FORM LINK -->
              <a class="form-link" href="#">Forgot Password?</a>
              <!-- /FORM LINK -->
            </div>
            <!-- /FORM ITEM -->
          </div>
          <!-- /FORM ROW -->

          <!-- FORM ROW -->
          <div class="form-row">
            <!-- FORM ITEM -->
            <div class="form-item">
              <!-- BUTTON -->
              <button class="button medium secondary" name="login" type="submit"><?php lang('login'); ?></button>
              <!-- /BUTTON -->
            </div>
            <!-- /FORM ITEM -->
          </div>
          <!-- /FORM ROW -->
        </form>
        <!-- /FORM -->
         <hr />
        <!-- LINED TEXT -->
        <p class="lined-text"><?php lang('donthaacc'); ?></p>
        <br />
        <!-- /LINED TEXT -->
        <button class="button medium tertiary"><a href="register.php" style="color: #fff;" ><?php lang('sign_up'); ?></a></button>
        <hr />
        <?php
        $o_type =  "login_ext";
        $bnlogin_ext = $db_con->prepare("SELECT  COUNT(id) as nbr FROM `options` WHERE o_type=:o_type ORDER BY `o_order` DESC" );
        $bnlogin_ext->bindParam(":o_type", $o_type);
        $bnlogin_ext->execute();
        $ablogin_ext=$bnlogin_ext->fetch(PDO::FETCH_ASSOC);
        $contlogin_ext= $ablogin_ext['nbr'];
        if(isset($contlogin_ext) AND ($contlogin_ext == 0)){

        }else{
        ?>
        <p class="lined-text">Login with your Social Account</p>
        <!-- SOCIAL LINKS -->
        <div class="social-links">
          <?php act_extensions("login_ext");  ?>
        </div>
        <!-- /SOCIAL LINKS -->
        <?php } ?>
      </div>
      <!-- /FORM BOX -->

</div>
	<!--typo-ends-->
<?php }else{ echo"404"; }  ?>