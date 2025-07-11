<?php if(isset($s_st) AND ($s_st=="buyfgeufb")){ dinstall_d();  ?>
<div class="grid grid-3-9 medium-space" >
<div class="grid-column" >
<?php template_mine('user_settings/nav_settings');  ?>
</div>
<div class="grid-column" >
<!--buttons-->

         <div class="widget-box">
         <h3>PTS History</h3>
         <hr />
         <div class="widget-box" >
			<table id="tablepagination" class="table table table-hover">
				 <thead>
				  <tr>
                   <th>#ID</th>
				   <th>PTS</th>
                   <th>Name</th>
                   <th>DATE</th>
                  </tr>
				 </thead>
				 <tbody>
                 <?php
                          $k_type = "hest_pts";
                          $k_uid = $_COOKIE['user'];
                          
                 $storknow = $db_con->prepare("SELECT *  FROM options WHERE  o_type=:o_type AND o_parent=:o_parent ORDER BY `id` " );
                 $storknow->bindParam(":o_type", $k_type);
                 $storknow->bindParam(":o_parent", $k_uid);
                 $storknow->execute();
                 while($sknowled=$storknow->fetch(PDO::FETCH_ASSOC) ) {
                    $name_lang = $sknowled['name'];
                    $time_hst=convertTime($sknowled['o_mode']);
                   echo "<tr>
                              <th>#{$sknowled['id']}</th>
							  <th>{$sknowled['o_valuer']}</th>
                              <th>{$lang["$name_lang"]}</th>
                              <th>{$time_hst}</th>
                            </tr>";

                          }
                          ?>

            
                 </tbody>
			</table>
         </div>
            </div>
            </div>
<?php
 }else{ echo "404"; }  ?>