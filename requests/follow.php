<?php

#####################################################################
##                                                                 ##
##                        My ads v2.4.x                            ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2021                        ##
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

    if($_GET['follow']){

           $bn_time = time();
           $bn_cmnt = $_GET['follow'];
           $bn_uid = $_COOKIE['user'];
           $bn_typ = 1;

            $stmsb = $db_con->prepare("INSERT INTO `like` (uid,sid,type,time_t)
            VALUES(:uid,:a_da,:opm,:ptdk)");
			$stmsb->bindParam(":uid", $bn_uid);
            $stmsb->bindParam(":opm", $bn_typ);
            $stmsb->bindParam(":ptdk", $bn_time);
            $stmsb->bindParam(":a_da", $bn_cmnt);
            if($stmsb->execute()){

            $bn_nurl = "followers/".$bn_cmnt;
            $bn_logo  = "add_follow.png";
            $bn_state = "1";
            $usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=".$bn_uid );
            $usz->execute();
            $sus=$usz->fetch(PDO::FETCH_ASSOC);
            $bn_name  = $sus['username']." has followed you up";
            $stmntf = $db_con->prepare("INSERT INTO notif (uid,name,nurl,logo,time,state)
            VALUES(:uid,:name,:nurl,:logo,:time,:state)");
			$stmntf->bindParam(":uid", $bn_cmnt);
            $stmntf->bindParam(":name", $bn_name);
            $stmntf->bindParam(":nurl", $bn_nurl);
            $stmntf->bindParam(":logo", $bn_logo);
            $stmntf->bindParam(":time", $bn_time);
            $stmntf->bindParam(":state", $bn_state);
            if($stmntf->execute()){
            header("Location: ../{$bn_nurl}");
         	}

         	}




    }else if($_GET['unfollow']){

           $bn_cmnt = $_GET['unfollow'];
           $bn_uid = $_COOKIE['user'];
           $bn_nurl = "u/".$bn_cmnt;

            $uszunf = $db_con->prepare("SELECT *  FROM `like` WHERE uid=".$bn_uid." AND sid=".$bn_cmnt  );
            $uszunf->execute();
            $susunf=$uszunf->fetch(PDO::FETCH_ASSOC);

            $stmt=$db_con->prepare("DELETE FROM `like` WHERE id=:id AND uid=:uid ");
        	$stmt->execute(array(':id'=>$susunf['id'],':uid'=>$bn_uid));

            $stmtnft=$db_con->prepare("DELETE FROM `notif` WHERE uid=:id AND time=:time AND state=1 ");
        	$stmtnft->execute(array(':id'=>$susunf['sid'],':time'=>$susunf['time_t']));

            header("Location: ../{$bn_nurl}");

     }
 }else{ echo"404"; }
?>