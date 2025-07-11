<?PHP

#####################################################################
##                                                                 ##
##                         MYads  v3.2.x                           ##
##                  https://github.com/mrghozzi                    ##
##                                                                 ##
##                                                                 ##
##                       copyright (c) 2025                        ##
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
   if(isset($_POST["sname"])){
                 $o_type = "store";
                 $o_name = $_POST["sname"];
                 $str_name = strlen($o_name);
                 $setcats = $db_con->prepare("SELECT * FROM `options` WHERE `name` = :o_name AND `o_type` = :o_type  ");
                 $setcats->bindParam(":o_name", $o_name);
                 $setcats->bindParam(":o_type", $o_type);
                 $setcats->execute();
                 $catstorRow=$setcats->fetch(PDO::FETCH_ASSOC);
                   if(!preg_match('/^[-a-zA-Z0-9_]+$/i', $o_name)){
                    echo "<div class=\"alert alert-danger\" role=\"alert\"><strong><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i></strong>&nbsp;{$lang['olanwas']}</div>";
                    echo "<input type=\"txt\" style=\"visibility:hidden\" value=\"\" name=\"vname\"  required>";
                  }else if(($str_name < 3) OR ($str_name > 35)){
                    echo "<div class=\"alert alert-danger\" role=\"alert\"><strong><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i></strong>&nbsp;{$lang['ttmbnlt']}</div>";
                    echo "<input type=\"txt\" style=\"visibility:hidden\" value=\"\" name=\"vname\"  required>";
                  }else if(isset($catstorRow['o_type']) AND ($catstorRow['o_type']==$o_type)){
                    echo "<div class=\"alert alert-danger\" role=\"alert\"><strong><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i></strong>&nbsp;{$lang['exists']}</div>";
                    echo "<input type=\"txt\" style=\"visibility:hidden\" value=\"\" name=\"vname\"  required>";
                  }
    }
 }else{ echo"404"; }
?>