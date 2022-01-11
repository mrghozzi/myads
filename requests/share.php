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
 if( ! ini_get('date.timezone') )
{
    date_default_timezone_set('GMT');
}
if($vrf_License=="65fgh4t8x5fe58v1rt8se9x"){
    if(isset($_POST['submit'])){

           $bn_time = time();
           $bn_type = $_POST['s_type'];
           $bn_tid = $_POST['tid'];
           session_start();
           $bn_uid = $_SESSION['user'];

            $stmsb = $db_con->prepare("INSERT INTO status (uid,date,s_type,tp_id)
            VALUES(:uid,:a_da,:opm,:ptdk)");
			$stmsb->bindParam(":uid", $bn_uid);
            $stmsb->bindParam(":opm", $bn_type);
            $stmsb->bindParam(":a_da", $bn_time);
            $stmsb->bindParam(":ptdk", $bn_tid);
            if($stmsb->execute()){
               if($_POST['s_type']==1){
 $s_type  = "directory";
 $bn_nurl = "u/".$bn_uid;
}else if($_POST['s_type']==2){
 $s_type ="forum";
 $bn_nurl = "t".$bn_tid;
}else if($_POST['s_type']==4){
 $s_type ="forum";
 $bn_nurl = "t".$bn_tid;
}
$catusz = $db_con->prepare("SELECT *  FROM `{$s_type}` WHERE statu=1 AND  id=".$bn_tid );
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);
              if($sucat['uid']!=$bn_uid){
            $bn_logo  = "share-icon.png";
            $bn_state = "1";
            $usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=".$bn_uid );
            $usz->execute();
            $sus=$usz->fetch(PDO::FETCH_ASSOC);
            $bn_name  = $sus['username']." has shared a publication";
            $stmntf = $db_con->prepare("INSERT INTO notif (uid,name,nurl,logo,time,state)
            VALUES(:uid,:name,:nurl,:logo,:time,:state)");
			$stmntf->bindParam(":uid", $sucat['uid']);
            $stmntf->bindParam(":name", $bn_name);
            $stmntf->bindParam(":nurl", $bn_nurl);
            $stmntf->bindParam(":logo", $bn_logo);
            $stmntf->bindParam(":time", $bn_time);
            $stmntf->bindParam(":state", $bn_state);
            if($stmntf->execute()){

         	}
            }
         	}




    }
 }else{ echo"404"; }
?>