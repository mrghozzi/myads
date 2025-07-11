<?php if(isset($s_st) AND ($s_st=="buyfgeufb")){ dinstall_d(); ?>
<div class="content-grid">

      <!-- FORM BOX -->
      <div class="widget-box" >
      <?php if(isset($_GET['bnerrMSG'])){  ?>
      <div class="alert alert-danger" role="alert"><?php echo $_GET['bnerrMSG'];  ?></div>
      <?php }  ?>
        <!-- FORM BOX TITLE -->
        <h2 class="form-box-title">Forgot your password?</h2>
        <!-- /FORM BOX TITLE -->
        <br />
        <!-- FORM -->
        <form class="form" method="post"  action="forgot-password">
          <!-- FORM ROW -->
          <div class="form-row">
            <!-- FORM ITEM -->
            <div class="form-item">
              <!-- FORM INPUT -->
              <div class="form-input">
                <label for="login-username">Username or Email</label>
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
              <!-- BUTTON -->
              <button class="button medium secondary" name="rpassword" type="submit">Recover</button>
              <!-- /BUTTON -->
            </div>
            <!-- /FORM ITEM -->
          </div>
          <!-- /FORM ROW -->
        </form>
        <!-- /FORM -->
         <hr />
        <!-- LINED TEXT -->
        <br />
        <!-- /LINED TEXT -->
        <p class="stats-decoration-text">
              <a href="<?php url_site();  ?>/login" class="button primary padded" >Login</a>
              <a class="button tertiary padded" href="<?php url_site();  ?>/register" >Signup here.</a>
         </p>

      </div>
      <!-- /FORM BOX -->

</div>
	<!--typo-ends-->
<?php }else{ echo"404"; }  ?>