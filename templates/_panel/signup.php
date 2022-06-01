<?php if($s_st=="buyfgeufb"){  ?>
<div class="content-grid">

      <!-- FORM BOX -->
      <div class="widget-box" >
      <?php if(isset($_GET['bnerrMSG'])){  ?>
      <div class="alert alert-danger" role="alert"><?php echo $_GET['bnerrMSG'];  ?></div>
      <?php }  ?>
        <!-- FORM BOX TITLE -->
        <h2 class="form-box-title">Create your Account!</h2>
        <!-- /FORM BOX TITLE -->

        <!-- FORM -->
        <form class="form" method="post"  action="register.php">
          <div class="form-row">
            <!-- FORM ITEM -->
            <div class="form-item">
              <!-- FORM INPUT -->
              <div class="form-input">
                <label for="register-email">Your Email</label>
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
                <label for="register-username">Username</label>
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
                <label for="register-password">Password</label>
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
                <label for="register-password-repeat">Repeat Password</label>
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
            <center><b><?php captcha() ;  ?>&nbsp;=</b></center>
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
              <button class="button medium secondary" name="submit" type="submit">Register Now!</button>
              <!-- /BUTTON -->
            </div>
            <!-- /FORM ITEM -->
          </div>
          <!-- /FORM ROW -->
        </form>
        <!-- /FORM -->
         <hr />
        <!-- LINED TEXT -->
        <p class="lined-text">Already have an account?</p>
        <br />
        <!-- /LINED TEXT -->
        <button class="button medium tertiary"><a href="login.php" style="color: #fff;" >Login</a></button>
        <hr />
        <p class="lined-text">Login with your Social Account</p>
        <!-- SOCIAL LINKS -->
        <div class="social-links">
          <?php act_extensions("login_ext");  ?>
        </div>
        <!-- /SOCIAL LINKS -->
      </div>
      <!-- /FORM BOX -->

</div>
	<!--typo-ends-->
<?php }else{ echo"404"; }  ?>