<?PHP

#####################################################################
##                                                                 ##
##                        MYads  v3.x.x                            ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2022                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################
 require_once "dbconfig.php";
 include "include/function.php";
 if(isset($_COOKIE['user']))
{
 if($_GET['id']==$uRow['id'])
	{
    $user= $_GET['id'];
    $stt = $db_con->prepare("SELECT * FROM users where  id=:id " );
    $stt->execute(array(':id'=>$user));
    $userRow=$stt->fetch(PDO::FETCH_ASSOC);
    $id = $userRow['id'];
    $stmt = $db_con->prepare("UPDATE users SET pts=pts+5, vu=vu+.5 WHERE id=:id");
    $stmt->bindParam(":id", $id);
        if($stmt->execute())
		{     }
       $replace = array("1","2","3","4");
       $replace_torzer = array("1","2","5","10");
       $replace_to = array(
 "<script>function refreshpage(interval, countdownel, totalel){
	var countdownel = document.getElementById(countdownel)
	var totalel = document.getElementById(totalel)
	var timeleft = interval+1
	var countdowntimer
    function countdown(){timeleft--countdownel.innerHTML = timeleftif (timeleft == 0){clearTimeout(countdowntimer)window.location.reload()}
	countdowntimer = setTimeout(function(){countdown()
		}, 1000)
	}
    countdown()
}
window.onload = function(){
	refreshpage(10, \"countdown\") // refreshpage(duration_in_seconds, id_of_element_to_show_result)
}

</script>",
 "<script>
function refreshpage(interval, countdownel, totalel){
	var countdownel = document.getElementById(countdownel)
	var totalel = document.getElementById(totalel)
	var timeleft = interval+1
	var countdowntimer
    function countdown(){
		timeleft--
		countdownel.innerHTML = timeleft
		if (timeleft == 0){
			clearTimeout(countdowntimer)
			window.location.reload()
		}
		countdowntimer = setTimeout(function(){
			countdown()
		}, 1000)
	}

	countdown()
}
window.onload = function(){
	refreshpage(20, \"countdown\") // refreshpage(duration_in_seconds, id_of_element_to_show_result)
}
</script>",
 "<script>
function refreshpage(interval, countdownel, totalel){
	var countdownel = document.getElementById(countdownel)
	var totalel = document.getElementById(totalel)
	var timeleft = interval+1
	var countdowntimer
    function countdown(){
		timeleft--
		countdownel.innerHTML = timeleft
		if (timeleft == 0){
			clearTimeout(countdowntimer)
			window.location.reload()
		}
		countdowntimer = setTimeout(function(){
			countdown()
		}, 1000)
	}
    countdown()
}
window.onload = function(){
	refreshpage(30, \"countdown\") // refreshpage(duration_in_seconds, id_of_element_to_show_result)
}

</script>",
 "<script>
function refreshpage(interval, countdownel, totalel){
	var countdownel = document.getElementById(countdownel)
	var totalel = document.getElementById(totalel)
	var timeleft = interval+1
	var countdowntimer
    function countdown(){
		timeleft--
		countdownel.innerHTML = timeleft
		if (timeleft == 0){
			clearTimeout(countdowntimer)
			window.location.reload()
		}
		countdowntimer = setTimeout(function(){
			countdown()
		}, 1000)
	}
    countdown()
}
window.onload = function(){
	refreshpage(60, \"countdown\") // refreshpage(duration_in_seconds, id_of_element_to_show_result)
}
</script>");


     $stm = $db_con->prepare("SELECT *,MD5(RAND()) AS m FROM visits where ( uid IN(
  SELECT id FROM users where  vu >= 1 AND NOT(id = '%{$id}%')
) AND statu=1 ) ORDER BY m " );
     $stm->execute();
     $ab=$stm->fetch(PDO::FETCH_ASSOC);
     $type = str_replace($replace,$replace_to,$ab['tims']);
  if($stm->rowCount() == 0)
	{
    {  echo "<script>



function refreshpage(interval, countdownel, totalel){
	var countdownel = document.getElementById(countdownel)
	var totalel = document.getElementById(totalel)
	var timeleft = interval+1
	var countdowntimer

	function countdown(){
		timeleft--
		countdownel.innerHTML = timeleft
		if (timeleft == 0){
			clearTimeout(countdowntimer)
			window.location.reload()
		}
		countdowntimer = setTimeout(function(){
			countdown()
		}, 1000)
	}

	countdown()
}

window.onload = function(){
	refreshpage(10, \"countdown\") // refreshpage(duration_in_seconds, id_of_element_to_show_result)
}

</script>";
  echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">\n<HTML><HEAD>
  <META http-equiv=Content-Type content=\"text/html;\"><title>{$title_s}</title></HEAD>
  <BODY>
  <table width='936' width2='234' height='60' cellspacing='1' cellpadding='0' border='0' bgcolor='#0099ff' style='background-color:#0099ff'>
 <tr>
 <td>
 <table width='934' width2='232' height='58' cellspacing='0' cellpadding='1' border='0' bgcolor='#FFFFFF' style='background-color:#FFFFFF'>
 	<tr>
    <td colspan='2' width height>
    <table width='464' width2='230' height cellspacing='0' cellpadding='2' border='0'>
    <tr>


    <td style='cursor:pointer' width='229' height='41' align='left' valign='top'   >
    	  <center>".$bads3."</center>
    </td> </tr>
  </table>
                            			</td>
                                        	</tr>
                                            	<tr>
     <td nowrap width='50%' height='11' align='left' bgcolor='#0099ff'>
        	<a class='attribution'  >
            	<font style='font-size:12px; font-family:verdana,arial,sans-serif; line-height:13px;color:#FFFFFF; text-decoration:none' color='#FFFFFF'>
                			<div>Next site in <b id=\"countdown\"></b> seconds</div></font></a></td>
     			</tr></table>		</td>	</tr></table>
  <IFRAME SRC='{$url_site}' width=100% height=100% marginwidth=0 marginheight=0 hspace=0 vspace=0 frameborder=0 scrolling='no'></IFRAME></BODY>";

}
     }else{
  echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">\n<HTML><HEAD>
  <META http-equiv=Content-Type content=\"text/html;\"><title>{$ab['name']}</title></HEAD>
  <BODY>
  {$type}
  <table width='936' width2='234' height='60' cellspacing='1' cellpadding='0' border='0' bgcolor='#0099ff' style='background-color:#0099ff'>
 <tr>
 <td>
 <table width='934' width2='232' height='58' cellspacing='0' cellpadding='1' border='0' bgcolor='#FFFFFF' style='background-color:#FFFFFF'>
 	<tr>
    <td colspan='2' width height>
    <table width='464' width2='230' height cellspacing='0' cellpadding='2' border='0'>
    <tr style='cursor:pointer' width='229' height='41' align='center' valign='top'   >
    		<font style='font-size:11px; font-family:verdana,arial,sans-serif; line-height:14px; text-decoration:underline;color:#660066' color='#660066'>
            				<b>{$ab['name']}</b>
            </font>
     </tr>
        <br />
    <tr style='cursor:pointer' width='229' height='41' align='center' valign='top'   >
    	<center>".$bads3."</center>
    </tr>
  </table>
                            			</td>
                                        	</tr>
                                            	<tr>
     <td nowrap width='50%' height='11' align='left' bgcolor='#0099ff'>
        	<a class='attribution' >
            	<font style='font-size:12px; font-family:verdana,arial,sans-serif; line-height:13px;color:#FFFFFF; text-decoration:none' color='#FFFFFF'>
                			<div>Next site in <b id=\"countdown\"></b> seconds</div></font></a></td>
     			</tr></table>		</td>	</tr></table>
  <IFRAME SRC='{$ab['url']}'  width=100% height=100% marginwidth=0 marginheight=0 hspace=0 vspace=0 frameborder=0 scrolling='no'></IFRAME></BODY>";

 	  $ids = $ab['id'];
      $idu = $ab['uid'];
      $stmo = $db_con->prepare("UPDATE visits SET vu=vu+1  WHERE id=:ids");
      $stmo->bindParam(":ids", $ids);
        if($stmo->execute())
		{     }
       $tytty = str_replace($replace,$replace_torzer,$ab['tims']);
       $stmv = $db_con->prepare("UPDATE users SET vu=vu-:ivu WHERE id=:id");
      $stmv->bindParam(":id", $idu);
      $stmv->bindParam(":ivu", $tytty);
        if($stmv->execute())
		{     }
}


}
}
?>