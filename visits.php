<?PHP

#####################################################################
##                                                                 ##
##                        MYads  v3.2.x                            ##
##                  https://github.com/mrghozzi                    ##
##                                                                 ##
##                                                                 ##
##                       copyright (c) 2025                        ##
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
 "<script>
function refreshPage(interval, countdownEl) {
  let timeLeft = interval;
  const countdownElement = document.getElementById(countdownEl);
  
  if (!countdownElement) return;
  
  countdownElement.textContent = timeLeft;
  
  const timer = setInterval(() => {
    timeLeft--;
    countdownElement.textContent = timeLeft;
    
    if (timeLeft <= 0) {
      clearInterval(timer);
      window.location.reload();
    }
  }, 1000);
}

document.addEventListener('DOMContentLoaded', () => {
  refreshPage(10, 'countdown');
});
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
  echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
  <meta charset=\"UTF-8\">
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
  <title>{$title_s}</title>
  <style>
    .traffic-container {
      max-width: 100%;
      margin: 0 auto;
      font-family: Arial, sans-serif;
    }
    .header {
      background-color: #0099ff;
      color: white;
      padding: 15px;
      border-radius: 5px 5px 0 0;
    }
    .content {
      background-color: #fff;
      padding: 20px;
      border-radius: 0 0 5px 5px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .countdown {
      font-weight: bold;
      color:rgb(255, 255, 255);
    }
    .site-frame {
      width: 100%;
      height: calc(100vh - 150px);
      border: none;
      margin-top: 10px;
    }
    @media (max-width: 768px) {
      .header, .content {
        padding: 10px;
      }
    }
  </style>
</head>
<body class=\"traffic-container\">
  <div class=\"header\">
    <div>Next site in <span class=\"countdown\" id=\"countdown\"></span> seconds</div>
  </div>
  <div class=\"content\">
    <iframe class=\"site-frame\" src=\"{$url_site}\"></iframe>
  </div>
</body>
</html>";

}
     }else{
  echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
  <meta charset=\"UTF-8\">
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
  <title>{$ab['name']}</title>
  <style>
    .traffic-container {
      max-width: 100%;
      margin: 0 auto;
      font-family: Arial, sans-serif;
    }
    .header {
      background-color: #0099ff;
      color: white;
      padding: 15px;
      border-radius: 5px 5px 0 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .site-info {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .report-btn {
      color: white;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 5px;
    }
    .content {
      background-color: #fff;
      padding: 20px;
      border-radius: 0 0 5px 5px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .countdown {
      font-weight: bold;
      color:rgb(255, 255, 255);
    }
    .site-frame {
      width: 100%;
      height: calc(100vh - 150px);
      border: none;
      margin-top: 10px;
    }
    @media (max-width: 768px) {
      .header, .content {
        padding: 10px;
      }
      .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
      }
    }
  </style>
</head>
<body class=\"traffic-container\">
  {$type}
  <div class=\"header\">
    <div class=\"site-info\">
      <a href=\"{$url_site}/report?visits={$ab['id']}\" target=\"_blank\" class=\"report-btn\">
        <img src=\"{$url_site}/templates/_panel/img/Alert-icon.png\" alt=\"Report\" width=\"16\">
        Report
      </a>
      <span>{$ab['name']}</span>
    </div>
	{$bads3}
    <div>Next site in <span class=\"countdown\" id=\"countdown\"></span> seconds</div>
  </div>
  <div class=\"content\">
    
    <iframe class=\"site-frame\" src=\"{$ab['url']}\"></iframe>
  </div>
</body>
</html>";

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