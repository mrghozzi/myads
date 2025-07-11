<?php if(isset($s_st) AND ($s_st=="buyfgeufb")){  ?>
<div class="grid grid-3-9 medium-space" >
<div class="grid-column" >
<?php template_mine('admin/admin_nav');  ?>
</div>
<div class="grid-column" >
				<!--buttons-->
				<div class="widget-box">
					<h2 class="hdg">Navigation Menu List</h2>


            <div class="col-md-12 table-grid">
             <?php if(isset($_GET['bnerrMSG'])){  ?>
                     <div class="alert alert-danger" role="alert"><?php echo $_GET['bnerrMSG'];  ?></div>
                        <?php }  ?>
                <div class="panel panel-widget">
					<table class="table table-hover">
						<thead>
							<tr>
                              <th>#ID</th>
							  <th>Name</th>
                              <th>Url</th>
                           </tr>
						</thead>
						<tbody>
   <form id="defaultForm" method="post" class="form-horizontal" action="admincp.php?a_menu"><tr>
  <td><i class="fa fa-plus "></i></td>
  <td><input type="text" class="form-control" name="name"  autocomplete="off" /></td>
  <td><input type="text" class="form-control" name="dir"  autocomplete="off" /></td>
  <td><button type="submit" name="ed_submit" value="ed_submit" class="btn btn-primary"><i class="fa fa-plus "></i></button></td>
</tr></form>
                        <?php lnk_list();  ?>
               </tbody>
					</table>
				</div>
				</div>
                </div>
				</div>
				</div>
<?php }else{ echo"404"; }  ?>