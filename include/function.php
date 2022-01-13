<?php

#####################################################################
##                                                                 ##
##                        My ads v2.4.3                            ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2022                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################

if($vrf_License=="65fgh4t8x5fe58v1rt8se9x"){
$s_st="buyfgeufb";
 //  $setting
 try{
 $stmt = $db_con->prepare("SELECT *  FROM setting   " );
 $stmt->execute();
 $stt=$stmt->fetch(PDO::FETCH_ASSOC);
   } catch(PDOException $e){
      header("Location: install") ;
    }
$stversion = "2.4.3";

$o_type = "version" ;
$name = "2-4-3";
$jversion = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = :o_type  ");
$jversion->bindParam(":o_type", $o_type);
$jversion->execute();
 $versionRow=$jversion->fetch(PDO::FETCH_ASSOC);
  if( isset($versionRow['o_type']) AND ($versionRow['o_type']==$o_type)){
  if( isset($versionRow['o_valuer']) AND ($versionRow['o_valuer']==$stversion)){  }else{
       $ostmsbs = $db_con->prepare("UPDATE options SET name=:name,o_valuer=:o_valuer,o_type=:o_type
            WHERE id=:id");
            $ostmsbs->bindParam(":o_type", $o_type);
            $ostmsbs->bindParam(":o_valuer", $stversion);
            $ostmsbs->bindParam(":name", $name);
            $ostmsbs->bindParam(":id", $versionRow['id']);
            if($ostmsbs->execute()){ }
   }
   }else{
   $ostmsbs = $db_con->prepare(" INSERT INTO options  (name,o_valuer,o_type,o_parent,o_order,o_mode)
            VALUES (:name,:o_valuer,:o_type,0,0,0) ");
	        $ostmsbs->bindParam(":o_type", $o_type);
            $ostmsbs->bindParam(":o_valuer", $stversion);
            $ostmsbs->bindParam(":name", $name);
            if($ostmsbs->execute()){   }
 }
 $template   = $stt['styles'];
 $title_s    = $stt['titer'];
 $descr_site = $stt['description'];
 $templates  = "templates/".$template;
 $url_site   = $stt['url'];
 $mail_site  = $stt['a_mail'];
 $lang_site  = $stt['lang'];


 function myads_version()         {    global  $versionRow ; if(isset($versionRow['o_valuer'])){ echo $versionRow['o_valuer']; }    }
 function myads_fversion()        {    global  $versionRow ; if(isset($versionRow['name'])){ echo $versionRow['name'];         }    }

 // menu
$stmut = $db_con->prepare("SELECT *  FROM menu   " );
$stmut->execute();

// ads
$sads = $db_con->prepare("SELECT *  FROM ads WHERE id=3" );
$sads->execute();
$stads=$sads->fetch(PDO::FETCH_ASSOC);
$bads3 = $stads['code_ads'];

 if( ! ini_get('date.timezone') )
{
    date_default_timezone_set($stt['timezone']);
}
//  user Session
session_start();
 if(isset($_COOKIE['userha'])!="")
{

  $_SESSION['user']=$_COOKIE['user'];

 $stmus = $db_con->prepare("SELECT * FROM users WHERE id=:user ");
 $stmus->bindParam(":user", $_SESSION['user']);
 $stmus->execute();
 $uRow=$stmus->fetch(PDO::FETCH_ASSOC);
 $bn_online = time();
 $conf_us_log=$uRow['id'].$uRow['username'].$uRow['email'];
 $md5_cook_us=$_COOKIE['userha'];
 $stmsbus = $db_con->prepare("UPDATE users SET online=:online WHERE id=:user  ");
            $stmsbus->bindParam(":user", $uRow['id']);
            $stmsbus->bindParam(":online",  $bn_online);
            if($stmsbus->execute()){

         	}
 if(md5($conf_us_log)==$md5_cook_us){ $_SESSION['user']=$uRow['id'];}
 else{ header("Location: {$url_site}/logout?logout"); }
class session{
  private static function _RegenerateId()
    {
        session_regenerate_id(true);
    }
  public static function init(){
   session_start();
  }
  public static function set($key,$value){
    $_SESSION[$key]=$value;
    session::_RegenerateId();
  }
  public static function get($key, $secondKey = false){
    session::_RegenerateId();
    if(isset($_SESSION[$key]))
    return $_SESSION[$key];
        if ($secondKey == true)
        {
            if (isset($_SESSION[$key][$secondKey]))
            return $_SESSION[$key][$secondKey];
        }
        else
        {
            if (isset($_SESSION[$key]))
            return $_SESSION[$key];
        }

  }
  public static function destroy(){
    session_destroy();
  }
} }
 // admin
 $id_admin = "1";
 $stadmin_select = $db_con->prepare('SELECT * FROM users WHERE id=:id ');
 $stadmin_select->bindParam(":id", $id_admin);
 $stadmin_select->execute();
 $usadmin=$stadmin_select->fetch(PDO::FETCH_ASSOC);
 $hachadmin= md5($usadmin['pass'].$usadmin['username']) ;
//  Language
 if (isset($_GET["en"])) {
     $lg_md5 = time() + (365 * 24 * 60 * 60);
     setcookie("lang", "en", $lg_md5);
     header('Location: ' . $_SERVER['HTTP_REFERER']);
    } else if (isset($_GET["ar"])){
      $lg_md5 = time() + (365 * 24 * 60 * 60);
     setcookie("lang", "ar", $lg_md5);
     header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
$o_type = "languages";
$exlanguages = $db_con->prepare("SELECT  * FROM `options` WHERE o_type=:o_type ORDER BY `o_order` DESC " );
$exlanguages->bindParam(":o_type", $o_type);
$exlanguages->execute();
while($exlang=$exlanguages->fetch(PDO::FETCH_ASSOC)){
  $lang_var = $exlang['o_valuer'];
   if (isset($_GET["{$lang_var}"])){
      $lg_md5 = time() + (365 * 24 * 60 * 60);
     setcookie("lang", $exlang['o_valuer'], $lg_md5);
     header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
         }
if (isset($_COOKIE['lang'])) {
      $c_lang=$_COOKIE['lang'] ;
       } else {  $c_lang=$lang_site ; }
include "content/languages/$c_lang.php"; //  Language File
function lang($name) {    global  $lang ;   echo  $lang["{$name}"];    }
//    install exists
 $filename = 'install';
 if (file_exists($filename)) {
 echo "<center>";
 lang('dinstall');
 echo "</center>";
function dinstall_d() {
   global  $lang ;
   echo "<div class=\"alert alert-danger alert-dismissible\" role=\"alert\">
  <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
  <strong>Warning!</strong> {$lang['dinstall']}
</div>";
    }
 }else{
  function dinstall_d() { }
 }

include "include/agent.php";  //  Get Browser
include "include/tpl_status.php";  //  Get templates status
include "include/f_awesome.php";  //  Get Font Awesome v. 4.6.
include "include/convertTime.php";  //  ConvertTime

//  messages   COUNT
function msg_nbr($name) {   global $_SESSION ;  global $db_con ; global $url_site;
$msgusid = $_SESSION['user'];
$mstmt = $db_con->prepare("SELECT  COUNT(id_msg) as nbr FROM messages WHERE state=1 and us_rec=:user " );
        $mstmt->bindParam(":user", $_SESSION['user']);
        $mstmt->execute();
        $amsg=$mstmt->fetch(PDO::FETCH_ASSOC);
        $msg_n=$amsg['nbr'];
        if($name=="vu"){ echo $msg_n; }
        if($name=="span"){ if($msg_n==0){}else{ echo "<span class='badge'>{$msg_n}</span>"; } }
        if($name=="list"){ $msgusen = $db_con->prepare("SELECT *  FROM messages
        WHERE us_rec=:msgusid ORDER BY `time` DESC LIMIT 5");
$msgusen->bindParam(":msgusid", $msgusid);
$msgusen->execute();
while($msgssen=$msgusen->fetch(PDO::FETCH_ASSOC)){
$catusen = $db_con->prepare("SELECT *  FROM users WHERE  id=:us_env");
$catusen->bindParam(":us_env", $msgssen['us_env']);
$catusen->execute();
$catussen=$catusen->fetch(PDO::FETCH_ASSOC);
$time_cmt=convertTime($msgssen['time']);
  if($msgssen['state']=="1"){
     echo "<li class=\"active\" >";
  }else{
     echo "<li>";
  }
  echo "<a href=\"{$url_site}/message/{$msgssen['us_env']}\" >
								 <div class=\"user_img\"><img class=\"imgu-bordered-sm\" src=\"{$url_site}/{$catussen['img']}\" alt=\"\"></div>
								 <div class=\"notification_desc\">
									<p style=\"color: #FF0033\" >{$catussen['username']}</p>
									<p><span>{$time_cmt}</span></p>
								</div>
								 <div class=\"clearfix\"></div>
								</a></li>";
} }
        }
//  notif   COUNT
function ntf_nbr($name) {   global $_SESSION ;  global $db_con ;  global $url_site;
        $msgusid = $_SESSION['user'];
        $etime = time();
        $stmnt = $db_con->prepare("SELECT  COUNT(id) as nbr FROM notif WHERE time<:etime AND state=1 and uid=:uid" );
        $stmnt->bindParam(":uid", $_SESSION['user']);
        $stmnt->bindParam(":etime", $etime);
        $stmnt->execute();
        $ant=$stmnt->fetch(PDO::FETCH_ASSOC);
        $net_n=$ant['nbr'];
        if($name=="vu"){ echo $net_n; }
        if($name=="span"){ if($net_n==0){}else{ echo "<span class='badge blue'>{$net_n}</span>"; } }
        if($name=="list"){ $msgusen = $db_con->prepare("SELECT *  FROM notif
        WHERE time<'{$etime}' AND uid=:msgusid ORDER BY `time` DESC LIMIT 5");
        $msgusen->bindParam(":msgusid", $msgusid);
        $msgusen->execute();
 while($msgssen=$msgusen->fetch(PDO::FETCH_ASSOC)){
  $time_cmt=convertTime($msgssen['time']);
  if(($msgssen['state']=="1") OR ($msgssen['state']=="3")){
     echo "<li class=\"active\" >";
  }else{
     echo "<li>";
  }
  echo "<a href=\"{$url_site}/notif/{$msgssen['id']}\" >
								 <div class=\"user_img\"><img src=\"{$url_site}/templates/_panel/images/{$msgssen['logo']}\" alt=\"\"></div>
								 <div class=\"notification_desc\">
									<p style=\"color: #FF0033\" >{$msgssen['name']}</p>
									<p><span>{$time_cmt}</span></p>
								</div>
								 <div class=\"clearfix\"></div>
								</a></li>";
} }
        }


// online
function online_admin()
{
  global  $db_con ;
$bn_online = time()-60;
$bncount = $db_con->prepare("SELECT  COUNT(id) as nbr FROM users WHERE online > :online ");
$bncount->bindParam(":online", $bn_online);
$bncount->execute();
$abbn=$bncount->fetch(PDO::FETCH_ASSOC);
$contbn= $abbn['nbr']; echo $contbn;
}
function online_us($id)
{
  global  $db_con ;
 $bn_online = time()-60;

$bncount = $db_con->prepare("SELECT  * FROM users WHERE id = :id ");
$bncount->bindParam(":id", $id);
$bncount->execute();
$abbn=$bncount->fetch(PDO::FETCH_ASSOC);
if($abbn['online']>$bn_online){
  echo "<i class=\"fa fa-fw fa-circle\" style=\"color: #00CC33;\" ></i>";
}
}

//
function check_us($id)
{
  global  $db_con ;
$bncount = $db_con->prepare("SELECT  * FROM users WHERE id = :id ");
$bncount->bindParam(":id", $id);
$bncount->execute();
$abbn=$bncount->fetch(PDO::FETCH_ASSOC);
if($abbn['ucheck']=="1"){
  echo "<i class=\"fa fa-fw fa-check-circle\" style=\"color: #0066CC;\" ></i>";
}
}
if(isset($_GET['ref'])!=""){
 $refeid= $_GET['ref'];
 $nextWeekr = time() + (2 * 24 * 60 * 60);
 setcookie("ref", $refeid, $nextWeekr);
}
if (isset($_SESSION['msgr']))  {
$msg_alertr=$_SESSION['msgr'];
}
if (isset($_SESSION['msgl']))  {
$msg_alertl=$_SESSION['msgl'];
}
if (isset($_COOKIE['user'])){
$user_conect=$_COOKIE['user'];
}
function nev_menu() {    global  $stmut ; global $user_conect ; global $uRow ;  global $url_site;
while($menu_tt=$stmut->fetch(PDO::FETCH_ASSOC)){
  $m_name=$menu_tt['name']; $m_dir=$menu_tt['dir']; echo "<li><a href='{$m_dir}' >{$m_name}</a></li>"; }
  if($user_conect){ echo "<li><a href='{$url_site}/home' >Dashboard</a></li><li><a href='{$url_site}/u/{$uRow['id']}' >{$uRow['username']}</a></li>"."<li><a href='{$url_site}/logout?logout' >Logout</a></li>"; }
  else{  echo "<li><a href='{$url_site}/login' >Login</a></li>"."<li><a href='{$url_site}/register' >Signup</a></li>"; }    }
function news_site() { global $db_con;
$stmut = $db_con->prepare("SELECT *  FROM news WHERE id ORDER BY `id` DESC LIMIT 5   " ); $stmut->execute();
while($news_tt=$stmut->fetch(PDO::FETCH_ASSOC)){
$s_text=substr($news_tt['text'],0,30);
$id= $news_tt['id'];
$news_time=date('Y-m-d',$news_tt['date']);
echo "<div class='message-bottom'> <div class='message1-left'>
	  <h4><a >{$news_tt['name']}</a></h4> <p>{$s_text}&nbsp;<a class=\"btn\" data-toggle=\"modal\" data-target=\"#text{$news_tt['id']}\" ><i class=\"fa fa-arrows-alt\" aria-hidden=\"true\"></i></a></p></div>
	  <div class='message1-right'><p>{$news_time}</p></div>
	  <div class='clearfix'></div></div>
      <!-- //modal news {$news_tt['id']} -->
              <div class=\"modal fade\" id=\"text{$news_tt['id']}\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-header\">
                        <b>{$news_tt['name']}</b>
                           <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">
                            <p>{$news_tt['text']}</p>
                            <hr/>
                             <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">";
                             lang('close');
                             echo "</button>
							</div>
						</div>
					</div>
				</div>
			</div>   <!-- //modal news {$news_tt['id']} -->"; }}
function user_row($name){ global $uRow ; echo $uRow["{$name}"]; }
function ref_url(){ global $uRow ; global  $url_site;   echo $url_site."?ref=".$uRow['id']; }
function title_site($name) {    global  $title_s ; if($name){ echo $name." - ".$title_s; } else{ echo $title_s; }    }
function url_site() {    global  $url_site; if($url_site){ echo $url_site; } else{ echo "#"; }    }
function url_page($name) {    global  $url_site; if(isset($name)){ echo $url_site.$name; } else if($name){ echo $url_site; }    }
function descr_site() {    global  $descr_site ;  echo $descr_site;   }
function mail_site() {    global  $mail_site ;  echo $mail_site;   }
function ads_site($name) {    global  $db_con ;    $sads = $db_con->prepare("SELECT *  FROM ads WHERE id=:name "); $sads->bindParam(":name", $name); $sads->execute();  $stads=$sads->fetch(PDO::FETCH_ASSOC);
$bads1 = $stads['code_ads']; echo $bads1;   }
function social_site($s_media) {    global  $stt ;  echo $stt["{$s_media}"];   }
function nbr_state($s_tabel) {    global  $db_con ;
$bncount = $db_con->prepare("SELECT  COUNT(id) as nbr FROM $s_tabel   " );
$bncount->execute();
$abbn=$bncount->fetch(PDO::FETCH_ASSOC);
$contbn= $abbn['nbr']; echo $contbn;   }
function admin_state($s_tabel) {    global  $db_con ;
$bncount = $db_con->prepare("SELECT  COUNT(id) as nbr FROM state WHERE t_name=:s_tabel  " );
$bncount->bindParam(":s_tabel", $s_tabel);
$bncount->execute();
$abbn=$bncount->fetch(PDO::FETCH_ASSOC);
$contbn= $abbn['nbr']; echo $contbn;   }
function nbr_state_row($s_tabel) {    global  $db_con ; global  $uRow ;  $usrow=$uRow['id'];
$bncount = $db_con->prepare("SELECT  COUNT(id) as nbr FROM $s_tabel WHERE uid=:usrow ");
$bncount->bindParam(":usrow", $usrow);
$bncount->execute();
$abbn=$bncount->fetch(PDO::FETCH_ASSOC);
$contbn= $abbn['nbr']; echo $contbn;   }
function vu_state_row($s_tabel,$tabl) {    global  $db_con ; global  $uRow ;
$usrow=$uRow['id'];
$contbn = 0;
$bnsum = $db_con->prepare("SELECT  * FROM $s_tabel WHERE uid=:usrow ");
$bnsum->bindParam(":usrow", $usrow);
$bnsum->execute();
while($subn=$bnsum->fetch(PDO::FETCH_ASSOC))
{ $contbn+= $subn["{$tabl}"]; } if(isset($contbn)){ echo $contbn; }else{ echo "0"; }    }
function nbr_posts($usr) {    global  $db_con ;
$bncount = $db_con->prepare("SELECT  COUNT(id) as nbr FROM status WHERE uid=:usrow  " );
$bncount->bindParam(":usrow", $usr);
$bncount->execute();
$abbn=$bncount->fetch(PDO::FETCH_ASSOC);
$contbn= $abbn['nbr']; echo $contbn;   }
function nbr_follow($usr,$follow) {    global  $db_con ;
$bnfollow = $db_con->prepare("SELECT  COUNT(id) as nbr FROM `like` WHERE {$follow}=:usrow  AND type=1 " );
$bnfollow->bindParam(":usrow", $usr);
$bnfollow->execute();
$abfollow=$bnfollow->fetch(PDO::FETCH_ASSOC);
$contfollow= $abfollow['nbr']; echo $contfollow;   }
function act_extensions($o_type) {    global  $db_con ;
$bnextensions = $db_con->prepare("SELECT  * FROM `options` WHERE o_type=:o_type ORDER BY `o_order` DESC " );
$bnextensions->bindParam(":o_type", $o_type);
$bnextensions->execute();
while($abextensions=$bnextensions->fetch(PDO::FETCH_ASSOC)){  echo $abextensions['o_valuer'];   }  }
function if_gstore($name)     {    global  $_SESSION    ; if(isset($_SESSION["{$name}"])){ echo $_SESSION["{$name}"];  unset($_SESSION["{$name}"]);  }    }
function msg_Signup()         {    global  $msg_alertr ; if($msg_alertr){ echo $msg_alertr; session_destroy(); unset($_SESSION['msgr']); }    }
function msg_Login()          {    global  $msg_alertl ; if($msg_alertl){ echo $msg_alertl; session_destroy(); unset($_SESSION['msgl']); }    }
function header_template()    {    global  $template ;   global  $c_lang;   global  $s_st;  $t = "templates/$template";  include "$t/header.php";    }
function footer_template()    {    global  $template ;   global  $s_st;  $t = "templates/$template";  include "$t/footer.php";    }
function template($name)      {    global  $template ;   global  $s_st;  $t = "templates/$template";  include "$t/$name.php";     }
function template_mine($name) {    global  $title_s  ;   global  $title_page ;  global  $db_con;  global  $_GET;   global $c_lang ; global $url_site; global $uRow ; global  $template ;  global $slctRow; global $s_st; global $f_awesome; global $hachadmin; global  $usrRow; global $username_topic; global $lang; global $_SESSION; $t = "templates/_panel";  include "$t/$name.php";    }
function grid_mine($a,$b)     {    global  $template ;   global  $url_site;     global $uRow ;  $t = "?ref=".$uRow['id']; $code= "<!-- ADStn code begin --><a href=\"{$url_site}/{$t}\"><img src=\"{$url_site}/bnr/{$a}x{$b}.gif\" width=\"{$a}\" height=\"{$b}\" ></a><!-- ADStn code begin -->"; echo htmlspecialchars($code);    }
function grid_bnr($a,$b)      {    global  $template ;   global  $url_site;     global $uRow ;  $t = "?ref=".$uRow['id']; $code= "<a href=\"{$url_site}/{$t}\"><img src=\"{$url_site}/bnr/{$a}x{$b}.gif\" width=\"{$a}\" height=\"{$b}\" ></a>"; echo $code;    }
function bnr_mine($a,$b)      {    global  $template ;   global  $url_site;     global $uRow ;  $t = $uRow['id']; $code= "<!-- ADStn code begin --><script  language=\"javascript\" src=\"{$url_site}/bn.php?ID={$t}&px={$a}\"></script><!-- ADStn code begin -->"; echo htmlspecialchars($code);    }
function cc_bnr($a,$b)        {    global  $template ;   global  $url_site;     global $uRow ;  $t = 1; $code= "<!-- ADStn code begin --><script  language=\"javascript\" src=\"{$url_site}/bn.php?ID={$t}&px={$a}\"></script><!-- ADStn code begin -->"; echo $code;    }
function lnk_mine($a,$b)      {    global  $template ;   global  $url_site;     global $uRow ;  if($a=="468"){ $p=1; } if($a=="510"){ $p=2; } $t = $uRow['id']; $code= "<!-- ADStn code begin --><script  language=\"javascript\" src=\"{$url_site}/link.php?ID={$t}&px={$p}\"></script><!-- ADStn code begin -->"; echo htmlspecialchars($code);    }
function cc_lnk($a,$b)        {    global  $template ;   global  $url_site;     global $uRow ;  if($a=="468"){ $p=1; } if($a=="510"){ $p=2; } $t = 1; $code= "<script  language=\"javascript\" src=\"{$url_site}/link.php?ID={$t}&px={$p}\"></script>"; echo $code;    }

}
?>
