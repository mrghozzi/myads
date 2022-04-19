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
#####################################################################
require_once "dbconfig.php";
include "include/function.php";
 if(isset($_GET))
	{
    	$id = $_GET['ID'];
        $px = $_GET['px'];
    $stt = $db_con->prepare("SELECT *FROM users where  id=:id " );
     $stt->execute(array(':id'=>$id));
     $userRow=$stt->fetch(PDO::FETCH_ASSOC);

       $ids  = $userRow['id'] ;


      $stmt = $db_con->prepare("UPDATE users SET pts=pts+1 WHERE id=:id");
      $stmt->bindParam(":id", $ids);
        if($stmt->execute())
		{     }

     $results = $db_con->prepare("SELECT *,MD5(RAND()) AS m FROM link where ( uid IN(
  SELECT id FROM users where  nlink >=1 AND NOT(id = '%{$ids}%')
) AND statu=1 ) ORDER BY m " );
     $results->execute();
     $ab=$results->fetch(PDO::FETCH_ASSOC);
     $adid = $ab['id'];
     $vusk= $ab['uid'];
    if($results->rowCount() == 0)
	{  }else{
     $results2 = $db_con->prepare("SELECT *,MD5(RAND()) AS m FROM link where uid IN(
  SELECT id FROM users where  nlink >=1 AND ( NOT(id = '%{$ids}%'AND id = '%{$vusk}%') )
) AND statu=1 AND NOT(id = :idl) ORDER BY m " );
     $results2->execute(array(':idl'=>$adid));

     $ab2=$results2->fetch(PDO::FETCH_ASSOC);

            $bn_nm = "link";
            if(empty($_SERVER['HTTP_REFERER'])==NULL){
              $bn_lnk =  $_SERVER['HTTP_REFERER'];
            }else{
              $bn_lnk =  "N";
            }
            $bn_dt = time();
            $bn_ag = $_SERVER['HTTP_USER_AGENT'];
            $bn_ip = $_SERVER['REMOTE_ADDR'];


            if ($num_rows = $results2->fetchColumn() == 0) {   } else {
            $idp2=$ab2['id'];;
		    $stmsb = $db_con->prepare("INSERT INTO state (sid,pid,t_name,r_link,r_date,visitor_Agent,v_ip)
            VALUES(:a_da,:a_db,:opm,:ptdk,:bn_px,:ptag,:bn_ip)");
			$stmsb->bindParam(":opm", $bn_nm);
            $stmsb->bindParam(":a_da", $id);
            $stmsb->bindParam(":a_db", $idp2);
            $stmsb->bindParam(":ptdk", $bn_lnk);
            $stmsb->bindParam(":bn_px", $bn_dt);
            $stmsb->bindParam(":ptag", $bn_ag);
            $stmsb->bindParam(":bn_ip", $bn_ip);
            $stmsb->execute();
            }


     echo "document.write(\"" ;
     if(isset($px) AND ($px=="1")){
    include "include/link1.php";
    $idp=$ab['id'];
		    $stmsb = $db_con->prepare("INSERT INTO state (sid,pid,t_name,r_link,r_date,visitor_Agent,v_ip)
            VALUES(:a_da,:a_db,:opm,:ptdk,:bn_px,:ptag,:bn_ip)");
			$stmsb->bindParam(":opm", $bn_nm);
            $stmsb->bindParam(":a_da", $id);
            $stmsb->bindParam(":a_db", $idp);
            $stmsb->bindParam(":ptdk", $bn_lnk);
            $stmsb->bindParam(":bn_px", $bn_dt);
            $stmsb->bindParam(":ptag", $bn_ag);
            $stmsb->bindParam(":bn_ip", $bn_ip);
            $stmsb->execute();
      }
    if(isset($px) AND ($px=="2")){
    include "include/link2.php";
    $idp=$ab['id'];

		    $stmsb = $db_con->prepare("INSERT INTO state (sid,pid,t_name,r_link,r_date,visitor_Agent,v_ip)
            VALUES(:a_da,:a_db,:opm,:ptdk,:bn_px,:ptag,:bn_ip)");
			$stmsb->bindParam(":opm", $bn_nm);
            $stmsb->bindParam(":a_da", $id);
            $stmsb->bindParam(":a_db", $idp);
            $stmsb->bindParam(":ptdk", $bn_lnk);
            $stmsb->bindParam(":bn_px", $bn_dt);
            $stmsb->bindParam(":ptag", $bn_ag);
            $stmsb->bindParam(":bn_ip", $bn_ip);
            $stmsb->execute();
      }
    echo "\");";
    }
 }

    ?>