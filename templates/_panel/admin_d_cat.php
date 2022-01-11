<?php if($s_st=="buyfgeufb"){  ?>
<style>
select {
  font-family: 'FontAwesome', 'sans-serif';
}</style>
		<div id="page-wrapper">
			<div class="main-page">
				<!--buttons-->
				<div class="grids-section">
					<h2 class="hdg">Directory categories</h2>

			<div class="clearfix"></div>
			</div>
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
                              <th>Folder</th>
                              <th>Order</th>
                           </tr>
						</thead>
						<tbody>
   <form id="defaultForm" method="post" class="form-horizontal" action="admincp.php?d_cat_a"><tr>
  <td><i class="fa fa-plus "></i></td>
  <td><input type="text" class="form-control" name="name"  autocomplete="off" /></td>
  <td><select name="sub" class="form-control" autocomplete="off">
  <option value="0" >--------</option>
  <?php $stcmut = $db_con->prepare("SELECT *  FROM cat_dir WHERE sub=0 ORDER BY `name` ASC" );
  $stcmut->execute();
while($ncat_tt=$stcmut->fetch(PDO::FETCH_ASSOC)){ ?>
<option value="<?php echo $ncat_tt['id']; ?>" ><?php echo $ncat_tt['name']; ?></option>
<?php } ?>
</select></td>
  <td><input type="number" class="form-control" name="ordercat" value="0" autocomplete="off" /></td>
  <td><button type="submit" name="ed_submit" value="ed_submit" class="btn btn-info"><i class="fa fa-plus "></i></button></td>
</tr></form>
                        <?php lnk_list();  ?>
               </tbody>
					</table>
				</div>
				</div> <div class="clearfix"></div>
				</div>
				</div>
<?php }else{ echo"404"; }  ?>