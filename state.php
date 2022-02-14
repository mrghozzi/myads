<?PHP

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

include "dbconfig.php";
include "include/function.php";
$title_page = "Statistics" ;
if (session_status() == PHP_SESSION_NONE) {
    session_start();
     }
$uidss = $_SESSION['user'];
if(isset($_GET)){
if(isset($_GET['ty'])){
 if($_GET['ty']=="clik"){
  $ty="link";
 }else if($_GET['ty']=="vu"){
  $ty="banner";
 }
 else{
$ty= $_GET['ty'];
}
if(isset($_GET['ty'])){ $ty2= $_GET['ty']; }
if(isset($_GET['id'])){ $ty_id= $_GET['id']; }
function ty_link() { global $ty; global $_GET; if($_GET['st']=="vu"){ echo "home.php"; }else if($ty=="link"){ echo "l_list.php"; }else if($ty=="banner"){ echo "b_list.php"; } }
if(isset($_GET['id'])){
$sadsty = $db_con->prepare("SELECT *  FROM {$ty} WHERE id={$ty_id}" );
$sadsty->execute();
$stadsty=$sadsty->fetch(PDO::FETCH_ASSOC);
$batyid = $stadsty['uid'];
}else{
 if (session_status() == PHP_SESSION_NONE) {
    session_start();
     }
  $batyid = $_SESSION['user'];
  }
if($uidss==$batyid){

 if(isset($_GET['id'])){
$statement = " `state` WHERE  pid='{$ty_id}' AND t_name='{$ty2}' ORDER BY `id` DESC";
$results =$db_con->prepare("SELECT * FROM {$statement} ");
$results->execute();
function bnr_list() {  global  $results;  global  $statement;     global  $url_site;
while($wt=$results->fetch(PDO::FETCH_ASSOC)) {
$getBrowser=getBrowser($wt['visitor_Agent']);
$covtime=date(' d,M Y - H:i:s',$wt['r_date']);
if($wt['r_link'] == "N"){
  $heuyuv= "";
}else{
 $heuyuv= "<a href=\"{$wt['r_link']}\">&nbsp;<i class=\"fa fa-link \"></i></a>";
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
  } if(isset($_GET['st'])=="vu"){
   $statement = " `state` WHERE  sid='{$uidss}' AND t_name='{$ty2}' ORDER BY `id` DESC";
$results =$db_con->prepare("SELECT * FROM {$statement} ");
$results->execute();
function bnr_list() {  global  $results;  global  $statement;   global  $url_site;
while($wt=$results->fetch(PDO::FETCH_ASSOC)) {
$getBrowser=getBrowser($wt['visitor_Agent']);
$covtime=date(' d,M Y - H:i:s',$wt['r_date']);
if($wt['r_link'] == "N"){
  $heuyuv= "";
}else{
 $heuyuv= "<a href=\"{$wt['r_link']}\">&nbsp;<i class=\"fa fa-link \"></i></a>";
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
 header("Location: 404.php") ;
}
}else{
 header("Location: 404.php?u") ;
}
}else{
 header("Location: 404.php") ;
}

?>

