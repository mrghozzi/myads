<?php if($s_st=="buyfgeufb"){  ?>
<style>
select {
  font-family: 'FontAwesome', 'sans-serif';
}</style>
<div class="grid grid-3-9 medium-space" >
<div class="grid-column" >
<?php template_mine('admin/admin_nav');  ?>
</div>
<div class="grid-column" >
				<!--buttons-->
				<div class="widget-box">
					<h2 class="hdg">Directory categories</h2>
                    <?php  echo "<a href=\"#\" data-toggle=\"modal\" data-target=\"#ADD\" class='btn btn-info' ><i class=\"fa fa-plus \"></i>&nbsp;{$lang['add']}</a>
 <div class=\"modal fade\" id=\"ADD\" data-backdrop=\"\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog modal-dialog-centered\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-header\">
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">
 <form id=\"defaultForm\" method=\"post\" class=\"form-horizontal\" action=\"admincp.php?d_cat_a\">
  <div class=\"input-group\">
  <span class=\"input-group-addon\" id=\"basic-addon1\">Name</span>
  <input type=\"text\" class=\"form-control\" name=\"name\"  autocomplete=\"off\" />
  </div>
  <div class=\"input-group\">
  <span class=\"input-group-addon\" id=\"basic-addon1\">Folder</span>
  <select name=\"sub\" class=\"form-control\" autocomplete=\"off\">
  <option value=\"0\" >--------</option>";
 $stcmut = $db_con->prepare("SELECT *  FROM cat_dir WHERE sub=0 ORDER BY `name` ASC" );
 $stcmut->execute();
 while($ncat_tt=$stcmut->fetch(PDO::FETCH_ASSOC)){
    echo "<option value=\"{$ncat_tt['id']}\" >{$ncat_tt['name']}</option>";
  }
echo "</select></div>
  <div class=\"input-group\">
  <span class=\"input-group-addon\" id=\"basic-addon1\">Order</span>
  <input type=\"number\" class=\"form-control\" name=\"ordercat\" value=\"0\" autocomplete=\"off\" />
</div>
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
                </div>
              <div class="widget-box">
            <div class="col-md-12 table-grid">
             <?php if(isset($_GET['bnerrMSG'])){  ?>
                     <div class="alert alert-danger" role="alert"><?php echo $_GET['bnerrMSG'];  ?></div>
                        <?php }  ?>
                <div class="panel panel-widget">
					<table id="tablepagination" class="table table-hover">
						<thead>
							<tr>
                              <th><center>#ID</center></th>
                              <th><center>Name</center></th>
                              <th><center>Order</center></th>
                              <th></th>
                           </tr>
						</thead>
						<tbody>

                        <?php lnk_list();  ?>
               </tbody>
               <tfoot>
							<tr>
                              <th><center>#ID</center></th>
                              <th><center>Name</center></th>
                              <th><center>Order</center></th>
                              <th></th>
                           </tr>
			  </tfoot>
					</table>

				</div>
				</div>
                </div>
				</div>
				</div>

<?php }else{ echo"404"; }  ?>