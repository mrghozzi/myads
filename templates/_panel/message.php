
<?php if($s_st=="buyfgeufb"){
$msgdid = $uRow['id'];
$msgeid = $_GET['m'];
$catus = $db_con->prepare("SELECT *  FROM users WHERE  id='{$_GET['m']}'");
$catus->execute();
$catuss=$catus->fetch(PDO::FETCH_ASSOC);  ?>
<div class="account-hub-content">
        <!-- SECTION HEADER -->
        <div class="section-header">
          <!-- SECTION HEADER INFO -->
          <div class="section-header-info">
            <!-- SECTION PRETITLE -->
            <p class="section-pretitle">My Profile</p>
            <!-- /SECTION PRETITLE -->

            <!-- SECTION TITLE -->
            <h2 class="section-title">Messages</h2>
            <!-- /SECTION TITLE -->
          </div>
          <!-- /SECTION HEADER INFO -->

        </div>
        <!-- /SECTION HEADER -->

        <!-- CHAT WIDGET WRAP -->
        <div class="chat-widget-wrap">
          <!-- CHAT WIDGET -->
          <div class="chat-widget" style="width: 100%;">
            <!-- CHAT WIDGET HEADER -->
            <div class="chat-widget-header">
              <!-- USER STATUS -->
              <div class="user-status">
                <!-- USER STATUS AVATAR -->
                <div class="user-status-avatar">
                  <!-- USER AVATAR -->
                  <div class="user-avatar small no-outline <?php online_us($catuss['id']); ?>">
                    <!-- USER AVATAR CONTENT -->
                    <div class="user-avatar-content">
                      <!-- HEXAGON -->
                      <div class="hexagon-image-30-32" data-src="<?php url_site();  ?>/<?php echo $catuss['img']; ?>" ><canvas width="30" height="32"></canvas></div>
                      <!-- /HEXAGON -->
                    </div>
                    <!-- /USER AVATAR CONTENT -->

                    <!-- USER AVATAR PROGRESS BORDER -->
                    <div class="user-avatar-progress-border">
                      <!-- HEXAGON -->
                      <div class="hexagon-border-40-44" ><canvas width="40" height="44"></canvas></div>
                      <!-- /HEXAGON -->
                    </div>
                    <!-- /USER AVATAR PROGRESS BORDER -->
<?php
if(check_us($catuss['id'],1)==1){
 echo                   " <!-- USER AVATAR BADGE -->
                            <div class=\"user-avatar-badge\">
                              <!-- USER AVATAR BADGE BORDER -->
                              <div class=\"user-avatar-badge-border\">
                                <!-- HEXAGON -->
                                <div class=\"hexagon-22-24\" ></div>
                                <!-- /HEXAGON -->
                              </div>
                              <!-- /USER AVATAR BADGE BORDER -->

                              <!-- USER AVATAR BADGE CONTENT -->
                              <div class=\"user-avatar-badge-content\">
                                <!-- HEXAGON -->
                                <div class=\"hexagon-dark-16-18\" ></div>
                                <!-- /HEXAGON -->
                              </div>
                              <!-- /USER AVATAR BADGE CONTENT -->

                              <!-- USER AVATAR BADGE TEXT -->
                              <p class=\"user-avatar-badge-text\"><i class=\"fa fa-fw fa-check\" ></i></p>
                              <!-- /USER AVATAR BADGE TEXT -->
                            </div>
                            <!-- /USER AVATAR BADGE -->       ";
                              }
?>
                </div>

                <!-- /USER STATUS AVATAR -->
                 </div>
                <!-- USER STATUS TITLE -->
                <p class="user-status-title"><span class="bold"><?php echo $catuss['username'];  ?></span></p>
                <!-- /USER STATUS TITLE -->

                <!-- USER STATUS TAG -->
                <p class="user-status-tag <?php online_us($catuss['id']); ?>"><?php online_us($catuss['id'],1); ?></p>
                <!-- /USER STATUS TAG -->
              </div>
              <!-- /USER STATUS -->
            </div>
            <!-- /CHAT WIDGET HEADER -->

            <!-- CHAT WIDGET CONVERSATION -->
            <div class="chat-widget-conversation" data-simplebar="init"><div class="simplebar-wrapper" ><div class="simplebar-height-auto-observer-wrapper"><div class="simplebar-height-auto-observer"></div></div><div class="simplebar-mask"><div class="simplebar-offset"><div class="simplebar-content-wrapper" ><div class="simplebar-content">
 <div id='new_msg'></div>
 <?php
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
                      <div class="hexagon-image-24-26" data-src="<?php url_site();  ?>/<?php echo $catussen['img']; ?>" ><canvas width="24" height="26"></canvas></div>
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

           </div></div></div></div><div class="simplebar-placeholder" ></div></div><div class="simplebar-track simplebar-horizontal" ><div class="simplebar-scrollbar" ></div></div><div class="simplebar-track simplebar-vertical" ><div class="simplebar-scrollbar" ></div></div></div>
            <!-- /CHAT WIDGET CONVERSATION -->

            <!-- CHAT WIDGET FORM -->
            <div class="chat-widget-form">
              <!-- FORM ROW -->
              <div class="form-row split">
                <!-- FORM ITEM -->
                <div class="form-item">
                  <!-- INTERACTIVE INPUT -->
                  <div class="interactive-input small">
                    <input type="text" id="comment"  placeholder="Write a message...">
                   <!-- INTERACTIVE INPUT ACTION -->
                    <div class="interactive-input-action">
                      <!-- INTERACTIVE INPUT ACTION ICON -->
                      <svg class="interactive-input-action-icon icon-cross-thin">
                        <use xlink:href="#svg-cross-thin"></use>
                      </svg>
                      <!-- /INTERACTIVE INPUT ACTION ICON -->
                    </div>
                    <!-- /INTERACTIVE INPUT ACTION -->
                  </div>
                  <!-- /INTERACTIVE INPUT -->
                </div>
                <!-- /FORM ITEM -->

                <!-- FORM ITEM -->
                <div class="form-item auto-width" id="btn">
                  <!-- BUTTON -->
                  <p class="button primary padded">
                    <!-- BUTTON ICON -->
                    <svg class="button-icon no-space icon-send-message">
                      <use xlink:href="#svg-send-message"></use>
                    </svg>
                    <!-- /BUTTON ICON -->
                  </p>
                  <!-- /BUTTON -->
                </div>
                <!-- /FORM ITEM -->
              </div>
              <!-- /FORM ROW -->
            </div>
            <!-- /CHAT WIDGET FORM -->
          </div>
          <!-- /CHAT WIDGET -->
        </div>
        <!-- /CHAT WIDGET WRAP -->
      </div>
<script>
$("document").ready(function() {
   $("#btn").click(postComent);

});

function postComent(){
    $("#new_msg").html("posting ...");
    $.ajax({
        url : '<?php url_site();  ?>/requests/msg.php?id=<?php echo $msgeid; ?>',
        data : {
            comment : $("#comment").val()
        },
        datatype : "json",
        type : 'post',
        success : function(result) {
                $("#new_msg").html(result);
                $("#comment").val("");
        },
        error : function() {
            alert("Error reaching the server. Check your connection");
        }
    });
}
     </script>

<?php }else{ echo"404"; }  ?>