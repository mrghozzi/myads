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
<?php  echo "<a href=\"#\" data-toggle=\"modal\" data-target=\"#ADD\" class='btn btn-info' ><i class=\"fa fa-plus \"></i></a>
 <div class=\"modal fade\" id=\"ADD\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-header\">
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">
 <form id=\"defaultForm\" method=\"post\" class=\"form-horizontal\" action=\"admincp.php?emojis_a\">
  <div class=\"input-group\">
  <span class=\"input-group-addon\" id=\"basic-addon1\">Emoji Shortcut</span>
  <input type=\"text\" class=\"form-control\" name=\"name\"  autocomplete=\"off\" />
  </div>
  <div class=\"input-group\">
  <span class=\"input-group-addon\" id=\"basic-addon1\">Emojis Icon Link</span>
  <input type=\"text\" class=\"form-control\" name=\"img\" autocomplete=\"off\" /></div>
  <div class=\"input-group\">
 <center><button type=\"submit\" name=\"ed_submit\" value=\"ed_submit\" class=\"btn btn-info\"><i class=\"fa fa-plus \"></i></button></center>
 <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button
 </div>
                                    </form>
                             </div>
						</div>
					</div>
				</div>
			</div>  </div>"; ?>
					<table id="tablepagination" class="table table-hover">
						<thead>
							<tr>
                              <th><center>#<b>ID</b></center></th>
                              <th><center><b>Emoji Shortcut</b></center></th>
                              <th><center><b>Emojis</b></center></th>
                              <th></th>

                           </tr>
						</thead>
						<tbody>

                        <?php emojis_list();  ?>
               </tbody>
					</table>
<script type="text/javascript">
            $(document).ready(function() {
    $('#tablepagination').DataTable({
      "order": [[0, 'DESC']]
    });
} );
</script>
				</div>
				</div>  <div class="clearfix"></div>
				</div>  
				</div>
<?php }else{ echo"404"; }  ?>