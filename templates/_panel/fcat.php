<?php if($s_st=="buyfgeufb"){  ?>
		<div id="page-wrapper">
           <div class="main-page">
     <?php if(isset($_COOKIE['user'])){  ?>
          <div  >
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
                             echo "<option value=\"{$selrs15['id']}\">{$selrs15['name']}</option>";
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
   <div class=" col-md-12 inbox-grid1">
        <div class="panel panel-default">
  <div class="panel-body">
 	<table class="table table-hover">
    <thead>
							<tr>
                              <th><center>#</center></th>
                              <th><center><?php lang('cat_s'); ?></center></th>
                              <th><center><?php lang('topics'); ?></center></th>
                              <th><center><?php lang('latest_post'); ?></center></th>

                           </tr>
						</thead>
						<tbody>
 <?php



$statement = "`f_cat` WHERE id ORDER BY `id` DESC";
$results =$db_con->prepare("SELECT * FROM {$statement} ");
$results->execute();
while($wt=$results->fetch(PDO::FETCH_ASSOC)) {
  $catdids = $wt['id'];
$catcount = $db_con->prepare("SELECT  COUNT(id) as nbr FROM forum WHERE statu=1 AND cat={$catdids} " );
$catcount->execute();
$abcat=$catcount->fetch(PDO::FETCH_ASSOC);
$catusz = $db_con->prepare("SELECT *  FROM `forum` WHERE statu=1 AND  cat={$catdids} ORDER BY `id` DESC " );
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);
$catdid = $sucat['id'];
if(isset($catdid)){
$catdnb = $db_con->prepare("SELECT  * FROM status WHERE tp_id='{$catdid}' AND s_type=2 " );
$catdnb->execute();
$abdnb=$catdnb->fetch(PDO::FETCH_ASSOC);
$time_stt= convertTime($abdnb['date']);
}else{

  $time_stt = "";

}
echo "
  <tr>
  <td><center><h1><a href=\"{$url_site}/f{$wt['id']}\" style=\"color : #B10DC9 \"><i class=\"fa {$wt['icons']}\" aria-hidden=\"true\"></i></a></h1></center></td>
  <td><center><h4><a   href=\"{$url_site}/f{$wt['id']}\" style=\"color : #B10DC9 \"> {$wt['name']}</a></h4></center></td>
  <td><center><span class=\"badge\"> {$abcat['nbr']} </span></center></td>
  <td><center><div class=\"well\">
  <b style=\"color : #3366FF \"> <i class=\"fa fa-tag\" aria-hidden=\"true\"></i></b>	<a   href=\"{$url_site}/t{$sucat['id']}\" > {$sucat['name']} </a> <br /><p><i class=\"glyphicon glyphicon-time\" aria-hidden=\"true\"></i> منذ {$time_stt}</p>
					   </div></center></td>
</tr>
";

   }

  ?>
      </tbody>
					</table>

                              </div>
                                 </div> <div class="clearfix"></div>
                                  </div> </div>
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