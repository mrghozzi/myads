<?PHP

#####################################################################
##                                                                 ##
##                        My ads v2.4.x                            ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2022                        ##
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
    //  setting
   $stmt = $db_con->prepare("SELECT *  FROM setting   " );
 $stmt->execute();
 $stt=$stmt->fetch(PDO::FETCH_ASSOC);
  $url_site   = $stt['url'];

    if(isset($_GET['id'])){
          if($_GET['t'] == "f"){
           $bn_time = time();
           $bn_id  = $_GET['id'];
           $f_like  = $_GET['f_like'];
           $bn_uid = $_COOKIE['user'];
           $bn_typ = 2;
             if($f_like=="like_up")   {
            $stmsb = $db_con->prepare("INSERT INTO `like` (uid,sid,type,time_t)
            VALUES(:uid,:a_da,:opm,:ptdk)");
			$stmsb->bindParam(":uid", $bn_uid);
            $stmsb->bindParam(":opm", $bn_typ);
            $stmsb->bindParam(":ptdk", $bn_time);
            $stmsb->bindParam(":a_da", $bn_id);
            if($stmsb->execute()){

            $bn_nurl = "t".$bn_id;
            $bn_logo  = "Weheartit-icon.png";
            $bn_state = "1";
            $usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=".$bn_uid );
            $usz->execute();
            $sus=$usz->fetch(PDO::FETCH_ASSOC);
            $catusz = $db_con->prepare("SELECT *  FROM `forum` WHERE statu=1 AND  id=".$bn_id );
            $catusz->execute();
            $sucat=$catusz->fetch(PDO::FETCH_ASSOC);
            $bn_sid = $sucat['uid'];
            if($bn_sid==$bn_uid){ }else{
            $bn_name  = $sus['username']." likes your publication.";
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
            $likenbcm = $db_con->prepare("SELECT  COUNT(id) as nbr FROM `like` WHERE sid='{$bn_id}' AND  type=2 " );
$likenbcm->execute();
$abdlike=$likenbcm->fetch(PDO::FETCH_ASSOC);
            echo "<a href=\"javascript:void(0);\" id=\"ulike".$bn_id."\" ><i class=\"fa fa-heart\" style=\"color: #FF0000;\" aria-hidden=\"true\"></i>{$abdlike['nbr']}</a>
             <input type=\"hidden\" id=\"lval\" value=\"test_like\" />
             <script>
     \$(\"document\").ready(function() {
   \$(\"#like{$sucat['id']}\").click(postlike{$sucat['id']});

});

function postlike{$sucat['id']}(){
    \$(\"#heart{$sucat['id']}\").html(\"<i class='fa fa-thumbs-up' aria-hidden='true'></i>\");
    \$.ajax({
        url : '{$url_site}/requests/f_like.php?id={$sucat['id']}&f_like=like_up&t=f',
        data : {
            test_like : \$(\"#lval\").val()
        },
        datatype : \"json\",
        type : 'post',
        success : function(result) {
               $(\"#heart{$sucat['id']}\").html(result);
        },
        error : function() {
            alert(\"Error reaching the server. Check your connection\");
        }
    });
}
 \$(\"document\").ready(function() {
   \$(\"#ulike{$sucat['id']}\").click(postulike{$sucat['id']});

});

function postulike{$sucat['id']}(){
    \$(\"#heart{$sucat['id']}\").html(\"<i class='fa fa-thumbs-down' aria-hidden='true'></i>\");
    \$.ajax({
        url : '{$url_site}/requests/f_like.php?id={$sucat['id']}&f_like=like_down&t=f',
        data : {
            test_like : \$(\"#lval\").val()
        },
        datatype : \"json\",
        type : 'post',
        success : function(result) {
               $(\"#heart{$sucat['id']}\").html(result);
        },
        error : function() {
            alert(\"Error reaching the server. Check your connection\");
        }
    });
}
     </script>";


         	}
            }else if($f_like=="like_down"){
            $bn_uid = $_COOKIE['user'];

            $uszunf = $db_con->prepare("SELECT *  FROM `like` WHERE uid=".$bn_uid." AND sid=".$bn_id." AND type=".$bn_typ  );
            $uszunf->execute();
            $susunf=$uszunf->fetch(PDO::FETCH_ASSOC);
            $catusz = $db_con->prepare("SELECT *  FROM `forum` WHERE statu=1 AND  id=".$bn_id );
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
            $likenbcm = $db_con->prepare("SELECT  COUNT(id) as nbr FROM `like` WHERE sid='{$bn_id}' AND  type=2 " );
$likenbcm->execute();
$abdlike=$likenbcm->fetch(PDO::FETCH_ASSOC);
             echo "<a href=\"javascript:void(0);\" id=\"like".$bn_id."\" ><i class=\"fa fa-heart-o\" aria-hidden=\"true\"></i>{$abdlike['nbr']}</a>
                  <input type=\"hidden\" id=\"lval\" value=\"test_like\" />
                  <script>
     \$(\"document\").ready(function() {
   \$(\"#like{$sucat['id']}\").click(postlike{$sucat['id']});

});

function postlike{$sucat['id']}(){
    \$(\"#heart{$sucat['id']}\").html(\"<i class='fa fa-thumbs-up' aria-hidden='true'></i>\");
    \$.ajax({
        url : '{$url_site}/requests/f_like.php?id={$sucat['id']}&f_like=like_up&t=f',
        data : {
            test_like : \$(\"#lval\").val()
        },
        datatype : \"json\",
        type : 'post',
        success : function(result) {
               $(\"#heart{$sucat['id']}\").html(result);
        },
        error : function() {
            alert(\"Error reaching the server. Check your connection\");
        }
    });
}
 \$(\"document\").ready(function() {
   \$(\"#ulike{$sucat['id']}\").click(postulike{$sucat['id']});

});

function postulike{$sucat['id']}(){
    \$(\"#heart{$sucat['id']}\").html(\"<i class='fa fa-thumbs-down' aria-hidden='true'></i>\");
    \$.ajax({
        url : '{$url_site}/requests/f_like.php?id={$sucat['id']}&f_like=like_down&t=f',
        data : {
            test_like : \$(\"#lval\").val()
        },
        datatype : \"json\",
        type : 'post',
        success : function(result) {
               $(\"#heart{$sucat['id']}\").html(result);
        },
        error : function() {
            alert(\"Error reaching the server. Check your connection\");
        }
    });
}
     </script>";

     }
     }
     }
 }else{ echo"404"; }
?>