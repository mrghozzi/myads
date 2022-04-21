<?PHP

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
 $stmt = $db_con->prepare("SELECT *  FROM setting   " );
 $stmt->execute();
 $ab=$stmt->fetch(PDO::FETCH_ASSOC);
 $lng=$ab['lang'];
 $url_site   = $ab['url'];
header('Content-type: text/html; charset=utf-8');
 if( ! ini_get('date.timezone') )
{
    date_default_timezone_set('GMT');
}
if($vrf_License=="65fgh4t8x5fe58v1rt8se9x"){
    if(isset($_POST['submit_post']) OR isset($_POST['submit'])){

           $bn_time = time();
           if(isset($_POST['name']))  { $bn_name = $_POST['name'];   }
           if(isset($_POST['url']))   { $bn_url  = $_POST['url'];    }
           if(isset($_POST['txt']))   { $bn_txt  = $_POST['txt'];    }
           if(isset($_POST['tag']))   { $bn_tag  = $_POST['tag'];    }
           if(isset($_POST['categ'])) { $bn_cat  = $_POST['categ'];  }
           if(isset($_POST['s_type'])){ $bn_type = $_POST['s_type']; }
           if(isset($_POST['capt']))  { $capt    = $_POST['capt'];   }
           if(isset($_POST['edte']))  { $edte    = $_POST['edte'];   }
           if(isset($_POST['img']))   { $file    = $_POST['img'];   }
           if(isset($_POST['edte']))  { $edte    = strtotime($edte); }

           session_start();
           if(isset($_COOKIE['user'])){
           $bn_uid  = $_SESSION['user'];
           }else{
             session_start();
             if((int)isset($_SESSION['CAPCHA'])){
             $capt_sess=$_SESSION['CAPCHA'];
             }else{
              $capt_sess="no";
             }


		if($capt==$capt_sess) {
            $bn_uid = "0";
            }else{
              $capt_sess="no";
            }

           }
           $bn_vu   = "0";
           $bn_statu= "1";


       if($bn_type=="1"){
         if (filter_var($bn_url, FILTER_VALIDATE_URL) === FALSE) {
             $VALIDATE_URL = "notvalid";
           }
		   if(isset($capt_sess) AND ($capt_sess=="no")){
             header("Location: {$url_site}/directory?p&errMSG=Wrong verification code");
           }else if(isset($VALIDATE_URL) AND ($VALIDATE_URL == "notvalid")){
            header("Location: {$url_site}/directory?p&errMSG=Url Not Valid");
           }else{
          if(empty($bn_cat)) { $bn_cat  = "";  }
          $stmsb = $db_con->prepare("INSERT INTO directory (uid,name,url,txt,metakeywords,cat,vu,statu)
            VALUES(:uid,:name,:url,:txt,:tag,:cat,:vu,:statu)");
			$stmsb->bindParam(":uid",   $bn_uid);
            $stmsb->bindParam(":name",  $bn_name);
            $stmsb->bindParam(":url",   $bn_url);
            $stmsb->bindParam(":txt",   $bn_txt);
            $stmsb->bindParam(":tag",   $bn_tag);
            $stmsb->bindParam(":cat",   $bn_cat);
            $stmsb->bindParam(":vu",    $bn_vu);
            $stmsb->bindParam(":statu", $bn_statu);
            if($stmsb->execute()){
            $bn_tid = $db_con->lastInsertId();
            $stmsbs = $db_con->prepare("INSERT INTO status (uid,date,s_type,tp_id)
            VALUES(:uid,:a_da,:opm,:ptdk)");
			$stmsbs->bindParam(":uid", $bn_uid);
            $stmsbs->bindParam(":opm", $bn_type);
            $stmsbs->bindParam(":a_da", $bn_time);
            $stmsbs->bindParam(":ptdk", $bn_tid);
            if($stmsbs->execute()){
             $dir_lnk_hash = hash('crc32', $bn_url.$bn_tid );
             $stmsbsh = $db_con->prepare("INSERT INTO short (uid,sho,url,clik,sh_type,tp_id)
            VALUES(:uid,:lnk_hash,:url,:clik,:sh_type,:tp_id)");
			$stmsbsh->bindParam(":uid", $bn_uid);
            $stmsbsh->bindParam(":lnk_hash", $dir_lnk_hash);
            $stmsbsh->bindParam(":url", $bn_url);
            $stmsbsh->bindParam(":clik", $bn_vu);
            $stmsbsh->bindParam(":sh_type", $bn_type);
            $stmsbsh->bindParam(":tp_id", $bn_tid);
            if($stmsbsh->execute()){

            header("Location: {$url_site}/dr{$bn_tid}");
            }
         	}
         	}
			}
       }else if($bn_type=="2"){
         if(empty($bn_cat)) { $bn_cat  = "";  }
          $stmsb = $db_con->prepare("INSERT INTO forum (uid,name,txt,cat,statu)
            VALUES(:uid,:name,:txt,:cat,:statu)");
			$stmsb->bindParam(":uid",   $bn_uid);
            $stmsb->bindParam(":name",  $bn_name);
            $stmsb->bindParam(":txt",   $bn_txt );
            $stmsb->bindParam(":cat",   $bn_cat);
            $stmsb->bindParam(":statu", $bn_statu);
            if($stmsb->execute()){
            if($edte > 0) {    $bn_time = $edte ;  }
            $bn_tid = $db_con->lastInsertId();
            $stmsbs = $db_con->prepare("INSERT INTO status (uid,date,s_type,tp_id)
            VALUES(:uid,:a_da,:opm,:ptdk)");
			$stmsbs->bindParam(":uid", $bn_uid);
            $stmsbs->bindParam(":opm", $bn_type);
            $stmsbs->bindParam(":a_da", $bn_time);
            $stmsbs->bindParam(":ptdk", $bn_tid);
            if($stmsbs->execute()){
            header("Location: {$url_site}/t{$bn_tid}");
         	}
         	}

}else if($bn_type=="4"){
            $bn_name = "image";
            $bn_cat = "0";
            $stmsb = $db_con->prepare("INSERT INTO forum (uid,name,txt,cat,statu)
            VALUES(:uid,:name,:txt,:cat,:statu)");
			$stmsb->bindParam(":uid",   $bn_uid);
            $stmsb->bindParam(":name",  $bn_name);
            $stmsb->bindParam(":txt",   $bn_txt );
            $stmsb->bindParam(":cat",   $bn_cat);
            $stmsb->bindParam(":statu", $bn_statu);
   if($stmsb->execute()){
            $bn_tid = $db_con->lastInsertId();
            $stmsbs = $db_con->prepare("INSERT INTO status (uid,date,s_type,tp_id)
            VALUES(:uid,:a_da,:opm,:ptdk)");
			$stmsbs->bindParam(":uid", $bn_uid);
            $stmsbs->bindParam(":opm", $bn_type);
            $stmsbs->bindParam(":a_da", $bn_time);
            $stmsbs->bindParam(":ptdk", $bn_tid);
    if($stmsbs->execute()){
            $o_type  = "image_post";
            $o_mode = "file";

            $ostmsbs = $db_con->prepare(" INSERT INTO options  (name,o_valuer,o_type,o_parent,o_order,o_mode)
            VALUES (:pts,:a_daf,:otpm,:dptdk,:uid,:o_mode) ");
	        $ostmsbs->bindParam(":uid", $bn_uid);
            $ostmsbs->bindParam(":otpm", $o_type);
            $ostmsbs->bindParam(":a_daf", $file);
            $ostmsbs->bindParam(":dptdk", $bn_tid);
            $ostmsbs->bindParam(":pts", $bn_time);
             $ostmsbs->bindParam(":o_mode", $o_mode);
            if($ostmsbs->execute()){
             header("Location: {$url_site}/t{$bn_tid}");
         	}
    }
  }

}else if($bn_type=="100"){
          $bn_cat  = "0";
          $bn_name = "post";
          $stmsb = $db_con->prepare("INSERT INTO forum (uid,name,txt,cat,statu)
            VALUES(:uid,:name,:txt,:cat,:statu)");
			$stmsb->bindParam(":uid",   $bn_uid);
            $stmsb->bindParam(":name",  $bn_name);
            $stmsb->bindParam(":txt",   $bn_txt );
            $stmsb->bindParam(":cat",   $bn_cat);
            $stmsb->bindParam(":statu", $bn_statu);
            if($stmsb->execute()){
            if($edte > 0) {    $bn_time = $edte ;  }
            $bn_tid = $db_con->lastInsertId();
            $stmsbs = $db_con->prepare("INSERT INTO status (uid,date,s_type,tp_id)
            VALUES(:uid,:a_da,:opm,:ptdk)");
			$stmsbs->bindParam(":uid", $bn_uid);
            $stmsbs->bindParam(":opm", $bn_type);
            $stmsbs->bindParam(":a_da", $bn_time);
            $stmsbs->bindParam(":ptdk", $bn_tid);
            if($stmsbs->execute()){
            header("Location: {$url_site}/t{$bn_tid}");
         	}
         	}

       }




        

    }
 }else{ echo"404"; }
?>