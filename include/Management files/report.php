<?php
#####################################################################
##                                                                 ##
##                        My ads v2.4.x                            ##
##                      http://www.krhost.ga                       ##
##                 e-mail: admin@kariya-host.com                   ##
##                                                                 ##
##                       copyright (c) 2021                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################
if($vrf_License=="65fgh4t8x5fe58v1rt8se9x"){
           //  report List
   if(isset($_GET['report']))
{
   if($_COOKIE['admin']==$hachadmin)
{


$statement = "`report` WHERE id ORDER BY `id` DESC";
$results =$db_con->prepare("SELECT * FROM {$statement} ");
$results->execute();
function report_list() {  global  $results;  global  $statement;   global  $db_con; global $f_awesome; global  $url_site;
while($wt=$results->fetch(PDO::FETCH_ASSOC)) {
  if($wt['s_type'] !=0){
if($wt['s_type']==1){
 $s_type ="directory";
}else if($wt['s_type']==2){
 $s_type ="forum";
}else if($wt['s_type']==3){
 $s_type ="news";
}else if($wt['s_type']==4){
 $s_type ="forum";
}else if($wt['s_type']==7867){
 $s_type ="forum";
}else if($wt['s_type']==99){
 $s_type ="users";
}
$rptus = $db_con->prepare("SELECT *  FROM users WHERE  id='{$wt['uid']}'");
$rptus->execute();
$rapruss=$rptus->fetch(PDO::FETCH_ASSOC);

$catusz = $db_con->prepare("SELECT *  FROM `{$s_type}` WHERE id=".$wt['tp_id'] );
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);
$sttcatusz = $db_con->prepare("SELECT *  FROM `status` WHERE tp_id={$wt['tp_id']} AND s_type={$wt['s_type']}" );
$sttcatusz->execute();
$sutcat=$sttcatusz->fetch(PDO::FETCH_ASSOC);


 if($wt['statu']=="1"){
  echo "<tr class=\"active\">";
 }else{
   echo "<tr>";
 }
echo "                        <td>#{$wt['id']}</td>
                              <td>{$rapruss['username']}&nbsp;<a href=\"{$url_site}/message/{$rapruss['id']}\" class=\"btn btn-info\"><p class=\"fa fa-envelope\" aria-hidden=\"true\"></p></a></td>
                              <td><a href=\"#\" data-toggle=\"modal\" data-target=\"#report{$wt['id']}\" class=\"btn btn-warning\" ><i class=\"fa fa-eye\" aria-hidden=\"true\"></i> </a>
     <!-- //modal report -->
              <div class=\"modal fade\" id=\"report{$wt['id']}\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-header\">
                        <b>{$s_type}</b>
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">";

 if($sutcat['s_type']==1){
 tpl_site_stt($sutcat,0);
}else if($sutcat['s_type']==2){
 tpl_topic_stt($sutcat,0);
}else if($sutcat['s_type']==3){
 tpl_news_stt($sutcat);
}else if($sutcat['s_type']==4){
 tpl_image_stt($sutcat,0);
}else if($sutcat['s_type']==7867){
 tpl_store_stt($sutcat,0);
}else if($wt['s_type']==99){
   if(isset($sucat['id'])){
 echo "<center><a href=\"{$url_site}/u/{$sucat['id']}\" class=\"btn btn-warning\"><i class=\"fa fa-user\" aria-hidden=\"true\"></i>&nbsp;{$sucat['username']}</a>&nbsp;";
  echo "<a href=\"{$url_site}/message/{$sucat['id']}\" class=\"btn btn-info\"><i class=\"fa fa-envelope\" aria-hidden=\"true\"></i>&nbsp;</a>&nbsp;";
  echo "<a href=\"{$url_site}/admincp?us_edit={$sucat['id']}\" class='btn btn-success' ><i class=\"fa fa-edit \"></i>&nbsp;</a>&nbsp;";
  echo "<a href=\"#\" data-toggle=\"modal\" data-target=\"#ban{$sucat['id']}\" class='btn btn-danger' ><i class=\"fa fa-ban \"></i>&nbsp;</a>";
  echo "<div class=\"modal fade\" id=\"ban{$sucat['id']}\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-header\">
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">
                                    <h3>Delete !</h3>
									<p>Sure to Delete User \"{$sucat['username']}\" ID no {$sucat['id']} ? </p><br />
                                    <center><a  href=\"{$url_site}/admincp?us_ban={$sucat['id']}\" class=\"btn btn-danger\" >Delete</a></center>
									  <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button

							</div>
						</div>
					</div>
				</div>
			</div>  </div></center>"; }else{
            echo "<center><b>The reported content has been removed</b>1</center>";
			}
}else{
 echo "<center><b>The reported content has been removed</b>2</center>";
}

						   echo "
                              <hr/>
                              <h3>{$rapruss['username']}&nbsp;<a href=\"{$url_site}/message/{$rapruss['id']}\" class=\"btn btn-info\"><i class=\"fa fa-envelope\" aria-hidden=\"true\"></i></a></h3>
                              <p>{$wt['txt']}</p>
                           </div><div class=\"clearfix\"></div>
						</div>
					</div>
				</div>
			</div>

	   <!-- //modal report --></td>
                              <td>";
             if($wt['statu']=="1"){
                              echo "<a href=\"{$url_site}/admincp?report&wtid={$wt['id']}\" class=\"btn btn-danger\">
                              <i class=\"fa fa-eye-slash\" aria-hidden=\"true\"></i>
                              </a>" ;
                                    }
            echo "</td>
                              <td></td>
</tr>";
 }
   } }
   if(isset($_GET['wtid'])){
  $bn_state="0";
  $bn_id = $_GET['wtid'];
  $stmsb = $db_con->prepare("UPDATE report SET statu=:state
            WHERE id=:id");
            $stmsb->bindParam(":state", $bn_state);
            $stmsb->bindParam(":id",    $bn_id);
            if($stmsb->execute()){
             header("Location: {$url_site}/admincp?report");
         	}
   }
  //  template
 template_mine('header');
 if(!isset($_COOKIE['user'])!="")
{
 template_mine('404');
}else{
 template_mine('admin_report');
 }
 template_mine('footer');

 }else{
   header("Location: home");
 }
 }
}else{
 header("Location: .../404.php ") ;
}
  ?>
