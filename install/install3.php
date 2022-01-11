 <?php
 require_once '../dbconfig.php';

     $stmt = $db_con->prepare("SELECT *  FROM setting   " );
        $stmt->execute();
         $ab=$stmt->fetch(PDO::FETCH_ASSOC);

   include "header.php";
    ?>

    <div class="main-content">
		<div class="form">
			<div class="sap_tabs">
				<div id="horizontalTab" style="display: block; width: 100%; margin: 0px;">



				        <div class="facts">
					        <div class="register">
						         <form method='post'  action='install4.php'>
                                 <h3>Titer:</h3>
							        <input  type="text" name='etit' value='<?php echo $ab['titer']; ?>'>
							        <h3>Website URL:</h3>
							        <input  type="text" name='eurl' value='http://'>

							        <div class="sign-up">
									    <input type="submit" value='Next' class="btn btn-primary" name="btn-save" id="btn-save"/>
								    </div>
                                </form>
						    </div>
				        </div>

			 	</div>
		    </div>
        </div>
        <div class="right">
			<h4>Step 2</h4>
			<ul>
				<li><p>Type the location and name of the link</p></li>
				<li><p>Click on the next button</p></li>

			</ul>


		</div>
		<div class="clear"></div>
	</div>



   <?php   include "footer.php";   ?>
