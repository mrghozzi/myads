<?php if($s_st=="buyfgeufb"){  ?>
<!-- footer -->
	<div class="footer" style="background-color: white;" >
		<div class="container">
			<div class="agileinfo_footer_grids">
				<div class="col-md-4 agileinfo_footer_grid">
					<h2><a href="<?php url_site(); ?>"><?php title_site(''); ?></a></h2>
					<p><?php  descr_site(); ?></p>

				</div>
				<div class="col-md-4 agileinfo_footer_grid">
					<h3><?php lang('contact'); ?></h3>
					<ul class="agileinfo_footer_grid_list">
						<li><i class="glyphicon glyphicon-envelope" aria-hidden="true"></i><a href="mailto:<?php mail_site();  ?>"><?php mail_site();  ?></a></li>
                    </ul>
                    <ul class="social-icons">
						<li><a href="<?php social_site('facebook'); ?>" class="icon-button twitter"><i class="icon-twitter"></i><span></span></a></li>
						<li><a href="<?php social_site('twitter'); ?>" class="icon-button google"><i class="icon-google"></i><span></span></a></li>
						<li><a href="<?php social_site('linkedin'); ?>" class="icon-button v"><i class="icon-v"></i><span></span></a></li>
                    </ul>
				</div>
				<div class="col-md-4 agileinfo_footer_grid">
					<h3><?php lang('Implink'); ?></h3>
					<ul class="agileinfo_footer_grid_nav">
						<li><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span><a href="<?php url_site();  ?>/index"><?php lang('home'); ?></a></li>
                        <li><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span><a href="<?php url_site();  ?>/privacy-policy">PRIVACY POLICY</a></li>
						<li><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span><a href="#">Mail Us</a></li>
					</ul>
				</div>
				<div class="clearfix"> </div>
			</div>
			<div class="w3agile_footer_copy">
				<p><?php echo "&copy;".date("Y")."&nbsp;"; title_site(''); ?>
                . All rights reserved
                | `MyAds v<?php myads_version();  ?>`  Devlope by <a href="http://www.krhost.ga/">Kariya Host</a></p>
			</div>
		</div>
	</div>
<!-- //footer -->
<!-- for bootstrap working -->
	<script src="<?php url_site(); echo "/templates/".$template ;  ?>/js/bootstrap.js"></script>
<!-- //for bootstrap working -->
<!-- here stars scrolling icon -->
	<script type="text/javascript">
		$(document).ready(function() {
			/*
				var defaults = {
				containerID: 'toTop', // fading element id
				containerHoverID: 'toTopHover', // fading element hover id
				scrollSpeed: 1200,
				easingType: 'linear'
				};
			*/

			$().UItoTop({ easingType: 'easeOutQuart' });

			});
	</script>
<!-- //here ends scrolling icon -->
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