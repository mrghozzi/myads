<?PHP

#####################################################################
##                                                                 ##
##                        My ads v1.2.x                            ##
##                 http://www.kariya-host.com                      ##
##                 e-mail: admin@kariya-host.com                   ##
##                                                                 ##
##                       copyright (c) 2018                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################

include "../dbconfig.php";
session_start();
header('Content-type: text/html; charset=utf-8');
 if( ! ini_get('date.timezone') )
{
    date_default_timezone_set('GMT');
}
 $id_admin = "1";
 $stadmin_select = $db_con->prepare('SELECT * FROM users WHERE id=:id ');
 $stadmin_select->bindParam(":id", $id_admin);
 $stadmin_select->execute();
 $usadmin=$stadmin_select->fetch(PDO::FETCH_ASSOC);
 $hachadmin= md5($usadmin['pass'].$usadmin['username']) ;
if($vrf_License=="65fgh4t8x5fe58v1rt8se9x"){
    if(isset($_POST['submit'])){

           $bn_time = time();
           $bn_id = $_POST['tid'];
           $bn_name = $_POST['name'];
           $bn_txt  = $_POST['txt'];
           $bn_type = $_POST['s_type'];
           if(isset($_SESSION['user']) OR (isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin))){
             if(isset($bn_type['s_type']) AND ($bn_type['s_type']==1)){
 $s_type ="directory";
 $bn_url  = $_POST['url'];
 $bn_tag  = $_POST['tag'];
 $bn_cat  = $_POST['categ'];
}else if(isset($_POST['s_type']) AND ($_POST['s_type']==2)){
 $s_type ="forum";
 $bn_cat  = $_POST['categ'];
}else if(isset($_POST['s_type']) AND ($_POST['s_type']==3)){
 $s_type ="news";
}else if(isset($_POST['s_type']) AND ($_POST['s_type']==4)){
 $s_type ="forum";
 $bn_cat  = "0";
}else if(isset($_POST['s_type']) AND ($_POST['s_type']==7867)){
 $s_type ="forum";
 $bn_cat  = "0";
}
$catusz = $db_con->prepare("SELECT *  FROM `{$s_type}` WHERE id=".$bn_id );
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);
     $bn_uid = $sucat['uid'];

           }
      if($bn_type=="1"){
            $stmsb = $db_con->prepare("UPDATE directory SET name=:name,url=:url,txt=:txt,metakeywords=:tag,cat=:cat
            WHERE id=:id AND uid=:uid");
            $stmsb->bindParam(":uid",   $bn_uid);
            $stmsb->bindParam(":name",  $bn_name);
            $stmsb->bindParam(":url",   $bn_url);
            $stmsb->bindParam(":txt",   $bn_txt);
            $stmsb->bindParam(":tag",   $bn_tag);
            $stmsb->bindParam(":cat",   $bn_cat);
            $stmsb->bindParam(":id",    $bn_id);
            if($stmsb->execute()){
            $dir_lnk_hash = hash('crc32', $bn_url.$bn_id );
            $stmsbsh = $db_con->prepare("UPDATE short SET sho=:lnk_hash,url=:url
            WHERE tp_id=:tp_id AND uid=:uid AND sh_type=:sh_type ");
            $stmsbsh->bindParam(":uid", $bn_uid);
            $stmsbsh->bindParam(":lnk_hash", $dir_lnk_hash);
            $stmsbsh->bindParam(":url", $bn_url);
            $stmsbsh->bindParam(":sh_type", $bn_type);
            $stmsbsh->bindParam(":tp_id", $bn_id);
            if($stmsbsh->execute()){
            }
         	}
      }else if(($bn_type=="2") OR ($bn_type=="4") OR ($bn_type=="7867")){

       $stmsb = $db_con->prepare("UPDATE forum SET name=:name,txt=:txt,cat=:cat
            WHERE id=:id AND uid=:uid");
            $stmsb->bindParam(":uid",   $bn_uid);
            $stmsb->bindParam(":name",  $bn_name);
            $stmsb->bindParam(":txt",   $bn_txt);
            $stmsb->bindParam(":cat",   $bn_cat);
            $stmsb->bindParam(":id",    $bn_id);
            if($stmsb->execute()){
            header("Location: ../t{$bn_id}");
         	}

      }

   }
 }else{ echo"404"; }
?>