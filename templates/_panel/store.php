<?php if($s_st=="buyfgeufb"){

 ?>



		<!-- main content start-->
		<div id="page-wrapper">
			<div class="main-page">
				<!--grids-->
				<div class="grids">
                <?php  if(isset($_COOKIE['user'])){  ?>
                <a href="<?php echo $url_site; ?>/add_store" class="btn btn-default" role="button"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;<?php lang('add_product');  ?></a><hr />
                    <p><b><i class="fa fa-gift" aria-hidden="true"></i>&nbsp;Tout Points&nbsp;:&nbsp;<font color="#339966"><?php
                         echo $uRow['pts'];   ?></font>&nbsp;<font face="Comic Sans MS">PTS</font></b>
                         </p>
                        <?php } ?>
                        <hr />
					<div class="progressbar-heading grids-heading">
						<h2><i class="fa fa-shopping-cart" aria-hidden="true"></i>&nbsp;<?php lang('Store');  ?></h2>

                        <hr />
                    </div>

      <div class="row">

    <?php
                 $errstor = 0;
                 $o_type = "store";
                 $stormt = $db_con->prepare("SELECT *  FROM options WHERE o_type=:o_type ORDER BY `id` " );
                 $stormt->bindParam(":o_type", $o_type);
                 $stormt->execute();
                 while($store=$stormt->fetch(PDO::FETCH_ASSOC) ) {

                 if(isset($store['o_order']) AND ($store['o_order']>0)){
                   $storepts = $store['o_order']."&nbspPTS";
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
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <a href="<?php echo $url_site; ?>/producer/<?php echo $store['name']; ?>">
      <img src="<?php echo $store['o_mode']; ?>" onerror="this.src='<?php echo $url_site;  ?>/templates/_panel/images/error_plug.png'" style="width: 280;height: 170;" ></a>
      <div class="caption">
        <h3><a href="<?php echo $url_site; ?>/producer/<?php echo $store['name']; ?>" style="color: black;" >
        <?php echo $store['name']; ?>_<sub><?php echo $storefile['name'];  ?></sub>
        <span class="badge badge-info"><font face="Comic Sans MS"><b><?php echo $storepts; ?>
        </b></font></span>
        <span class="badge badge-warning"><b><?php echo $lang["$stortype"]; ?>
        </b></span></a></h3>
        <?php echo "<a  href=\"{$url_site}/u/{$catussen['id']}\"   ><img class=\"imgu-bordered-sm\" src=\"{$url_site}/{$catussen['img']}\" align=\"left\" style=\"width: 35px;\" alt=\"{$catussen['username']}\">{$catussen['username']}";
            online_us($catussen['id']);
            check_us($catussen['id']);
            echo "</a>  " ;  ?><hr />
        <p><?php echo $store['o_valuer']; ?></p> <hr />
        <p><?php if(isset($_COOKIE['user'])){ ?>
        <a href="javascript:void(0);" data-toggle="modal" data-target="#Download<?php echo $store['id'];  ?>" class="btn btn-primary" role="button"><i class="fa fa-download"></i>&nbsp;<?php lang('download');  ?>&nbsp;<span class="badge badge-info"><font face="Comic Sans MS"><b><?php echo $contfils; ?></b><br></font></span></a>
        <?php }else{ ?>
        <a href="javascript:void(0);" data-toggle="modal" data-target="#Dlogin<?php echo $store['id'];  ?>" class="btn btn-primary" role="button"><i class="fa fa-download"></i>&nbsp;<?php lang('download');  ?>&nbsp;<span class="badge badge-info"><font face="Comic Sans MS"><b><?php echo $contfils; ?></b><br></font></span></a>
        <?php     }  ?>
        <a href="<?php echo $url_site; ?>/producer/<?php echo $store['name']; ?>" class="btn btn-default"  role="button"><i class="fa fa-info-circle"></i>&nbsp;Details</a>
        </p>
       </div>
    </div>
  </div>
   <?php
   if(isset($store['o_order']) AND ($store['o_order']>0)){
                   $storeinfo = $store['o_order']."&nbspPTS";
                 }else{
                    $storeinfo = $lang['tpfree'];
                 }
   echo " <!-- //modal Download {$store['id']} -->
              <div class=\"modal fade\" id=\"Download{$store['id']}\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-header\">
                        <i class=\"fa fa-info-circle\" aria-hidden=\"true\"></i>&nbsp;{$storeinfo}
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">
                                 <center>
                                   <a onclick=\"ourl('{$sdf}');\" href=\"javascript:void(0);\" id=\"D{$store['id']}\" class=\"btn btn-success\" ><i class=\"fa fa-download\"></i>&nbsp;{$lang['download']}</a>
                                   <button type=\"button\" class=\"btn btn-danger\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\"><i class=\"fa fa-times-circle\"></i>&nbsp;{$lang['close']}</span></button>
                                    </center>
                            <br />

							</div>
						</div>
					</div>
				</div>
			</div>
           <script>
     \$(\"document\").ready(function() {
   \$(\"#D{$store['id']}\").click(postlike{$store['id']});

});

function postlike{$store['id']}(){
    \$.ajax({
        url : '$dir_lnk_hash',
        data : {
            test_like : \$(\"#lval\").val()
        },
        datatype : \"json\",
        type : 'post',
        success : function(result) {

        },
        error : function() {

        }
    });
}
   </script>
	   <!-- //modal Download {$store['id']} -->";
    echo " <!-- //modal Dlogin {$store['id']} -->
              <div class=\"modal fade\" id=\"Dlogin{$store['id']}\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-header\">
                        You do not have an account!
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">
                                 <center>
                                   <a href=\"{$url_site}/login\" class=\"btn btn-success\" ><i class=\"fa fa-sign-in\"></i>{$lang['login']}</a>
                        <a href=\"{$url_site}/register\" class=\"btn btn-danger\" ><i class=\"fa fa-user-plus\"></i>{$lang['sign_up']}</a>
                                    </center>
                            <br />

							</div>
						</div>
					</div>
				</div>
			</div>

	   <!-- //modal Dlogin {$store['id']} -->";
     $errstor ++;
    }
    if(isset($errstor) AND ($errstor==0)){
      echo "<center><pre>";
     lang('sieanpr');
     echo "</pre></center>";
    }

     ?>


</div>







  				</div>
				<!--//grids-->

			</div>
		</div>




<?php  }else{ echo "404"; }  ?>