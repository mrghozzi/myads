<?php
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

// online user
function online_us($id)
{
  global  $db_con ;
 $bn_online = time()-240;

$bncount = $db_con->prepare("SELECT  * FROM users WHERE id = :id ");
$bncount->bindParam(":id", $id);
$bncount->execute();
$abbn=$bncount->fetch(PDO::FETCH_ASSOC);
$bn_online = time()-240;
if($abbn['online']>$bn_online){
  echo "online";
}
}

//  check_user
function check_us($id,$yesno = false)
{
  global  $db_con ;
$bncount = $db_con->prepare("SELECT  * FROM users WHERE id = :id ");
$bncount->bindParam(":id", $id);
$bncount->execute();
$abbn=$bncount->fetch(PDO::FETCH_ASSOC);
if(isset($yesno) AND ($yesno==1)){
 return  $abbn['ucheck'];
}else if($abbn['ucheck']=="1"){
  echo "<i class=\"fa fa-fw fa-check-circle\" style=\"color: #0066CC;\" ></i>";
}
}

if(isset($_GET['tid']))   { $bn_id = $_GET['tid'];   }

  if(isset($_GET['limet']))   { $comentlimet = $_GET['limet']+5;   }
  else{
    if(isset($_GET['s_type']) AND ($_GET['s_type']==2)){ $comentlimet = 10; }
    else{   $comentlimet = 5; }
  }


if(isset($_GET['s_type']) AND (($_GET['s_type']==100) OR ($_GET['s_type']==4) OR ($_GET['s_type']==2) OR ($_GET['s_type']==7867))){
 $s_type ="f_coment";
 $d_type ="f_coment";
  $o_type ="tid='{$bn_id}'";
}else if(isset($_GET['s_type']) AND ($_GET['s_type']==1)){
 $s_type ="options";
 $d_type ="d_coment";
 $o_type ="o_parent='{$bn_id}' AND o_type='$d_type'";
}

?>

<?php
$statement = "`{$s_type}` WHERE {$o_type} ORDER BY `id` DESC";
$catsum = $db_con->prepare("SELECT  * FROM {$statement} LIMIT {$comentlimet} ");
$catsum->execute();
while($sutcat=$catsum->fetch(PDO::FETCH_ASSOC))
{
 if(isset($s_type) AND ($s_type=="f_coment")){ $cmnt_us=$sutcat['uid']; }else if(isset($s_type) AND ($s_type=="options")){ $cmnt_us=$sutcat['o_order']; }
$catuscm = $db_con->prepare("SELECT *  FROM users WHERE  id='{$cmnt_us}'");
$catuscm->execute();
$catusscm=$catuscm->fetch(PDO::FETCH_ASSOC);
 if(isset($s_type) AND ($s_type=="f_coment")){
$time_cmt=convertTime($sutcat['date']);
$comment =  $sutcat['txt'] ;
 }else if(isset($s_type) AND ($s_type=="options")){
$time_cmt=convertTime($sutcat['o_mode']);
$comment =  $sutcat['o_valuer'] ;
 }

$comment = preg_replace('/ #([^\s]+) /', '<a  href="'.$url_site.'/tag/$1" >#$1</a>', $comment );
$comment = strip_tags($comment, '<p><a><b><br><li><ul><font><span><pre><u><s><img>');
  ?>
            <!-- POST COMMENT -->
            <div class="post-comment coment<?php echo $sutcat['id'];  ?>">
              <!-- USER AVATAR -->
              <a class="user-avatar small no-outline <?php online_us($catusscm['id']); ?>" href="<?php echo "{$url_site}/u/{$catusscm['id']}"; ?>">
                <!-- USER AVATAR CONTENT -->
                <div class="user-avatar-content ">
                  <!-- HEXAGON -->
                  <div class="hexagon-image-30-32" data-src="<?php echo "{$url_site}/{$catusscm['img']}"; ?>" style="width: 30px; height: 32px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="30" height="32"></canvas></div>
                  <!-- /HEXAGON -->
                </div>
                <!-- /USER AVATAR CONTENT -->

                <!-- USER AVATAR PROGRESS BORDER -->
                <div class="user-avatar-progress-border">
                  <!-- HEXAGON -->
                  <div class="hexagon-border-40-44" style="width: 40px; height: 44px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="40" height="44"></canvas></div>
                  <!-- /HEXAGON -->
                </div>
                <!-- /USER AVATAR PROGRESS BORDER -->
 <?php  if(check_us($catusscm['id'],1)==1){  ?>
                <!-- USER AVATAR BADGE -->
                <div class="user-avatar-badge">
                  <!-- USER AVATAR BADGE BORDER -->
                  <div class="user-avatar-badge-border">
                    <!-- HEXAGON -->
                    <div class="hexagon-22-24" style="width: 22px; height: 24px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="22" height="24"></canvas></div>
                    <!-- /HEXAGON -->
                  </div>
                  <!-- /USER AVATAR BADGE BORDER -->

                  <!-- USER AVATAR BADGE CONTENT -->
                  <div class="user-avatar-badge-content">
                    <!-- HEXAGON -->
                    <div class="hexagon-dark-16-18" style="width: 16px; height: 18px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="16" height="18"></canvas></div>
                    <!-- /HEXAGON -->
                  </div>
                  <!-- /USER AVATAR BADGE CONTENT -->

                  <!-- USER AVATAR BADGE TEXT -->
                  <p class="user-avatar-badge-text"><i class="fa fa-fw fa-check" ></i></p>
                  <!-- /USER AVATAR BADGE TEXT -->
                </div>
<?php } ?>
                <!-- /USER AVATAR BADGE -->
              </a>
              <!-- /USER AVATAR -->

              <!-- POST COMMENT TEXT -->
              <p class="post-comment-text">
              <a class="post-comment-text-author" href="<?php echo "{$url_site}/u/{$catusscm['id']}"; ?>"><?php echo $catusscm['username']; ?></a>
              <?php echo $comment; ?>
              </p>
              <!-- /POST COMMENT TEXT -->

              <!-- CONTENT ACTIONS -->
              <div class="content-actions">
                <!-- CONTENT ACTION -->
                <div class="content-action">

                  <!-- META LINE -->
                  <div class="meta-line">
                    <!-- META LINE TIMESTAMP -->
                    <p class="meta-line-timestamp"><?php echo $time_cmt; ?></p>
                    <!-- /META LINE TIMESTAMP -->
                  </div>
                  <!-- /META LINE -->
  <?php if((isset($_COOKIE['user']) AND ($_COOKIE['user']==$cmnt_us) ) OR ((isset($_COOKIE['admin'])))){  ?>
                  <!-- META LINE -->
                  <div class="meta-line trash_comment<?php echo $sutcat['id'];  ?>" id="btntrash<?php echo $sutcat['id'];  ?>">
                    <!-- META LINE TIMESTAMP -->
                    <input type="hidden" id="trashid<?php echo $sutcat['id']; ?>" value="<?php echo $sutcat['id'];  ?>'>" />
                    <a class="meta-line-timestamp" >
                       <!-- ICON DELETE -->
                       <i class="fa fa-trash" aria-hidden="true"></i>
                       <!-- /ICON DELETE -->
                    </a>
                    <!-- /META LINE TIMESTAMP -->
                  </div>
                  <!-- /META LINE -->
<?php }  ?>
                </div>
                <!-- /CONTENT ACTION -->
              </div>
              <!-- /CONTENT ACTIONS -->
            </div>
            <!-- /POST COMMENT -->
  <script>
     $("document").ready(function() {
   $("#btntrash<?php echo $sutcat['id'];  ?>").click(btntrashComent<?php echo $sutcat['id'];  ?>);
});

function btntrashComent<?php echo $sutcat['id'];  ?>(){
    var trashid<?php echo $sutcat['id']; ?> = $("#trashid<?php echo $sutcat['id']; ?>").val();
    $(".trash_comment<?php echo $sutcat['id'];  ?>").html("trash comment ...");
    $.ajax({
        url : '<?php echo $url_site;  ?>/requests/<?php echo $d_type; ?>.php?trash=<?php echo $sutcat['id'];  ?>',
        data : {
            trashid : trashid<?php echo $sutcat['id']; ?>
        },
        datatype : "json",
        type : 'post',
        success : function(result) {
                $(".coment<?php echo $sutcat['id'];  ?>").html("");
        },
        error : function() {
            alert("Error reaching the server. Check your connection");
        }
    });
}
     </script>
<?php }  ?>
            <div class="comment_form<?php echo $bn_id; ?>"></div>
            <!-- POST COMMENT HEADING -->
            <p class="post-comment-heading comment_heading<?php echo $bn_id; ?>"><?php echo $lang['LMComments']; ?> <span class="highlighted">+</span></p>
            <!-- /POST COMMENT HEADING -->
<?php   if(isset($_COOKIE['user'])){  ?>
            <!-- POST COMMENT FORM -->
            <div class="post-comment-form">
<?php
$catuscm = $db_con->prepare("SELECT *  FROM users WHERE  id='{$_COOKIE['user']}'");
$catuscm->execute();
$catusscm=$catuscm->fetch(PDO::FETCH_ASSOC);
?>
              <!-- USER AVATAR -->
              <a class="user-avatar small no-outline <?php online_us($catusscm['id']); ?>" href="<?php echo "{$url_site}/u/{$catusscm['id']}"; ?>">
                <!-- USER AVATAR CONTENT -->
                <div class="user-avatar-content ">
                  <!-- HEXAGON -->
                  <div class="hexagon-image-30-32" data-src="<?php echo "{$url_site}/{$catusscm['img']}"; ?>" style="width: 30px; height: 32px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="30" height="32"></canvas></div>
                  <!-- /HEXAGON -->
                </div>
                <!-- /USER AVATAR CONTENT -->

                <!-- USER AVATAR PROGRESS BORDER -->
                <div class="user-avatar-progress-border">
                  <!-- HEXAGON -->
                  <div class="hexagon-border-40-44" style="width: 40px; height: 44px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="40" height="44"></canvas></div>
                  <!-- /HEXAGON -->
                </div>
                <!-- /USER AVATAR PROGRESS BORDER -->
 <?php  if(check_us($catusscm['id'],1)==1){  ?>
                <!-- USER AVATAR BADGE -->
                <div class="user-avatar-badge">
                  <!-- USER AVATAR BADGE BORDER -->
                  <div class="user-avatar-badge-border">
                    <!-- HEXAGON -->
                    <div class="hexagon-22-24" style="width: 22px; height: 24px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="22" height="24"></canvas></div>
                    <!-- /HEXAGON -->
                  </div>
                  <!-- /USER AVATAR BADGE BORDER -->

                  <!-- USER AVATAR BADGE CONTENT -->
                  <div class="user-avatar-badge-content">
                    <!-- HEXAGON -->
                    <div class="hexagon-dark-16-18" style="width: 16px; height: 18px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="16" height="18"></canvas></div>
                    <!-- /HEXAGON -->
                  </div>
                  <!-- /USER AVATAR BADGE CONTENT -->

                  <!-- USER AVATAR BADGE TEXT -->
                  <p class="user-avatar-badge-text"><i class="fa fa-fw fa-check" ></i></p>
                  <!-- /USER AVATAR BADGE TEXT -->
                </div>
<?php } ?>
                <!-- /USER AVATAR BADGE -->
              </a>
              <!-- /USER AVATAR -->
              <!-- FORM -->
               <div class="form" >
                <!-- FORM ROW -->
                <div class="form-row">
                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small">
                      <input type="text" id="txt_comment<?php echo $bn_id; ?>">
                      <button id ="btn_comment<?php echo $bn_id; ?>" class="btn" >
                       <svg class="interactive-input-icon icon-send-message">
                        <use xlink:href="#svg-send-message"></use>
                       </svg>
                      </button>
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
                  <!-- /FORM ITEM -->
                </div>
                <!-- /FORM ROW -->

              <!-- /FORM -->
            </div>
            <!-- /POST COMMENT FORM -->
            </div>
<?php } ?>
<script src="<?php echo $url_site;  ?>/templates/_panel/js/global.hexagons.js"></script>
<?php   if(isset($_COOKIE['user'])){  ?>
<script>
     $("document").ready(function() {
   $("#btn_comment<?php echo $bn_id; ?>").click(postcomment<?php echo $bn_id; ?>);


});

function postcomment<?php echo $bn_id; ?>(){
  var txt<?php echo $bn_id; ?> = $("#txt_comment<?php echo $bn_id; ?>").val();
    $(".comment_form<?php echo $bn_id; ?>").html("posting edit ...<div id='report<?php echo $bn_id; ?>' ></div>");

    $.ajax({
        url : '<?php echo $url_site;  ?>/requests/<?php echo $d_type; ?>.php?id=<?php echo $bn_id; ?>',
        data : {
            comment : txt<?php echo $bn_id; ?>
        },
        datatype : "json",
        type : 'post',
        success : function(result) {

                $(".comment_form<?php echo $bn_id; ?>").html(result);
        },
        error : function() {
            alert("Error reaching the server. Check your connection");
        }
    });
}
</script>
<?php } ?>
<script>$('.comment_heading<?php echo $bn_id; ?>').click(function(){
  $(".comment_<?php echo $_GET['s_type']; ?>_<?php echo $bn_id; ?>").load('<?php echo $url_site;  ?>/templates/_panel/status/post_comment.php?s_type=<?php echo $_GET['s_type']; ?>&tid=<?php echo $bn_id; ?>&limet=<?php echo $comentlimet; ?>');
    });
</script>
