<?PHP

#####################################################################
##                                                                 ##
##                        MYads  v3.x.x                            ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2022                        ##
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
}else if($wt['s_type']==100){
 $s_type ="forum";
}else if($wt['s_type']==99){
 $s_type ="users";
}
$rptus = $db_con->prepare("SELECT *  FROM users WHERE  id='{$wt['uid']}'");
$rptus->execute();
$rapruss=$rptus->fetch(PDO::FETCH_ASSOC);
if($wt['uid']==0){
$r_username = "<b><i class=\"fa fa-user\" aria-hidden=\"true\"></i>&nbsp;Guest</b>";
}else{
$r_username = "<b>{$rapruss['username']}</b>&nbsp;<a href=\"{$url_site}/message/{$rapruss['id']}\" class=\"btn btn-info\"><i class=\"fa fa-envelope\" aria-hidden=\"true\"></i></a>";
}
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
echo "                        <td><center><b>{$wt['id']}</b></center></td>
                              <td><center>{$r_username}</center></td>
                              <td><center><div class=\"grid grid-12\" ><div class=\"grid-column\" >";

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
  echo "</center>"; }else{
            echo "<center><b>The reported content has been removed</b>1</center>";
			}
}else{
 echo "<center><b>The reported content has been removed</b>2</center>";
}

						   echo "
                              <hr/>
                              <p>{$wt['txt']}</p>
                           </center></td>
                              <td><center>";
             if($wt['statu']=="1"){
                              echo "<a href=\"{$url_site}/admincp?report&wtid={$wt['id']}\" class=\"btn btn-danger\">
                              <i class=\"fa fa-eye-slash\" aria-hidden=\"true\"></i>
                              </a>" ;
                                    }
            echo "</div></div></center></td>

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
 template_mine('admin/admin_header');
 template_mine('admin/admin_report');
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
