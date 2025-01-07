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
  session_start();
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
if(isset($_POST['svtxt']) AND ($_POST['svtxt']==1)){

$catusz = $db_con->prepare("SELECT *  FROM `forum` WHERE statu=1 AND  id='".$_GET['id']."'" );
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);
     ?>
<div class="input-group">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/default.min.css" />
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/sceditor.min.js"></script>
<form action="<?php echo $url_site;  ?>/requests/store_edit.php?id=<?php echo $_GET['id']; ?>" method="POST">
<textarea type="txt" name="svtxt" id="stxt" class="form-control"  rows="16" placeholder="Site Description" required><?php if(isset($sucat['txt'])){ echo $sucat['txt']; }  ?></textarea>
<br /><center><button  type="submit" name="submit" value="Publish" class="btn btn-primary" ><i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp; <?php echo $lang['save'];  ?></button></center>
</form>
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/formats/xhtml.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/jquery.sceditor.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/languages/<?php echo $lang['lg']; ?>.js"></script>

                       <script>
// Replace the textarea #example with SCEditor
var textarea = document.getElementById('stxt');
sceditor.create(textarea, {
	format: 'xhtml',
    locale : 'ar',
<?php
$smlusen = $db_con->prepare("SELECT *  FROM emojis ");
$smlusen->execute();
$c = 1;
while($smlssen=$smlusen->fetch(PDO::FETCH_ASSOC)){
  if($c == 1){
  ?> emoticons: {
  dropdown: {
    <?php
  }else if($c == 11){
    ?>
    },
  more: {
    <?php
    }
   ?>
   '<?php echo $smlssen['name'];  ?>': '<?php echo $smlssen['img'];  ?>',
   <?php


$c++; }

if($c >= 2){
  echo "}
  },";
}
 ?>
style: 'https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/content/default.min.css'
});
</script>
                       </div>
                       <?php
}else if(isset($_POST['svtxt']) AND ($_POST['svtxt']!=1)){
   $id_admin = "1";
 $stadmin_select = $db_con->prepare('SELECT * FROM users WHERE id=:id ');
 $stadmin_select->bindParam(":id", $id_admin);
 $stadmin_select->execute();
 $usadmin=$stadmin_select->fetch(PDO::FETCH_ASSOC);
 $hachadmin= md5($usadmin['pass'].$usadmin['username']) ;
  $catusz = $db_con->prepare("SELECT *  FROM `forum` WHERE statu=1 AND  id='".$_GET['id']."'" );
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);
if(isset($_GET['id']) AND ($_GET['id']==$sucat['id'])){
  $bn_id   = $sucat['id'] ;
  $bn_cat  = "0";
  $bn_txt  = $_POST['svtxt'];
  $bn_name = $sucat['name'];
  if((isset($_SESSION['user']) AND ($_SESSION['user']==$sucat['uid']) ) OR (isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin))){
  $bn_uid  = $sucat['uid'];
   $stmsb = $db_con->prepare("UPDATE forum SET name=:name,txt=:txt,cat=:cat
            WHERE id=:id AND uid=:uid");
            $stmsb->bindParam(":uid",   $bn_uid);
            $stmsb->bindParam(":name",  $bn_name);
            $stmsb->bindParam(":txt",   $bn_txt);
            $stmsb->bindParam(":cat",   $bn_cat);
            $stmsb->bindParam(":id",    $bn_id);
            if($stmsb->execute()){
              header("Location: {$url_site}/producer/{$bn_name}");
         	}
            }else{
              header("Location: {$url_site}/404");
            }
            }else{
              header("Location: {$url_site}/404");
            }
   echo "<input type=\"hidden\" class=\"svtxt\" name=\"svtxt\" value=\"1\" />";
}
 }else{ echo"404"; }
?>