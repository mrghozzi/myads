<?php if(isset($s_st)=="buyfgeufb"){

 ?>
    <!-- SECTION BANNER -->
<div class="section-banner" style="background: url(<?php url_site();  ?>/templates/_panel/img/banner/Newsfeed.png) no-repeat 50%;" >
      <!-- SECTION BANNER ICON -->
      <img class="section-banner-icon" src="<?php url_site();  ?>/templates/_panel/img/banner/marketplace-icon.png" >
      <!-- /SECTION BANNER ICON -->

      <!-- SECTION BANNER TITLE -->
      <p class="section-banner-title"><?php lang('update'); ?>&nbsp;|&nbsp;<?php echo $_GET['update'];  ?></p>
      <!-- /SECTION BANNER TITLE -->

      <!-- SECTION BANNER TEXT -->
      <p class="section-banner-text"></p>
      <!-- /SECTION BANNER TEXT -->
</div>
    <!-- /SECTION BANNER -->
   <div class="grid grid">
     <div class="widget-box no-padding">
         <div class="widget-box-status">
          <div class="widget-box-status-content" >
           <form id="addstore" method="post" class="form-horizontal" action="<?php url_site();  ?>/requests/up_product.php?name=<?php echo $_GET['update'];  ?>" >
             <div class="form-row" >
               <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="Version_nbr"><?php lang('Version_nbr');  ?></label>
                      <input type="text" id="profile-name" name="vnbr" value="<?php if_gstore('svnbr');  ?>" placeholder="<?php lang('version');  ?> | EX: v1.0" minlength="2" maxlength="12" pattern="^[-a-zA-Z0-9.]+$" required >
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
             </div>
             <div class="form-row" >
               <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small full">
                      <label for="desc"><?php lang('desc');  ?></label>
                      <textarea id="txt" name="desc"  minlength="10" maxlength="2400" required><?php if_gstore('sdesc');  ?></textarea>
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
             </div>
             <div class="form-row" >
               <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input small active">
                      <label for="file" style=" background-color: #8e44ad; color: #fff; "><?php lang('file');  ?></label>
                      <input type="file" class="form-control" style=" font-family: calibri; -webkit-border-radius: 5px; border: 1px dashed #fff; text-align: center; background-color: #8e44ad; cursor: pointer; color: #fff; " accept=".zip" name="fzip" id="media" >
                      <br />
                       <div class="result">
                        <?php if(isset($_SESSION['slinkzip'])){  echo "<img src=\"{$url_site}/templates/_panel/img/zip.png\"  />&nbsp; ";
                                echo $_SESSION['slinkzip'];
                                echo "<br />" ; }  ?>
                        <input type="txt" style="visibility:hidden" value="<?php if_gstore('slinkzip');  ?>" name="linkzip" id="text" required>
                       </div>
                    </div>
                    <!-- /FORM INPUT -->
                  </div>
             </div>
             <div class="form-item split">
               <!-- FORM SELECT -->
               <?php if(isset($elnk_site) AND ($elnk_site==1)){ ?>
               <a href="https://www.adstn.gq/kb/myads:store:update" class="button default" target="_blank" >&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></a>
               <?php } ?>
               <!-- BUTTON -->
               <button type="submit" name="submit" id="button" value="Publish" class="button primary"><i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp; <?php lang('save');  ?></button>
             </div>
           </form>
          </div>
          <div class="widget-box-status-content" >
           <?php if(isset($_SESSION['snotvalid'])){ echo "<div class=\"alert alert-danger\" role=\"alert\"><strong><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i></strong>&nbsp; ";
            if_gstore('snotvalid');
           echo "</div>" ; }  ?>
           <hr />
          </div>
		 </div>
	 </div>
</div>

<script>
   $(document).ready(function(){
        $('#media').change(function(e){
          $(".result").html("<div class='progress'><div class='progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100' style='width:100%'> Uploading </div> </div> ");
            var file = this.files[0];
            var form = new FormData();
            form.append('fzip', file);
            $.ajax({
                url : "<?php url_site();  ?>/requests/filezip.php",
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data : form,
                success: function(response){
                    $('.result').html(response)
                }
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
<?php
 }else{ echo "404"; }  ?>