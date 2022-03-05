<?php if($s_st=="buyfgeufb"){  ?>
		<div id="page-wrapper">
           <div class="main-page">
     <?php if(isset($_COOKIE['user'])){

     $catdids = $_GET['f'];
$rescat =$db_con->prepare("SELECT * FROM `f_cat` WHERE id={$catdids} ORDER BY `id` DESC ");
$rescat->execute();
$wtcat=$rescat->fetch(PDO::FETCH_ASSOC);

     ?>
          <div  >

        <ol class="breadcrumb">
					  <li><a href="<?php url_site();  ?>/forum"><?php lang('forum'); ?></a></li>
					  <li class="active"><?php echo $wtcat['name'];  ?></li>
					</ol>
      <div class="well">
					<h1><i class="fa <?php echo $wtcat['icons'];  ?>" aria-hidden="true"></i><center><?php echo $wtcat['name'];  ?></center></h1>
					   </div>
    	<a href="#" data-toggle="modal" data-target="#newdir" class="btn btn-success" ><i class="fa fa-plus" aria-hidden="true"></i> <?php lang('w_new_tpc'); ?></a>

     <!-- //modal newdir -->
              <div class="modal fade bs-example-modal-lg" id="newdir"  role="dialog" aria-labelledby="myLargeModalLabel">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content modal-info">
						<div class="modal-header">
                       <center> <h2><?php lang('w_new_tpc'); ?></h2> </center>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
                             echo "<option value=\"{$selrs15['id']}\" ";
                                  if(isset($_GET['f']) AND ($_GET['f']==$selrs15['id'])) {echo "selected"; }
                             echo " >{$selrs15['name']}</option>";
                             } ?>
                       </select>
                       </div>
                           <input type="hidden" name="s_type" value="2" />
                           <input type="hidden" name="set" value="Publish" />
                           <center>
                           <button  type="submit" name="submit" value="Publish" class="btn btn-primary" >
                           <?php lang('spread'); ?></button>
                           <a href="<?php url_site();  ?>/post" class="btn btn-default" >&nbsp;<i class="fa fa-arrows-alt" aria-hidden="true"></i>&nbsp;</a>
                           <a href="https://www.adstn.gq/kb/myads:Add a new Topic" class="btn btn-default" target="_blank" >&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></a>
                           <button type="button" class="btn btn-default" data-dismiss="modal"><?php lang('close');  ?></button>
                           </center>
                       </form>
							</div>
						</div>
					</div>
				</div>
			</div>

	   <!-- //modal newdir -->

     <div class="clearfix"></div>
    </div>
    <?php } ?>
   <hr />
 <?php ads_site(4); ?>

 <?php

 forum_tpc_list();

  ?>

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
                    var typeId = $(this).parent().find('textarea[name="txt"]');
                    returnArray['txt'] = typeId.val();
                    var typeId = $(this).parent().find('select[name="categ"]');
                    returnArray['categ'] = typeId.val();
                    var typeId = $(this).parent().find('input[name="s_type"]');
                    returnArray['s_type'] = typeId.val();
                    returnArray['submit'] = "Valider";
                    link="<?php url_site();  ?>/requests/status.php" ;
                    }else if(getval=="edit"){
                    var typeId = $(this).parent().find('input[name="name"]');
                    returnArray['name'] = typeId.val();
                    var typeId = $(this).parent().find('textarea[name="txt"]');
                    returnArray['txt'] = typeId.val();
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
<?php }else{ echo"404"; }  ?>