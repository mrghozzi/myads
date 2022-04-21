<?php if($s_st=="buyfgeufb"){  ?>
<div class="btn-group-vertical">
    <a href="<?php url_site();  ?>/e<?php echo $_COOKIE['user']; ?>" class="btn btn-primary" ><i class="fa fa-tachometer "></i>&nbsp;<?php lang('e_profile'); ?></a>
    <a href="<?php url_site();  ?>/p<?php echo $_COOKIE['user']; ?>" class="btn btn-success" ><i class="fa fa-image "></i>&nbsp;Change Avatar/Cover</a>
    <a href="<?php url_site();  ?>/options/<?php echo $_COOKIE['user']; ?>" class="btn btn-warning" ><i class="fa fa-plug "></i>&nbsp;<?php lang('options'); ?></a>
</div>
<?php }else{ echo"404"; }  ?>