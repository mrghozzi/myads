<?php if($s_st=="buyfgeufb"){  ?>
		<div id="page-wrapper">
			<div class="main-page">
				<!--buttons-->
				<div class="grids-section">
					<h2 class="hdg"><?php lang('list'); echo"&nbsp;"; lang('bannads'); ?></h2>

			<div class="clearfix"></div>
			</div>
            <div class="col-md-12 table-grid">
                <div class="panel panel-widget">
                <a href="promote?p=banners" class="btn btn-info" ><?php lang('add'); ?></a>
					<table id="tablepagination" class="table table-hover">
						<thead>
							<tr>
                              <th>#ID</th>
							  <th>Name</th>
                              <th>Vu</th>
							  <th>Clik</th>
                              <th>Size</th>
                              <th>Statu</th>
                              <th></th>
                            </tr>
						</thead>
						<tbody>
                        <?php bnr_list();  ?>
               </tbody>
					</table>
                </div>
				</div>  <div class="clearfix"></div>
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