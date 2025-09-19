<?PHP

#####################################################################
##                                                                 ##
##                        MYads  v3.1.0                            ##
##                  https://github.com/mrghozzi                    ##
##                                                                 ##
##                                                                 ##
##                       copyright (c) 2025                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################
if($s_st=="buyfgeufb"){

//  MyAds Version
$myads_generation  = "3";
$myads_Version     = "2";
$myads_Update      = "0";
$stversion = "{$myads_generation}.{$myads_Version}.{$myads_Update}";
$o_type = "version" ;
$version_name = "{$myads_generation}-{$myads_Version}-{$myads_Update}";
$jversion = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = :o_type  ");
$jversion->bindParam(":o_type", $o_type);
$jversion->execute();
 $versionRow=$jversion->fetch(PDO::FETCH_ASSOC);
  if( isset($versionRow['o_type']) AND ($versionRow['o_type']==$o_type)){
  if( isset($versionRow['o_valuer']) AND ($versionRow['o_valuer']==$stversion)){  }else{
       $ostmsbs = $db_con->prepare("UPDATE options SET name=:name,o_valuer=:o_valuer,o_type=:o_type
            WHERE id=:id");
            $ostmsbs->bindParam(":o_type", $o_type);
            $ostmsbs->bindParam(":o_valuer", $stversion);
            $ostmsbs->bindParam(":name", $version_name);
            $ostmsbs->bindParam(":id", $versionRow['id']);
            if($ostmsbs->execute()){ }
   }
   }else{
   $ostmsbs = $db_con->prepare(" INSERT INTO options  (name,o_valuer,o_type,o_parent,o_order,o_mode)
            VALUES (:name,:o_valuer,:o_type,0,0,0) ");
	        $ostmsbs->bindParam(":o_type", $o_type);
            $ostmsbs->bindParam(":o_valuer", $stversion);
            $ostmsbs->bindParam(":name", $version_name);
            if($ostmsbs->execute()){   }
 }

}else{ echo"404"; }
 ?>