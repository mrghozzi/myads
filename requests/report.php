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
    if($_POST['submit']){
           $admin_uid=1;
           $bn_txt = $_POST['txt'];
           $bn_type = $_POST['s_type'];
           $bn_tid = $_POST['tid'];
           session_start();
           if($_SESSION['user']>=1){
           $bn_uid = $_SESSION['user'];
           }else{
           $bn_uid = "0";
           }
           $bn_statu = "1";

            $stmsb = $db_con->prepare("INSERT INTO report (uid,txt,s_type,tp_id,statu)
            VALUES(:uid,:a_da,:opm,:ptdk,:statu)");
			$stmsb->bindParam(":uid",  $bn_uid);
            $stmsb->bindParam(":opm",  $bn_type);
            $stmsb->bindParam(":a_da", $bn_txt);
            $stmsb->bindParam(":statu",$bn_statu);
            $stmsb->bindParam(":ptdk", $bn_tid);
            if($stmsb->execute()){
              $bn_name  = "Report abuse";
              $bn_nurl = "admincp?report";
              $bn_logo  = "flag-icon.png";
              $bn_time = time();
              $bn_state = "1";
            $stmntf = $db_con->prepare("INSERT INTO notif (uid,name,nurl,logo,time,state)
            VALUES(:uid,:name,:nurl,:logo,:time,:state)");
			$stmntf->bindParam(":uid", $admin_uid);
            $stmntf->bindParam(":name", $bn_name);
            $stmntf->bindParam(":nurl", $bn_nurl);
            $stmntf->bindParam(":logo", $bn_logo);
            $stmntf->bindParam(":time", $bn_time);
            $stmntf->bindParam(":state", $bn_state);
            if($stmntf->execute()){

         	}
         	}




    }
 }else{ echo"404"; }
?>