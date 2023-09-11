<?php if($s_st=="buyfgeufb"){  ?>
<div class="grid grid-3-9 medium-space" >
<div class="grid-column" >
<?php template_mine('admin/admin_nav');  ?>
</div>
<div class="grid-column" >
				<!--buttons-->
				<div class="widget-box">
					<h2 class="hdg"><?php lang('users'); ?></h2>

            <div class="col-12">
                <table id="tablepagination" class="table table-hover">
						<thead>
							<tr>
                              <th><center>#<b>ID</b></center></th>
							  <th><center><b>Username</b></center></th>
                              <th><center><b>Online</b></center></th>
                              <th><center><b>Check</b></center></th>
                              <th><center><b>PTS</b></center></th>
                            </tr>
						</thead>
						<tbody>
              <?php




$statement = "`users` WHERE id ORDER BY `id` DESC";
$results =$db_con->prepare("SELECT * FROM {$statement} ");
$results->execute();

while($wt=$results->fetch(PDO::FETCH_ASSOC)) {
$str_username = mb_strlen($wt['username'], 'utf8');
if($str_username > 15){
   $username = substr($wt['username'],0,15)."&nbsp;...";
 }else{
   $username = $wt['username'];
 }

echo "<tr>
  <td><center>{$wt['id']}</center></td>
  <td><center><a href=\"{$url_site}/u/{$wt['id']}\"><img class=\"imgu-bordered-sm\" src=\"{$url_site}/{$wt['img']}\" style=\"width: 34px;\" alt=\"user image\">&nbsp;<b>{$username}</b></a></center>
  <br /><div><a href=\"{$url_site}/admincp?us_edit={$wt['id']}\" class='btn btn-success' ><i class=\"fa fa-edit \"></i></a>
  <a href=\"#\" data-toggle=\"modal\" data-target=\"#ban{$wt['id']}\" class='btn btn-danger' ><i class=\"fa fa-ban \"></i></a>
  </div></td>";
  echo "<center><td>";
  online_us($wt['id']);
  echo "<br /><i class=\"fa fa-clock-o \"></i>&nbsp;{$wt['online']}</center></td><td><center>&nbsp;";
   check_us($wt['id']);
  echo "&nbsp;<br />{$wt['ucheck']}</center></td>
  <td><center>{$wt['pts']}</center><br />
  <div><a href=\"{$url_site}/admincp?state&ty=banner&st={$wt['id']}\" class='btn btn-warning' ><i class=\"fa fa-link \"></i></a>
  <a href=\"{$url_site}/admincp?state&ty=link&st={$wt['id']}\" class='btn btn-primary' ><i class=\"fa fa-eye \"></i></a></div></td>

</tr>";
echo "<div class=\"modal fade\" id=\"ban{$wt['id']}\" data-backdrop=\"\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog modal-dialog-centered\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-header\">
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">
                                    <center><h3>{$lang['delete']}&nbsp;!</h3></center>
                                    <hr />
									<center><h4>{$lang['aysywtd']} \"{$wt['username']}\"  ? </h4></center>
                                    <hr />
                                    <center>
                                    <a  href=\"{$url_site}/admincp?us_ban={$wt['id']}\" class=\"btn btn-danger\" >{$lang['delete']}&nbsp;<i class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></i></a>
                                    <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">{$lang['close']}</button>
                                    </center>
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
                              <th><center>PTS</center></th>
                            </tr>
						</tfoot>
					</table>

				</div>
			  </div>
            </div>
	   </div>
<?php }else{ echo"404"; }  ?> 