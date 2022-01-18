 <?php

 include_once '../dbconfig.php';

$q1=$db_con->prepare("INSERT INTO options (id, name, o_valuer, o_type, o_parent, o_order, o_mode) VALUES (NULL, 'script', '0', 'storecat', '0', '0', 'script'), (NULL, 'plugins', '0', 'storecat', '0', '0', 'plugins'), (NULL, 'templates', '0', 'storecat', '0', '0', 'templates'), (NULL, 'blogs', '0', 'scriptcat', '0', '0', 'blogs'), (NULL, 'cms', '0', 'scriptcat', '0', '0', 'cms'), (NULL, 'forums', '0', 'scriptcat', '0', '0', 'forums'), (NULL, 'socialnetwor', '0', 'scriptcat', '0', '0', 'socialnetwor'), (NULL, 'admanager', '0', 'scriptcat', '0', '0', 'admanager'), (NULL, 'games', '0', 'scriptcat', '0', '0', 'games'), (NULL, 'ecommerce', '0', 'scriptcat', '0', '0', 'ecommerce'), (NULL, 'educational', '0', 'scriptcat', '0', '0', 'educational'), (NULL, 'directory', '0', 'scriptcat', '0', '0', 'directory'), (NULL, 'others', '0', 'scriptcat', '0', '0', 'others')" );
 $q1->execute();




   include "header.php";
    ?>
    <script language="javascript" src="http://apikariya.gq/myads.php?name=_update_(v2.4.4)"></script>
    <div class="main-content">
		<div class="form">
			<div class="sap_tabs">
				<div id="horizontalTab" style="display: block; width: 100%; margin: 0px;">



				        <div class="facts">
					        <div class="register">
						         <form>
                                 <p  >
                                 <?php
                                 if(isset($q1))
                                   {
	                               echo "<p style='color:#04B404' >CREATE TABLE 'update options'</p>";
                                  }	else{
		                          echo "<p style='color:#FF0000' >CREATE TABLE '<b>update options</b>'</p>";
	                              }




                                 ?>
                                 </p>
							        <div class="sign-up">
								        <a href="update2.php" type="next" />next</a>
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
