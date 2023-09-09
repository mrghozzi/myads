<?php

#####################################################################
##                                                                 ##
##                         MYads  v3.x.x                           ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2022                        ##
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
    if(isset($_GET['submit']) OR isset($_POST['submit'])){

           $bn_time = time();
           if(isset($_GET['tid']))   { $bn_id = $_GET['tid'];   }
           if(isset($_POST['name']))  { $bn_name = $_POST['name'];   }
           if(isset($_POST['txt']))   { $bn_txt  = $_POST['txt'];    }
           if(isset($_GET['s_type'])){ $bn_type = $_GET['s_type']; }else if(isset($_POST['s_type'])){ $bn_type = $_POST['s_type']; }

if(isset($_SESSION['user']) OR (isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin))){
             if(isset($bn_type) AND ($bn_type==1)){
 $s_type ="directory";
}else if(isset($bn_type) AND ($bn_type==2)){
 $s_type ="forum";
 $bn_cat  = $_POST['categ'];
}else if(isset($bn_type) AND ($bn_type==3)){
 $s_type ="news";
}else if(isset($bn_type) AND ($bn_type==4)){
 $s_type ="forum";
 $bn_cat  = "0";
}else if(isset($bn_type) AND ($bn_type==7867)){
 $s_type ="forum";
 $bn_cat  = "0";
}else if(isset($bn_type) AND ($bn_type==100)){
 $s_type ="forum";
 $bn_cat  = "0";
}
$catusz = $db_con->prepare("SELECT *  FROM `{$s_type}` WHERE id=".$bn_id );
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);
     $bn_uid = $sucat['uid'];

           }
      if($bn_type=="1"){
            $stmsb = $db_con->prepare("UPDATE directory SET txt=:txt
            WHERE id=:id AND uid=:uid");
            $stmsb->bindParam(":uid",   $bn_uid);
            $stmsb->bindParam(":txt",   $bn_txt);
            $stmsb->bindParam(":id",    $bn_id);
            if($stmsb->execute()){

         	}
      }else if(($bn_type=="2") OR ($bn_type=="7867")){

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

      }else if(($bn_type=="100") OR ($bn_type=="4")){
        if($bn_type=="100"){      $bn_name   = "post";      }
        else if($bn_type=="4"){   $bn_name   = "image";     }
       $stmsb = $db_con->prepare("UPDATE forum SET name=:name,txt=:txt,cat=:cat
            WHERE id=:id AND uid=:uid");
            $stmsb->bindParam(":uid",   $bn_uid);
            $stmsb->bindParam(":name",  $bn_name);
            $stmsb->bindParam(":txt",   $bn_txt);
            $stmsb->bindParam(":cat",   $bn_cat);
            $stmsb->bindParam(":id",    $bn_id);
            if($stmsb->execute()){
            echo $_POST['txt']."<div id='report{$bn_id}' ></div>";
         	}

      }

   }
 }else{ echo"404"; }
?>