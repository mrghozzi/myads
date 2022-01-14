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
                              <th><center>#<b>ID</b></center></th>
							  <th><center><b>Username</b></center></th>
                              <th><center><b>Online</b></center></th>
                              <th><center><b>Check</b></center></th>
                              <th><center><b>Mail</b></center></th>
							  <th><center><b>PTS</b></center></th>
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
  <td><center>{$wt['id']}</center></td>
  <td><center><a href=\"u/{$wt['id']}\"><img class=\"imgu-bordered-sm\" src=\"{$url_site}/{$wt['img']}\" style=\"width: 34px;\" alt=\"user image\">&nbsp;<b>{$wt['username']}</b></a></center></td>";
  echo "<center><td>";
  online_us($wt['id']);
  echo "<br /><i class=\"fa fa-clock-o \"></i>&nbsp;{$wt['online']}</center></td><td><center>&nbsp;";
   check_us($wt['id']);
  echo "&nbsp;<br />{$wt['ucheck']}</center></td>
  <td><center>{$wt['email']}</center></td>
  <td><center>{$wt['pts']}</center></td>
  <td><center><div><a href=\"admincp?state&ty=banner&st={$wt['id']}\" class='btn btn-warning' ><i class=\"fa fa-link \"></i></a>
  <a href=\"admincp?state&ty=link&st={$wt['id']}\" class='btn btn-primary' ><i class=\"fa fa-eye \"></i></a></div>
  &nbsp;<div><a href=\"admincp?us_edit={$wt['id']}\" class='btn btn-success' ><i class=\"fa fa-edit \"></i></a>
  <a href=\"#\" data-toggle=\"modal\" data-target=\"#ban{$wt['id']}\" class='btn btn-danger' ><i class=\"fa fa-ban \"></i></a></div></center></td>
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
                              <th><center>#ID</center></th>
							  <th><center>Username</center></th>
                              <th><center>online</center></th>
                              <th><center>check</center></th>
                              <th><center>Mail</center></th>
							  <th><center>PTS</center></th>
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