<?php if($s_st=="buyfgeufb"){  ?>
    <div id="page-wrapper">
			<div class="main-page">
				<div class="sign-form">
					<h4>Sign Up</h4>
					<h5><strong>Create</strong> your account.</h5>
					<form method="post"  action="register.php">
                          <?php if(isset($_GET['bnerrMSG'])){  ?>
                     <div class="alert alert-danger" role="alert"><?php echo $_GET['bnerrMSG'];  ?></div>
                        <?php }  ?>
						<input type="text" name="username" placeholder="Choose a username" required>
						<input type="text" name="email" class="pass" placeholder="Email address" required>
						<input type="password"  name="pass1" placeholder="Password" required>
						<input type="password" name="pass2" class="pass" placeholder="Confirm password" required>
						<button class="btn btn-info btn-block" name="submit" type="submit">Sign up</button>
						<p class="center-block mg-t mg-b text-center">Already have an account?</p>
							
					</form>

					<a href="login.php" class="button1"><button class="btn btn-warning btn-block" type="submit">Login</button></a>
                    <?php act_extensions("login_ext");  ?>
                    <br />
				</div>
		</div>
	</div>
	<!--typo-ends-->
<?php }else{ echo"404"; }  ?>