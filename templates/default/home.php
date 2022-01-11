<?php if($s_st=="buyfgeufb"){  ?>
<!-- steps -->
	<div class="steps" id="log" style="background-color: white;" >
		<div class="container">
			<h3 class="head"><?php lang('iysstat'); ?></h3>
			<p class="urna"><?php lang('simples'); ?></p>
			<div class="wthree_steps_grids">
				<div class="col-md-4 wthree_steps_grid">
					<div class="wthree_steps_grid1 wthree_steps_grid1_after">
						<div class="wthree_steps_grid1_sub">
							<span class="glyphicon glyphicon-bullhorn" aria-hidden="true"></span>
						</div>
					</div>
					<h4><?php lang('bannads'); ?></h4>
					<p><?php lang('pboysit'); ?></p>
				</div>
				<div class="col-md-4 wthree_steps_grid">
					<div class="wthree_steps_grid1 wthree_steps_grid1_after">
						<div class="wthree_steps_grid1_sub">
							<span class="glyphicon glyphicon-text-size" aria-hidden="true"></span>
						</div>
					</div>
					<h4><?php lang('textads'); ?></h4>
					<p><?php lang('ptaoyws'); ?></p>
				</div>
				<div class="col-md-4 wthree_steps_grid">
					<div class="wthree_steps_grid1">
						<div class="wthree_steps_grid1_sub">
							<span class="glyphicon glyphicon-sort" aria-hidden="true"></span>
						</div>
					</div>
					<h4><?php lang('exvisit'); ?></h4>
					<p><?php lang('ryrialx'); ?></p>
				</div>
				<div class="clearfix"> </div>
			</div>
		</div>
	</div>
<!-- //steps -->
<!-- count-down -->
	<div class="newsletter" >
		<div class="container">
			<div class="col-md-3 agile_count_grid">
				<div class="agile_count_grid_left">
					<span class="glyphicon glyphicon-bullhorn" aria-hidden="true"></span>
				</div>
				<div class="agile_count_grid_right">
					<p class="counter"><?php lang('bannads'); ?> : <?php nbr_state('banner'); ?></p>

				</div>
				<div class="clearfix"> </div>
				<h3><?php lang('Views'); ?> : <?php admin_state('banner'); ?></h3>
             </div>
			<div class="col-md-3 agile_count_grid">
				<div class="agile_count_grid_left">
					<span class="glyphicon glyphicon-text-background" aria-hidden="true"></span>
				</div>
				<div class="agile_count_grid_right">
					<p class="counter"><?php lang('textads'); ?> : <?php nbr_state('link'); ?></p>
				</div>
				<div class="clearfix"> </div>
				<h3><?php lang('Views'); ?> : <?php admin_state('link'); ?></h3>
			</div>
			<div class="col-md-3 agile_count_grid">
				<div class="agile_count_grid_left">
					<span class="glyphicon glyphicon-transfer" aria-hidden="true"></span>
				</div>
				<div class="agile_count_grid_right">
					<p class="counter"><?php lang('exvisit'); ?> : <?php nbr_state('visits');  ?></p>
				</div>
				<div class="clearfix"> </div>
				<h3>Directory Link : <?php nbr_state('directory');  ?></h3>
			</div>
			<div class="col-md-3 agile_count_grid">
				<div class="agile_count_grid_left">
					<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
				</div>
				<div class="agile_count_grid_right">
					<p class="counter"><?php lang('users'); ?> : <?php nbr_state('users');  ?></p>
				</div>
				<div class="clearfix"> </div>
				<h3><?php lang('topics'); ?> : <?php nbr_state('forum');  ?></h3>
			</div>
			<div class="clearfix"> </div>
				<!-- Starts-Number-Scroller-Animation-JavaScript -->
					<script src="<?php url_site(); echo "/templates/".$template ;  ?>/js/waypoints.min.js"></script>
					<script src="<?php url_site(); echo "/templates/".$template ;  ?>/js/counterup.min.js"></script>
					<script>
						jQuery(document).ready(function( $ ) {
							$('.counter').counterUp({
								delay: 20,
								time: 1000
							});
						});
					</script>
				<!-- //Starts-Number-Scroller-Animation-JavaScript -->
		</div>
	</div>
<!-- //count-down -->

<!-- mail -->
	<div class="mail" style="background-color: white;" >
		<div class="container">
        	<div class="agileinfo_mail_grids">

             <center><h3><?php lang('ads'); ?></h3><hr />

             <?php ads_site(1);  ?>  <hr /></center>
				<div class="clearfix"> </div>
			</div>
		</div>
	</div>
<!-- //mail -->

<!-- banner1 -->
	<div class="banner1">
		<div class="container">
                <hr />
            <center><!-- ADStn code begin --><script language="javascript" src="<?php url_site();  ?>/bn.php?ID=1&px=728"></script><!-- ADStn code begin --></center> <hr />
		</div>
	</div>
<!-- //banner1 -->
<?php }else{ echo"404"; }  ?>