 <?php
   include_once '../dbconfig.php';
   include "header.php";
    ?>

    <div class="main-content">
		<div class="form">
			<div class="sap_tabs">
				<div id="horizontalTab" style="display: block; width: 100%; margin: 0px;">
                         <div class="facts">
					        <div class="register">
						         <form>
                                 <h4>install</h4>
                                 <h4>MyAds <?php echo $version; ?></h4>
							        <div class="sign-up">
								        <a href="install2.php" type="next" />Install</a>
							        </div>
                                    <div class="sign-up">
                                        <a href="update1.php" type="next" />Update</a>

							        </div>
                                </form>
						    </div>
				        </div>

			 	</div>
		    </div>
        </div>
        <div class="right">
			<h4>Welcome!</h4>
			<ul>
				<li><p>Edit dbconfig.php file and add the name of the rule and the word User name and pass </p></li>
				<li>Installation<p>Click on the Install button</p></li>
            </ul>
        </div>
		<div class="clear"></div>
	</div>
 <?php  include "footer.php";   ?>