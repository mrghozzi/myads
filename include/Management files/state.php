﻿<?PHP

#####################################################################
##                                                                 ##
##                        My ads v2.4.x                            ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2022                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################
if($vrf_License=="65fgh4t8x5fe58v1rt8se9x"){
          // state List
   if(isset($_GET['state']))
{   $admin_page=1;
   if($_COOKIE['admin']==$hachadmin)
{

if($_GET['ty']){
 if($_GET['ty']=="clik"){
  $ty="link";
 }else if($_GET['ty']=="vu"){
  $ty="banner";
 }
 else{
$ty= $_GET['ty'];
}
$ty2= $_GET['ty'];
if(isset($_GET['id'])) {
  $ty_id= $_GET['id'];
}

function ty_link() { global $ty; global $_GET; if(isset($_GET['st'])){ echo "admincp?users"; }else if($ty=="link"){ echo "admincp?l_list"; }else if($ty=="banner"){ echo "admincp?b_list"; } }


 if(isset($_GET['id'])){
$statement = " `state` WHERE  pid='{$ty_id}' AND t_name='{$ty2}' ORDER BY `id` DESC";
$results =$db_con->prepare("SELECT * FROM {$statement} ");
$results->execute();
function bnr_list() {  global  $results;     global  $statement;
while($wt=$results->fetch(PDO::FETCH_ASSOC)) {
$getBrowser=getBrowser($wt['visitor_Agent']);
$covtime=date(' d,M Y - H:i:s',$wt['r_date']);
if($wt['r_link'] == "N"){
  $heuyuv= "";
}else{
 $heuyuv= "<a href=\"{$wt['r_link']}\" target=\"_blank\" >&nbsp;<i class=\"fa fa-link \"></i></a>";
}
echo "<tr>
  <td>{$wt['id']}</td>
  <td>{$wt['r_link']}$heuyuv</td>
  <td>{$covtime}</td>
  <td>{$getBrowser['name']}<br />{$getBrowser['version']}</td>
  <td>{$getBrowser['platform']}</td>
  <td><a href=\"http://ip.krhost.ga/?ip={$wt['v_ip']}\">{$wt['v_ip']}</a></td>
  </tr>";

   }
      }
  } if(isset($_GET['st'])){
$uidss=$_GET['st'];
$statement = " `state` WHERE  sid='{$uidss}' AND t_name='{$ty2}' ORDER BY `id` DESC";
$results =$db_con->prepare("SELECT * FROM {$statement} ");
$results->execute();
function bnr_list() {  global  $results;  global  $statement;
while($wt=$results->fetch(PDO::FETCH_ASSOC)) {
$getBrowser=getBrowser($wt['visitor_Agent']);
$covtime=date(' d,M Y - H:i:s',$wt['r_date']);
if($wt['r_link'] == "N"){
  $heuyuv= "";
}else{
 $heuyuv= "<a href=\"{$wt['r_link']}\" target=\"_blank\" >&nbsp;<i class=\"fa fa-link \"></i></a>";
}
echo "<tr>
  <td>{$wt['id']}</td>
  <td>{$wt['r_link']}$heuyuv</td>
  <td>{$covtime}</td>
  <td>{$getBrowser['name']}<br />{$getBrowser['version']}</td>
  <td>{$getBrowser['platform']}</td>
  <td><a href=\"http://ip.krhost.ga/?ip={$wt['v_ip']}\">{$wt['v_ip']}</a></td>
  </tr>";

   }
      }
  }

 template_mine('header');
 if(!isset($_COOKIE['user'])!="")
{
 template_mine('404');
}else{
 template_mine('state');
 }
 template_mine('footer');
}else{
 header("Location: 404?u") ;
}

 }else {  header("Location: 404");  }
 }

}else{
 header("Location: .../404.php ") ;
}
  ?>
