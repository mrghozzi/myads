<?PHP

#####################################################################
##                                                                 ##
##                         MYads  v3.x.x                           ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2023                        ##
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

    if($_GET['id']){

           $bn_time = time();
           $bn_id  = $_GET['id'];
           $f_like  = $_GET['f_like'];
           $tt_like = $_POST['test_like'];
           $bn_uid = $_COOKIE['user'];
           $bn_typ = 33;
             if($f_like=="like_up")   {
            $stmsb = $db_con->prepare("INSERT INTO `like` (uid,sid,type,time_t)
            VALUES(:uid,:a_da,:opm,:ptdk)");
			$stmsb->bindParam(":uid", $bn_uid);
            $stmsb->bindParam(":opm", $bn_typ);
            $stmsb->bindParam(":ptdk", $bn_time);
            $stmsb->bindParam(":a_da", $bn_id);
            if($stmsb->execute()){

            
            $bn_logo  = "Weheartit-icon.png";
            $bn_state = "1";
            $usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=".$bn_uid );
            $usz->execute();
            $sus=$usz->fetch(PDO::FETCH_ASSOC);
            $catusz = $db_con->prepare("SELECT *  FROM `f_coment` WHERE   id=".$bn_id );
            $catusz->execute();
            $sucat=$catusz->fetch(PDO::FETCH_ASSOC);
            $bn_sid = $sucat['uid'];
            if($bn_sid==$bn_uid){ }else{
            $bn_name  = $sus['username']." likes your comment.";
            $bn_nurl = "t".$sucat['tid'];
            $stmntf = $db_con->prepare("INSERT INTO notif (uid,name,nurl,logo,time,state)
            VALUES(:uid,:name,:nurl,:logo,:time,:state)");
			$stmntf->bindParam(":uid", $bn_sid);
            $stmntf->bindParam(":name", $bn_name);
            $stmntf->bindParam(":nurl", $bn_nurl);
            $stmntf->bindParam(":logo", $bn_logo);
            $stmntf->bindParam(":time", $bn_time);
            $stmntf->bindParam(":state", $bn_state);
            if($stmntf->execute()){ }
            }
            echo "<a style=\"color: #FF0000;\"  href=\"javascript:void(0);\" id=\"ulike".$bn_id."\" class=\"btn btn-default\"  ><i class=\"fa fa-heart\"  style=\"color: #FF0000;\"  aria-hidden=\"true\"></i></a>
             <input type=\"hidden\" id=\"lval\" value=\"test_like\" />";


         	}
            }else if($f_like=="like_down"){
            $bn_uid = $_COOKIE['user'];

            $uszunf = $db_con->prepare("SELECT *  FROM `like` WHERE uid=".$bn_uid." AND sid=".$bn_id." AND type=".$bn_typ  );
            $uszunf->execute();
            $susunf=$uszunf->fetch(PDO::FETCH_ASSOC);
            $catusz = $db_con->prepare("SELECT *  FROM `f_coment` WHERE   id=".$bn_id );
            $catusz->execute();
            $sucat=$catusz->fetch(PDO::FETCH_ASSOC);
            $bn_sid    = $sucat['uid'];
            $bn_time_t = $susunf['time_t'];

            $stmt=$db_con->prepare("DELETE FROM `like` WHERE id=:id AND uid=:uid ");
        	$stmt->execute(array(':id'=>$susunf['id'],':uid'=>$bn_uid));
            if($bn_sid==$bn_uid){ }else{
            $stmtnft=$db_con->prepare("DELETE FROM `notif` WHERE uid=:id AND time=:time AND state=1 ");
        	$stmtnft->execute(array(':id'=>$bn_sid,':time'=>$bn_time_t));
            }
             echo "<a href=\"javascript:void(0);\" id=\"like".$bn_id."\"  class=\"btn btn-default\"  ><i class=\"fa fa-heart-o\" aria-hidden=\"true\"></i></a>
                  <input type=\"hidden\" id=\"lval\" value=\"test_like\" />";

     }
     }
 }else{ echo"404"; }
?>