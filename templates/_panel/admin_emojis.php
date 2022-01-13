<?php if($s_st=="buyfgeufb"){  ?>
<style>
select {
  font-family: 'FontAwesome', 'sans-serif';
}</style>
		<div id="page-wrapper">
			<div class="main-page">
				<!--buttons-->
				<div class="grids-section">
					<h2 class="hdg">Emojis</h2>

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
                              <th>Emojis Icon Link</th>
                              <th></th>
                              <th></th>

                           </tr>
						</thead>
						<tbody>
   <form id="defaultForm" method="post" class="form-horizontal" action="admincp.php?emojis_a"><tr>
  <td><i class="fa fa-plus "></i></td>
  <td><input type="text" class="form-control" name="name"  autocomplete="off" /></td>
  <td><input type="text" class="form-control" name="img" autocomplete="off" /></td>
  <td><button type="submit" name="ed_submit" value="ed_submit" class="btn btn-info"><i class="fa fa-plus "></i></button></td>
</tr></form>
                        <?php emojis_list();  ?>
               </tbody>
					</table>
				</div>
				</div>  <div class="clearfix"></div>
				</div>  
				</div>
<?php }else{ echo"404"; }  ?>