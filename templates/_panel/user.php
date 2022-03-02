<?php if(isset($s_st)=="buyfgeufb"){
$my_user_1=$_COOKIE['user'];
$usz = $db_con->prepare("SELECT *  FROM `users` WHERE id=:u_id");
$usz->bindParam(":u_id", $usrRow['o_order']);
$usz->execute();
if($sus=$usz->fetch(PDO::FETCH_ASSOC)){
$flusz = $db_con->prepare("SELECT *  FROM `like` WHERE uid=:u_id AND sid=:s_id AND type=1 ");
$flusz->bindParam(":u_id", $my_user_1);
$flusz->bindParam(":s_id", $usrRow['o_order']);
$flusz->execute();
if($flsus=$flusz->fetch(PDO::FETCH_ASSOC)){
 $follow = "<a   class=\"btn btn-danger\" href=\"{$url_site}/requests/follow.php?unfollow={$sus['id']}\" ><p class=\"fa fa-user-times\" aria-hidden=\"true\"></p></a>";
  }else{
 $follow = "<a  class=\"btn btn-success\" href=\"{$url_site}/requests/follow.php?follow={$sus['id']}\" ><p class=\"fa fa-user-plus\" aria-hidden=\"true\"></p></a>";
  }
 ?>
		<div id="page-wrapper">
           <style>

.coffee-top a {
  position: absolute;
  top: 80%;
  left: 85%;

}
.coffee-top p {
    color: #FFFFFF

}
.coffee-top button {
  position: absolute;
  top: 80%;
  left: 72%;
}
.coffee-a a {
  position: absolute;
  top: 80%;
  left: 72%;
}
           </style>
			<div class="main-page">
                <div class="widget_4">

              
     <?php if(isset($_COOKIE['user']) AND ($_COOKIE['user']==$usrRow['o_order'])){  ?>
      <!-- //modal camera -->
              <div class="modal fade" id="camera" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document">
					<div class="modal-content modal-info">
                        <div class="modal-body">
							<div class="more-grids">
                            <center>
                            <a href="<?php url_site();  ?>/p<?php echo $sus['id']; ?>" class="btn btn-success"><i class="fa fa-upload" aria-hidden="true"></i></a>
                            <a onclick="ourl('<?php url_site();  ?>/<?php echo $sus['img']; ?>');"  class="btn btn-info"><i class="fa fa-image" aria-hidden="true"></i></a>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </center>
                            </div>
                        </div>
                    </div>
               </div>
             </div>
                     <!-- //modal camera -->
             <div class="col-md-12 contentwidgets-grid">
                  <div class="panel panel-primary">
 <div class="panel-body">
<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item">
    <a class="nav-link " data-toggle="tab" href="#Topic" role="tab" aria-controls="Topic"><?php lang('add_topic');  ?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#Directory" role="tab" aria-controls="Site"><?php lang('addWebsite');  ?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#x" role="tab" aria-controls="x"><i class="fa fa-chevron-up" aria-hidden="true"></i></a>
  </li>
</ul>

<div class="tab-content">
<div class="tab-pane " id="x" role="tabpanel" >
  </div>
  <div class="tab-pane " id="Topic" role="tabpanel">
  <div class="panel panel-primary"><div class="panel-heading">
  <div class="modal-content modal-info">
						<div class="modal-header">
                        <p style="color: #000000"><b><?php lang('w_new_tpc');  ?></b></p>
						</div>
						<div class="modal-body">
							<div class="more-grids">
                            <div class="alert alert-success" role="alert" style="display: none;">
                               Published

                              </div>
                              <div class="alert alert-danger" role="alert" style="display: none;">
                              There error. Try again
                               </div>
                      <form  method="POST">
                       <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-edit" aria-hidden="true"></i></span>
                       <input type="text" class="form-control" name="name" placeholder="Web site name" aria-describedby="basic-addon1" required>
                       </div>
                       <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-text-width" aria-hidden="true"></i></span>
                       <textarea name="txt" id="txt" class="form-control"  placeholder="Site Description" required></textarea>
                       </div>
                       <div class="input-group">
                       <span class="input-group-addon" id="basic-addon1"><i class="fa fa-folder" aria-hidden="true"></i></span>
                       <select class="form-control" name="categ" >
                      <?php $selectdir = $db_con->prepare("SELECT *  FROM f_cat ORDER BY `name` ASC ");
                             $selectdir->execute();
                             while($selrs15=$selectdir->fetch(PDO::FETCH_ASSOC)){
                             echo "<option value=\"{$selrs15['id']}\">{$selrs15['name']}</option>";
                             } ?>
                       </select>
                       </div>
                           <input type="hidden" name="s_type" value="2" />
                           <input type="hidden" name="set" value="Publish" />
                           <center>
                           <button  type="submit" name="submit" value="Publish" class="btn btn-primary" >
                           <?php lang('spread');  ?></button>
                           <a href="<?php url_site();  ?>/post" class="btn btn-default" >&nbsp;<i class="fa fa-arrows-alt" aria-hidden="true"></i>&nbsp;</a>
                           <a href="https://www.adstn.gq/kb/myads:Add a new Topic" class="btn btn-default" target="_blank" >&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></a>
                           </center>
                       </form>
							</div>
						</div>
					</div>

  </div></div>
  </div>


  <div class="tab-pane" id="Directory" role="tabpanel">
  <div class="panel panel-primary"><div class="panel-heading">
<div class="modal-content modal-info">
						<div class="modal-header">
                       <p style="color: #000000"><b> <?php lang('addwebsitdir');  ?> </b></p>
						</div>
						<div class="modal-body">
							<div class="more-grids">
                            <div class="alert alert-success" role="alert" style="display: none;">
                               Published

                              </div>
                              <div class="alert alert-danger" role="alert" style="display: none;">
                              There error. Try again
                               </div>
                      <form  method="POST">
                       <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-edit" aria-hidden="true"></i></span>
                       <input type="text" class="form-control" name="name" placeholder="Web site name" aria-describedby="basic-addon1" required>
                       </div>
                       <div class="input-group">
                       <span class="input-group-addon" id="basic-addon1"><i class="fa fa-link" aria-hidden="true"></i></span>
                       <input type="url" class="form-control" name="url" placeholder="http://" aria-describedby="basic-addon1" required>
                       </div>
                       <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-text-width" aria-hidden="true"></i></span>
                       <textarea name="txt" id="txt" class="form-control"  placeholder="Site Description" required></textarea>
                       </div>
                       <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-tag" aria-hidden="true"></i></span>
                       <input type="text" class="form-control" name="tag" placeholder="Keywords: Place a comma (,) between words" aria-describedby="basic-addon1">
                       </div>
                       <div class="input-group">
                       <span class="input-group-addon" id="basic-addon1"><i class="fa fa-folder" aria-hidden="true"></i></span>
                       <select class="form-control" name="categ" >
                      <?php $selectdir = $db_con->prepare("SELECT *  FROM cat_dir WHERE  statu=1 ORDER BY `name` ASC ");
                             $selectdir->execute();
                             while($selrs15=$selectdir->fetch(PDO::FETCH_ASSOC)){
                             echo "<option value=\"{$selrs15['id']}\">{$selrs15['name']}</option>";
                             } ?>
                       </select>
                       </div>
                           <input type="hidden" name="s_type" value="1" />
                           <input type="hidden" name="set" value="Publish" />
                           <center>
                           <button  type="submit" name="submit" value="Publish" class="btn btn-primary" >
                           <?php lang('spread');  ?></button>
                           <a href="<?php url_site();  ?>/add-site.html" class="btn btn-default" >&nbsp;<i class="fa fa-arrows-alt" aria-hidden="true"></i>&nbsp;</a>
                           <a href="https://www.adstn.gq/kb/myads:Add a new Web site" class="btn btn-default" target="_blank" >&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></a>
                           </center>
                       </form>
							</div>
						</div>
					</div>

  </div></div></div>

</div>

<script>
  $(function () {
    $('#myTab a:last').tab('show')
  })
</script>   </div> </div>
             </div>
             <?php } ?>
             <div class="clearfix"> </div>
             </div>
      	<div class="photoday-section">
        <?php if($c_lang=="ar"){  ?>
         <div class="col-md-4 photoday-grid">
        <div class="photoday">


               <div class="photo1" >
		   	 	<div class="coffee">
				<div class="coffee-top">
					<img class="img-responsive" src="<?php url_site();  ?>/<?php echo $sus['img']; ?>" alt="">

                    <?php if(isset($_COOKIE['user']) AND ($_COOKIE['user']==$usrRow['o_order'])){  ?>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#camera" ><p class="fa fa-camera" aria-hidden="true"></p></button>

                 <?php } ?>
                    <?php if(isset($_COOKIE['user']) AND ($_COOKIE['user']==$usrRow['o_order'])){  ?>
				   <a href="<?php url_site();  ?>/e<?php echo $sus['id']; ?>" class="btn btn-success"><p class="fa fa-edit" aria-hidden="true"></p></a>
                    <?php }else if(isset($_COOKIE['user'])){  ?>
                    <div class="coffee-a">
                    <?php echo $follow; ?>
                    </div>
                    <a href="<?php url_site();  ?>/message/<?php echo $sus['id']; ?>" class="btn btn-info"><p class="fa fa-envelope" aria-hidden="true"></p></a>
                    <?php } ?>
				</div>

		       </div>
               <h4><b>@<?php echo $sus['username']." "; online_us($sus['id'])." "; check_us($sus['id']); ?></b></h4>
<h5><?php echo "  أخر إتصال منذ ".convertTime($sus['online']); ?></h5>
    <?php  if(isset($_COOKIE['user']) AND isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin)  ){ ?>
                             <br>    <a href="<?php url_site();  ?>/admincp?us_edit=<?php echo $sus['id']; ?>" class="btn btn-success"><p class="fa fa-edit" aria-hidden="true"></p></a>
                                <?php }
                                if((isset($_COOKIE['user']) AND ($_COOKIE['user']!=$usrRow['o_order'])) OR !isset($_COOKIE['user']) ){ ?>
                     <a href="#" class="btn btn-warning" data-toggle="modal" data-target="#uReport<?php echo $sus['id']; ?>" ><i class="fa fa-flag" aria-hidden="true"></i></a>
                     <?php echo "<!-- //modal Report {$sus['id']} -->
              <div class=\"modal fade\" id=\"uReport{$sus['id']}\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-header\">
                        <i class=\"glyphicon glyphicon-flag\" aria-hidden=\"true\"></i>&nbsp;{$lang['report']}
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">
                                 <h4>{$sus['username']}</h4>
                                 <div class=\"alert alert-success\" role=\"alert\" style=\"display: none;\">
                               has been sent
                              </div>
                              <div class=\"alert alert-danger\" role=\"alert\" style=\"display: none;\">
                              There error. Try again
                               </div>
                               <form method=\"POST\">
                                 <hr>
                                 <textarea name=\"txt\" class=\"form-control\" required></textarea>
                                 <hr>
                                 <input type=\"hidden\" name=\"tid\" value=\"{$sus['id']}\" />
                                 <input type=\"hidden\" name=\"s_type\" value=\"99\" />
                                 <input type=\"hidden\" name=\"set\" value=\"Report\" />
                                 <input type=\"submit\" name=\"submit\" value=\"Send\" class=\"btn btn-primary\" />
                                 <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button>
</form>
							</div>
						</div>
					</div>
				</div>
			</div>

	   <!-- //modal Report {$sus['id']} -->";  ?>
                                <?php } ?>
               <div class="follow">
					<div class="col-xs-4 two">
						<p><?php lang('Followers'); ?></p>
						<a href="<?php url_site();  ?>/followers/<?php echo $sus['id']; ?>" ><span><?php nbr_follow($sus['id'],"sid"); ?></span></a>
					</div>
					<div class="col-xs-4 two">
						<p><?php lang('Posts'); ?></p>
						<span><?php nbr_posts($sus['id']); ?></span>
					</div>
					<div class="col-xs-4 two">
						<p><?php lang('Following'); ?></p>
						<a href="<?php url_site();  ?>/following/<?php echo $sus['id']; ?>" ><span><?php nbr_follow($sus['id'],"uid"); ?></span></a>
					</div>
					<div class="clearfix"> </div>
				</div>
             </div>  <?php ads_site(5); ?> <div class="clearfix"> </div>
            </div>  <div class="clearfix"> </div>
        </div>
        <?php } ?>
      <div class="col-md-8 photoday-grid">
 <?php


 forum_tpc_list();

  ?>
         </div>
          <?php if($c_lang!=="ar"){  ?>
         <div class="col-md-4 photoday-grid">
        <div class="photoday">


               <div class="photo1" >
		   	 	<div class="coffee">
				<div class="coffee-top">
					<img class="img-responsive" src="<?php url_site();  ?>/<?php echo $sus['img']; ?>" alt="">

                    <?php if(isset($_COOKIE['user']) AND ($_COOKIE['user']==$usrRow['o_order'])){  ?>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#camera" ><p class="fa fa-camera" aria-hidden="true"></p></button>

                 <?php } ?>
                    <?php if(isset($_COOKIE['user']) AND ($_COOKIE['user']==$usrRow['o_order'])){  ?>
				   <a href="<?php url_site();  ?>/e<?php echo $sus['id']; ?>" class="btn btn-success"><p class="fa fa-edit" aria-hidden="true"></p></a>
                    <?php }else if(isset($_COOKIE['user'])){  ?>
                    <div class="coffee-a">
                    <?php echo $follow; ?>
                    </div>
                    <a href="<?php url_site();  ?>/message/<?php echo $sus['id']; ?>" class="btn btn-info"><p class="fa fa-envelope" aria-hidden="true"></p></a>
                    <?php } ?>
				</div>

		       </div>
             <h4><b>@<?php echo $sus['username']." "; online_us($sus['id'])." "; check_us($sus['id']); ?></b></h4>
<h5><?php echo " أخر إتصال منذ ".convertTime($sus['online']); ?></h5>
    <?php  if(isset($_COOKIE['user']) AND isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin)  ){ ?>
                             <br>    <a href="<?php url_site();  ?>/admincp?us_edit=<?php echo $sus['id']; ?>" class="btn btn-success"><p class="fa fa-edit" aria-hidden="true"></p></a>
                                <?php }
                                 if((isset($_COOKIE['user']) AND ($_COOKIE['user']!=$usrRow['o_order'])) OR !isset($_COOKIE['user']) ){ ?>
                     <a href="#" class="btn btn-warning" data-toggle="modal" data-target="#uReport<?php echo $sus['id']; ?>" ><i class="fa fa-flag" aria-hidden="true"></i></a>
                     <?php echo "<!-- //modal Report {$sus['id']} -->
              <div class=\"modal fade\" id=\"uReport{$sus['id']}\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-header\">
                        <i class=\"glyphicon glyphicon-flag\" aria-hidden=\"true\"></i>&nbsp;{$lang['report']}
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">
                                 <h4>{$sus['username']}</h4>
                                 <div class=\"alert alert-success\" role=\"alert\" style=\"display: none;\">
                               has been sent
                              </div>
                              <div class=\"alert alert-danger\" role=\"alert\" style=\"display: none;\">
                              There error. Try again
                               </div>
                               <form method=\"POST\">
                                 <hr>
                                 <textarea name=\"txt\" class=\"form-control\" required></textarea>
                                 <hr>
                                 <input type=\"hidden\" name=\"tid\" value=\"{$sus['id']}\" />
                                 <input type=\"hidden\" name=\"s_type\" value=\"99\" />
                                 <input type=\"hidden\" name=\"set\" value=\"Report\" />
                                 <input type=\"submit\" name=\"submit\" value=\"Send\" class=\"btn btn-primary\" />
                                 <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button>
</form>
							</div>
						</div>
					</div>
				</div>
			</div>

	   <!-- //modal Report {$sus['id']} -->";  ?>
                                <?php } ?>
               <div class="follow">
					<div class="col-xs-4 two">
						<p><?php lang('Followers'); ?></p>
						<a href="<?php url_site();  ?>/followers/<?php echo $sus['id']; ?>" ><span><?php nbr_follow($sus['id'],"sid"); ?></span></a>
					</div>
					<div class="col-xs-4 two">
						<p><?php lang('Posts'); ?></p>
						<span><?php nbr_posts($sus['id']); ?></span>
					</div>
					<div class="col-xs-4 two">
						<p><?php lang('Following'); ?></p>
						<a href="<?php url_site();  ?>/following/<?php echo $sus['id']; ?>" ><span><?php nbr_follow($sus['id'],"uid"); ?></span></a>
					 </div>
					<div class="clearfix"> </div>
				</div>
             </div> <?php ads_site(5); ?>
             </div>
        </div>

        <?php } ?>
         <script>
              $(function () {
  $('[data-toggle="popover"]').popover()
});
                   $(function(){


function objectifyForm(formArray) {//serialize data function

var returnArray = {};
for (var i = 0; i < formArray.length; i++){
returnArray[formArray[i]['name']] = formArray[i]['value'];
}
returnArray['submit'] = "Valider";
return returnArray;
}

                     $('form').on('submit', function (e) {
                       e.preventDefault();
                    var returnArray = {};
                    var getSelected = $(this).parent().find('input[name="set"]');
                    var link = "";
                    var getval = getSelected.val();

                    if(getval=="share"){
                    var typeId = $(this).parent().find('input[name="tid"]');
                     returnArray['tid'] = typeId.val();
                     var sType = $(this).parent().find('input[name="s_type"]');
                     returnArray['s_type'] = sType.val();
                     returnArray['submit'] = "Valider";
                     link="<?php url_site();  ?>/requests/share.php" ;
                        }else if(getval=="delete"){
                    var typeId = $(this).parent().find('input[name="did"]');
                    returnArray['did'] = typeId.val();
                    returnArray['submit'] = "Valider";
                    link="<?php url_site();  ?>/requests/delete.php" ;
                        }else if(getval=="Publish"){
                    var typeId = $(this).parent().find('input[name="name"]');
                    returnArray['name'] = typeId.val();
                    var typeId = $(this).parent().find('input[name="url"]');
                    returnArray['url'] = typeId.val();
                    var typeId = $(this).parent().find('textarea[name="txt"]');
                    returnArray['txt'] = typeId.val();
                    var typeId = $(this).parent().find('input[name="tag"]');
                    returnArray['tag'] = typeId.val();
                    var typeId = $(this).parent().find('select[name="categ"]');
                    returnArray['categ'] = typeId.val();
                    var typeId = $(this).parent().find('input[name="s_type"]');
                    returnArray['s_type'] = typeId.val();
                    returnArray['submit'] = "Valider";
                    link="<?php url_site();  ?>/requests/status.php" ;
                     }else if(getval=="edit"){
                    var typeId = $(this).parent().find('input[name="name"]');
                    returnArray['name'] = typeId.val();
                    var typeId = $(this).parent().find('input[name="pts"]');
                    returnArray['pts'] = typeId.val();
                    var typeId = $(this).parent().find('textarea[name="file"]');
                    returnArray['file'] = typeId.val();
                    var typeId = $(this).parent().find('input[name="url"]');
                    returnArray['url'] = typeId.val();
                    var typeId = $(this).parent().find('textarea[name="txt"]');
                    returnArray['txt'] = typeId.val();
                    var typeId = $(this).parent().find('input[name="tag"]');
                    returnArray['tag'] = typeId.val();
                    var typeId = $(this).parent().find('select[name="categ"]');
                    returnArray['categ'] = typeId.val();
                    var typeId = $(this).parent().find('select[name="statu"]');
                    returnArray['statu'] = typeId.val();
                    var typeId = $(this).parent().find('input[name="tid"]');
                    returnArray['tid'] = typeId.val();
                    var typeId = $(this).parent().find('input[name="s_type"]');
                    returnArray['s_type'] = typeId.val();
                    returnArray['submit'] = "Valider";
                    link="<?php url_site();  ?>/requests/edit_status.php" ;
                    }else if(getval=="Report"){
                    var typeId = $(this).parent().find('textarea[name="txt"]');
                    returnArray['txt'] = typeId.val();
                     var typeId = $(this).parent().find('input[name="tid"]');
                    returnArray['tid'] = typeId.val();
                    var typeId = $(this).parent().find('input[name="s_type"]');
                    returnArray['s_type'] = typeId.val();
                    returnArray['submit'] = "Valider";
                    link="<?php url_site();  ?>/requests/report.php" ;
                    }
        $.ajax({
type:"POST",
data:returnArray,
url: link,
contentType: "application/x-www-form-urlencoded;charset=utf-8",
success:function(){ $(".alert-success").fadeIn();},
error: function(){ $(".alert-danger").fadeIn(); }

});
    });
});

                   </script>
                    <script>
    $(function ()
    {
        $('#txt').keyup(function (e){
            if(e.keyCode == 13){
                var curr = getCaret(this);
                var val = $(this).val();
                var end = val.length;

                $(this).val( val.substr(0, curr) + '<br>' + val.substr(curr, end));
            }

        })
    });

    function getCaret(el) {
        if (el.selectionStart) {
            return el.selectionStart;
        }
        else if (document.selection) {
            el.focus();

            var r = document.selection.createRange();
            if (r == null) {
                return 0;
            }

            var re = el.createTextRange(),
            rc = re.duplicate();
            re.moveToBookmark(r.getBookmark());
            rc.setEndPoint('EndToStart', re);

            return rc.text.length;
        }
        return 0;
    }

</script>
                       <div class="clearfix"></div>
								</div>

            	<div class="clearfix"> </div>

               </div>
			</div>
<?php }else{ template_mine('404'); } }else{ echo"404"; }  ?>