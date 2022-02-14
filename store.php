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


require "dbconfig.php";
require "include/function.php";

 if(isset($s_st) AND ($s_st=="buyfgeufb")){
 if(isset($_GET['download'])){       //     download
 if(isset($_SESSION['user']) OR (isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin))){
$ndfk=$_GET['download'];
$catusd = $db_con->prepare("SELECT *  FROM `short` WHERE  sh_type=7867 AND sho='{$ndfk}'" );
$catusd->execute();
if($catussd=$catusd->fetch(PDO::FETCH_ASSOC)){
$catdid=$catussd['id'];
$catdurl=$url_site."/".$catussd['url'];
$stmdr = $db_con->prepare("UPDATE short SET clik=clik+1 WHERE id=:ertb");
$stmdr->bindParam(":ertb", $catdid);
  if($stmdr->execute()){

    }else{
    template_mine('header');
    template_mine('404');
    template_mine('footer');
    }
 }
    }else{

    template_mine('header');
    template_mine('404');
    template_mine('footer');

    }
 }else  if(isset($_GET['add'])){
 $title_page = $lang['add_product'];
 template_mine('header');
 if(isset($_SESSION['user']) OR (isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin))){
 template_mine('add_store');
 }else{
    template_mine('404');
 }
 template_mine('footer');

 }else  if(isset($_GET['producer'])){
 $title_page = $_GET['producer'];
 $gproducer =  $_GET['producer'];
  $stname = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'store' AND `name` =:name " );
  $stname->bindParam(":name", $gproducer);
$stname->execute();
$strname=$stname->fetch(PDO::FETCH_ASSOC);
 template_mine('header');
 if(isset($strname['name']) AND ($strname['name']==$_GET['producer'])){
 template_mine('producer');
 }else{
 template_mine('404');
 }
 template_mine('footer');

 }else  if(isset($_GET['kb'])){
 $title_page = $_GET['kb']."&nbsp;&raquo;&nbsp;".$lang['knowledgebase'];
 if(isset($_GET['st'])){
 $title_page = $_GET['kb']."&nbsp;&raquo;&nbsp;".$_GET['st'];
 }
 $gproducer =  $_GET['kb'];
  $stname = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'store' AND `name` =:name " );
  $stname->bindParam(":name", $gproducer);
$stname->execute();
$strname=$stname->fetch(PDO::FETCH_ASSOC);
 template_mine('header');
 if(isset($strname['name']) AND ($strname['name']==$_GET['kb'])){
template_mine('knowledgebase');
 }else{
 template_mine('404');
 }
 template_mine('footer');

 }else  if(isset($_GET['tr'])){
 $title_page = $lang['edit']."&nbsp;&raquo;&nbsp;".$_GET['tr']."&nbsp;&raquo;&nbsp;".$_GET['ed'];
 $gproducer =  $_GET['tr'];
  $stname = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'store' AND `name` =:name " );
  $stname->bindParam(":name", $gproducer);
$stname->execute();
$strname=$stname->fetch(PDO::FETCH_ASSOC);
 template_mine('header');
 if(isset($strname['name']) AND ($strname['name']==$_GET['tr'])){
template_mine('knowledgebase');
 }else{
 template_mine('404');
 }
 template_mine('footer');

 }else  if(isset($_GET['pr'])){
 $title_page = $lang['pending']."&nbsp;&raquo;&nbsp;".$_GET['pr']."&nbsp;&raquo;&nbsp;".$_GET['pg'];
 $gproducer =  $_GET['pr'];
  $stname = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'store' AND `name` =:name " );
  $stname->bindParam(":name", $gproducer);
$stname->execute();
$strname=$stname->fetch(PDO::FETCH_ASSOC);
 template_mine('header');
 if(isset($strname['name']) AND ($strname['name']==$_GET['pr'])){
template_mine('knowledgebase');
 }else{
 template_mine('404');
 }
 template_mine('footer');

 }else  if(isset($_GET['pp'])){
 $title_page = $lang['history']."&nbsp;&raquo;&nbsp;".$_GET['pp']."&nbsp;&raquo;&nbsp;".$_GET['tt'];
 $gproducer =  $_GET['pp'];
  $stname = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'store' AND `name` =:name " );
  $stname->bindParam(":name", $gproducer);
$stname->execute();
$strname=$stname->fetch(PDO::FETCH_ASSOC);
 template_mine('header');
 if(isset($strname['name']) AND ($strname['name']==$_GET['pp'])){
template_mine('knowledgebase');
 }else{
 template_mine('404');
 }
 template_mine('footer');

 }else  if(isset($_GET['nf'])){

 template_mine('header');
 template_mine('404');
 template_mine('footer');

 }else  if(isset($_GET['update'])){
 $title_page = "update_".$_GET['update'];
 $gproducer =  $_GET['update'];
  $stname = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'store' AND `name` =:name " );
  $stname->bindParam(":name", $gproducer);
$stname->execute();
$strname=$stname->fetch(PDO::FETCH_ASSOC);
 template_mine('header');
 if(isset($strname['name']) AND ($strname['name']==$_GET['update'])){
  if((isset($_COOKIE['user']) AND ($_COOKIE['user'] == $strname['o_parent'])) OR (isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin))){
 template_mine('up_product');
 }else{
    template_mine('404');
 }
 }else{
 template_mine('404');
 }
 template_mine('footer');

 }else  if(isset($_GET['v'])){
 $gproducer =  $_GET['v'];
 $stname = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'store' AND `name` =:name " );
 $stname->bindParam(":name", $gproducer);
 $stname->execute();
 $strname=$stname->fetch(PDO::FETCH_ASSOC);
 if(isset($strname['name']) AND ($strname['name']==$_GET['v'])){
                 $f_type = "store_file";
                 $stormf = $db_con->prepare("SELECT *  FROM options WHERE o_type=:o_type AND o_parent=:o_parent ORDER BY `o_order`  DESC " );
                 $stormf->bindParam(":o_type", $f_type);
                 $stormf->bindParam(":o_parent", $strname['id']);
                 $stormf->execute();
                 $storefile=$stormf->fetch(PDO::FETCH_ASSOC);
     $cvl = preg_replace('/-/', '.', $_GET['l']);
     if(isset($storefile['name']) AND ($storefile['name']==$cvl)){
       echo "document.write(\"{$storefile['name']}\");" ;
     }else{
       echo "document.write(\"{$storefile['name']}<a href='{$url_site}/producer/{$strname['name']}' class='btn btn-info' ><i class='fa fa-download' ></i></a>\");";
     }
  }else{
     echo "document.write(\"not exist\");" ;
  }
 }else  if(isset($_GET['c'])){
 $gproducer =  $_GET['c'];
 $stname = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'store' AND `name` =:name " );
 $stname->bindParam(":name", $gproducer);
 $stname->execute();
 $strname=$stname->fetch(PDO::FETCH_ASSOC);
 if(isset($strname['name']) AND ($strname['name']==$_GET['c'])){
                 $f_type = "store_file";
                 $stormf = $db_con->prepare("SELECT *  FROM options WHERE o_type=:o_type AND o_parent=:o_parent ORDER BY `o_order`  DESC " );
                 $stormf->bindParam(":o_type", $f_type);
                 $stormf->bindParam(":o_parent", $strname['id']);
                 $stormf->execute();
                 $storefile=$stormf->fetch(PDO::FETCH_ASSOC);
     echo $storefile['name'];

  }else{
     echo "not exist" ;
  }
 }else  if(isset($_GET['f'])){
 $gproducer =  $_GET['f'];
 $stname = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'store' AND `name` =:name " );
 $stname->bindParam(":name", $gproducer);
 $stname->execute();
 $strname=$stname->fetch(PDO::FETCH_ASSOC);
 if(isset($strname['name']) AND ($strname['name']==$_GET['f'])){
                 $f_type = "store_file";
                 $stormf = $db_con->prepare("SELECT *  FROM options WHERE o_type=:o_type AND o_parent=:o_parent ORDER BY `o_order`  DESC " );
                 $stormf->bindParam(":o_type", $f_type);
                 $stormf->bindParam(":o_parent", $strname['id']);
                 $stormf->execute();
                 $storefile=$stormf->fetch(PDO::FETCH_ASSOC);

     echo $url_site."/".$storefile['o_mode'];

  }else{
     echo "not exist" ;
  }
 }else{
 $title_page = $lang['Store'];
 template_mine('header');
 template_mine('store');
 template_mine('footer');
  }
  }else{
  print  401 ;
  }

?>