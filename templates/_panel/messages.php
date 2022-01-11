<?php if($s_st=="buyfgeufb"){  ?>
		<div id="page-wrapper">
			<div class="main-page">
				<!--buttons-->
				<div class="grids-section">
					<h2 class="hdg"><?php lang('msgs'); ?></h2>

			<div class="clearfix"></div>
			</div>
            <div class="col-md-12 table-grid">
                <div class="panel panel-widget">
                <a data-toggle="modal" data-target="#e_msg" class="btn btn-info" ><i class="fa fa-envelope" >&nbsp;</i><?php lang('e_msg'); ?></a>
                <!-- //modal <?php lang('e_msg'); ?> -->
              <div class="modal fade" id="e_msg" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document">
					<div class="modal-content modal-info">
                        <div class="modal-body">
							<div class="more-grids">
                            <center>
                            <hr />
                         <form action="<?php url_site();  ?>/messages.php" method="GET">
                           <div class="input-group">
                       <span class="input-group-addon" id="basic-addon1"><i class="fa fa-user" aria-hidden="true"></i></span>
                       <select class="form-control" name="m" >
                      <?php  $us_id_gt=$_COOKIE['user'];
                             $selectdir = $db_con->prepare("SELECT *  FROM users WHERE  NOT(id={$us_id_gt}) ORDER BY `username` ASC ");
                             $selectdir->execute();
                             while($selrs15=$selectdir->fetch(PDO::FETCH_ASSOC)){
                             echo "<option value=\"{$selrs15['id']}\">{$selrs15['username']}</option>";
                             } ?>
                       </select>
                       </div>
                       <button  type="submit" class="btn btn-primary" >
                       <i class="fa fa-envelope" >&nbsp;</i><?php lang('e_msg'); ?></button>
                       </form>
                        <hr /> 
                       <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </center>
                            </div>
                        </div>
                    </div>
               </div>
             </div>
                     <!-- //modal <?php lang('e_msg'); ?> -->
                <table class="table table-hover">
						<thead>
							<tr>
                              <th>#ID</th>
							  <th>User Name</th>
							  <th></th>
                            </tr>
						</thead>
						<tbody>
                        <?php msg_list();  ?>
               </tbody>
					</table>
                </div>
				</div><div class="clearfix"></div>
				</div>
				</div>
<?php }else{ echo"404"; }  ?>