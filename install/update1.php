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
                                 <h4>Update TO <?php echo $version; ?></h4>
							        <div class="sign-up">
                                        <a href="update2.php?v=2-3-x" type="next" /><span>(<u>v2.3.x</u>)&nbsp;&nbsp;<b>TO</b>&nbsp;&nbsp;(<u><?php echo $version; ?></u>)</span></a>
                                        <a href="update2.php?v=3-1-0" type="next" /><span>(<u>v3.1.0</u>)&nbsp;&nbsp;<b>TO</b>&nbsp;&nbsp;(<u><?php echo $version; ?></u>)</span></a>
                                    </div>
                                </form>
						    </div>
				        </div>

			 	</div>
		    </div>
        </div>
        <div class="right">
			<h4>Update!</h4>
			<ul>
				<li><p>Choose the current version you are using</p></li>
				<li><b>Important:</b><p>Before updating, please take a backup of your database and other files.</p></li>
            </ul>
        </div>
		<div class="clear"></div>
	</div>
 <?php  include "footer.php";   ?>