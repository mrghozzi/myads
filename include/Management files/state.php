<?PHP

#####################################################################
##                                                                 ##
##                        MYads  v3.2.x                            ##
##                  https://github.com/mrghozzi                    ##
##                                                                 ##
##                                                                 ##
##                       copyright (c) 2025                        ##
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

if(isset($_GET['ty'])){
 if($_GET['ty']=="clik"){
  $ty="link";
 }else if($_GET['ty']=="vu"){
  $ty="banner";
 }else if(($_GET['ty']=="banner") OR ($_GET['ty']=="link")){
  $ty= $_GET['ty'];
 }else{
  header("Location: 404.php") ;
  }
$ty2= $_GET['ty'];
if(isset($_GET['id']) AND is_numeric($_GET['id'])) {
  $ty_id= $_GET['id'];
}

function ty_link() { global $ty; global $_GET; if(isset($_GET['st'])){ echo "admincp?users"; }else if($ty=="link"){ echo "admincp?l_list"; }else if($ty=="banner"){ echo "admincp?b_list"; } }


 if(isset($_GET['id']) AND is_numeric($_GET['id'])){
$statement = " `state` WHERE  pid='{$ty_id}' AND t_name='{$ty2}' ORDER BY `id` DESC";
$results =$db_con->prepare("SELECT * FROM {$statement} ");
if($results->execute()){
function bnr_list() {  global  $results;     global  $statement;
while($wt=$results->fetch(PDO::FETCH_ASSOC)) {
$getBrowser=getBrowser($wt['visitor_Agent']);
$covtime=date(' d,M Y',$wt['r_date'])."</br><i class=\"fa-solid fa-clock\"></i> ".date('H:i:s',$wt['r_date']);
if($wt['r_link'] == "N"){
  $heuyuv= "<p class=\"btn btn-danger\" ><i class=\"fa-solid fa-link-slash\"></i></p>";
}else{
 $heuyuv= "<a class=\"btn btn-success\" href=\"{$wt['r_link']}\" target=\"_blank\" ><i class=\"fa-solid fa-up-right-from-square\"></i></a>";
}
echo "<tr>
  <td>{$wt['id']}</td>
  <td>{$heuyuv}</td>
  <td>{$covtime}</td>
  <td>{$getBrowser['name']}<br />{$getBrowser['version']}</td>
  <td>{$getBrowser['platform']}</td>
  <td><a class=\"btn btn-primary\" href=\"http://ip.is-best.net/?ip={$wt['v_ip']}\" target=\"_blank\" ><i class=\"fa-solid fa-file-invoice fa-bounce\"></i></a></td>
  </tr>";

   }
      }
    }else{
      header("Location: 404.php ") ;
     }
  }else if(isset($_GET['st']) AND is_numeric($_GET['st'])){
$uidss=$_GET['st'];
$statement = " `state` WHERE  sid='{$uidss}' AND t_name='{$ty2}' ORDER BY `id` DESC";
$results =$db_con->prepare("SELECT * FROM {$statement} ");
$results->execute();
function bnr_list() {  global  $results;  global  $statement;
while($wt=$results->fetch(PDO::FETCH_ASSOC)) {
$getBrowser=getBrowser($wt['visitor_Agent']);
$covtime=date(' d,M Y',$wt['r_date'])."</br><i class=\"fa-solid fa-clock\"></i> ".date('H:i:s',$wt['r_date']);
if($wt['r_link'] == "N"){
  $heuyuv= "<p class=\"btn btn-danger\" ><i class=\"fa-solid fa-link-slash\"></i></p>";
}else{
 $heuyuv= "<a class=\"btn btn-success\" href=\"{$wt['r_link']}\" target=\"_blank\" ><i class=\"fa-solid fa-up-right-from-square\"></i></a>";
}
echo "<tr>
  <td>{$wt['id']}</td>
  <td>{$heuyuv}</td>
  <td>{$covtime}</td>
  <td>{$getBrowser['name']}<br />{$getBrowser['version']}</td>
  <td>{$getBrowser['platform']}</td>
  <td><a class=\"btn btn-primary\" href=\"http://ip.is-best.net/?ip={$wt['v_ip']}\"><i class=\"fa-solid fa-file-invoice fa-bounce\"></i></a></td>
  </tr>";

   }
      }
  }else{
    header("Location: 404.php ") ;
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
