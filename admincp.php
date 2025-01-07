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
 include "dbconfig.php";
 include "include/function.php";
 include_once('include/pagination.php');
  if(isset($conf_us_log) AND isset($md5_cook_us) AND (md5($conf_us_log)==$md5_cook_us)){

 $stadmin = $db_con->prepare("SELECT * FROM users WHERE id=1 ");
 $stadmin->execute();
 $adminusRow=$stadmin->fetch(PDO::FETCH_ASSOC);
 $conf_admin_log=$adminusRow['id'].$adminusRow['username'].$adminusRow['email'];
if(md5($conf_admin_log)==$_COOKIE['userha'])
{
  if(isset($_GET['cont'])) {  // admin login
    if($_COOKIE['user']==$uRow['id']) {  $eid =$hachadmin;  $nextWeek = time() + (365 * 24 * 60 * 60);  setcookie("admin", $eid, $nextWeek); $rpage ="admincp?home";   header("Location: {$rpage}"); }
    else{    header("Location: home");  }
   }
  if(isset($_GET['dcont'])) {  //  admin logout
   if($_COOKIE['user']==$uRow['id']) { setcookie("admin", "", time()-3600);    header("Location: home");
  }else{  header("Location: home");     }
 }
 $title_page = "Admin cP";
 //  admin Home
  if(isset($_GET['home']))
{
   if($_COOKIE['admin']==$hachadmin)
{
   template_mine('header');
 if(!isset($_COOKIE['user'])!="")
{
 template_mine('404');
}else{
 template_mine('admin/admin_header');
 template_mine('admin/admin_home');
 }
 template_mine('footer');

 }else{
   header("Location: home");
 }
 }
     // include files
  include "include/Management files/settings.php";   //settings
  include "include/Management files/menu.php";   //menu
  include "include/Management files/news.php";   //news
  include "include/Management files/state.php";   //state
  include "include/Management files/categories.php";   //categories
  include "include/Management files/report.php";   //report
  include "include/Management files/emojis.php";   //emojis
  include "include/Management files/plugins.php";  //plugins
  include "include/Management files/knowledgebase.php";  //knowledgebase
  include "include/Management files/social_login.php";  //social login
  include "include/Management files/updates.php";  //updates
  include "include/Management files/users.php";  //users
  include "include/Management files/visits.php";  //visits
  include "include/Management files/banners.php";  //banners
  include "include/Management files/link.php";  //link
  include "include/Management files/widgets.php";  //widgets
  include "include/Management files/advertisement.php";   //advertisement

     // include DB
  $o_type = "Management_files";
  $stmut = $db_con->prepare("SELECT *  FROM options WHERE o_type='{$o_type}' ORDER BY `o_order` DESC" );
  $stmut->execute();
while($mang_fil=$stmut->fetch(PDO::FETCH_ASSOC)){
    include $mang_fil['o_valuer'];
}

} //  END
else{ header("Location: 404"); }
} //  END
else{ header("Location: 404"); }

 ?>
