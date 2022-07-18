<?php

 $n_like = 0;
 $n_love = 0;
 $n_dislike = 0;
 $n_sad = 0;
 $n_angry = 0;
 $n_happy = 0;
 $n_funny = 0;
 $n_wow = 0;

$r_type   = "data_reaction";
$likeuscmrc = $db_con->prepare("SELECT  * FROM `options` WHERE  o_parent IN( SELECT  id FROM `like` WHERE sid='{$catdid}' AND  type={$st_type} ) AND  o_type='{$r_type}' ORDER BY id " );
$likeuscmrc->execute();
while($uslikerc=$likeuscmrc->fetch(PDO::FETCH_ASSOC)){

 if($uslikerc['o_valuer']=="like")   { $n_like=$n_like+1;    }
 if($uslikerc['o_valuer']=="love")   { $n_love=$n_love+1;    }
 if($uslikerc['o_valuer']=="dislike"){ $n_dislike=$n_dislike+1; }
 if($uslikerc['o_valuer']=="sad")    { $n_sad=$n_sad+1;     }
 if($uslikerc['o_valuer']=="angry")  { $n_angry=$n_angry+1;   }
 if($uslikerc['o_valuer']=="happy")  { $n_happy=$n_happy+1;   }
 if($uslikerc['o_valuer']=="funny")  { $n_funny=$n_funny+1;   }
 if($uslikerc['o_valuer']=="wow")    { $n_wow=$n_wow+1;     }


}

 ?>
<div class="meta-line-list reaction-item-list">
 <?php if($n_like>0){ ?>
                      <!-- REACTION ITEM -->
                      <div class="reaction-item">
                        <!-- REACTION IMAGE -->
                        <img class="reaction-image reaction-item-dropdown-trigger" src="<?php echo $url_site; ?>/templates/_panel/img/reaction/like.png" alt="reaction-like">
                        <!-- /REACTION IMAGE -->

                        <!-- SIMPLE DROPDOWN -->
                        <div class="simple-dropdown padded reaction-item-dropdown" >
                          <!-- SIMPLE DROPDOWN TEXT -->
                          <p class="simple-dropdown-text"><img class="reaction" src="<?php echo $url_site; ?>/templates/_panel/img/reaction/like.png" alt="reaction-like"> <span class="bold">Like</span></p>
                          <!-- /SIMPLE DROPDOWN TEXT -->
          <?php
$r_type   = "data_reaction";
$likeuscmrc = $db_con->prepare("SELECT  * FROM `options` WHERE  o_parent IN( SELECT  id FROM `like` WHERE sid='{$catdid}' AND  type={$st_type} ) AND o_valuer='like' AND  o_type='{$r_type}' ORDER BY id " );
$likeuscmrc->execute();
while($uslikerc=$likeuscmrc->fetch(PDO::FETCH_ASSOC)){
$catrusr = $db_con->prepare("SELECT *  FROM users WHERE  id='{$uslikerc['o_order']}'");
$catrusr->execute();
$catrussr=$catrusr->fetch(PDO::FETCH_ASSOC);
           ?>
                          <!-- SIMPLE DROPDOWN TEXT -->
                          <p class="simple-dropdown-text"><?php echo $catrussr['username']; ?></p>
                          <!-- /SIMPLE DROPDOWN TEXT -->
    <?php } ?>
                        </div>
                        <!-- /SIMPLE DROPDOWN -->
                      </div>
                      <!-- /REACTION ITEM -->
<?php } ?>
 <?php if($n_love>0){ ?>
                      <!-- REACTION ITEM -->
                      <div class="reaction-item">
                        <!-- REACTION IMAGE -->
                        <img class="reaction-image reaction-item-dropdown-trigger" src="<?php echo $url_site; ?>/templates/_panel/img/reaction/love.png" alt="reaction-love">
                        <!-- /REACTION IMAGE -->

                        <!-- SIMPLE DROPDOWN -->
                        <div class="simple-dropdown padded reaction-item-dropdown" >
                          <!-- SIMPLE DROPDOWN TEXT -->
                          <p class="simple-dropdown-text"><img class="reaction" src="<?php echo $url_site; ?>/templates/_panel/img/reaction/love.png" alt="reaction-love"> <span class="bold">Love</span></p>
                          <!-- /SIMPLE DROPDOWN TEXT -->
          <?php
$r_type   = "data_reaction";
$likeuscmrc = $db_con->prepare("SELECT  * FROM `options` WHERE  o_parent IN( SELECT  id FROM `like` WHERE sid='{$catdid}' AND  type=2 ) AND o_valuer='love' AND  o_type='{$r_type}' ORDER BY id " );
$likeuscmrc->execute();
while($uslikerc=$likeuscmrc->fetch(PDO::FETCH_ASSOC)){
$catrusr = $db_con->prepare("SELECT *  FROM users WHERE  id='{$uslikerc['o_order']}'");
$catrusr->execute();
$catrussr=$catrusr->fetch(PDO::FETCH_ASSOC);
           ?>
                          <!-- SIMPLE DROPDOWN TEXT -->
                          <p class="simple-dropdown-text"><?php echo $catrussr['username']; ?></p>
                          <!-- /SIMPLE DROPDOWN TEXT -->
    <?php } ?>
                        </div>
                        <!-- /SIMPLE DROPDOWN -->
                      </div>
                      <!-- /REACTION ITEM -->
<?php } ?>
 <?php if($n_dislike>0){ ?>
                      <!-- REACTION ITEM -->
                      <div class="reaction-item">
                        <!-- REACTION IMAGE -->
                        <img class="reaction-image reaction-item-dropdown-trigger" src="<?php echo $url_site; ?>/templates/_panel/img/reaction/dislike.png" alt="reaction-dislike">
                        <!-- /REACTION IMAGE -->

                        <!-- SIMPLE DROPDOWN -->
                        <div class="simple-dropdown padded reaction-item-dropdown" >
                          <!-- SIMPLE DROPDOWN TEXT -->
                          <p class="simple-dropdown-text"><img class="reaction" src="<?php echo $url_site; ?>/templates/_panel/img/reaction/dislike.png" alt="reaction-dislike"> <span class="bold">Dislike</span></p>
                          <!-- /SIMPLE DROPDOWN TEXT -->
          <?php
$r_type   = "data_reaction";
$likeuscmrc = $db_con->prepare("SELECT  * FROM `options` WHERE  o_parent IN( SELECT  id FROM `like` WHERE sid='{$catdid}' AND  type=2 ) AND o_valuer='dislike' AND  o_type='{$r_type}' ORDER BY id " );
$likeuscmrc->execute();
while($uslikerc=$likeuscmrc->fetch(PDO::FETCH_ASSOC)){
$catrusr = $db_con->prepare("SELECT *  FROM users WHERE  id='{$uslikerc['o_order']}'");
$catrusr->execute();
$catrussr=$catrusr->fetch(PDO::FETCH_ASSOC);
           ?>
                          <!-- SIMPLE DROPDOWN TEXT -->
                          <p class="simple-dropdown-text"><?php echo $catrussr['username']; ?></p>
                          <!-- /SIMPLE DROPDOWN TEXT -->
    <?php } ?>
                        </div>
                        <!-- /SIMPLE DROPDOWN -->
                      </div>
                      <!-- /REACTION ITEM -->
<?php } ?>
 <?php if($n_sad>0){ ?>
                      <!-- REACTION ITEM -->
                      <div class="reaction-item">
                        <!-- REACTION IMAGE -->
                        <img class="reaction-image reaction-item-dropdown-trigger" src="<?php echo $url_site; ?>/templates/_panel/img/reaction/sad.png" alt="reaction-sad">
                        <!-- /REACTION IMAGE -->

                        <!-- SIMPLE DROPDOWN -->
                        <div class="simple-dropdown padded reaction-item-dropdown" >
                          <!-- SIMPLE DROPDOWN TEXT -->
                          <p class="simple-dropdown-text"><img class="reaction" src="<?php echo $url_site; ?>/templates/_panel/img/reaction/sad.png" alt="reaction-sad"> <span class="bold">Sad</span></p>
                          <!-- /SIMPLE DROPDOWN TEXT -->
          <?php
$r_type   = "data_reaction";
$likeuscmrc = $db_con->prepare("SELECT  * FROM `options` WHERE  o_parent IN( SELECT  id FROM `like` WHERE sid='{$catdid}' AND  type=2 ) AND o_valuer='sad' AND  o_type='{$r_type}' ORDER BY id " );
$likeuscmrc->execute();
while($uslikerc=$likeuscmrc->fetch(PDO::FETCH_ASSOC)){
$catrusr = $db_con->prepare("SELECT *  FROM users WHERE  id='{$uslikerc['o_order']}'");
$catrusr->execute();
$catrussr=$catrusr->fetch(PDO::FETCH_ASSOC);
           ?>
                          <!-- SIMPLE DROPDOWN TEXT -->
                          <p class="simple-dropdown-text"><?php echo $catrussr['username']; ?></p>
                          <!-- /SIMPLE DROPDOWN TEXT -->
    <?php } ?>
                        </div>
                        <!-- /SIMPLE DROPDOWN -->
                      </div>
                      <!-- /REACTION ITEM -->
<?php } ?>
 <?php if($n_angry>0){ ?>
                      <!-- REACTION ITEM -->
                      <div class="reaction-item">
                        <!-- REACTION IMAGE -->
                        <img class="reaction-image reaction-item-dropdown-trigger" src="<?php echo $url_site; ?>/templates/_panel/img/reaction/angry.png" alt="reaction-angry">
                        <!-- /REACTION IMAGE -->

                        <!-- SIMPLE DROPDOWN -->
                        <div class="simple-dropdown padded reaction-item-dropdown" >
                          <!-- SIMPLE DROPDOWN TEXT -->
                          <p class="simple-dropdown-text"><img class="reaction" src="<?php echo $url_site; ?>/templates/_panel/img/reaction/angry.png" alt="reaction-angry"> <span class="bold">Angry</span></p>
                          <!-- /SIMPLE DROPDOWN TEXT -->
          <?php
$r_type   = "data_reaction";
$likeuscmrc = $db_con->prepare("SELECT  * FROM `options` WHERE  o_parent IN( SELECT  id FROM `like` WHERE sid='{$catdid}' AND  type=2 ) AND o_valuer='angry' AND  o_type='{$r_type}' ORDER BY id " );
$likeuscmrc->execute();
while($uslikerc=$likeuscmrc->fetch(PDO::FETCH_ASSOC)){
$catrusr = $db_con->prepare("SELECT *  FROM users WHERE  id='{$uslikerc['o_order']}'");
$catrusr->execute();
$catrussr=$catrusr->fetch(PDO::FETCH_ASSOC);
           ?>
                          <!-- SIMPLE DROPDOWN TEXT -->
                          <p class="simple-dropdown-text"><?php echo $catrussr['username']; ?></p>
                          <!-- /SIMPLE DROPDOWN TEXT -->
    <?php } ?>
                        </div>
                        <!-- /SIMPLE DROPDOWN -->
                      </div>
                      <!-- /REACTION ITEM -->
<?php } ?>
 <?php if($n_happy>0){ ?>
                      <!-- REACTION ITEM -->
                      <div class="reaction-item">
                        <!-- REACTION IMAGE -->
                        <img class="reaction-image reaction-item-dropdown-trigger" src="<?php echo $url_site; ?>/templates/_panel/img/reaction/happy.png" alt="reaction-happy">
                        <!-- /REACTION IMAGE -->

                        <!-- SIMPLE DROPDOWN -->
                        <div class="simple-dropdown padded reaction-item-dropdown" >
                          <!-- SIMPLE DROPDOWN TEXT -->
                          <p class="simple-dropdown-text"><img class="reaction" src="<?php echo $url_site; ?>/templates/_panel/img/reaction/happy.png" alt="reaction-happy"> <span class="bold">Happy</span></p>
                          <!-- /SIMPLE DROPDOWN TEXT -->
          <?php
$r_type   = "data_reaction";
$likeuscmrc = $db_con->prepare("SELECT  * FROM `options` WHERE  o_parent IN( SELECT  id FROM `like` WHERE sid='{$catdid}' AND  type=2 ) AND o_valuer='happy' AND  o_type='{$r_type}' ORDER BY id " );
$likeuscmrc->execute();
while($uslikerc=$likeuscmrc->fetch(PDO::FETCH_ASSOC)){
$catrusr = $db_con->prepare("SELECT *  FROM users WHERE  id='{$uslikerc['o_order']}'");
$catrusr->execute();
$catrussr=$catrusr->fetch(PDO::FETCH_ASSOC);
           ?>
                          <!-- SIMPLE DROPDOWN TEXT -->
                          <p class="simple-dropdown-text"><?php echo $catrussr['username']; ?></p>
                          <!-- /SIMPLE DROPDOWN TEXT -->
    <?php } ?>
                        </div>
                        <!-- /SIMPLE DROPDOWN -->
                      </div>
                      <!-- /REACTION ITEM -->
<?php } ?>
 <?php if($n_funny>0){ ?>
                      <!-- REACTION ITEM -->
                      <div class="reaction-item">
                        <!-- REACTION IMAGE -->
                        <img class="reaction-image reaction-item-dropdown-trigger" src="<?php echo $url_site; ?>/templates/_panel/img/reaction/funny.png" alt="reaction-funny">
                        <!-- /REACTION IMAGE -->

                        <!-- SIMPLE DROPDOWN -->
                        <div class="simple-dropdown padded reaction-item-dropdown" >
                          <!-- SIMPLE DROPDOWN TEXT -->
                          <p class="simple-dropdown-text"><img class="reaction" src="<?php echo $url_site; ?>/templates/_panel/img/reaction/funny.png" alt="reaction-funny"> <span class="bold">Funny</span></p>
                          <!-- /SIMPLE DROPDOWN TEXT -->
          <?php
$r_type   = "data_reaction";
$likeuscmrc = $db_con->prepare("SELECT  * FROM `options` WHERE  o_parent IN( SELECT  id FROM `like` WHERE sid='{$catdid}' AND  type=2 ) AND o_valuer='funny' AND  o_type='{$r_type}' ORDER BY id " );
$likeuscmrc->execute();
while($uslikerc=$likeuscmrc->fetch(PDO::FETCH_ASSOC)){
$catrusr = $db_con->prepare("SELECT *  FROM users WHERE  id='{$uslikerc['o_order']}'");
$catrusr->execute();
$catrussr=$catrusr->fetch(PDO::FETCH_ASSOC);
           ?>
                          <!-- SIMPLE DROPDOWN TEXT -->
                          <p class="simple-dropdown-text"><?php echo $catrussr['username']; ?></p>
                          <!-- /SIMPLE DROPDOWN TEXT -->
    <?php } ?>
                        </div>
                        <!-- /SIMPLE DROPDOWN -->
                      </div>
                      <!-- /REACTION ITEM -->
<?php } ?>
 <?php if($n_wow>0){ ?>
                      <!-- REACTION ITEM -->
                      <div class="reaction-item">
                        <!-- REACTION IMAGE -->
                        <img class="reaction-image reaction-item-dropdown-trigger" src="<?php echo $url_site; ?>/templates/_panel/img/reaction/wow.png" alt="reaction-wow">
                        <!-- /REACTION IMAGE -->

                        <!-- SIMPLE DROPDOWN -->
                        <div class="simple-dropdown padded reaction-item-dropdown" >
                          <!-- SIMPLE DROPDOWN TEXT -->
                          <p class="simple-dropdown-text"><img class="reaction" src="<?php echo $url_site; ?>/templates/_panel/img/reaction/wow.png" alt="reaction-wow"> <span class="bold">Wow</span></p>
                          <!-- /SIMPLE DROPDOWN TEXT -->
          <?php
$r_type   = "data_reaction";
$likeuscmrc = $db_con->prepare("SELECT  * FROM `options` WHERE  o_parent IN( SELECT  id FROM `like` WHERE sid='{$catdid}' AND  type=2 ) AND o_valuer='wow' AND  o_type='{$r_type}' ORDER BY id " );
$likeuscmrc->execute();
while($uslikerc=$likeuscmrc->fetch(PDO::FETCH_ASSOC)){
$catrusr = $db_con->prepare("SELECT *  FROM users WHERE  id='{$uslikerc['o_order']}'");
$catrusr->execute();
$catrussr=$catrusr->fetch(PDO::FETCH_ASSOC);
           ?>
                          <!-- SIMPLE DROPDOWN TEXT -->
                          <p class="simple-dropdown-text"><?php echo $catrussr['username']; ?></p>
                          <!-- /SIMPLE DROPDOWN TEXT -->
    <?php } ?>
                        </div>
                        <!-- /SIMPLE DROPDOWN -->
                      </div>
                      <!-- /REACTION ITEM -->
<?php } ?>
</div>
