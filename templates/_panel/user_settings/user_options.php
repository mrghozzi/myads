<?php if(isset($s_st)=="buyfgeufb"){ dinstall_d(); ?>
<div class="grid grid-3-6-3 medium-space" >
<div class="grid-column" >
<?php template_mine('user_settings/nav_settings');  ?>
</div>
<div class="grid-column" >
<!--buttons-->
 <div class="widget-box">
      <div class="grid">
						<div class="grid-header">
                        <b><?php  lang('lang'); ?></b>
						</div>
						<div class="grid-body">
							<div class="more-grids">
                            <div class="row">
  <div class="col-sm-6 col-md-3">
    <div class="thumbnail">
    <a href="<?php url_site();  ?>?ar">
      <img src="<?php url_site();  ?>/templates/_panel/img/ar.png" alt="">
      <div class="caption">
        <p>العربية</p>
      </div>
    </a>
    </div>
  </div>
  <div class="col-sm-6 col-md-3">
    <div class="thumbnail">
    <a href="<?php url_site();  ?>?en">
      <img src="<?php url_site();  ?>/templates/_panel/img/en.png" alt="">
      <div class="caption">
        <p>English</p>
      </div>
    </a>
    </div>
  </div>
    <div class="col-sm-6 col-md-3">
    <div class="thumbnail">
    <a href="<?php url_site();  ?>?fr">
      <img src="<?php url_site();  ?>/templates/_panel/img/fr.png" alt="">
      <div class="caption">
        <p>Francais <br>par_absouini</p>
      </div>
    </a>
    </div>
  </div>
  <?php
$o_type = "languages";
$exlanguages = $db_con->prepare("SELECT  * FROM `options` WHERE o_type=:o_type ORDER BY `o_order` DESC " );
$exlanguages->bindParam(":o_type", $o_type);
$exlanguages->execute();
while($exlang=$exlanguages->fetch(PDO::FETCH_ASSOC)){
    ?>
   <div class="col-sm-6 col-md-3">
    <div class="thumbnail">
    <a href="?<?php echo $exlang['o_valuer']; ?>">
      <img src="https://flagcdn.com/32x24/<?php echo $exlang['o_valuer']; ?>.png" onerror="this.src='<?php url_site();  ?>/templates/_panel/img/language.png'" >
      <div class="caption">
        <p><?php echo $exlang['name']; ?></p>
      </div>
    </a>
    </div>
  </div>
 <?php }  ?>
</div>
   </div>
						</div>
					</div>

   				</div>
         <div class="widget-box">
         <h3>Mode</h3>
         <hr />
         <a href="<?php url_site();  ?>?light" class="btn btn-light" >Light</a>
         <a href="<?php url_site();  ?>?dark" class="btn btn-dark" >Dark</a>
         </div>
            </div>
            </div>
<?php
 }else{ echo "404"; }  ?>