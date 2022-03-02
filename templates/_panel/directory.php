<?php if(isset($s_st)=="buyfgeufb"){  ?>
		<div id="page-wrapper">
           <div class="main-page">
    <?php if(isset($_COOKIE['user'])){  ?>
           <div  >
   	<a href="#" data-toggle="modal" data-target="#newdir" class="btn btn-success" ><?php lang('addWebsite');  ?>&nbsp;<i class="fa fa-plus" aria-hidden="true"></i> </a>
     <br /><!-- //modal newdir -->
              <div class="modal fade" id="newdir" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document">
					<div class="modal-content modal-info">
						<div class="modal-header">
                       <center> <h2><?php lang('addwebsitdir');  ?></h2> </center>
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
    </div> <hr />
    <?php }else{?>
    <a href="<?php url_site();  ?>/add-site.html" class="btn btn-success" ><i class="fa fa-plus" aria-hidden="true"></i> </a>
     <?php }  ?>
           <ol class="breadcrumb">
  <li><a href="<?php url_site();  ?>/directory"><?php lang('home');  ?></a></li>
  <?php if(isset($_GET['cat'])){

 try{
$catdir = $db_con->prepare("SELECT *  FROM cat_dir WHERE  statu=1 AND id=".$_GET['cat'] );
$catdir->execute();
$catdirs=$catdir->fetch(PDO::FETCH_ASSOC);
 }
	catch(PDOException $e){
     template_mine('404');
    }
 if($catdirs['sub']=="0"){
  echo "<li>{$catdirs['name']}</li>";
  }else{
 $catdirsb = $db_con->prepare("SELECT *  FROM cat_dir WHERE  statu=1 AND id=".$catdirs['sub'] );
$catdirsb->execute();
$catdirsub=$catdirsb->fetch(PDO::FETCH_ASSOC);
 echo "<li><a href=\"{$url_site}/cat/{$catdirsub['id']}\">{$catdirsub['name']}</a></li>";
 echo "<li>{$catdirs['name']}</li>";

  }
   } ?>
</ol>
 <?php
       if(isset($_GET['errMSG'])){  ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <center>Not a valid URL</center>
</div>
 <?php }  ?>
 <?php if(isset($_GET['cat'])){ $get_cat=$_GET['cat'];
   if($catdirs['sub']=="0"){
 ?>
     <div class="page-header">
  <center><h1><b><?php echo $catdirs['name']; ?></b></h1></center>
</div>

<ul class="nav nav-pills" role="tablist">
<?php $catsum = $db_con->prepare("SELECT  * FROM cat_dir WHERE sub={$get_cat} AND statu=1 ORDER BY `ordercat` " );
$catsum->execute();
while($sucats=$catsum->fetch(PDO::FETCH_ASSOC))
{
$catdids=$sucats['id'];
 ?>
  <li role="presentation" class="active"><a href="<?php url_site();  ?>/cat/<?php echo $catdids; ?>"><?php echo $sucats['name']; ?>
  <span class="badge"><?php $catcount = $db_con->prepare("SELECT  COUNT(id) as nbr FROM directory WHERE cat=$catdids AND statu=1" );
$catcount->execute();
$abcat=$catcount->fetch(PDO::FETCH_ASSOC);
echo $abcat['nbr']; ?></span></a></li>
  <?php } ?>
</ul>
<hr />
<?php }  ?>
<?php ads_site(4); ?>
  <div class="page-header">
  <h1><?php lang('latest_sites');  ?> </h1>
</div>

   <div class="photoday-section">

  <?php dir_cat_list(); }else{   ?>
  <div class="page-header">
  <center><h1><b><?php lang('cat_s');  ?></b></h1></center>
</div>
<ul class="nav nav-pills" role="tablist">
<?php $catsum = $db_con->prepare("SELECT  * FROM cat_dir WHERE sub=0 AND statu=1 ORDER BY `ordercat` " );
$catsum->execute();
while($sucats=$catsum->fetch(PDO::FETCH_ASSOC))
{
$catdids=$sucats['id'];
 ?>
  <li role="presentation" class="active"><a href="<?php url_site();  ?>/cat/<?php echo $catdids; ?>"><?php echo $sucats['name']; ?>
  <span class="badge"><?php $catcount = $db_con->prepare("SELECT  COUNT(id) as nbr FROM directory WHERE cat=$catdids AND statu=1" );
$catcount->execute();
$abcat=$catcount->fetch(PDO::FETCH_ASSOC);
echo $abcat['nbr']; ?></span></a></li>
  <?php } ?>
</ul>
<hr />
<?php ads_site(4); ?>
  <div class="page-header">
  <h1><?php lang('latest_sites');  ?></h1>
</div>

   <div class="photoday-section">
 <?php

 dir_cat_list();

  } ?>

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
<?php }else{ echo"404"; }  ?>