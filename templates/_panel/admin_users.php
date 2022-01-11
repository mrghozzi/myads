<?php if($s_st=="buyfgeufb"){  ?>

		<div id="page-wrapper">
			<div class="main-page">
				<!--buttons-->
				<div class="grids-section">
					<h2 class="hdg"><?php lang('users'); ?></h2>

			<div class="clearfix"></div>
			</div>
            <div class="col-12">
                <div class="panel">

                 <table id="tablepagination" class="table table-hover">
						<thead>
							<tr>
                              <th>#ID</th>
							  <th>Username</th>
                              <th>online</th>
                              <th>check</th>
                              <th>Mail</th>
							  <th>PTS</th>
                              <th></th>
                            </tr>
						</thead>
						<tbody>
              <?php




$statement = "`users` WHERE id ORDER BY `id` DESC";
$results =$db_con->prepare("SELECT * FROM {$statement} ");
$results->execute();

while($wt=$results->fetch(PDO::FETCH_ASSOC)) {


echo "<tr>
  <td>{$wt['id']}</td>
  <td><a href=\"u/{$wt['id']}\"><img class=\"imgu-bordered-sm\" src=\"{$url_site}/{$wt['img']}\" style=\"width: 35px;\" alt=\"user image\">{$wt['username']}</a></td>";
  echo "<td>";
  online_us($wt['id']);
  echo "<br /><i class=\"fa fa-clock-o \"></i>&nbsp;{$wt['online']}</td><td>";
   check_us($wt['id']);
  echo "</td>
  <td>{$wt['email']}</td>
  <td>{$wt['pts']}</td>
  <td><a href=\"admincp?state&ty=banner&st={$wt['id']}\" class='btn btn-warning' ><i class=\"fa fa-link \"></i></a>
  <a href=\"admincp?state&ty=link&st={$wt['id']}\" class='btn btn-primary' ><i class=\"fa fa-eye \"></i></a>
  <a href=\"admincp?us_edit={$wt['id']}\" class='btn btn-success' ><i class=\"fa fa-edit \"></i></a>
  <a href=\"#\" data-toggle=\"modal\" data-target=\"#ban{$wt['id']}\" class='btn btn-danger' ><i class=\"fa fa-ban \"></i></a></td>
</tr>";
echo "<div class=\"modal fade\" id=\"ban{$wt['id']}\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-header\">
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">
                                    <h3>Delete !</h3>
									<p>Sure to Delete User \"{$wt['username']}\" ID no {$wt['id']} ? </p><br />
                                    <center><a  href=\"admincp?us_ban={$wt['id']}\" class=\"btn btn-danger\" >Delete</a></center>
									  <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button

							</div>
						</div>
					</div>
				</div>
			</div>  </div>";
   }

 ?>
               </tbody>
               <tfoot>
							<tr>
                              <th>#ID</th>
							  <th>Username</th>
                              <th>Mail</th>
							  <th>PTS</th>
                              <th></th>
                            </tr>
						</tfoot>
					</table>
                              <script type="text/javascript">
            $(document).ready(function() {
    $('#tablepagination').DataTable();
} );
</script>
				</div>
				</div><div class="clearfix"></div>

				</div> <div class="clearfix"></div>
				</div>

<?php }else{ echo"404"; }  ?> 