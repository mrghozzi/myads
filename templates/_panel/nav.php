<?php if($s_st=="buyfgeufb"){  ?>
<script type="text/javascript">
<!--
    function ourl(s)
    {
        window.open(s,"man","status=yes,toolbar=yes,menubar=yes,location=yes,resizable=yes");
        return false;
    }
//-->
</script>
<!--left-fixed -navigation-->
		<div class="sidebar" role="navigation"  >
            <div class="navbar-collapse" >
				<nav <?php if(isset ($_COOKIE['user']) && isset($_COOKIE['admin']) && $_COOKIE['user']=="1" && ($_COOKIE['admin']==$hachadmin) ){   ?> style="background-color: #660066" <?php }  ?> class="cbp-spmenu cbp-spmenu-vertical cbp-spmenu-right dev-page-sidebar mCustomScrollbar _mCS_1 mCS-autoHide mCS_no_scrollbar" id="cbp-spmenu-s1">
					<div class="scrollbar scrollbar1"  >
						<ul class="nav" id="side-menu">
							<li> <?php if(isset($_COOKIE['user']) && ( isset($_COOKIE['admin']) != $hachadmin) ){ ?>

                                <?php }else if(isset($_COOKIE['user']) && isset($_COOKIE['admin']) && ($_COOKIE['admin']==$hachadmin)  ){ ?>
                                <a href="<?php url_site();  ?>/admincp?home" ><i class="fa fa-tachometer nav_icon"></i><?php lang('board'); ?></a>
                                <?php }else{ ?>
                                <a href="<?php url_site();  ?>/index" ><i class="fa fa-home nav_icon"></i><?php lang('home'); ?></a>
                                <?php } ?>
							</li>
                            <?php if(!(isset($_COOKIE['admin']) && $_COOKIE['admin']==$hachadmin)){  ?>
                            <li>
								<a href="<?php url_site();  ?>/portal"><i class="fa fa-comments-o nav_icon"></i><?php lang('Community'); ?></a>
							</li>
                            <li>
								<a href="<?php url_site();  ?>/store"  ><i class="fa fa-shopping-cart nav_icon">&nbsp;<span class="badge badge-info"><font face="Comic Sans MS">beta</font></span></i><?php lang('Store');  ?></a>
							</li>
                       <?php } ?>
                            <?php if(isset($_COOKIE['user']) && ( isset($_COOKIE['admin']) != $hachadmin) ){ ?>
							<li>
								<a href="#"><i class="fa fa-users nav_icon"></i><?php lang('referal'); ?><span class="fa arrow"></span></a>
								<ul class="nav nav-second-level collapse">
									<li>
										<a href="<?php url_site();  ?>/referral"><?php lang('list'); ?> <?php lang('referal'); ?></a>
									</li>
									<li>
										<a href="<?php url_site();  ?>/r_code"><?php lang('codes'); ?> <?php lang('referal'); ?> </a>
									</li>
								</ul>
								<!-- /nav-second-level -->
							</li>
							<li>
								<a href="#"><i class="fa fa-code nav_icon"></i><?php lang('ads'); ?> <?php lang('codes'); ?> <span class="fa arrow"></span></a>
								<ul class="nav nav-second-level collapse">
									<li>
										<a href="<?php url_site();  ?>/b_code"><?php lang('bannads'); ?></a>
									</li>
									<li>
										<a href="<?php url_site();  ?>/l_code"><?php lang('textads'); ?></a>
									</li>
  <li>
								<a onclick="ourl('<?php url_site();  ?>/visits?id=<?php user_row('id') ; ?>');" href="javascript:void(0);"><i class="fa fa-exchange nav_icon"></i><?php lang('exvisit'); ?></a>
							</li>
								</ul>
								<!-- /nav-second-level -->
							</li>
                          
                            <li>
								<a href="<?php url_site();  ?>/promote"><i class="fa fa-desktop nav_icon"></i><?php lang('Promotysite'); ?></a>
							</li>

							<li>
								<a href="#"><i class="fa fa-bar-chart nav_icon"></i><?php lang('list'); ?> <?php lang('ads'); ?><span class="fa arrow"></span></a>
								<ul class="nav nav-second-level collapse">
									<li>
								        <a href="<?php url_site();  ?>/b_list"><?php lang('list'); echo"&nbsp;"; lang('bannads'); ?></a>
									</li>
									<li>
								    	<a href="<?php url_site();  ?>/l_list"><?php lang('list'); echo"&nbsp;"; lang('textads'); ?></a>
									</li>
                                    <li>
								    	<a href="<?php url_site();  ?>/v_list"><?php lang('list'); echo"&nbsp;"; lang('exvisit'); ?></a>
									</li>

								</ul>
								<!-- //nav-second-level -->
							</li>
                            <!-- extensions -->
                            <?php act_extensions("navbar");  ?>
                            <!-- End extensions -->
                             <?php }
                                    else if(isset($_COOKIE['user']) && isset($_COOKIE['admin']) && $_COOKIE['user']=="1" && ($_COOKIE['admin']==$hachadmin)){
                                     ?>
                                     <li>
								<a href="<?php url_site();  ?>/portal"><i class="fa fa-comments-o nav_icon"></i><?php lang('Community'); ?></a>
							</li>
                            <li>
								<a href="<?php url_site();  ?>/store"  ><i class="fa fa-shopping-cart nav_icon">&nbsp;<span class="badge badge-info"><font face="Comic Sans MS">beta</font></span></i><?php lang('Store');  ?></a>
							</li>

                                     <li>
							     	<a  href="<?php url_site();  ?>/admincp?users"><i class="fa fa-user nav_icon"></i><?php lang('list'); ?> <?php lang('users'); ?></a>
						        	</li>
                                    <li>
								<a href="#"><i class="fa fa-bar-chart nav_icon"></i><?php lang('list'); ?> <?php lang('ads'); ?><span class="fa arrow"></span></a>
								<ul class="nav nav-second-level collapse">
									<li>
								        <a href="<?php url_site();  ?>/admincp?b_list"><?php lang('list'); ?> <?php lang('bannads'); ?></a>
									</li>
									<li>
								    	<a href="<?php url_site();  ?>/admincp?l_list"><?php lang('list'); ?> <?php lang('textads'); ?></a>
									</li>
                                    <li>
								    	<a href="<?php url_site();  ?>/admincp?v_list"><?php lang('list'); ?> <?php lang('exvisit'); ?></a>
									</li>

								</ul>
								<!-- //nav-second-level -->
							</li>
                            <li>
								<a href="#"><i class="fa fa-plug nav_icon"></i> Plugins<span class="fa arrow"></span></a>
								<ul class="nav nav-second-level collapse">
									<li>
								        <a href="<?php url_site();  ?>/admincp?plug"><?php lang('list'); ?></a>
									</li>
                                </ul>
								<!-- //nav-second-level -->
							</li>
                            <li>
								<a href="#"><i class="fa fa-cog nav_icon"><i class="fa fa-comments"></i></i><?php lang('Comusetting'); ?><span class="fa arrow"></span></a>
								<ul class="nav nav-second-level collapse">
									<li>
								        <a href="<?php url_site();  ?>/admincp?knowledgebase"><?php lang('knowledgebase'); ?></a>
									</li>
                                    <li>
								        <a href="<?php url_site();  ?>/admincp?f_cat">Forum Categories</a>
									</li>
                                    <li>
								        <a href="<?php url_site();  ?>/admincp?d_cat">Directory Categories</a>
									</li>
                                    <li>
								        <a href="<?php url_site();  ?>/admincp?emojis">Emojis</a>
									</li>
                                    <li>
						               	<a  href="<?php url_site();  ?>/admincp?news">News Site</a>
						            </li>
                                    <li>
								        <a href="<?php url_site();  ?>/admincp?report">Report</a>
									</li>
                                  </ul>
								<!-- //nav-second-level -->
							</li>
                            <li>
								<a href="#"><i class="fa fa-cog nav_icon"></i><?php lang('options'); ?><span class="fa arrow"></span></a>
								<ul class="nav nav-second-level collapse">
									<li>
								        <a href="<?php url_site();  ?>/admincp?settings"><?php lang('settings'); ?></a>
									</li>
									<li>
								    	<a href="<?php url_site();  ?>/admincp?menu"><?php lang('menu'); ?></a>
									</li>
                                    <li>
								    	<a href="<?php url_site();  ?>/admincp?ads"><?php lang('e_ads'); ?></a>
									</li>
                                    <li>
								    	<a href="<?php url_site();  ?>/admincp?social_login"><?php lang('social_login'); ?></a>
									</li>
                                    </ul>
								<!-- //nav-second-level -->
							</li>

                          <!-- extensions -->
                            <?php act_extensions("admin_navbar");  ?>
                            <!-- End extensions -->
                                     <?php } ?>
						</ul>
					</div>
					<!-- //sidebar-collapse -->
				</nav>
			</div>
		</div>

		<!--left-fixed -navigation-->
<?php }else{ echo"404"; }  ?>