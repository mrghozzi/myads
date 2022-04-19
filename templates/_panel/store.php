<?php if($s_st=="buyfgeufb"){

 ?>
   <!-- SECTION BANNER -->
<div class="section-banner" style="background: url(<?php url_site();  ?>/templates/_panel/img/banner/Newsfeed.png) no-repeat 50%;" >
      <!-- SECTION BANNER ICON -->
      <img class="section-banner-icon" src="<?php url_site();  ?>/templates/_panel/img/banner/marketplace-icon.png" >
      <!-- /SECTION BANNER ICON -->

      <!-- SECTION BANNER TITLE -->
      <p class="section-banner-title"><?php lang('Store'); ?></p>
      <!-- /SECTION BANNER TITLE -->

      <!-- SECTION BANNER TEXT -->
      <p class="section-banner-text"><b><i class="fa fa-gift" aria-hidden="true"></i>&nbsp;Tout Points&nbsp;:&nbsp;<font color="#339966">
        <?php   echo $uRow['pts'];   ?></font>&nbsp;<font face="Comic Sans MS">PTS</font></b></p>
      <!-- /SECTION BANNER TEXT -->
</div>
    <!-- /SECTION BANNER -->
<div class="section-header">
      <!-- SECTION HEADER INFO -->
      <div class="section-header-info">
        <!-- SECTION PRETITLE -->
        <p class="section-pretitle">Search what you want!</p>
        <!-- /SECTION PRETITLE -->

        <!-- SECTION TITLE -->
        <h2 class="section-title">Market Categories</h2>
        <!-- /SECTION TITLE -->
      </div>
      <!-- /SECTION HEADER INFO -->
</div>
<div class="grid grid-3-3-3 centered">
      <!-- PRODUCT CATEGORY BOX -->
      <a class="product-category-box category-all"  href="#" style="background: url(<?php url_site();  ?>/templates/_panel/img/banner/script.png) no-repeat 100% 0,linear-gradient(90deg,#615dfa,#8d7aff);">
        <!-- PRODUCT CATEGORY BOX TITLE -->
        <p class="product-category-box-title"><?php echo $lang['script']; ?></p>
        <!-- /PRODUCT CATEGORY BOX TITLE -->

        <!-- PRODUCT CATEGORY BOX TEXT -->
        <p class="product-category-box-text">...</p>
        <!-- /PRODUCT CATEGORY BOX TEXT -->

        <!-- PRODUCT CATEGORY BOX TAG -->
        <p class="product-category-box-tag">soon</p>
        <!-- /PRODUCT CATEGORY BOX TAG -->
      </a>
      <!-- /PRODUCT CATEGORY BOX -->

      <!-- PRODUCT CATEGORY BOX -->
      <a class="product-category-box category-featured" href="#" style="background: url(<?php url_site();  ?>/templates/_panel/img/banner/templates.png) no-repeat 100% 0,linear-gradient(90deg,#417ae1,#5aafff);">
        <!-- PRODUCT CATEGORY BOX TITLE -->
        <p class="product-category-box-title"><?php echo $lang['templates']; ?></p>
        <!-- /PRODUCT CATEGORY BOX TITLE -->

        <!-- PRODUCT CATEGORY BOX TEXT -->
        <p class="product-category-box-text">...</p>
        <!-- /PRODUCT CATEGORY BOX TEXT -->

        <!-- PRODUCT CATEGORY BOX TAG -->
        <p class="product-category-box-tag">soon</p>
        <!-- /PRODUCT CATEGORY BOX TAG -->
      </a>
      <!-- /PRODUCT CATEGORY BOX -->

      <!-- PRODUCT CATEGORY BOX -->
      <a class="product-category-box category-digital" href="#" style="background: url(<?php url_site();  ?>/templates/_panel/img/banner/plugins.png) no-repeat 100% 0,linear-gradient(90deg,#2ebfef,#4ce4ff);">
        <!-- PRODUCT CATEGORY BOX TITLE -->
        <p class="product-category-box-title"><?php echo $lang['plugins']; ?></p>
        <!-- /PRODUCT CATEGORY BOX TITLE -->

        <!-- PRODUCT CATEGORY BOX TEXT -->
        <p class="product-category-box-text">...</p>
        <!-- /PRODUCT CATEGORY BOX TEXT -->

        <!-- PRODUCT CATEGORY BOX TAG -->
        <p class="product-category-box-tag">soon</p>
        <!-- /PRODUCT CATEGORY BOX TAG -->
      </a>
      <!-- /PRODUCT CATEGORY BOX -->

</div>
<div class="section-header">
      <!-- SECTION HEADER INFO -->
      <div class="section-header-info">
        <!-- SECTION PRETITLE -->
        <p class="section-pretitle">See what's new!</p>
        <!-- /SECTION PRETITLE -->

        <!-- SECTION TITLE -->
        <h2 class="section-title">Latest Items</h2>
        <!-- /SECTION TITLE -->
      </div>
      <!-- /SECTION HEADER INFO -->

      <!-- SECTION HEADER ACTIONS -->
      <div class="section-header-actions">
        <!-- SECTION HEADER ACTION -->
        <a class="button secondary" role="button" href="<?php echo $url_site; ?>/add_store">&nbsp;&nbsp;<i class="fa fa-plus" aria-hidden="true"></i>&nbsp;<?php lang('add_product');  ?>&nbsp;&nbsp;</a>
        <!-- /SECTION HEADER ACTION -->
      </div>
      <!-- /SECTION HEADER ACTIONS -->
</div>
<div class="grid grid-3-3-3-3 centered">
<?php
                 $errstor = 0;
                 $o_type = "store";
                 $stormt = $db_con->prepare("SELECT *  FROM options WHERE o_type=:o_type ORDER BY `id` " );
                 $stormt->bindParam(":o_type", $o_type);
                 $stormt->execute();
                 while($store=$stormt->fetch(PDO::FETCH_ASSOC) ) {

                 if(isset($store['o_order']) AND ($store['o_order']>0)){
                   $storepts = $store['o_order']."&nbsp;<span class=\"highlighted\">PTS</span>";
                 }else{
                   $storepts = $lang['free'];
                 }
                 $o_parent = $store['o_parent'];
                 $catusen = $db_con->prepare("SELECT *  FROM users WHERE  id=:id ");
                 $catusen->bindParam(":id",$o_parent );
                 $catusen->execute();
                 $catussen=$catusen->fetch(PDO::FETCH_ASSOC);
                 //file
                 $f_type = "store_file";
                 $stormf = $db_con->prepare("SELECT *  FROM options WHERE o_type=:o_type AND o_parent=:o_parent ORDER BY `o_order`  DESC " );
                 $stormf->bindParam(":o_type", $f_type);
                 $stormf->bindParam(":o_parent", $store['id']);
                 $stormf->execute();
                 $storefile=$stormf->fetch(PDO::FETCH_ASSOC);
                 $ndfk = $storefile['id'];
                 $sdf= $storefile['o_mode'];
                 $dir_lnk_hash = $url_site."/download/".hash('crc32', $sdf.$ndfk );

                 $contfils = 0;
                  $sttnid = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'store_file' AND `o_parent` =:o_parent " );
                  $sttnid->bindParam(":o_parent", $store['id']);
                  $sttnid->execute();
                  while($strtnid=$sttnid->fetch(PDO::FETCH_ASSOC)){
                    $ndfkn = $strtnid['id'];
                 $stormfnb = $db_con->prepare("SELECT  clik FROM short WHERE sh_type=7867 AND tp_id=:tp_id  " );
                 $stormfnb->bindParam(":tp_id", $ndfkn);
                 $stormfnb->execute();
                 $sfilenbr=$stormfnb->fetch(PDO::FETCH_ASSOC);
                 $contfils += $sfilenbr['clik'];
                 }

                 // store type
                 $t_type = "store_type";
                 $stormy = $db_con->prepare("SELECT *  FROM options WHERE o_type=:o_type AND o_parent=:o_parent ORDER BY `o_order`  DESC " );
                 $stormy->bindParam(":o_type", $t_type);
                 $stormy->bindParam(":o_parent", $store['id']);
                 $stormy->execute();
                 $stortyp=$stormy->fetch(PDO::FETCH_ASSOC);
                 $stortype = $stortyp['name'];

?>
<div class="product-preview">
        <!-- PRODUCT PREVIEW IMAGE -->
        <a href="<?php echo $url_site; ?>/producer/<?php echo $store['name']; ?>">
          <figure class="product-preview-image liquid" style="background: rgba(0, 0, 0, 0) url(<?php url_site();  ?>/templates/_panel/img/error_plug.png) no-repeat scroll center center / cover;">
            <img src="<?php echo $store['o_mode']; ?>" alt="<?php echo $store['name']; ?>" style="display: none;">
          </figure>
        </a>
        <!-- /PRODUCT PREVIEW IMAGE -->

        <!-- PRODUCT PREVIEW INFO -->
        <div class="product-preview-info">
          <!-- TEXT STICKER -->
          <p class="text-sticker"><?php echo $storepts; ?></p>
          <!-- /TEXT STICKER -->

          <!-- PRODUCT PREVIEW TITLE -->
          <p class="product-preview-title"><a href="<?php echo $url_site; ?>/producer/<?php echo $store['name']; ?>"><?php echo $store['name']; ?></a></p>
          <!-- /PRODUCT PREVIEW TITLE -->

          <!-- PRODUCT PREVIEW CATEGORY -->
          <p class="product-preview-category digital"><a href="#"><?php echo $lang["$stortype"]; ?></a></p>
          <!-- /PRODUCT PREVIEW CATEGORY -->

          <!-- PRODUCT PREVIEW TEXT -->
          <p class="product-preview-text"><?php echo $store['o_valuer']; ?></p>
          <!-- /PRODUCT PREVIEW TEXT -->
        </div>
        <!-- /PRODUCT PREVIEW INFO -->

        <!-- PRODUCT PREVIEW META -->
        <div class="product-preview-meta">
          <!-- PRODUCT PREVIEW AUTHOR -->
          <div class="product-preview-author">
            <!-- PRODUCT PREVIEW AUTHOR IMAGE -->
            <a class="product-preview-author-image user-avatar micro no-border" href="<?php echo "{$url_site}/u/{$catussen['id']}"; ?>">
              <!-- USER AVATAR CONTENT -->
              <div class="user-avatar-content">
                <!-- HEXAGON -->
                <div class="hexagon-image-18-20" data-src="<?php echo "{$url_site}/{$catussen['img']}"; ?>" style="width: 18px; height: 20px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="18" height="20"></canvas></div>
                <!-- /HEXAGON -->
              </div>
              <!-- /USER AVATAR CONTENT -->
            </a>
            <!-- /PRODUCT PREVIEW AUTHOR IMAGE -->

            <!-- PRODUCT PREVIEW AUTHOR TITLE -->
            <p class="product-preview-author-title">Posted By</p>
            <!-- /PRODUCT PREVIEW AUTHOR TITLE -->

            <!-- PRODUCT PREVIEW AUTHOR TEXT -->
            <p class="product-preview-author-text"><a href="<?php echo "{$url_site}/u/{$catussen['id']}"; ?>"><?php echo $catussen['username']; ?></a></p>
            <!-- /PRODUCT PREVIEW AUTHOR TEXT -->
          </div>
          <!-- /PRODUCT PREVIEW AUTHOR -->

          <!-- RATING LIST -->
          <div class="rating-list">
            <b><?php echo $storefile['name'];  ?></b>
          </div>
          <!-- /RATING LIST -->
        </div>
        <!-- /PRODUCT PREVIEW META -->
      </div>
<?php
   if(isset($store['o_order']) AND ($store['o_order']>0)){
                   $storeinfo = $store['o_order']."&nbspPTS";
                 }else{
                    $storeinfo = $lang['tpfree'];
                 }
     $errstor ++;
    }
    if(isset($errstor) AND ($errstor==0)){
      echo "<center><pre>";
     lang('sieanpr');
     echo "</pre></center>";
    }

     ?>
</div>
<?php  }else{ echo "404"; }  ?>