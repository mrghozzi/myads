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
 if(isset($_COOKIE['user']))
{
 $stmt = $db_con->prepare("SELECT *  FROM setting   " );
        $stmt->execute();
        $ab=$stmt->fetch(PDO::FETCH_ASSOC);
        $lng=$ab['lang'];
        $url_site   = $ab['url'];
   include "../content/languages/$lng.php";

 if( ! ini_get('date.timezone') )
{
    date_default_timezone_set('GMT');
}
if($vrf_License=="65fgh4t8x5fe58v1rt8se9x"){
   if(isset($_FILES["fimg"])){

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

      $name_r=rand_string(8);
      $type = str_replace($replace,$replace_to,$_FILES["fimg"]["type"]);
    $filename = time()."_".$name_r.$type;
  $destination = "../upload/".$filename;
if ((($_FILES["fimg"]["type"] == "image/gif")
||   ($_FILES["fimg"]["type"] == "image/jpeg")
||   ($_FILES["fimg"]["type"] == "image/png")
||   ($_FILES["fimg"]["type"] == "image/pjpeg"))
&&   ($_FILES["fimg"]["size"] < 10000000))
{
$minwidth  = 100000;
$minheight = 100000;
$image_sizes = getimagesize($_FILES['fimg']['tmp_name']);
if ($image_sizes < $minwidth OR $image_sizes < $minheight)
{
echo "<p style=\"color: #ff0000f7;-webkit-border-radius: 5px;border: 1px dashed #f00;\">{$lang['invalidfile']}</p><input type=\"txt\" style=\"visibility:hidden\" name=\"img\" value=\"\" id=\"text\" required>";
}else if ($_FILES["fimg"]["error"] > 0)
{
echo "<p style=\"color: #ff0000f7;-webkit-border-radius: 5px;border: 1px dashed #f00;\">{$lang['invalidfile']}</p><input type=\"txt\" style=\"visibility:hidden\" name=\"img\" value=\"\" id=\"text\" required>";
}else{
  $bn_zip="{$url_site}/upload/" . $filename;
  $zipname =$_FILES["fimg"]["name"];
  move_uploaded_file($_FILES["fimg"]["tmp_name"],$destination);
  echo "<img src=\"{$bn_zip}\"  /><br />" ;
  echo "<input type=\"txt\" name=\"img\" style=\"visibility:hidden\" value=\"{$bn_zip}\">";

}

}else {

echo "<p style=\"color: #ff0000f7;-webkit-border-radius: 5px;border: 1px dashed #f00;\">{$lang['invalidfile']}</p><input type=\"txt\" style=\"visibility:hidden\" name=\"img\" value=\"\" id=\"text\" required>";

}


    }else{
echo "<p style=\"color: #ff0000f7;-webkit-border-radius: 5px;border: 1px dashed #f00;\">{$lang['invalidfile']}</p><input type=\"txt\" style=\"visibility:hidden\" name=\"img\" value=\"\" id=\"text\" required>";

    }
 }else{ echo"404"; }
}else{ echo"404"; }
?>