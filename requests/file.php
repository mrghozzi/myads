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
   if($_POST['up']){

    $replace = array(
"image/gif",
"image/jpeg",
"image/png",
"image/pjpeg");
$replace_to = array(
 ".gif",
 ".jpg",
 ".png",
 "");
          function rand_string($num_chars)
{
    $chars = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
    $string = array_rand($chars, $num_chars);
    foreach($string as $s)
    {
        $ret = $chars[$s];
    }
    return $ret;}
        $stmt = $db_con->prepare("SELECT *  FROM setting   " );
        $stmt->execute();
        $ab=$stmt->fetch(PDO::FETCH_ASSOC);
        $lng=$ab['lang'];
   include "../content/languages/$lng.php";
      $name_r=rand_string(8);
      $type = str_replace($replace,$replace_to,$_FILES["file"]["type"]);
    $filename = time()."_".$name_r.$type;
  $destination = "../upload/".$filename;
if ((($_FILES["file"]["type"] == "image/gif")
|| ($_FILES["file"]["type"] == "image/jpeg")
|| ($_FILES["file"]["type"] == "image/png")
|| ($_FILES["file"]["type"] == "image/pjpeg"))
&& ($_FILES["file"]["size"] < 10000000))
{
$minwidth = 560000;
$minheight = 560000;
$image_sizes = getimagesize($_FILES['file']['tmp_name']);
if ($image_sizes < $minwidth OR $image_sizes < $minheight)
{
     $bn_uid=$_COOKIE['user'];
     echo "{$lang['invalidfile']}";
     $go_link=$ab['url']."/u/".$bn_uid  ;
     echo "<meta http-equiv='refresh' content='3; url={$go_link}'> ";
}else
{
if ($_FILES["file"]["error"] > 0)
{
echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
}
else
{

  if(!preg_match('#^[a-zA-Z0-9_-]{1,200}\.(jpe?g|gif|png)$#', $_FILES['file']['name']))
{
  $bn_uid=$_COOKIE['user'];
  echo "{$lang['invalidfile']}1";
  $go_link=$ab['url']."/u/".$bn_uid;
  echo "<meta http-equiv='refresh' content='3; url={$go_link}'> ";
} else
{
            $bn_uid=$_COOKIE['user'];
            $bn_img="upload/" . $filename;
            $stmsb = $db_con->prepare("UPDATE users SET img=:img WHERE id=:id");
            $stmsb->bindParam(":img",   $bn_img);
            $stmsb->bindParam(":id",    $bn_uid);
            if($stmsb->execute()){
            echo "{$lang['img']}<img src='{$ab['url']}/upload/" . $filename . "' /><br />";
echo "Type: " . $_FILES["file"]["type"] . "<br />";
echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";


if (file_exists("../upload/" . $_FILES["file"]["name"]))
{
echo $_FILES["file"]["name"] . "{$lang['exists']}";
}
else
{

move_uploaded_file($_FILES["file"]["tmp_name"],$destination);
echo "{$lang['filename']}" . "upload/" . $filename;
}
            $go_link=$ab['url']."/u/".$bn_uid ;
            echo "<meta http-equiv='refresh' content='3; url={$go_link}'> ";
         	}

}
}
}
}
else
{
$bn_uid=$_COOKIE['user'];  
echo "{$lang['invalidfile']}";
$go_link=$ab['url']."/u/".$bn_uid ;
echo "<meta http-equiv='refresh' content='3;url={$go_link}'> ";
}


    }
 }else{ echo"404"; }
?>