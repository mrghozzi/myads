<?php if($s_st=="buyfgeufb"){  ?>
<div class="btn-group-vertical">
    <a href="<?php url_site();  ?>/admincp?home" class="btn btn-primary" ><i class="fa fa-tachometer "></i>&nbsp;<?php lang('board'); ?></a>
    <a  href="<?php url_site();  ?>/admincp?users" class="btn btn-secondary" ><i class="fa fa-users "></i>&nbsp;<?php lang('list'); ?> <?php lang('users'); ?></a>
    <p class="btn btn-light" ><i class="fa fa-bar-chart "></i>&nbsp;<?php lang('list'); ?>&nbsp;<?php lang('ads'); ?></p>
    <a class="btn btn-dark" href="<?php url_site();  ?>/admincp?b_list"><?php lang('list'); ?> <?php lang('bannads'); ?></a>
	<a class="btn btn-dark" href="<?php url_site();  ?>/admincp?l_list"><?php lang('list'); ?> <?php lang('textads'); ?></a>
	<a class="btn btn-dark" href="<?php url_site();  ?>/admincp?v_list"><?php lang('list'); ?> <?php lang('exvisit'); ?></a>
    <p class="btn btn-light" ><i class="fa fa-cog"></i><i class="fa fa-comments"></i>&nbsp;<?php lang('Comusetting'); ?></p>
    <a class="btn btn-dark" href="<?php url_site();  ?>/admincp?knowledgebase"><?php lang('knowledgebase'); ?></a>
	<a class="btn btn-dark" href="<?php url_site();  ?>/admincp?f_cat">Forum Categories</a>
	<a class="btn btn-dark" href="<?php url_site();  ?>/admincp?d_cat">Directory Categories</a>
	<a class="btn btn-dark" href="<?php url_site();  ?>/admincp?emojis">Emojis</a>
	<a class="btn btn-dark" href="<?php url_site();  ?>/admincp?news">News Site</a>
	<a class="btn btn-dark" href="<?php url_site();  ?>/admincp?report">Report</a>
    <p class="btn btn-light" ><i class="fa fa-paint-brush"></i>&nbsp;<?php lang('style'); ?></p>
    <a class="btn btn-dark" href="<?php url_site();  ?>/admincp?widgets"><i class="fa fa-th-large"></i> <?php lang('widgets'); ?></a>
    <a class="btn btn-dark" href="<?php url_site();  ?>/admincp?menu"><i class="fa fa-bars"></i> <?php lang('menu'); ?></a>
    <a class="btn btn-dark" href="<?php url_site();  ?>/admincp?plug" ><i class="fa fa-plug"></i> Plugins</a>
    <p class="btn btn-light" ><i class="fa fa-cog"></i>&nbsp;<?php lang('options'); ?></p>
    <a class="btn btn-dark" href="<?php url_site();  ?>/admincp?settings"><?php lang('settings'); ?></a>
    <a class="btn btn-dark" href="<?php url_site();  ?>/admincp?social_login"><?php lang('social_login'); ?></a>

</div>
<?php }else{ echo"404"; }  ?>