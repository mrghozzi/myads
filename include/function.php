<?php

#####################################################################
##                                                                 ##
##                        MYads  v3.0.5                            ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2023                        ##
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

 $template   = $stt['styles'];
 $title_s    = $stt['titer'];
 $descr_site = $stt['description'];
 $templates  = "templates/".$template;
 $url_site   = $stt['url'];
 $mail_site  = $stt['a_mail'];
 $lang_site  = $stt['lang'];
 $elnk_site  = $stt['e_links'];

 //  MyAds Version
 include "include/myads_version.php";
 function myads_version()         {    global  $versionRow ; if(isset($versionRow['o_valuer'])){ echo $versionRow['o_valuer']; }    }
 function myads_fversion()        {    global  $versionRow ; if(isset($versionRow['name'])){ echo $versionRow['name'];         }    }


// ads
$sads = $db_con->prepare("SELECT *  FROM ads WHERE id=3" );
$sads->execute();
$stads=$sads->fetch(PDO::FETCH_ASSOC);
$bads3 = $stads['code_ads'];

 if( ! ini_get('date.timezone') )
{
    date_default_timezone_set($stt['timezone']);
}

include "include/user_session.php";  //  user Session

//  Mode (Light/Dark)
 if (isset($_GET["light"])) {
     $lg_md5 = time() + (365 * 24 * 60 * 60);
     setcookie("modedark", "css", $lg_md5);
     header('Location: ' . $_SERVER['HTTP_REFERER']);
    } else if (isset($_GET["dark"])){
      $lg_md5 = time() + (365 * 24 * 60 * 60);
     setcookie("modedark", "css_d", $lg_md5);
     header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
    if (isset($_COOKIE['modedark'])) {
      $c_mode=$_COOKIE['modedark'];
       } else {  $c_mode="css";  }
       
//  Language
 if (isset($_GET["en"])) {
     $lg_md5 = time() + (365 * 24 * 60 * 60);
     setcookie("lang", "en", $lg_md5);
     header('Location: ' . $_SERVER['HTTP_REFERER']);
    } else if (isset($_GET["ar"])){
      $lg_md5 = time() + (365 * 24 * 60 * 60);
     setcookie("lang", "ar", $lg_md5);
     header('Location: ' . $_SERVER['HTTP_REFERER']);
    } else if (isset($_GET["fr"])){
      $lg_md5 = time() + (365 * 24 * 60 * 60);
     setcookie("lang", "fr", $lg_md5);
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
        if($name=="span"){ if($msg_n==0){}else{ echo "<span class='badge badge-danger'>{$msg_n}</span>"; } }
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

echo "               <!-- DROPDOWN BOX LIST ITEM -->
              <a class=\"dropdown-box-list-item \" href=\"{$url_site}/message/{$msgssen['us_env']}\">
                <!-- USER STATUS -->
                <div class=\"user-status\">
                  <!-- USER STATUS AVATAR -->
                  <div class=\"user-status-avatar\">
                    <!-- USER AVATAR -->
                    <div class=\"user-avatar small no-outline\">
                      <!-- USER AVATAR CONTENT -->
                      <div class=\"user-avatar-content\">
                        <!-- HEXAGON -->
                        <div class=\"hexagon-image-30-32\" data-src=\"{$url_site}/{$catussen['img']}\"></div>
                        <!-- /HEXAGON -->
                      </div>
                      <!-- /USER AVATAR CONTENT -->
                      <!-- USER AVATAR PROGRESS BORDER -->
                      <div class=\"user-avatar-progress-border\">
                        <!-- HEXAGON -->
                        <div class=\"hexagon-border-40-44\"></div>
                        <!-- /HEXAGON -->
                      </div>
                      <!-- /USER AVATAR PROGRESS BORDER -->

                    </div>
                    <!-- /USER AVATAR -->
                  </div>
                  <!-- /USER STATUS AVATAR -->

                  <!-- USER STATUS TITLE -->
                  <p class=\"user-status-title\"><span class=\"bold\">{$catussen['username']}</span></p>
                  <!-- /USER STATUS TITLE -->

                 <!-- USER STATUS TIMESTAMP -->
                  <p class=\"user-status-timestamp floaty\">{$time_cmt}</p>
                  <!-- /USER STATUS TIMESTAMP -->
                </div>
                <!-- /USER STATUS -->
              </a>
              <!-- /DROPDOWN BOX LIST ITEM -->";
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
        if($name=="span"){ if($net_n==0){}else{ echo "<span class='badge badge-danger'>{$net_n}</span>"; } }
        if($name=="list"){ $msgusen = $db_con->prepare("SELECT *  FROM notif
        WHERE time<'{$etime}' AND uid=:msgusid ORDER BY `time` DESC LIMIT 10");
        $msgusen->bindParam(":msgusid", $msgusid);
        $msgusen->execute();
 while($msgssen=$msgusen->fetch(PDO::FETCH_ASSOC)){
  $time_cmt=convertTime($msgssen['time']);

echo "
              <!-- DROPDOWN BOX LIST ITEM -->
              <div class=\"dropdown-box-list-item unread\">
                <!-- USER STATUS -->
                <div class=\"user-status notification\">
                  <!-- USER STATUS TITLE -->
                  <p class=\"user-status-title\"><a href=\"{$url_site}/notif/{$msgssen['id']}\" >{$msgssen['name']}</a></p>
                  <!-- /USER STATUS TITLE -->

                  <!-- USER STATUS TIMESTAMP -->
                  <p class=\"user-status-timestamp\">{$time_cmt}</p>
                  <!-- /USER STATUS TIMESTAMP -->

                  <!-- USER STATUS ICON -->
                  <div class=\"user-status-icon\">
                    <!-- ICON COMMENT -->
                    <svg class=\"icon-{$msgssen['logo']}\">
                      <use xlink:href=\"#svg-{$msgssen['logo']}\"></use>
                    </svg>
                    <!-- /ICON COMMENT -->
                  </div>
                  <!-- /USER STATUS ICON -->
                </div>
                <!-- /USER STATUS -->
              </div>
              <!-- /DROPDOWN BOX LIST ITEM -->
          ";
}
 }
        }

// online all
function online_admin()
{
  global  $db_con ;
$bn_online = time()-240;
$bncount = $db_con->prepare("SELECT  COUNT(id) as nbr FROM users WHERE online > :online ");
$bncount->bindParam(":online", $bn_online);
$bncount->execute();
$abbn=$bncount->fetch(PDO::FETCH_ASSOC);
$contbn= $abbn['nbr']; echo $contbn;
}

// online user
function online_us($id,$yesno = false)
{
  global  $db_con ;
 $bn_online = time()-240;

$bncount = $db_con->prepare("SELECT  * FROM users WHERE id = :id ");
$bncount->bindParam(":id", $id);
$bncount->execute();
$abbn=$bncount->fetch(PDO::FETCH_ASSOC);
if(isset($yesno) AND ($yesno==1)){
if($abbn['online']>$bn_online){
  echo "online";
}else{
  echo "offline";
}
}else{
if($abbn['online']>$bn_online){
  echo "online";
}
}
}

//  check_user
function check_us($id,$yesno = false)
{
  global  $db_con ;
$bncount = $db_con->prepare("SELECT  * FROM users WHERE id = :id ");
$bncount->bindParam(":id", $id);
$bncount->execute();
$abbn=$bncount->fetch(PDO::FETCH_ASSOC);
if(isset($yesno) AND ($yesno==1)){
 return  $abbn['ucheck'];
}else if($abbn['ucheck']=="1"){
  echo "<i class=\"fa fa-fw fa-check-circle\" style=\"color: #0066CC;\" ></i>";
}
}

// GET user
function get_user($id,$name)
{
  global  $db_con ;
 $bn_online = time()-60;

$bncount = $db_con->prepare("SELECT  * FROM users WHERE id = :id ");
$bncount->bindParam(":id", $id);
$bncount->execute();
$abbn=$bncount->fetch(PDO::FETCH_ASSOC);
  echo $abbn["{$name}"];
}

// Referral
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

function nev_menu() {   global $user_conect ; global $uRow ;  global $url_site; global $db_con;
$stmut = $db_con->prepare("SELECT *  FROM menu   " );   $stmut->execute();
while($menu_tt=$stmut->fetch(PDO::FETCH_ASSOC)){
  $m_name=$menu_tt['name']; $m_dir=$menu_tt['dir']; echo "<li class='menu-item'><a class='menu-item-link text-tooltip-tfr' href='{$m_dir}' >{$m_name}</a></li>"; }
  }
function sid_menu() {   global $user_conect ; global $uRow ;  global $url_site; global $db_con;
$stmut = $db_con->prepare("SELECT *  FROM menu   " );   $stmut->execute();
while($menu_tt=$stmut->fetch(PDO::FETCH_ASSOC)){
  $m_name=$menu_tt['name']; $m_dir=$menu_tt['dir']; echo "<li class='menu-main-item'><a class='menu-main-item-link' href='{$m_dir}' >{$m_name}</a></li>"; }
  }
function mob_menu() {   global $user_conect ; global $uRow ;  global $url_site; global $db_con;
$stmut = $db_con->prepare("SELECT *  FROM menu   " );   $stmut->execute();
while($menu_tt=$stmut->fetch(PDO::FETCH_ASSOC)){
  $m_name=$menu_tt['name']; $m_dir=$menu_tt['dir']; echo "<a class='navigation-widget-section-link' href='{$m_dir}' >{$m_name}</a>"; }
  }
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
function nbr_state($s_tabel,$ret = false) {    global  $db_con ;
$bncount = $db_con->prepare("SELECT  COUNT(id) as nbr FROM $s_tabel   " );
$bncount->execute();
$abbn=$bncount->fetch(PDO::FETCH_ASSOC);
$contbn= $abbn['nbr'];  if(isset($ret) AND ($ret==1)){  return  $contbn;  }else{ echo $contbn;   }   }
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
function last_state($s_tabel,$tabl,$ret = false) {    global  $db_con ;

$bnsum = $db_con->prepare("SELECT  * FROM $s_tabel ORDER BY `id` DESC");
$bnsum->execute();
$subn=$bnsum->fetch(PDO::FETCH_ASSOC);
if(isset($ret) AND ($ret==1)){
 return  $subn["{$tabl}"];
}else{
echo $subn["{$tabl}"];   }  }
function nbr_posts($usr) {    global  $db_con ;
$bncount = $db_con->prepare("SELECT  COUNT(id) as nbr FROM status WHERE uid=:usrow  " );
$bncount->bindParam(":usrow", $usr);
$bncount->execute();
$abbn=$bncount->fetch(PDO::FETCH_ASSOC);
$contbn= $abbn['nbr']; echo $contbn;   }
function nbr_follow($usr,$follow,$ret = false) {    global  $db_con ;
$bnfollow = $db_con->prepare("SELECT  COUNT(id) as nbr FROM `like` WHERE {$follow}=:usrow  AND type=1 " );
$bnfollow->bindParam(":usrow", $usr);
$bnfollow->execute();
$abfollow=$bnfollow->fetch(PDO::FETCH_ASSOC);
$contfollow= $abfollow['nbr']; if(isset($ret) AND ($ret==1)){  return  $contfollow;  }else{ echo $contfollow;   } }
function nbr_follows($ret = false) {    global  $db_con ;
$bnfollow = $db_con->prepare("SELECT  COUNT(id) as nbr FROM `like` WHERE type=1 " );
$bnfollow->execute();
$abfollow=$bnfollow->fetch(PDO::FETCH_ASSOC);
$contfollow= $abfollow['nbr']; if(isset($ret) AND ($ret==1)){  return  $contfollow;  }else{ echo $contfollow;   } }
function act_extensions($o_type) {    global  $db_con ;    // extensions
$bnextensions = $db_con->prepare("SELECT  * FROM `options` WHERE o_type=:o_type ORDER BY `o_order` DESC " );
$bnextensions->bindParam(":o_type", $o_type);
$bnextensions->execute();
while($abextensions=$bnextensions->fetch(PDO::FETCH_ASSOC)){  echo $abextensions['o_valuer'];   }  }
function widgets($bn_plas) {    global  $db_con ;   global $s_st;      // widgets
$o_type = "box_widget";
$bnwidgets = $db_con->prepare("SELECT  * FROM `options` WHERE o_parent=:o_parent AND o_type=:o_type ORDER BY `o_order` DESC " );
$bnwidgets->bindParam(":o_parent", $bn_plas);
$bnwidgets->bindParam(":o_type", $o_type);
$bnwidgets->execute();
while($abwidgets=$bnwidgets->fetch(PDO::FETCH_ASSOC)){ $name =$abwidgets['o_mode'] ; $t = "templates/_panel/widgets";   include "$t/$name.php";   }  }
function convert_links($text) {
  $pattern = '/ (http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/';
  preg_match_all($pattern, $text, $matches);
  $links = $matches[0];
  $num_links = count($links);

  if ($num_links == 1 && preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $links[0])) {
      $text = preg_replace($pattern, '<br><img src="$0" alt="Image" style="max-width: 100%; max-height: 500px; height: auto;"><br>', $text);
  } else {
      $text = preg_replace($pattern, '<a href="$0">$0</a>', $text);
  }
  return $text;
}
function if_gstore($name)     {    global  $_SESSION    ; if(isset($_SESSION["{$name}"])){ echo $_SESSION["{$name}"];  unset($_SESSION["{$name}"]);  }    }
function msg_Signup()         {    global  $msg_alertr ; if($msg_alertr){ echo $msg_alertr; session_destroy(); unset($_SESSION['msgr']); }    }
function msg_Login()          {    global  $msg_alertl ; if($msg_alertl){ echo $msg_alertl; session_destroy(); unset($_SESSION['msgl']); }    }
function header_template()    {    global  $template ;   global  $c_lang;   global  $s_st;  $t = "templates/$template";  include "$t/header.php";    }
function footer_template()    {    global  $template ;   global  $s_st;  $t = "templates/$template";  include "$t/footer.php";    }
function template($name)      {    global  $template ;   global  $s_st;  $t = "templates/$template";  include "$t/$name.php";     }
function template_mine($name) {    global  $title_s  ;   global  $title_page ;  global $description_page;  global $image_page;  global  $db_con;  global  $_GET;   global $c_lang ; global $c_mode; global $url_site; global $uRow ; global  $template ;  global $slctRow; global $statuRow; global $s_st; global $f_awesome; global $hachadmin; global  $usrRow; global $username_topic; global $lang; global $_SESSION; global $versionRow; global $elnk_site; global $us_cover; $t = "templates/_panel";  include "$t/$name.php";    }
function grid_mine($a,$b)     {    global  $template ;   global  $url_site;     global $uRow ;  $t = "?ref=".$uRow['id']; $code= "<!-- ADStn code begin --><a href=\"{$url_site}/{$t}\"><img src=\"{$url_site}/bnr/{$a}x{$b}.gif\" width=\"{$a}\" height=\"{$b}\" ></a><!-- ADStn code begin -->"; echo htmlspecialchars($code);    }
function grid_bnr($a,$b)      {    global  $template ;   global  $url_site;     global $uRow ;  $t = "?ref=".$uRow['id']; $code= "<a href=\"{$url_site}/{$t}\"><img src=\"{$url_site}/bnr/{$a}x{$b}.gif\" width=\"{$a}\" height=\"{$b}\" ></a>"; echo $code;    }
function bnr_mine($a,$b)      {    global  $template ;   global  $url_site;     global $uRow ;  $t = $uRow['id']; $code= "<!-- ADStn code begin --><script  language=\"javascript\" src=\"{$url_site}/bn.php?ID={$t}&px={$a}\"></script><!-- ADStn code begin -->"; echo htmlspecialchars($code);    }
function cc_bnr($a,$b)        {    global  $template ;   global  $url_site;     global $uRow ;  $t = 1; $code= "<!-- ADStn code begin --><script  language=\"javascript\" src=\"{$url_site}/bn.php?ID={$t}&px={$a}\"></script><!-- ADStn code begin -->"; echo $code;    }
function lnk_mine($a,$b)      {    global  $template ;   global  $url_site;     global $uRow ;  if($a=="468"){ $p=1; } if($a=="510"){ $p=2; } $t = $uRow['id']; $code= "<!-- ADStn code begin --><script  language=\"javascript\" src=\"{$url_site}/link.php?ID={$t}&px={$p}\"></script><!-- ADStn code begin -->"; echo htmlspecialchars($code);    }
function cc_lnk($a,$b)        {    global  $template ;   global  $url_site;     global $uRow ;  if($a=="468"){ $p=1; } if($a=="510"){ $p=2; } $t = 1; $code= "<script  language=\"javascript\" src=\"{$url_site}/link.php?ID={$t}&px={$p}\"></script>"; echo $code;    }

}
?>