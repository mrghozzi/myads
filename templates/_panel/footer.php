<?php if(isset($s_st) AND ($s_st=="buyfgeufb")){  ?>

    <?php ads_site(6); ?>
	</div>
    <div class="widget-box">
    <center>
    <?php echo "All rights reserved &nbsp;&copy;".date("Y")."&nbsp;"; title_site(''); ?>&nbsp;
    | <a href="<?php url_site();  ?>/privacy-policy">PRIVACY POLICY</a>
    | `MyAds v<?php myads_version();  ?>`  Devlope by <a href="https://github.com/mrghozzi/myads">MrGhozzi</a>
    </center>
    </div>	
<!-- bootstrap -->
<script src="<?php url_site();  ?>/templates/_panel/js/bootstrap.min.js"></script>
<!-- datatables -->
<script type="text/javascript"> $(document).ready(function() { $('#tablepagination').DataTable( { "order": [[0, 'DESC']], language: {  url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/<?php lang('lg'); ?>.json'  } } );  } ); </script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.11.5/sl-1.3.4/datatables.min.js"></script>
<!-- app -->
<script src="<?php url_site();  ?>/templates/_panel/js/app.js"></script>
<!-- page loader -->
<script src="<?php url_site();  ?>/templates/_panel/js/page-loader.js"></script>
<!-- simplebar -->
<script src="<?php url_site();  ?>/templates/_panel/js/simplebar.min.js"></script>
<!-- liquidify -->
<script src="<?php url_site();  ?>/templates/_panel/js/liquidify.js"></script>
<!-- XM_Plugins -->
<script src="<?php url_site();  ?>/templates/_panel/js/xm_plugins.min.js"></script>
<!-- tiny-slider -->
<script src="<?php url_site();  ?>/templates/_panel/js/tiny-slider.min.js"></script>
<!-- chartJS -->
<script src="<?php url_site();  ?>/templates/_panel/js/Chart.bundle.min.js"></script>
<!-- global.hexagons -->
<script src="<?php url_site();  ?>/templates/_panel/js/global.hexagons.js"></script>
<!-- global.tooltips -->
<script src="<?php url_site();  ?>/templates/_panel/js/global.tooltips.js"></script>
<!-- global.charts -->
<script src="<?php url_site();  ?>/templates/_panel/js/global.charts.js"></script>
<!-- global.popups -->
<script src="<?php url_site();  ?>/templates/_panel/js/global.popups.js"></script>
<!-- header -->
<script src="<?php url_site();  ?>/templates/_panel/js/header.js"></script>
<!-- sidebar -->
<script src="<?php url_site();  ?>/templates/_panel/js/sidebar.js"></script>
<!-- content -->
<script src="<?php url_site();  ?>/templates/_panel/js/content.js"></script>
<!-- form.utils -->
<script src="<?php url_site();  ?>/templates/_panel/js/form.utils.js"></script>
<!-- SVG icons -->
<script src="<?php url_site();  ?>/templates/_panel/js/svg-loader.js"></script>
<script type="text/javascript" id="cookieinfo"
	src="//cookieinfoscript.com/js/cookieinfo.min.js"
	data-bg="#615dfa"
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