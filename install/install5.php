<?php
 require_once '../dbconfig.php';
  if(isset($_POST['btn-signup']))
{

    $uname = $_POST['user_name'];
	$email = $_POST['user_email'];
	$upass = $_POST['password'];
    $new_password = password_hash($upass, PASSWORD_DEFAULT);
    $ucheck = "1";

    $stmtq = $db_con->prepare("INSERT INTO users(username,email,pass,ucheck)
        VALUES(:usern,:email,:passhach,:ucheck)");
     $stmtq->bindParam(":email", $email);
     $stmtq->bindParam(":usern", $uname);
     $stmtq->bindParam(":passhach", $new_password);
     $stmtq->bindParam(":ucheck", $ucheck);

	   if($stmtq->execute())
		{
			$msg = "<p style='color:#04B404'> &nbsp; successfully registered !
					</p>";
		}
		else
		{
		  $msg = "<p style='color:#FF0000'> &nbsp; error while registering !
					</p>";
		}
	}

   include "header.php";
    ?>

    <div class="main-content">
		<div class="form">
			<div class="sap_tabs">
				<div id="horizontalTab" style="display: block; width: 100%; margin: 0px;">



				        <div class="facts">
					        <div class="register">
                            <p><?php if(isset($msg)){
			echo $msg;
		} else{
             	echo "&nbsp; all the fields are mandatory !";
		} ?></p>
						         <h4>Installation has been successfully</h4>
                                 <h3><p style='color:#FF0000'>DELETE THE FOLDER "INSTALL" OR CHANGING THE NAME</p></h3>
							        <div class="sign-up">
								        <a href="../index.php" type="next" />Preview Site</a>
                                        <a href="../login.php" type="next" />Administration Panel</a>
							        </div>
						    </div>
				        </div>

			 	</div>
		    </div>
        </div>
        <div class="right">
			<h4>Step 4</h4>
			<ul>
				<li><p>The last step</p></li>
				<li><p>Hello  in your site</p></li>

			</ul>


		</div>
		<div class="clear"></div>
	</div>



   <?php   include "footer.php";   ?>
