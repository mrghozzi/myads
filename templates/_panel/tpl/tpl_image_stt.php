<?PHP

#####################################################################
##                                                                 ##
##                        My ads v3.0.5(+)                         ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2023                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################
if($s_st=="buyfgeufb"){ 

 //  Get Browser
function tpl_image_stt($sutcat,$Suggestion)
{
global  $db_con; global  $catsum;  global  $uRow; global $lang; global $url_site; global $title_s; global $hachadmin;
$catusz = $db_con->prepare("SELECT *  FROM `forum` WHERE   id=".$sutcat['tp_id'] );
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);
$comtxt  = nl2br($sucat['txt']);
$comtxt  = strip_tags($comtxt, '<br>');
$comtxt = preg_replace('/#([^\s]+)/', '<a  href="'.$url_site.'/tag/$1" >#$1</a>', $comtxt );
$imgtxt = strip_tags($comtxt, '');

$st_type = "2";
$dir_text=substr($comtxt,0,340);
$catdid=$sucat['id'];
$catus = $db_con->prepare("SELECT *  FROM users WHERE  id='{$sucat['uid']}'");
$catus->execute();
$catuss=$catus->fetch(PDO::FETCH_ASSOC);
$image_post = "image_post" ;
$catusc = $db_con->prepare("SELECT *  FROM options WHERE  ( o_type='{$image_post}' AND o_parent='{$sucat['id']}' ) ");
$catusc->execute();
$catussc=$catusc->fetch(PDO::FETCH_ASSOC);
$catdnb = $db_con->prepare("SELECT  COUNT(id) as nbr FROM status WHERE tp_id='{$catdid}' AND s_type=4 " );
$catdnb->execute();
$abdnb=$catdnb->fetch(PDO::FETCH_ASSOC);
$share_nbr = $abdnb['nbr']-1;
if($share_nbr==0){ $share_nbr=""; }
$catdnbcm = $db_con->prepare("SELECT  COUNT(id) as nbr FROM f_coment WHERE tid='{$catdid}' " );
$catdnbcm->execute();
$abdcmnt=$catdnbcm->fetch(PDO::FETCH_ASSOC);
$likenbcm = $db_con->prepare("SELECT  COUNT(id) as nbr FROM `like` WHERE sid='{$catdid}' AND  type=2 " );
$likenbcm->execute();
$abdlike=$likenbcm->fetch(PDO::FETCH_ASSOC);
$likeuscm = $db_con->prepare("SELECT  * FROM `like` WHERE uid='{$uRow['id']}' AND sid='{$catdid}' AND  type=2 " );
$likeuscm->execute();
$uslike=$likeuscm->fetch(PDO::FETCH_ASSOC);

if(isset($uslike) AND ($uslike['sid']==$catdid)){
$o_parent = $uslike['id'];
$o_type   = "data_reaction";
$likeuscmr = $db_con->prepare("SELECT  * FROM `options` WHERE o_order='{$uRow['id']}' AND o_parent='{$o_parent}' AND  o_type='{$o_type}' " );
$likeuscmr->execute();
$usliker=$likeuscmr->fetch(PDO::FETCH_ASSOC);
 if(isset($usliker)  AND ($usliker['o_parent']==$o_parent)){
$reaction_img  = "<img class=\"reaction-option-image\" src=\"{$url_site}/templates/_panel/img/reaction/{$usliker['o_valuer']}.png\"  width=\"30\" alt=\"reaction-{$usliker['o_valuer']}\">";
$reaction_name = $usliker['o_valuer'];
     if($usliker['o_valuer']=="like"){         $reaction_color = "style=\"color: #1bc8db;\""; }
     else if($usliker['o_valuer']=="love"){    $reaction_color = "style=\"color: #fc1f3b;\""; }
     else if($usliker['o_valuer']=="dislike"){ $reaction_color = "style=\"color: #3f3cf8;\""; }
     else if($usliker['o_valuer']=="sad"){     $reaction_color = "style=\"color: #139dff;\""; }
     else if($usliker['o_valuer']=="angry"){   $reaction_color = "style=\"color: #fa690e;\""; }
     else if($usliker['o_valuer']=="happy"){   $reaction_color = "style=\"color: #ffda21;\""; }
     else if($usliker['o_valuer']=="funny"){   $reaction_color = "style=\"color: #ffda21;\""; }
     else if($usliker['o_valuer']=="wow"){     $reaction_color = "style=\"color: #ffda21;\""; }
     else {                                    $reaction_color = "style=\"color: #ffda21;\""; }
 }else{
$reaction_img   = "<img class=\"reaction-option-image\" src=\"{$url_site}/templates/_panel/img/reaction/like.png\"  width=\"30\" alt=\"reaction-like\">";
$reaction_color = "style=\"color: #1bc8db;\"";
$reaction_name  = "like";
 }
}

$time_stt=convertTime($sutcat['date']);


$namesher =  "{$sucat['name']} - {$title_s}";
$namesher = strip_tags($namesher, '');
$linksher =  "{$url_site}/t{$catdid}";
$linksher = strip_tags($linksher, '');

if($sucat['uid']==$sutcat['uid']){
  $usecho = "";
      }else{
$catrus = $db_con->prepare("SELECT *  FROM users WHERE  id='{$sutcat['uid']}'");
$catrus->execute();
$catruss=$catrus->fetch(PDO::FETCH_ASSOC);
$usecho =  "<b>{$catruss['username']}</b> <i class=\"fa fa-retweet\" aria-hidden=\"true\"></i>   " ;
}     ?>
 <div class="widget-box no-padding post<?php echo $sutcat['id']; ?>">
          <!-- WIDGET BOX SETTINGS -->
          <div class="widget-box-settings">
            <!-- POST SETTINGS WRAP -->
            <div class="post-settings-wrap" style="position: relative;">
              <!-- POST SETTINGS -->
              <div class="post-settings widget-box-post-settings-dropdown-trigger">
                <!-- POST SETTINGS ICON -->
                <svg class="post-settings-icon icon-more-dots">
                  <use xlink:href="#svg-more-dots"></use>
                </svg>
                <!-- /POST SETTINGS ICON -->
              </div>
              <!-- /POST SETTINGS -->

              <!-- SIMPLE DROPDOWN -->
              <div class="simple-dropdown widget-box-post-settings-dropdown" >
<?php if(((isset($uRow['id'])AND isset($sucat['uid']) AND ($uRow['id']==$sucat['uid'])) OR (isset($_COOKIE['admin'])  AND ($_COOKIE['admin']==$hachadmin) )) AND ($Suggestion==0)){ ?>
              <!-- SIMPLE DROPDOWN LINK -->
                <p class="simple-dropdown-link post_edit<?php echo $sucat['id']; ?>"><i class="fa fa-edit" aria-hidden="true"></i>&nbsp;<?php echo $lang['edit']; ?></p>
                <!-- /SIMPLE DROPDOWN LINK -->
<?php } ?>
<?php if(((isset($uRow['id']) AND ($uRow['id']==$sucat['uid'])) OR (isset($uRow['id']) AND ($uRow['id']==$sutcat['uid'])) OR (isset($_COOKIE['admin']) AND ($_COOKIE['admin']== $hachadmin))) AND ($Suggestion==0)){ ?>
                <!-- SIMPLE DROPDOWN LINK -->
                <p class="simple-dropdown-link post_delete<?php echo $sutcat['id']; ?>" ><i class="fa fa-trash" aria-hidden="true"></i>&nbsp;<?php echo $lang['delete']; ?></p>
                <!-- /SIMPLE DROPDOWN LINK -->
<?php } ?>
                <!-- SIMPLE DROPDOWN LINK -->
                <p class="simple-dropdown-link post_report<?php echo $sucat['id']; ?>"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;<?php echo $lang['report']; ?></p>
                <!-- /SIMPLE DROPDOWN LINK -->

                <!-- SIMPLE DROPDOWN LINK -->
                <p class="simple-dropdown-link author_report<?php echo $sucat['id']; ?>"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;<?php echo $lang['report']; ?> <?php echo $lang['author']; ?></p>
                <!-- /SIMPLE DROPDOWN LINK -->
              </div>
              <!-- /SIMPLE DROPDOWN -->
            </div>
            <!-- /POST SETTINGS WRAP -->
          </div>
          <!-- /WIDGET BOX SETTINGS -->

          <!-- WIDGET BOX STATUS -->
          <div class="widget-box-status">
            <!-- WIDGET BOX STATUS CONTENT -->
            <div class="widget-box-status-content">
<?php
echo"                      <!-- USER STATUS -->
                      <div class=\"user-status\">
                        <!-- USER STATUS AVATAR -->
                        <a class=\"user-status-avatar\" href=\"{$url_site}/u/{$sucat['uid']}\">
                          <!-- USER AVATAR -->
                          <div class=\"user-avatar small no-outline "; online_us($catuss['id']); echo " \">
                            <!-- USER AVATAR CONTENT -->
                            <div class=\"user-avatar-content\">
                              <!-- HEXAGON -->
                              <div class=\"hexagon-image-30-32\" data-src=\"{$url_site}/{$catuss['img']}\" ><canvas ></canvas></div>
                              <!-- /HEXAGON -->
                            </div>
                            <!-- /USER AVATAR CONTENT -->

                            <!-- /USER AVATAR PROGRESS -->

                            <!-- USER AVATAR PROGRESS BORDER -->
                            <div class=\"user-avatar-progress-border\">
                              <!-- HEXAGON -->
                              <div class=\"hexagon-border-40-44\" ></div>
                              <!-- /HEXAGON -->
                            </div>
                            <!-- /USER AVATAR PROGRESS BORDER -->  ";
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
echo                 " </div>
                          <!-- /USER AVATAR -->
                        </a>
                        <!-- /USER STATUS AVATAR -->

                        <!-- USER STATUS TITLE -->
                        <p class=\"user-status-title medium\">
                        <a class=\"bold\" href=\"{$url_site}/u/{$catuss['id']}\">{$catuss['username']}</a>
                        &nbsp;{$lang['add_photo']}
                        </p>
                        <!-- /USER STATUS TITLE -->

                        <!-- USER STATUS TEXT -->
                        <p class=\"user-status-text small\">
                        <i class=\"fa fa-clock-o\" ></i>&nbsp;{$lang['ago']}&nbsp; {$time_stt}";
                        if($Suggestion==1){ echo "&nbsp;<i class=\"fa fa-random\" aria-hidden=\"true\"></i>&nbsp;Suggestion"; }
                        else if($Suggestion==2){ echo "&nbsp;<i class=\"fa fa-bullhorn\" aria-hidden=\"true\"></i>&nbsp;Sponsoring"; }
echo                   "</p>
                        <!-- /USER STATUS TEXT -->
                      </div>
                      <!-- /USER STATUS -->
                    ";
?>

              <!-- WIDGET BOX STATUS TEXT -->
              <p class="widget-box-status-text post_text<?php echo $sucat['id']; ?>">
              <div class="textpost"  id="post_form<?php echo $sucat['id']; ?>" >
              <?php echo $comtxt; ?>
              <div id="report<?php echo $sucat['id']; ?>" ></div>
              </div></p>
              <!-- /WIDGET BOX STATUS TEXT -->
              <style> .post-box-picture img { margin-top: 24px; width: 100%; height: auto;   border-radius: 12px; } </style>
              <a class="post-box-picture" href="<?php echo $url_site; ?>/t<?php echo $sucat['id']; ?>">
               <img  src="<?php echo $catussc['o_valuer'];  ?>" alt="<?php echo $imgtxt;  ?>">
              </a>
              <!-- CONTENT ACTIONS -->
              <div class="content-actions">
                <!-- CONTENT ACTION -->
                <div class="content-action">
                <?php

                 if(isset($_COOKIE['user'])){

                 include "templates/_panel/status/reaction_list.php";

                 }

                 ?>
                  <!-- META LINE -->
                  <div class="meta-line">
                    <!-- META LINE TEXT -->
                    <p class="meta-line-text"><?php echo $abdlike['nbr']; ?> <?php echo $lang['reactions']; ?></p>
                    <!-- /META LINE TEXT -->
                  </div>
                  <!-- /META LINE -->
                </div>
                <!-- /CONTENT ACTION -->

                <!-- CONTENT ACTION -->
                <div class="content-action">
                  <!-- META LINE -->
                  <div class="meta-line">
                    <!-- META LINE LINK -->
                    <p class="meta-line-link">
                    <a href="<?php echo $url_site; ?>/t<?php echo $sucat['id']; ?>"><?php echo $abdcmnt['nbr']; ?> <?php echo $lang['comments']; ?></a>
                    </p>
                    <!-- /META LINE LINK -->
                  </div>
                  <!-- /META LINE -->

                </div>
                <!-- /CONTENT ACTION -->
              </div>
              <!-- /CONTENT ACTIONS -->
            </div>
            <!-- /WIDGET BOX STATUS CONTENT -->
          </div>
          <!-- /WIDGET BOX STATUS -->

          <!-- POST OPTIONS -->
          <div class="post-options">
<?php   if(isset($_COOKIE['user'])){ ?>
            <!-- POST OPTION WRAP -->
            <div class="post-option-wrap" >
              <!-- POST OPTION -->
              <div class="post-option reaction-options-dropdown-trigger">
              <div id="reaction_image<?php echo $sutcat['id']; ?>" >
<?php if($uslike['sid']==$catdid){  ?>
                <?php echo $reaction_img;  ?>
<?php }else{ ?>
              <!-- POST OPTION ICON -->
                <svg class="post-option-icon icon-thumbs-up">
                  <use xlink:href="#svg-thumbs-up" ></use>
                </svg>
                <!-- /POST OPTION ICON -->
<?php } ?>
                </div>
                <!-- POST OPTION TEXT -->
                <p class="post-option-text reaction_txt<?php echo $sutcat['id']; ?>" <?php if($uslike['sid']==$catdid){ echo $reaction_color; } ?> >
                &nbsp;<?php if($uslike['sid']==$catdid){ echo $reaction_name; }else{ echo $lang['react']; } ?></p>
                <!-- /POST OPTION TEXT -->
              </div>
              <!-- /POST OPTION -->

              <!-- REACTION OPTIONS -->
              <div class="reaction-options reaction-options-dropdown" >
                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft reaction_4_<?php echo $sucat['id']; ?>" data-title="like" >
                  <!-- REACTION OPTION IMAGE -->
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/reaction/like.png" alt="reaction-like">
                  <!-- /REACTION OPTION IMAGE -->
                </div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft reaction_4_<?php echo $sucat['id']; ?>" data-title="love" >
                  <!-- REACTION OPTION IMAGE -->
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/reaction/love.png" alt="reaction-love">
                  <!-- /REACTION OPTION IMAGE -->
                </div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft reaction_4_<?php echo $sucat['id']; ?>" data-title="dislike" >
                  <!-- REACTION OPTION IMAGE -->
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/reaction/dislike.png" alt="reaction-dislike">
                  <!-- /REACTION OPTION IMAGE -->
                </div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft reaction_4_<?php echo $sucat['id']; ?>" data-title="happy" >
                  <!-- REACTION OPTION IMAGE -->
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/reaction/happy.png" alt="reaction-happy">
                  <!-- /REACTION OPTION IMAGE -->
                </div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft reaction_4_<?php echo $sucat['id']; ?>" data-title="funny" >
                  <!-- REACTION OPTION IMAGE -->
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/reaction/funny.png" alt="reaction-funny">
                  <!-- /REACTION OPTION IMAGE -->
                </div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft reaction_4_<?php echo $sucat['id']; ?>" data-title="wow" >
                  <!-- REACTION OPTION IMAGE -->
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/reaction/wow.png" alt="reaction-wow">
                  <!-- /REACTION OPTION IMAGE -->
                </div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft reaction_4_<?php echo $sucat['id']; ?>" data-title="angry" >
                  <!-- REACTION OPTION IMAGE -->
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/reaction/angry.png" alt="reaction-angry">
                  <!-- /REACTION OPTION IMAGE -->
                </div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft reaction_4_<?php echo $sucat['id']; ?>" data-title="sad" >
                  <!-- REACTION OPTION IMAGE -->
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/reaction/sad.png" alt="reaction-sad">
                  <!-- /REACTION OPTION IMAGE -->
                </div>
                <!-- /REACTION OPTION -->
              </div>
              <!-- /REACTION OPTIONS -->
            </div>
            <!-- /POST OPTION WRAP -->
<?php } ?>
<?php   if(isset($_COOKIE['user'])){ ?>
            <!-- POST OPTION -->
            <div  class="post-option sh_comment_i<?php echo $sutcat['id']; ?>">
              <!-- POST OPTION ICON -->
              <svg class="post-option-icon icon-comment">
                <use xlink:href="#svg-comment"></use>
              </svg>
              <!-- /POST OPTION ICON -->
              <!-- POST OPTION TEXT -->
              <p class="post-option-text"><?php echo $lang['comment']; ?></p>
              <!-- /POST OPTION TEXT -->
            </div>
            <!-- /POST OPTION -->
<?php } ?>
            <!-- POST OPTION -->
            <div class="post-option-wrap" >
              <!-- POST OPTION -->
              <div class="post-option reaction-options-dropdown-trigger">
                <!-- POST OPTION ICON -->
                <svg class="post-option-icon icon-share">
                  <use xlink:href="#svg-share"></use>
                </svg>
                <!-- /POST OPTION ICON -->

                <!-- POST OPTION TEXT -->
                <p class="post-option-text"><?php echo $lang['share']; ?></p>
                <!-- /POST OPTION TEXT -->
              </div>
              <!-- /POST OPTION -->

              <!-- REACTION OPTIONS -->
              <div class="reaction-options reaction-options-dropdown" >
                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft" data-title="facebook" >
                  <!-- REACTION OPTION IMAGE -->
                  <a onClick="window.open('https://www.facebook.com/sharer/sharer.php?u=<?php echo $linksher; ?>');" href="javascript:void(0);" >
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/icons/facebook-icon.png" >
                  </a>
                  <!-- /REACTION OPTION IMAGE -->
                </div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft" data-title="twitter" >
                  <!-- REACTION OPTION IMAGE -->
                  <a onClick="window.open('https://twitter.com/intent/tweet?text=<?php echo $namesher; ?>&url=<?php echo $linksher; ?>');" href="javascript:void(0);" >
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/icons/twitter-icon.png" >
                  </a>
                  <!-- /REACTION OPTION IMAGE -->
                </div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft" data-title="linkedin" >
                  <!-- REACTION OPTION IMAGE -->
                  <a onClick="window.open('https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $linksher; ?>');" href="javascript:void(0);" >
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/icons/linkedin-icon.png" >
                  </a>
                  <!-- /REACTION OPTION IMAGE -->
                </div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft" data-title="wasp" >
                  <!-- REACTION OPTION IMAGE -->
                  <a onClick="window.open('https://www.wasp.gq/sharer?url=<?php echo $linksher; ?>&nbsp;<?php echo $namesher; ?>');" href="javascript:void(0);" >
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/icons/wasp-icon.png" >
                  </a>
                  <!-- /REACTION OPTION IMAGE -->
                </div>
                <!-- /REACTION OPTION -->

               </div>
              <!-- /REACTION OPTIONS -->
            </div>
            <!-- /POST OPTION -->
          </div>
<div class="post-comment-list comment_4_<?php echo $sucat['id']; ?>" ></div>
<script>$('.post_edit<?php echo $sucat['id']; ?>').click(function(){
  $("#post_form<?php echo $sucat['id']; ?>").load('<?php url_site();  ?>/templates/_panel/status/post_edit.php?s_type=4&tid=<?php echo $sucat['id']; ?>');
        });
</script>
<script>$('.post_report<?php echo $sucat['id']; ?>').click(function(){
  $("#report<?php echo $sucat['id']; ?>").load('<?php url_site();  ?>/templates/_panel/status/post_report.php?s_type=4&tid=<?php echo $sucat['id']; ?>');
        });
</script>
<script>$('.author_report<?php echo $sucat['id']; ?>').click(function(){
  $("#report<?php echo $sucat['id']; ?>").load('<?php url_site();  ?>/templates/_panel/status/post_report.php?s_type=4&tid=<?php echo $sucat['id']; ?>&a_type=99');
        });
</script>
<script>$('.post_delete<?php echo $sutcat['id']; ?>').click(function(){
  $("#post_form<?php echo $sucat['id']; ?>").load('<?php url_site();  ?>/templates/_panel/status/post_delete.php?sid=<?php echo $sutcat['id']; ?>');
        });
</script>
<script>$('.sh_comment_i<?php echo $sutcat['id']; ?>').click(function(){
  $(".comment_4_<?php echo $sucat['id']; ?>").load('<?php url_site();  ?>/templates/_panel/status/post_comment.php?s_type=4&tid=<?php echo $sucat['id']; ?>');
  $(".sh_comment_i<?php echo $sutcat['id']; ?>").addClass('active');
    });
</script>
<script>
     $("document").ready(function() {
   $(".reaction_4_<?php echo $sucat['id']; ?>").click(postreaction<?php echo $sutcat['id']; ?>);

});
function postreaction<?php echo $sutcat['id']; ?>(){
    var data_reaction = $(this).attr("data-title");
   $.ajax({
type: "POST",
url: "<?php url_site();  ?>/requests/f_like.php?id=<?php echo $sucat['id']; ?>&f_like=like_up&t=f",
data: "data_reaction=" + data_reaction,
success: function (response) {
// This code will run after the Ajax is successful
$("#reaction_image<?php echo $sutcat['id']; ?>").html(response);
$(".reaction_txt<?php echo $sutcat['id']; ?>").html("");

}
})
}
</script>
          <!-- /POST OPTIONS -->
        </div>
<?php
}

}else{ echo"404"; }   
 ?>