<?php
if(isset($s_st) AND ($s_st=="buyfgeufb")){ dinstall_d();
  $gproducer =  $_GET['producer'];
  $stname = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'store' AND `name` ='".$gproducer."' " );
$stname->execute();
$strname=$stname->fetch(PDO::FETCH_ASSOC);
$sttid = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'store_file' AND `o_parent` =".$strname['id']." ORDER BY `o_order`  DESC " );
$sttid->execute();
$strtid=$sttid->fetch(PDO::FETCH_ASSOC);
$servictp = "store_type" ;
$catustp = $db_con->prepare("SELECT *  FROM options WHERE  ( o_type='{$servictp}' AND o_parent='{$strname['id']}' ) ");
$catustp->execute();
$catusstp=$catustp->fetch(PDO::FETCH_ASSOC);
$catusz = $db_con->prepare("SELECT *  FROM `forum` WHERE statu=1 AND  id=".$catusstp['o_order'] );
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);
if(isset($sucat['id'])) {

$catdid=$sucat['id'];
$catust = $db_con->prepare("SELECT * FROM status WHERE s_type IN (2,4,7867) AND tp_id =".$catdid );
$catust->execute();
$susat=$catust->fetch(PDO::FETCH_ASSOC);

$catus = $db_con->prepare("SELECT *  FROM users WHERE  id='{$sucat['uid']}'");
$catus->execute();
$catuss=$catus->fetch(PDO::FETCH_ASSOC);
$catusc = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'store_type' AND `o_parent` =".$strname['id'] );
$catusc->execute();
$catussc=$catusc->fetch(PDO::FETCH_ASSOC);

$catdnb = $db_con->prepare("SELECT  COUNT(id) as nbr FROM status WHERE s_type IN (2,4,7867) AND tp_id ='{$catdid}' " );
$catdnb->execute();
$abdnb=$catdnb->fetch(PDO::FETCH_ASSOC);

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

$catdnbcm = $db_con->prepare("SELECT  COUNT(id) as nbr FROM f_coment WHERE tid='{$catdid}' " );
$catdnbcm->execute();
$abdcmnt=$catdnbcm->fetch(PDO::FETCH_ASSOC);

$time_stt=convertTime($susat['date']);
$namesher =  "{$sucat['name']} - {$title_s}";
$namesher = strip_tags($namesher, '');
$linksher =  "{$url_site}/producer/{$strname['name']}";
$linksher = strip_tags($linksher, '');

$comtxt = strip_tags($sucat['txt'], '<p><a><b><br><li><ul><font><span><pre><u><s><img><iframe>');
$comtxt = preg_replace('/ #([^\s]+) /', '<a  href="'.$url_site.'/tag/$1" >#$1</a>', $comtxt );
$ndfk = $strtid['id'];

                 $sdf= $strtid['o_mode'];
                 $dir_lnk_hash = $url_site."/download/".hash('crc32', $sdf.$ndfk );
                  $contfils = 0;
                  $sttnid = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'store_file' AND `o_parent` =".$strname['id']." " );
                  $sttnid->execute();
                  while($strtnid=$sttnid->fetch(PDO::FETCH_ASSOC)){
                    $ndfkn = $strtnid['id'];
                 $stormfnb = $db_con->prepare("SELECT  clik FROM short WHERE sh_type=7867 AND tp_id=:tp_id  " );
                 $stormfnb->bindParam(":tp_id", $ndfkn);
                 $stormfnb->execute();
                 $sfilenbr=$stormfnb->fetch(PDO::FETCH_ASSOC);
                 $contfils += $sfilenbr['clik'];
                 }

                 if(isset($strname['o_order']) AND ($strname['o_order']>0)){
                   $storepts = $strname['o_order']."&nbsp;<span class=\"highlighted\">PTS</span>";
                 }else{
                    $storepts = $lang['free'];
                 }

  ?>
<style> .paragraph_producet img { margin-top: 24px; width: 75%; height: auto;   border-radius: 12px; } </style>
   <!-- SECTION BANNER -->
<div class="section-banner" style="background: url(<?php url_site();  ?>/templates/_panel/img/banner/Newsfeed.png) no-repeat 50%;" >
      <!-- SECTION BANNER ICON -->
      <img class="section-banner-icon" src="<?php url_site();  ?>/templates/_panel/img/banner/marketplace-icon.png" >
      <!-- /SECTION BANNER ICON -->

      <!-- SECTION BANNER TITLE -->
      <p class="section-banner-title"><?php lang('Store'); ?></p>
      <!-- /SECTION BANNER TITLE -->

      <!-- SECTION BANNER TEXT -->
      <p class="section-banner-text"></p>
      <!-- /SECTION BANNER TEXT -->
</div>
    <!-- /SECTION BANNER -->
<div class="section-header">
      <!-- SECTION HEADER INFO -->
      <div class="section-header-info">
        <!-- SECTION PRETITLE -->
        <p class="section-pretitle"><?php echo $catussc['name']; ?></p>
        <!-- /SECTION PRETITLE -->

        <!-- SECTION TITLE -->
        <h2 class="section-title"><?php echo $strname['name']; ?></h2>
        <!-- /SECTION TITLE -->
      </div>
      <!-- /SECTION HEADER INFO -->

      <!-- SECTION HEADER ACTIONS -->
      <div class="section-header-actions">
        <!-- SECTION HEADER SUBSECTION -->
        <a class="section-header-subsection" href="<?php url_site();  ?>/store"><?php lang('Store'); ?></a>
        <!-- /SECTION HEADER SUBSECTION -->
        <?php
 $catname = $catussc['name'];
 $catname = $lang["{$catname}"];

if(isset($catussc["name"]) AND (($catussc["name"]=="plugins") OR ($catussc["name"]=="templates"))) {
?>
           <a class="section-header-subsection" href="#"><?php echo $catname; ?></a>
<?php
if(isset($catussc['o_mode']) AND ($catussc['o_mode']=="others")){
?>
          <a class="btn btn-sm btn-warning"   ><b> <?php lang('others'); ?></b></a>
<?php
}else{

$catval   = $catussc['o_mode'];
$settps = $db_con->prepare("SELECT * FROM `options` WHERE id = :id  ");
$settps->bindParam(":id", $catval);
$settps->execute();
$tpstorRow=$settps->fetch(PDO::FETCH_ASSOC);
?>
        <a class="section-header-subsection" href="<?php url_site();  ?>/producer/<?php echo $tpstorRow['name']; ?>"><?php echo $tpstorRow['name']; ?></a>
<?php }  }else if(isset($catussc["name"]) AND ($catussc["name"]=="script")){
$scatname = $catussc['o_mode'];
$scatname = $lang["{$scatname}"];
?>
       <a class="section-header-subsection" href="#"><?php echo $catname; ?></a>
       <a class="section-header-subsection" href="#"><?php echo $scatname; ?></a>
<?php   } ?>
        <!-- SECTION HEADER SUBSECTION -->

        <!-- /SECTION HEADER SUBSECTION -->

        <!-- SECTION HEADER SUBSECTION -->
       <p class="section-header-subsection"><?php echo $strname['name']; ?></p>
        <!-- /SECTION HEADER SUBSECTION -->
      </div>
      <!-- /SECTION HEADER ACTIONS -->
</div>
<?php ads_site(5); ?>
<div class="grid grid post<?php echo $susat['id']; ?>">
     <div class="widget-box no-padding">
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
              <div class="simple-dropdown widget-box-post-settings-dropdown" style="position: absolute; z-index: 9999; top: 30px; right: 9px; opacity: 0; visibility: hidden; transform: translate(0px, -20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
<?php if(((isset($uRow['id'])AND isset($sucat['uid']) AND ($uRow['id']==$sucat['uid'])) OR (isset($_COOKIE['admin'])  AND ($_COOKIE['admin']==$hachadmin) ))){ ?>
                <!-- SIMPLE DROPDOWN LINK -->
                <a class="simple-dropdown-link" href="<?php echo $url_site; ?>/editor/<?php echo $sucat['id']; ?>"><i class="fa fa-edit" aria-hidden="true"></i>&nbsp;<?php echo $lang['edit']; ?></a>
                <!-- /SIMPLE DROPDOWN LINK -->
<?php } ?>
<?php if(((isset($uRow['id']) AND ($uRow['id']==$sucat['uid'])) OR (isset($uRow['id']) AND ($uRow['id']==$susat['uid'])) OR (isset($_COOKIE['admin']) AND ($_COOKIE['admin']== $hachadmin)))){ ?>
                <!-- SIMPLE DROPDOWN LINK -->
                <p class="simple-dropdown-link post_delete<?php echo $susat['id']; ?>" ><i class="fa fa-trash" aria-hidden="true"></i>&nbsp;<?php echo $lang['delete']; ?></p>
                <!-- /SIMPLE DROPDOWN LINK -->
<?php } ?>
                <!-- SIMPLE DROPDOWN LINK -->
                <a href="<?php echo $url_site; ?>/update/<?php echo $strname['name']; ?>" class="simple-dropdown-link"><i class="fa fa-refresh" aria-hidden="true"></i>&nbsp;<?php lang('update');  ?></a>
                <!-- /SIMPLE DROPDOWN LINK -->

                <!-- SIMPLE DROPDOWN LINK -->
                <p class="simple-dropdown-link post_report<?php echo $sucat['id']; ?>"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;<?php echo $lang['report']; ?></p>
                <!-- /SIMPLE DROPDOWN LINK -->

                <!-- SIMPLE DROPDOWN LINK -->
                <p class="simple-dropdown-link author_report<?php echo $sucat['id']; ?>"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;<?php echo $lang['report']; ?> Author</p>
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
                              <div class=\"hexagon-image-30-32\" data-src=\"{$url_site}/{$catuss['img']}\" style=\"width: 30px; height: 32px; position: relative;\"><canvas style=\"position: absolute; top: 0px; left: 0px;\" width=\"30\" height=\"32\"></canvas></div>
                              <!-- /HEXAGON -->
                            </div>
                            <!-- /USER AVATAR CONTENT -->

                            <!-- /USER AVATAR PROGRESS -->

                            <!-- USER AVATAR PROGRESS BORDER -->
                            <div class=\"user-avatar-progress-border\">
                              <!-- HEXAGON -->
                              <div class=\"hexagon-border-40-44\" style=\"width: 40px; height: 44px; position: relative;\"></div>
                              <!-- /HEXAGON -->
                            </div>
                            <!-- /USER AVATAR PROGRESS BORDER -->  ";
                            if(check_us($catuss['id'],1)==1){
 echo                   " <!-- USER AVATAR BADGE -->
                            <div class=\"user-avatar-badge\">
                              <!-- USER AVATAR BADGE BORDER -->
                              <div class=\"user-avatar-badge-border\">
                                <!-- HEXAGON -->
                                <div class=\"hexagon-22-24\" style=\"width: 22px; height: 24px; position: relative;\"></div>
                                <!-- /HEXAGON -->
                              </div>
                              <!-- /USER AVATAR BADGE BORDER -->

                              <!-- USER AVATAR BADGE CONTENT -->
                              <div class=\"user-avatar-badge-content\">
                                <!-- HEXAGON -->
                                <div class=\"hexagon-dark-16-18\" style=\"width: 16px; height: 18px; position: relative;\"></div>
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
                        </p>
                        <!-- /USER STATUS TITLE -->

                        <!-- USER STATUS TEXT -->
                        <p class=\"user-status-text small\">
                        <i class=\"fa fa-clock-o\" ></i>&nbsp;منذ {$time_stt}";
                       "</p>
                        <!-- /USER STATUS TEXT -->
                      </div>
                      <!-- /USER STATUS -->
                    ";
?>
              <hr />
              <div   id="post_form<?php echo $sucat['id']; ?>" ><div id="report<?php echo $sucat['id']; ?>" ></div></div>
              <!-- WIDGET BOX STATUS TEXT -->
              <p class="widget-box-status-text"><div class="product-preview">
          <!-- PRODUCT PREVIEW IMAGE -->
          <a href="<?php echo $url_site; ?>/producer/<?php echo $strname['name']; ?>">
            <figure class="product-preview-image liquid" style="background: rgba(0, 0, 0, 0) url(<?php url_site();  ?>/templates/_panel/img/error_plug.png) no-repeat scroll center center / cover;">
              <img src="<?php echo $strname['o_mode']; ?>" alt="<?php echo $strname['name']; ?>" style="display: none;">
            </figure>
          </a>
          <!-- /PRODUCT PREVIEW IMAGE -->

          <!-- PRODUCT PREVIEW INFO -->
          <div class="product-preview-info">
            <!-- TEXT STICKER -->
            <p class="text-sticker"><?php echo $storepts; ?></p>
            <!-- /TEXT STICKER -->

            <!-- PRODUCT PREVIEW TITLE -->
            <p class="product-preview-title"><a href="<?php echo $url_site."/producer/".$strname['name']; ?>"><?php echo $strname['name']; ?></a></p>
            <!-- /PRODUCT PREVIEW TITLE -->

            <!-- PRODUCT PREVIEW CATEGORY -->
            <p class="product-preview-category digital"><a href="#"><?php echo $catname; ?></a></p>
            <!-- /PRODUCT PREVIEW CATEGORY -->

            <!-- PRODUCT PREVIEW TEXT -->
            <!-- /PRODUCT PREVIEW TEXT -->
          </div>
          <!-- /PRODUCT PREVIEW INFO -->

        </div>
        </p>
              <!-- /WIDGET BOX STATUS TEXT -->
       <hr />
              </div>
            <!-- /WIDGET BOX STATUS CONTENT -->
          </div>
          <!-- /WIDGET BOX STATUS -->
     </div>
     <div class="section-filters-bar v6">
      <!-- SECTION FILTERS BAR ACTIONS -->
      <div class="section-filters-bar-actions">
      <?php if(isset($elnk_site) AND ($elnk_site==1)){ ?>
      <a href="https://github.com/mrghozzi/myads/wiki/producer" class="button primary " target="_blank">&nbsp;<b><i class="fa fa-question-circle" aria-hidden="true"></i></b></a>
      &nbsp;
      <?php } ?>
      <a class="button tertiary " href="<?php url_site();  ?>/kb/<?php echo $strname['name']; ?>"><i class="fa fa-database" aria-hidden="true"></i>&nbsp;<?php lang('knowledgebase');  ?></a>
      </div>
      <p class="text-sticker">
          <!-- TEXT STICKER ICON -->
          <svg class="text-sticker-icon icon-info">
            <use xlink:href="#svg-info"></use>
          </svg>
          <!-- TEXT STICKER ICON -->
          <?php lang('Version_nbr'); ?>&nbsp;<?php echo $strtid['name']; ?></p>
      <div class="section-filters-bar-actions">
        <!-- BUTTON -->
        <a <?php if(isset($_COOKIE['user'])){ echo "href=\"{$url_site}/{$sdf}\" id=\"D{$strname['id']}\" "; }else{ echo "href=\"{$url_site}/login\""; } ?> class="button secondary" style="color: #fff;">
        <i class="fa fa-download"></i>&nbsp;<?php lang('download');  ?>
        <span class="badge badge-light"><font face="Comic Sans MS"><b><?php echo $contfils; ?></b></font></span>
        </a>
<script>
     $("document").ready(function() {
   $("#D<?php echo $strname['id']; ?>").click(downloadf<?php echo $strname['id']; ?>);

});

function downloadf<?php echo $strname['id']; ?>(){
    $.ajax({
        url : '<?php echo $dir_lnk_hash; ?>',
        data : {
            test_like : $("#lval").val()
        },
        datatype : "json",
        type : 'post',
        success : function(result) {

        },
        error : function() {

        }
    });
}
</script>
        <!-- /BUTTON -->
      </div>
      <!-- /SECTION FILTERS BAR ACTIONS -->
</div>
<div class="tab-box">
          <!-- TAB BOX OPTIONS -->
          <div class="tab-box-options">
            <!-- TAB BOX OPTION -->
            <div class="tab-box-option active">
              <!-- TAB BOX OPTION TITLE -->
              <p class="tab-box-option-title"><?php lang('desc');  ?></p>
              <!-- /TAB BOX OPTION TITLE -->
            </div>
            <!-- /TAB BOX OPTION -->

            <!-- TAB BOX OPTION -->
            <div class="tab-box-option">
              <!-- TAB BOX OPTION TITLE -->
              <p class="tab-box-option-title"><?php lang('comments');  ?> <span class="highlighted"><?php echo $abdcmnt['nbr']; ?></span></p>
              <!-- /TAB BOX OPTION TITLE -->
            </div>
            <!-- /TAB BOX OPTION -->

            <!-- TAB BOX OPTION -->
            <div class="tab-box-option">
              <!-- TAB BOX OPTION TITLE -->
              <p class="tab-box-option-title"><?php lang('version');  ?> <span class="highlighted"><?php echo $strtid['name']; ?></span></p>
              <!-- /TAB BOX OPTION TITLE -->
            </div>
            <!-- /TAB BOX OPTION -->
          </div>
          <!-- /TAB BOX OPTIONS -->

          <!-- TAB BOX ITEMS -->
          <div class="tab-box-items">
            <!-- TAB BOX ITEM -->
            <div class="tab-box-item" style="display: block; transition: none 0s ease 0s;">
              <!-- TAB BOX ITEM CONTENT -->
              <div class="tab-box-item-content paragraph_producet">
                <!-- TAB BOX ITEM PARAGRAPH -->
                <p class="tab-box-item-paragraph"><?php echo $comtxt; ?></p>
                <!-- /TAB BOX ITEM PARAGRAPH -->

              </div>
              <!-- /TAB BOX ITEM CONTENT -->
            </div>
            <!-- /TAB BOX ITEM -->

            <!-- TAB BOX ITEM -->
            <div class="tab-box-item" style="display: none; transition: none 0s ease 0s;">
             <div class="tab-box-item-content" >
               <p class="tab-box-item-title"></p>
              <!-- POST COMMENT LIST -->
              <div class="product-preview" >
              <div class="post-comment-list comment_7867_<?php echo $sucat['id']; ?>" ></div>
              </div>
              <!-- /POST COMMENT LIST -->
             </div>
            </div>
            <!-- /TAB BOX ITEM -->

            <!-- TAB BOX ITEM -->
            <div class="tab-box-item" style="display: none; transition: none 0s ease 0s;">
              <div class="tab-box-item-content" >
               <p class="tab-box-item-title"></p>
                   <table id="tablepagination" class="table table-borderless table-hover">
						<thead>
							<tr>
                              <th><center>ID</center></th>
                              <th><center><?php lang('version'); ?></center></th>
							  <th><center><?php lang('download');  ?></center></th>
                              <?php if((isset($uRow['id']) AND ($uRow['id']==$catuss['id'])) OR (isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin))){ ?>
                              <th><center><?php lang('desc');  ?></center></th>
                              <th><center><?php lang('options');  ?></center></th>
                              <?php } ?>
                            </tr>
						</thead>
						<tbody>
<?php
$sttidv = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'store_file' AND `o_parent` =".$strname['id']." ORDER BY `o_order`  DESC " );
$sttidv->execute();
while($strtidv=$sttidv->fetch(PDO::FETCH_ASSOC)){
$comtxtv = strip_tags($strtidv['o_valuer'], '<br><b><a><p><img><span>');
echo "<tr>
      <td>{$strtidv['id']}</td>
      <td><center><b>{$strtidv['name']}</b></center></td>
      <td><center>";
$sdfv = $strtidv['o_mode'];
$ndfkv = $strtidv['id'];
$dir_lnk_hash_v = $url_site."/download/".hash('crc32', $sdfv.$ndfkv );
$contfilsv = 0;
$stormfnbv = $db_con->prepare("SELECT  clik FROM short WHERE sh_type=7867 AND tp_id=:tp_id  " );
$stormfnbv->bindParam(":tp_id", $ndfkv);
$stormfnbv->execute();
$sfilenbrv=$stormfnbv->fetch(PDO::FETCH_ASSOC);
$contfilsv += $sfilenbrv['clik'];

if(isset($_COOKIE['user'])){ ?>
        <a href="<?php echo $url_site."/".$sdfv; ?>"  id="V<?php echo $strtidv['id']; ?>" class="button secondary" style="color: #fff;" >&nbsp;<i class="fa fa-download"></i>&nbsp;<?php lang('download');  ?>&nbsp;<span class="badge badge-light"><font face="Comic Sans MS"><b><?php echo $contfilsv; ?></b></font></span>&nbsp;</a>
<?php }else{ ?>
        <a href="<?php echo $url_site."/login"; ?>"  id="V<?php echo $strtidv['id']; ?>" class="button secondary" style="color: #fff;" >&nbsp;<i class="fa fa-download"></i>&nbsp;<?php lang('download');  ?>&nbsp;<span class="badge badge-light"><font face="Comic Sans MS"><b><?php echo $contfilsv; ?></b></font></span>&nbsp;</a>
<?php     }
echo "</center></td>";
if((isset($uRow['id']) AND ($uRow['id']==$catuss['id'])) OR (isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin))){
echo " <td>{$comtxtv}</td>";
echo " <td><center></center></td>";
       }
echo " </tr>
<script>

   \$(\"document\").ready(function() {
   \$(\"#V{$strtidv['id']}\").click(downloadv{$strtidv['id']});

});

function downloadv{$strtidv['id']}(){
    \$.ajax({
        url : '$dir_lnk_hash_v',
        data : {
            test_like : \$(\"#lval\").val()
        },
        datatype : \"json\",
        type : 'post',
        success : function(result) {

        },
        error : function() {

        }
    });
}
   </script>
";
    }
  ?>

             </tbody>
             <tfoot>
			  <tr>
               <th><center>ID</center></th>
               <th><center><?php lang('version'); ?></center></th>
			   <th><center><?php lang('download');  ?></center></th>
               <?php if((isset($uRow['id']) AND ($uRow['id']==$catuss['id'])) OR (isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin))){ ?>
               <th><center><?php lang('desc');  ?></center></th>
               <th><center><?php lang('options');  ?></center></th>
  <?php } ?>
              </tr>
			 </tfoot>
  </table>
                 </div>
            </div>
            <!-- /TAB BOX ITEM -->
          </div>
          <!-- /TAB BOX ITEMS -->
        </div>
        <!-- POST OPTIONS -->
          <div class="post-options">
<?php   if(isset($_COOKIE['user'])){ ?>
            <!-- POST OPTION WRAP -->
            <div class="post-option-wrap" style="position: relative;">
              <!-- POST OPTION -->
              <div class="post-option reaction-options-dropdown-trigger">
              <div id="reaction_image<?php echo $susat['id']; ?>" >
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
                <p class="post-option-text reaction_txt<?php echo $susat['id']; ?>" <?php if($uslike['sid']==$catdid){ echo $reaction_color; } ?> >
                &nbsp;<?php if($uslike['sid']==$catdid){ echo $reaction_name; }else{ echo $lang['react']; } ?></p>
                <!-- /POST OPTION TEXT -->
              </div>
              <!-- /POST OPTION -->

              <!-- REACTION OPTIONS -->
              <div class="reaction-options reaction-options-dropdown" style="position: absolute; z-index: 9999; bottom: 54px; left: -16px; opacity: 0; visibility: hidden; transform: translate(0px, 20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft reaction_7867_<?php echo $sucat['id']; ?>" data-title="like" style="position: relative;">
                  <!-- REACTION OPTION IMAGE -->
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/reaction/like.png" alt="reaction-like">
                  <!-- /REACTION OPTION IMAGE -->
                <div class="xm-tooltip" style="white-space: nowrap; position: absolute; z-index: 99999; top: -28px; left: 50%; margin-left: -22px; opacity: 0; visibility: hidden; transform: translate(0px, 10px); transition: all 0.3s ease-in-out 0s;"><p class="xm-tooltip-text">Like</p></div></div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft reaction_7867_<?php echo $sucat['id']; ?>" data-title="love" style="position: relative;">
                  <!-- REACTION OPTION IMAGE -->
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/reaction/love.png" alt="reaction-love">
                  <!-- /REACTION OPTION IMAGE -->
                <div class="xm-tooltip" style="white-space: nowrap; position: absolute; z-index: 99999; top: -28px; left: 50%; margin-left: -23.5px; opacity: 0; visibility: hidden; transform: translate(0px, 10px); transition: all 0.3s ease-in-out 0s;"><p class="xm-tooltip-text">Love</p></div></div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft reaction_7867_<?php echo $sucat['id']; ?>" data-title="dislike" style="position: relative;">
                  <!-- REACTION OPTION IMAGE -->
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/reaction/dislike.png" alt="reaction-dislike">
                  <!-- /REACTION OPTION IMAGE -->
                <div class="xm-tooltip" style="white-space: nowrap; position: absolute; z-index: 99999; top: -28px; left: 50%; margin-left: -28px; opacity: 0; visibility: hidden; transform: translate(0px, 10px); transition: all 0.3s ease-in-out 0s;"><p class="xm-tooltip-text">Dislike</p></div></div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft reaction_7867_<?php echo $sucat['id']; ?>" data-title="happy" style="position: relative;">
                  <!-- REACTION OPTION IMAGE -->
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/reaction/happy.png" alt="reaction-happy">
                  <!-- /REACTION OPTION IMAGE -->
                <div class="xm-tooltip" style="white-space: nowrap; position: absolute; z-index: 99999; top: -28px; left: 50%; margin-left: -27.5px; opacity: 0; visibility: hidden; transform: translate(0px, 10px); transition: all 0.3s ease-in-out 0s;"><p class="xm-tooltip-text">Happy</p></div></div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft reaction_7867_<?php echo $sucat['id']; ?>" data-title="funny" style="position: relative;">
                  <!-- REACTION OPTION IMAGE -->
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/reaction/funny.png" alt="reaction-funny">
                  <!-- /REACTION OPTION IMAGE -->
                <div class="xm-tooltip" style="white-space: nowrap; position: absolute; z-index: 99999; top: -28px; left: 50%; margin-left: -27px; opacity: 0; visibility: hidden; transform: translate(0px, 10px); transition: all 0.3s ease-in-out 0s;"><p class="xm-tooltip-text">Funny</p></div></div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft reaction_7867_<?php echo $sucat['id']; ?>" data-title="wow" style="position: relative;">
                  <!-- REACTION OPTION IMAGE -->
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/reaction/wow.png" alt="reaction-wow">
                  <!-- /REACTION OPTION IMAGE -->
                <div class="xm-tooltip" style="white-space: nowrap; position: absolute; z-index: 99999; top: -28px; left: 50%; margin-left: -24px; opacity: 0; visibility: hidden; transform: translate(0px, 10px); transition: all 0.3s ease-in-out 0s;"><p class="xm-tooltip-text">Wow</p></div></div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft reaction_7867_<?php echo $sucat['id']; ?>" data-title="angry" style="position: relative;">
                  <!-- REACTION OPTION IMAGE -->
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/reaction/angry.png" alt="reaction-angry">
                  <!-- /REACTION OPTION IMAGE -->
                <div class="xm-tooltip" style="white-space: nowrap; position: absolute; z-index: 99999; top: -28px; left: 50%; margin-left: -26.5px; opacity: 0; visibility: hidden; transform: translate(0px, 10px); transition: all 0.3s ease-in-out 0s;"><p class="xm-tooltip-text">Angry</p></div></div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft reaction_7867_<?php echo $sucat['id']; ?>" data-title="sad" style="position: relative;">
                  <!-- REACTION OPTION IMAGE -->
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/reaction/sad.png" alt="reaction-sad">
                  <!-- /REACTION OPTION IMAGE -->
                <div class="xm-tooltip" style="white-space: nowrap; position: absolute; z-index: 99999; top: -28px; left: 50%; margin-left: -21.5px; opacity: 0; visibility: hidden; transform: translate(0px, 10px); transition: all 0.3s ease-in-out 0s;"><p class="xm-tooltip-text">Sad</p></div></div>
                <!-- /REACTION OPTION -->
              </div>
              <!-- /REACTION OPTIONS -->
            </div>
            <!-- /POST OPTION WRAP -->
<?php } ?>
            <!-- POST OPTION -->
            <div class="post-option-wrap" style="position: relative;">
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
              <div class="reaction-options reaction-options-dropdown" style="position: absolute; z-index: 9999; bottom: 54px; left: -16px; opacity: 0; visibility: hidden; transform: translate(0px, 20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft" data-title="facebook" style="position: relative;">
                  <!-- REACTION OPTION IMAGE -->
                  <a onClick="window.open('https://www.facebook.com/sharer/sharer.php?u=<?php echo $linksher; ?>');" href="javascript:void(0);" >
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/icons/facebook-icon.png" >
                  </a>
                  <!-- /REACTION OPTION IMAGE -->
                </div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft" data-title="twitter" style="position: relative;">
                  <!-- REACTION OPTION IMAGE -->
                  <a onClick="window.open('https://twitter.com/intent/tweet?text=<?php echo $namesher; ?>&url=<?php echo $linksher; ?>');" href="javascript:void(0);" >
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/icons/twitter-icon.png" >
                  </a>
                  <!-- /REACTION OPTION IMAGE -->
                </div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft" data-title="linkedin" style="position: relative;">
                  <!-- REACTION OPTION IMAGE -->
                  <a onClick="window.open('https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $linksher; ?>');" href="javascript:void(0);" >
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/icons/linkedin-icon.png" >
                  </a>
                  <!-- /REACTION OPTION IMAGE -->
                </div>
                <!-- /REACTION OPTION -->

                <!-- REACTION OPTION -->
                <div class="reaction-option text-tooltip-tft" data-title="telegram" style="position: relative;">
                  <!-- REACTION OPTION IMAGE -->
                  <a onClick="window.open('https://telegram.me/share/url?url=<?php echo $linksher; ?>&text=<?php echo $namesher; ?>');" href="javascript:void(0);" >
                  <img class="reaction-option-image" src="<?php url_site();  ?>/templates/_panel/img/icons/telegram-icon.png" >
                  </a>
                  <!-- /REACTION OPTION IMAGE -->
                </div>
                <!-- /REACTION OPTION -->

                </div>
              <!-- /REACTION OPTIONS -->
            </div>
            <!-- /POST OPTION -->
          </div>
</div>
<script>
$('.post_report<?php echo $sucat['id']; ?>').click(function(){
  $("#report<?php echo $sucat['id']; ?>").load('<?php url_site();  ?>/templates/_panel/status/post_report.php?s_type=7867&tid=<?php echo $sucat['id']; ?>');
        });
</script>
<script>
$('.author_report<?php echo $sucat['id']; ?>').click(function(){
  $("#report<?php echo $sucat['id']; ?>").load('<?php url_site();  ?>/templates/_panel/status/post_report.php?s_type=7867&tid=<?php echo $sucat['id']; ?>&a_type=99');
        });
</script>
<script>
$('.post_delete<?php echo $susat['id']; ?>').click(function(){
  $("#post_form<?php echo $sucat['id']; ?>").load('<?php url_site();  ?>/templates/_panel/status/post_delete.php?sid=<?php echo $susat['id']; ?>');
        });
</script>
<script>
$(".comment_7867_<?php echo $sucat['id']; ?>").load('<?php url_site();  ?>/templates/_panel/status/post_comment.php?s_type=7867&tid=<?php echo $sucat['id']; ?>');
$(".sh_comment_r<?php echo $susat['id']; ?>").addClass('active');
</script>
<script>
     $("document").ready(function() {
   $(".reaction_7867_<?php echo $sucat['id']; ?>").click(postreaction<?php echo $susat['id']; ?>);

});
function postreaction<?php echo $susat['id']; ?>(){
    var data_reaction = $(this).attr("data-title");
   $.ajax({
type: "POST",
url: "<?php url_site();  ?>/requests/f_like.php?id=<?php echo $sucat['id']; ?>&f_like=like_up&t=f",
data: "data_reaction=" + data_reaction,
success: function (response) {
// This code will run after the Ajax is successful
$("#reaction_image<?php echo $susat['id']; ?>").html(response);
$(".reaction_txt<?php echo $susat['id']; ?>").html("");

}
})
}
</script>
<?php }else{ template_mine('404'); } }else{ echo"404"; }  ?>
