<?php if($s_st=="buyfgeufb"){  ?>
		<div id="page-wrapper">
			<div class="main-page">
				<!--buttons-->
				<div class="grids-section">
					<h2 class="hdg"><?php if(isset($_GET['ty'])){ echo $_GET['ty']; }  ?> : N&deg;<?php if(isset($_GET['id'])){  echo $_GET['id']; }  ?></h2>
                    <div class="panel panel-widget"><a href="<?php ty_link();  ?>" class='btn btn-info'><<</a> <a href="" class='btn btn-warning'><i class="fa fa-refresh"></i> Refresh</a></div>
			<div class="clearfix"></div>
			</div>
            <div class="col-md-12 table-grid">
                <div class="panel panel-widget">
					<table id="tablepagination" class="table table-hover">
						<thead>
							<tr>
                              <th>#ID</th>
							  <th>Url</th>
                              <th>Time</th>
							  <th>Browser</th>
                              <th>platform</th>
                              <th>Ip</th>
                            </tr>
						</thead>
						<tbody>
                        <?php bnr_list();  ?>
               </tbody>
					</table>
                </div>
				</div>   <div class="clearfix"></div>
                   <script type="text/javascript">
            $(document).ready(function() {
    $('#tablepagination').dataTable( {
    "order": [[ 0, 'DESC' ]]
} );
} );
</script>
				</div>
				</div>
<?php }else{ echo"404"; }  ?>