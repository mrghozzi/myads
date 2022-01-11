<?php if($s_st=="buyfgeufb"){  ?>
<script>
$('code').each(function(){
$(this).replaceWith($('<pre>' + this.innerHTML + '</pre>'));
});
</script>
			<div class="copy-section">
         <p><a href="#" data-toggle="modal" data-target="#language"><i class="fa fa-language"></i> <?php  lang('language'); ?></a>
         &nbsp;.&nbsp;<a href="<?php url_site();  ?>/privacy-policy">PRIVACY POLICY</a><br />
         <?php echo "&copy;".date("Y")."&nbsp;"; title_site(''); ?>
                . All rights reserved
                | `MyAds v<?php myads_version();  ?>` Devlope by <a href="http://www.krhost.ga/">Kariya Host</a></p>
		</div>
        <!-- //modal Lang -->
              <div class="modal fade" id="language" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document">
					<div class="modal-content modal-info">
						<div class="modal-header">
                        <b><?php  lang('lang'); ?></b>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						</div>
						<div class="modal-body">
							<div class="more-grids">
                            <div class="row">
  <div class="col-sm-6 col-md-3">
    <div class="thumbnail">
    <a href="?ar">
      <img src="<?php url_site();  ?>/templates/_panel/images/ar.png" alt="">
      <div class="caption">
        <p>العربية</p>
      </div>
    </a>
    </div>
  </div>
  <div class="col-sm-6 col-md-3">
    <div class="thumbnail">
    <a href="?en">
      <img src="<?php url_site();  ?>/templates/_panel/images/en.png" alt="">
      <div class="caption">
        <p>English</p>
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
      <img src="https://www.countryflags.io/<?php echo $exlang['o_valuer']; ?>/flat/32.png" onerror="this.src='<?php url_site();  ?>/templates/_panel/images/language.png'" >
      <div class="caption">
        <p><?php echo $exlang['name']; ?></p>
      </div>
    </a>
    </div>
  </div>
 <?php }  ?>
</div>
                            <button type="button" class="btn btn-default" data-dismiss="modal"><?php lang('close'); ?></button>
                          </div>
						</div>
					</div>
				</div>
			</div>

			<!-- //modal Lang -->
            <!-- extensions -->
            <?php act_extensions("footer");  ?>
            <!-- End extensions -->
	</div>
              <script src="<?php url_site();  ?>/templates/_panel/js/modernizr.custom.js"></script>



				<!--scrolling js-->
				<script src="<?php url_site();  ?>/templates/_panel/js/jquery.nicescroll.js"></script>
                <script src="<?php url_site();  ?>/templates/_panel/js/underscore-min.js" type="text/javascript"></script>
<script src= "<?php url_site();  ?>/templates/_panel/js/moment-2.2.1.js" type="text/javascript"></script>
<script src="<?php url_site();  ?>/templates/_panel/js/clndr.js" type="text/javascript"></script>
<script src="<?php url_site();  ?>/templates/_panel/js/site.js" type="text/javascript"></script>
<!-- Metis Menu -->
<!-- Bootstrap Core JavaScript -->
          <script type="text/javascript" src="https://cdn.datatables.net/r/bs-3.3.5/jqc-1.11.3,dt-1.10.8/datatables.min.js"></script>
<script src="<?php url_site();  ?>/templates/_panel/js/metisMenu.min.js"></script>
<script src="<?php url_site();  ?>/templates/_panel/js/custom.js"></script>
<script type="text/javascript" id="cookieinfo"
	src="//cookieinfoscript.com/js/cookieinfo.min.js"
	data-bg="#660066"
	data-fg="#FFFFFF"
	data-link="#FFFFFF"
	data-cookie="CookieInfo"
	data-text-align="left"
    data-close-text="<?php lang('close'); ?>"
    data-linkmsg="<?php lang('MoreInfo'); ?>"
    data-moreinfo ="<?php url_site();  ?>/privacy-policy"
    data-message="<?php lang('cookieinfo'); ?>"
     >
</script>


</body>
</html>
<?php }else{ echo"404"; }  ?>