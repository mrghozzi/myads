<?php if($s_st=="buyfgeufb"){  ?>
 <style>

.grid-inbox i {
    color: #FFFFFF

}

}
           </style>
		<div id="page-wrapper">
           <div class="main-page">
<?php  if(isset($_GET['tag'])){   ?>


<h1> <?php echo "#".$_GET['tag'];   ?></h1>


<?php  }else{ ?>
 <?php if(isset($_COOKIE['user'])){  ?>
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
    <a class="nav-link" data-toggle="tab" href="#service" role="tab" aria-controls="service"><?php lang('add_photo');  ?>&nbsp;<span class="badge badge-info"><font face="Comic Sans MS">beta<br></font></span></a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#x" role="tab" aria-controls="x"><i class="fa fa-chevron-up" aria-hidden="true"></i></a>
  </li>
</ul>

<div class="tab-content">
<div class="tab-pane " id="x" role="tabpanel" >
  </div>
  <div class="tab-pane " id="Topic" role="tabpanel">
  <div class="panel"><div class="panel-heading">
  <div class="modal-content modal-info">
						<div class="modal-header">
                        <p style="color: #000000"><b><?php lang('w_new_tpc');  ?></b></p>
						</div>
						<div class="modal-body">
							<div class="more-grids">
                            <div class="alert alert-success" role="alert" style="display: none;">
<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                               Published

                              </div>
                              <div class="alert alert-danger" role="alert" style="display: none;">
<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                              There error. Try again
                               </div>
                      <form  method="POST">
                       <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-edit" aria-hidden="true"></i></span>
                       <input type="text" class="form-control" name="name" placeholder="Topic name" aria-describedby="basic-addon1" required>
                       </div>
                       <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-text-width" aria-hidden="true"></i></span>
                       <textarea name="txt"  id="txt" class="form-control"  placeholder="Description" required></textarea>

                       </div>
<?php   if($uRow['ucheck']==1) {
?>
               <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                       <input type="datetime-local" class="form-control" name="edte" aria-describedby="basic-addon1" >
                       <span class="input-group-addon" id="basic-addon1"> (options) </span>
                       </div>
<?php } ?>
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


  <div class="tab-pane " id="service" role="tabpanel">
  <div class="panel"><div class="panel-heading">
  <div class="modal-content modal-info">
						<div class="modal-header">

                        <p style="color: #000000"><b><?php lang('add_newphoto');  ?></b> &nbsp;<span class="badge badge-info"><font face="Comic Sans MS">beta<br></font></span></p>
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
                       <input type="text" class="form-control" name="name" placeholder="service name" aria-describedby="basic-addon1" required>
                       </div>
                       <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-text-width" aria-hidden="true"></i></span>
                       <textarea name="txt"  id="txt" class="form-control"  placeholder="Description" required></textarea>
                       </div>


<div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-image" aria-hidden="true"></i></span>
<textarea style="visibility:hidden" name="file"  required></textarea>

                       </div>

                           <input type="hidden" name="s_type" value="4" />
                           <input type="hidden" name="set" value="Publish" />
                           <center>
                           <button  type="submit" name="submit" value="Publish" class="btn btn-primary" >
                           <?php lang('spread');  ?></button>
                           <a href="https://www.adstn.gq/kb/myads:Add a new image" class="btn btn-default" target="_blank" >&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></a>
                          </center>
                       </form>
                            </div>
						</div>
					</div>

  </div></div>
  </div>


  <div class="tab-pane" id="Directory" role="tabpanel">
  <div class="panel"><div class="panel-heading">
<div class="modal-content modal-info">
						<div class="modal-header">
                       <p style="color: #000000"><b> <?php lang('addwebsitdir');  ?> </b></p>
						</div>
						<div class="modal-body">
							<div class="more-grids">
                            <div class="alert alert-success"  role="alert" style="display: none;">
                               Published

                              </div>
                              <div class="alert alert-danger"  role="alert" style="display: none;">
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
                       <textarea name="txt" id="txt"  class="form-control"   placeholder="Site Description" required></textarea>
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
<?php } ?>
<?php } ?>
   <hr />
   <?php ads_site(4); ?>
     <div class="col-md-8 photoday-grid">
<?php


 forum_tpc_list();

  ?>
     </div>
       </div>
<div class="col-md-4 photoday-grid">
     <div class="photoday">
        <div class="col-md-12 inbox-grid">
          <div class="grid-inbox">
           <div class="inbox-bottom">
<ul>
  <li><a href="<?php url_site();  ?>/forum" class="compose" ><i class="fa fa-comments-o"></i> <?php lang('forum');  ?></a></li>
  <li><a href="<?php url_site();  ?>/directory" class="compose" ><i class="fa fa-globe"></i> <?php lang('directory');  ?></a></li>
  <li><a href="<?php url_site();  ?>/store" class="compose" ><i class="fa fa-shopping-cart"></i> <?php lang('Store');  ?>&nbsp;<span class="badge badge-info"><font face="Comic Sans MS">beta<br></font></span></a></li>
</ul>
           </div>
<div class="online">
	 <h4 style="color: #000099;" >Suggestions</h4>
	 <ul>
<?php
                if(isset($uRow['id'])){
                  $s_usid =$uRow['id'];
                }else{
                  $s_usid = 0;
                }
     $stt = $db_con->prepare("SELECT *,MD5(RAND()) AS m FROM users where  NOT(id = :id)  ORDER BY m LIMIT 5 " );
     $stt->execute(array(':id'=>$s_usid));
     while($usb=$stt->fetch(PDO::FETCH_ASSOC)){ ?>
		   <li><a href="<?php url_site();  ?>/u/<?php echo $usb['id']; ?>"> <b><?php check_us($usb['id']); echo $usb['username']; ?></b> <?php online_us($usb['id']); ?></a></li>
     <?php } ?>
	</ul>
</div>
<hr>
<?php ads_site(5); ?>
        </div>
      </div>
    </div>
</div>
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
                    var typeId = $(this).parent().find('input[name="pts"]');
                    returnArray['pts'] = typeId.val();
                    var typeId = $(this).parent().find('textarea[name="file"]');
                    returnArray['file'] = typeId.val();
                    var typeId = $(this).parent().find('input[name="url"]');
                    returnArray['url'] = typeId.val();
                     var typeId = $(this).parent().find('input[name="edte"]');
                    returnArray['edte'] = typeId.val();
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
<script> $('.btn').on('click', function () { var $btn = $(this).button('loading') // business logic...
$btn.button('reset') }) </script>
             <div class="clearfix"></div>
			 </div>
             <div class="clearfix"> </div>
           </div>
           </div>
<?php }else{ echo"404"; }  ?>