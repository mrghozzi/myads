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
        $lang_site=$ab['lang'];
        $url_site   = $ab['url'];
        if (isset($_COOKIE["lang"])) {
      $lng=$_COOKIE["lang"] ;
       } else {  $lng=$lang_site ; }
   include "../content/languages/$lng.php";

 if( ! ini_get('date.timezone') )
{
    date_default_timezone_set('GMT');
}
if($vrf_License=="65fgh4t8x5fe58v1rt8se9x"){
   if(isset($_POST["cat_s"])){
         $o_type = "storecat";
                 $setcats = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = :o_type  ");
                 $setcats->bindParam(":o_type", $o_type);
                 $setcats->execute();
                 $catstorRow=$setcats->fetch(PDO::FETCH_ASSOC);
                  if(isset($catstorRow['o_type']) AND ($catstorRow['o_type']==$o_type)){
                    echo "<select class=\"form-control cat_s\" id=\"cat_s\" name=\"cat_s\" required>
                    <option value=\"\">-- Select a categorie --</option>";
                 $stormt = $db_con->prepare("SELECT *  FROM options WHERE o_type=:o_type ORDER BY `id` " );
                 $stormt->bindParam(":o_type", $o_type);
                 $stormt->execute();
                 while($storecat=$stormt->fetch(PDO::FETCH_ASSOC) ) {
                  $catname = $storecat['name'];
                  $catval  = $storecat['name'];
                  $catname = $lang["{$catname}"];
                    echo "<option value=\"{$catval}\" ";
                   if(isset($_POST["cat_s"]) AND ($_POST["cat_s"]==$catval)) {
                     echo "selected=\"selected\"";
                   }
                    echo " >{$catname}</option>";

                 }  echo " </select> ";
                  }
                if(isset($_POST["cat_s"]) AND (($_POST["cat_s"]=="plugins") OR ($_POST["cat_s"]=="templates"))) {
                    $o_type = "store_type";
                 $setcats = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = :o_type  ");
                 $setcats->bindParam(":o_type", $o_type);
                 $setcats->execute();
                 $catstorRow=$setcats->fetch(PDO::FETCH_ASSOC);
                 echo "<br /><select class=\"form-control\" name=\"sc_cat\" required>
                    <option value=\"\">-- Select a Script --</option>";
                  if(isset($catstorRow['o_type']) AND ($catstorRow['o_type']==$o_type)){
                 $o_name ="script";
                 $stormt = $db_con->prepare("SELECT *  FROM options WHERE name=:name AND o_type=:o_type ORDER BY `id` " );
                 $stormt->bindParam(":name" , $o_name);
                 $stormt->bindParam(":o_type", $o_type);
                 $stormt->execute();
                 while($storecat=$stormt->fetch(PDO::FETCH_ASSOC) ) {
                 $catval   = $storecat['o_parent'];
                 $settps = $db_con->prepare("SELECT * FROM `options` WHERE id = :id  ");
                 $settps->bindParam(":id", $catval);
                 $settps->execute();
                 $tpstorRow=$settps->fetch(PDO::FETCH_ASSOC);
                  $catname = $tpstorRow['name'];

                   echo "<option value=\"{$catval}\" >{$catname}</option>";

                 }
                  }  $catothers = $lang['others']; echo "<option value=\"others\" >{$catothers}</option>  </select>";
                   }else if(isset($_POST["cat_s"]) AND ($_POST["cat_s"]=="script")) {
                    $o_type = "scriptcat";
                 $setcats = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = :o_type  ");
                 $setcats->bindParam(":o_type", $o_type);
                 $setcats->execute();
                 $catstorRow=$setcats->fetch(PDO::FETCH_ASSOC);
                  if(isset($catstorRow['o_type']) AND ($catstorRow['o_type']==$o_type)){
                    echo "<br /><select class=\"form-control\" name=\"sc_cat\" required>
                    <option value=\"\">-- Select a categorie --</option>";
                 $stormt = $db_con->prepare("SELECT *  FROM options WHERE o_type=:o_type ORDER BY `id` " );
                 $stormt->bindParam(":o_type", $o_type);
                 $stormt->execute();
                 while($storecat=$stormt->fetch(PDO::FETCH_ASSOC) ) {
                  $catname = $storecat['name'];
                  $catval   = $storecat['name'];
                  $catname = $lang["{$catname}"];
                    echo "<option value=\"{$catval}\" >{$catname}</option>";

                 }  echo "</select>";
                  }
                   }
                   echo "<script>
    $(document).ready(function(){
        $('#cat_s').change(function(){
   var cat_s=$(this).val();
  var dataString = 'cat_s='+ cat_s;

  $.ajax
  ({
   type: \"POST\",
   url: \"{$url_site}/requests/store_cat.php\",
   data: dataString,
   cache: false,
   success: function(html)
   {
      $(\"#storecat\").html(html);
   }
   });

        });
    });
    </script>";

    }else{
        $o_type = "storecat";
                 $setcats = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = :o_type  ");
                 $setcats->bindParam(":o_type", $o_type);
                 $setcats->execute();
                 $catstorRow=$setcats->fetch(PDO::FETCH_ASSOC);
                  if(isset($catstorRow['o_type']) AND ($catstorRow['o_type']==$o_type)){
                    echo "<select class=\"form-control cat_s\" id=\"cat_s\" name=\"cat_s\" required>
                    <option value=\"\">-- Select a categorie --</option>";
                 $stormt = $db_con->prepare("SELECT *  FROM options WHERE o_type=:o_type ORDER BY `id` " );
                 $stormt->bindParam(":o_type", $o_type);
                 $stormt->execute();
                 while($storecat=$stormt->fetch(PDO::FETCH_ASSOC) ) {
                  $catname = $storecat['name'];
                  $catval   = $storecat['name'];
                  $catname = $lang["{$catname}"];
                    echo "<option value=\"{$catval}\">{$catname}</option>";

                 }  echo " </select> ";
                  }
                    echo "<script>
    $(document).ready(function(){
        $('#cat_s').change(function(){
   var cat_s=$(this).val();
  var dataString = 'cat_s='+ cat_s;

  $.ajax
  ({
   type: \"POST\",
   url: \"{$url_site}/requests/store_cat.php\",
   data: dataString,
   cache: false,
   success: function(html)
   {
      $(\"#storecat\").html(html);
   }
   });

        });
    });
    </script>";
    }
 }else{ echo"404"; }





?>