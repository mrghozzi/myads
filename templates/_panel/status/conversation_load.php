<?php
if(isset($_GET['id']) AND isset($_GET['mid'])){
include "../../../dbconfig.php";
 $stmt = $db_con->prepare("SELECT *  FROM setting   " );
        $stmt->execute();
        $ab=$stmt->fetch(PDO::FETCH_ASSOC);
        $lng=$ab['lang'];
        $url_site   = $ab['url'];
 $s_st="buyfgeufb";
  if( ! ini_get('date.timezone') )
{
    date_default_timezone_set('GMT');
}
   include "../../../content/languages/$lng.php";
   include "../../../include/convertTime.php";
   function convert_links($text) {
    $pattern = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/';
    preg_match_all($pattern, $text, $matches);
    $links = $matches[0];
    $num_links = count($links);

    if ($num_links == 1 && preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $links[0])) {
        $text = preg_replace($pattern, '<br><img src="$0" alt="Image" style="max-width: 100%; max-height: 500px; height: auto;"><br>', $text);
    } else {
        $text = preg_replace($pattern, '<a href="$0">$0</a>', $text);
    }
    return $text;
}

     if(isset($_COOKIE['user']))
    {
    if (session_status() == PHP_SESSION_NONE) {
       session_start();
    }
  $bn_uid = $_SESSION['user'];
  $msgdid=$_GET['id'];
  $msgeid=$_GET['mid'];
 echo "<div id=\"load_msg\" ></div>";
$statement = "`messages` WHERE (us_env='{$msgdid}' AND us_rec='{$msgeid}') OR (us_env='{$msgeid}' AND us_rec='{$msgdid}') ORDER BY `id_msg` DESC";
$catsum = $db_con->prepare("SELECT  * FROM {$statement}" );
$catsum->execute();
while($sutcat=$catsum->fetch(PDO::FETCH_ASSOC))
{
$catusen = $db_con->prepare("SELECT *  FROM users WHERE  id='{$sutcat['us_env']}'");
$catusen->execute();
$catussen=$catusen->fetch(PDO::FETCH_ASSOC);
  $bn_state="0";
  $bn_id = $sutcat['id_msg'];
  $stmsb = $db_con->prepare("UPDATE messages SET state=:state
            WHERE id_msg=:id AND us_rec=:uid");
            $stmsb->bindParam(":uid",   $msgdid);
            $stmsb->bindParam(":state", $bn_state);
            $stmsb->bindParam(":id",    $bn_id);
            if($stmsb->execute()){

         	}
$comment =  $sutcat['msg'] ;
$comment  = nl2br($comment);
$emojis = array();
$smlusen = $db_con->prepare("SELECT *  FROM emojis ");
$smlusen->execute();
while($smlssen=$smlusen->fetch(PDO::FETCH_ASSOC)){
    $emojis['name'][]=$smlssen['name'];
    $emojis['img'][]="<img src=\"{$smlssen['img']}\" width=\"23\" height=\"23\" />";
}
 if(isset($emojis['name']) && isset($emojis['img']) ) {
         $comment = str_replace($emojis['name'], $emojis['img'], $comment);
}
$comment  = convert_links($comment);
$comment = strip_tags($comment, '<p><a><b><br><li><ul><font><span><pre><u><s><img>');
$comment = preg_replace("/[\r\n]*/","",$comment);
 $time_cmt=convertTime($sutcat['time']);
 ?>
<?php if($msgdid==$sutcat['us_env']){ ?>
              <!-- CHAT WIDGET SPEAKER -->
              <div class="chat-widget-speaker right">
                <!-- CHAT WIDGET SPEAKER MESSAGE -->
                <p class="chat-widget-speaker-message"><?php echo $comment; ?></p>
                <!-- /CHAT WIDGET SPEAKER MESSAGE -->

                <!-- CHAT WIDGET SPEAKER TIMESTAMP -->
                <p class="chat-widget-speaker-timestamp"><?php echo $time_cmt; ?></p>
                <!-- /CHAT WIDGET SPEAKER TIMESTAMP -->
              </div>
              <!-- /CHAT WIDGET SPEAKER -->
 <?php }else{ ?>
             <!-- CHAT WIDGET SPEAKER -->
              <div class="chat-widget-speaker left">
                <!-- CHAT WIDGET SPEAKER AVATAR -->
                <div class="chat-widget-speaker-avatar">
                  <!-- USER AVATAR -->
                  <div class="user-avatar tiny no-border">
                    <!-- USER AVATAR CONTENT -->
                    <div class="user-avatar-content">
                      <!-- HEXAGON -->
                      <img src="<?php echo $url_site;  ?>/<?php echo $catussen['img']; ?>"  width="24" height="26" alt="">
                      <!-- /HEXAGON -->
                    </div>
                    <!-- /USER AVATAR CONTENT -->
                  </div>
                  <!-- /USER AVATAR -->
                </div>
                <!-- /CHAT WIDGET SPEAKER AVATAR -->

                <!-- CHAT WIDGET SPEAKER MESSAGE -->
                <p class="chat-widget-speaker-message"><?php echo $comment; ?></p>
                <!-- /CHAT WIDGET SPEAKER MESSAGE -->

                <!-- CHAT WIDGET SPEAKER TIMESTAMP -->
                <p class="chat-widget-speaker-timestamp"><?php echo $time_cmt; ?></p>
                <!-- /CHAT WIDGET SPEAKER TIMESTAMP -->
              </div>
              <!-- /CHAT WIDGET SPEAKER -->
 <?php } ?>
 <?php } ?>
 <?php }else{ echo "404"; }
        }else{ echo "404"; }
         ?>