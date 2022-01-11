<?php if($s_st=="buyfgeufb"){  ?>
<div id="page-wrapper">
			<div class="main-page">
				<div class="sign-form">
					<h4>Login</h4>
                    <h5><strong>Welcome</strong> login to get started!</h5>
                    <form method="post"  action="login.php">
                          <?php if(isset($_GET['bnerrMSG'])){  ?>
                     <div class="alert alert-danger" role="alert"><?php echo $_GET['bnerrMSG'];  ?></div>
                        <?php }  ?>
						<input type="text" name="username" placeholder="Username" required>
						<input type="password" name="pass" class="pass" placeholder="Password" required>
						<div class="clearfix"></div>
						<button class="btn btn-info btn-block" name="login" type="submit">Sign in</button>

                        <p class="center-block mg-t mg-b">Dont have and account?</p>
					</form>
                    <a href="register.php" class="button1"><button class="btn btn-warning btn-block" type="submit">Signup here.</button></a>
                    <?php act_extensions("login_ext");  ?>
                    <br />
				</div>
		</div>	
	</div>	
	<!--typo-ends-->
<?php }else{ echo"404"; }  ?>