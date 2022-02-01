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
   if(isset($_FILES["fzip"])){

    $replace = array(
"application/x-zip-compressed"
);
$replace_to = array(
 ".zip"
 );
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
      $type = str_replace($replace,$replace_to,$_FILES["fzip"]["type"]);
    $filename = time()."_".$name_r.$type;
  $destination = "../upload/".$filename;
if (($_FILES["fzip"]["type"] == "application/x-zip-compressed")
&& (($_FILES["fzip"]["size"] < 1000000000)
&& ($_FILES["fzip"]["size"]  >       1024)
))
{

if ($_FILES["fzip"]["error"] > 0)
{
echo "Return Code: " . $_FILES["fzip"]["error"] . "<input type=\"txt\" style=\"visibility:hidden\" name=\"linkzip\" value=\"\" id=\"text\" required>";
}else{
  $bn_zip="upload/" . $filename;
  $zipname =$_FILES["fzip"]["name"];
  move_uploaded_file($_FILES["fzip"]["tmp_name"],$destination);
  echo "<img src=\"{$url_site}/templates/_panel/images/fzip.png\"  />&nbsp;{$zipname}<br />" ;
  echo "<input type=\"txt\" name=\"linkzip\" style=\"visibility:hidden\" value=\"{$bn_zip}\" id=\"text\">";
  
}

}else {

echo "<p style=\"color: #ff0000f7;-webkit-border-radius: 5px;border: 1px dashed #f00;\">{$lang['zipfile']}</p><input type=\"txt\" style=\"visibility:hidden\" name=\"linkzip\" value=\"\" id=\"text\" required>";

}


    }else{
    echo "<p style=\"color: #ff0000f7;-webkit-border-radius: 5px;border: 1px dashed #f00;\">{$lang['zipfile']}</p><input type=\"hidden\" name=\"linkzip\"  id=\"text\" required>";

    }
 }else{ echo"404"; }
?>