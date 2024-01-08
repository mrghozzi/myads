<?php if($s_st=="buyfgeufb"){  ?>
<div class="content">

      <!-- FORM BOX -->
      <div class="widget-box" >
      <?php if(isset($_GET['bnerrMSG'])){  ?>
      <div class="alert alert-danger" role="alert"><?php echo $_GET['bnerrMSG'];  ?></div>
      <?php }  ?>
        <!-- FORM BOX TITLE -->
        <center><h2><?php lang('creayoacc'); ?></h2></center><br />
        <!-- /FORM BOX TITLE -->

        <!-- FORM -->
        <form class="form" method="post"  action="register.php">
          <div class="form-row">
            <!-- FORM ITEM -->
            <div class="form-item">
              <!-- FORM INPUT -->
              <div class="form-input">
                <label for="register-email"><?php lang('email'); ?></label>
                <input type="text" id="register-email" name="email">
              </div>
              <!-- /FORM INPUT -->
            </div>
            <!-- /FORM ITEM -->
          </div>
          <!-- FORM ROW -->
          <div class="form-row">
            <!-- FORM ITEM -->
            <div class="form-item">
              <!-- FORM INPUT -->
              <div class="form-input">
                <label for="register-username"><?php lang('username'); ?></label>
                <input type="text" id="register-username" name="username">
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
                <label for="register-password"><?php lang('password'); ?></label>
                <input type="password" id="register-password" name="pass1">
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
                <label for="register-password-repeat"><?php lang('rep_password'); ?></label>
                <input type="password" id="register-password" name="pass2">
              </div>
              <!-- /FORM INPUT -->
            </div>
            <!-- /FORM ITEM -->
          </div>
          <!-- /FORM ROW -->
          <!-- FORM ROW -->
          <div class="form-row split">
            <!-- FORM ITEM -->
            <div class="form-item">
            <center><?php captcha() ;  ?></center>
             </div>
          <div class="form-item">
              <div class="form-input social-input small active">
                      <!-- name -->

                      <!-- /name -->
                      <label for="capt">verification code</label>
                      <input type="text" id="capt" name="capt" required>
                    </div>
              <!-- /FORM SELECT -->
             <!-- /FORM INPUT -->
            </div>
            <!-- /FORM ITEM -->
          </div>
          <!-- /FORM ROW -->
          <!-- FORM ROW -->
          <div class="form-row">
            <!-- FORM ITEM -->
            <div class="form-item">
              <!-- BUTTON -->
              <button class="button medium secondary" name="submit" type="submit"><?php lang('sign_up'); ?></button>
              <!-- /BUTTON -->
            </div>
            <!-- /FORM ITEM -->
          </div>
          <!-- /FORM ROW -->
        </form>
        <!-- /FORM -->
         <hr />
        <!-- LINED TEXT -->
        <p class="lined-text"><?php lang('alrehaacc'); ?></p>
        <br />
        <!-- /LINED TEXT -->
        <button class="button medium tertiary"><a href="login.php" style="color: #fff;" ><?php lang('login'); ?></a></button>
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