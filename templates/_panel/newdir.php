<?php if($s_st=="buyfgeufb"){  ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/default.min.css" />
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/sceditor.min.js"></script>
<div id="page-wrapper">
			<div class="widget-box">
              <div class="modal-content modal-info">
                    	<div class="modal-header">
                        <h2><?php lang('addwebsitdir');  ?></h2>
                        </div>
                        <?php if(isset($_GET["errMSG"])) {   ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <strong>Error!</strong> <?php echo $_GET["errMSG"];  ?>.
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<?php  }  ?>
						<div class="modal-body">
                        	<div class="more-grids">
                 <form  method="POST" action="<?php url_site();  ?>/requests/status.php" >
                 <div class="form-row split" >
                  <div class="form-item">
                    <div class="form-input social-input small active">
                      <!-- name -->
                      <div class="social-link no-hover name">
                        <!-- ICON  -->
                        <i class="fa fa-edit" aria-hidden="true"></i>
                        <!-- /ICON  -->
                      </div>
                      <!-- /name -->
                      <label for="name">Web Site Name</label>
                      <input type="text" id="name" name="name" value="<?php if(isset($_GET["title"])) { echo $_GET["title"]; }  ?>">
                    </div>
                 </div>
                 <div class="form-item">
                    <div class="form-input social-input small active">
                      <!--  LINK -->
                      <div class="social-link no-hover url">
                        <!-- ICON  -->
                        <i class="fa fa-link" aria-hidden="true"></i>
                        <!-- /ICON -->
                      </div>
                      <!-- / LINK -->
                      <label for="url">http://</label>
                      <input type="text" id="url" name="url" value="<?php if(isset($_GET["url"])) { echo $_GET["url"]; }  ?>" required>
                    </div>
                 </div>
                 </div>
                 <div class="form-row">
                  <!-- FORM ITEM -->
                   <div class="form-item">
                     <!-- FORM INPUT -->
                      <div class="form-input small mid-textarea">
                       <label for="description"><i class="fa fa-text-width" aria-hidden="true"></i>&nbsp;|&nbsp;Description</label>
                        <textarea id="description" name="txt"><?php if(isset($_GET["txt"])) { echo $_GET["txt"]; }  ?></textarea>
                      </div>
                     <!-- /FORM INPUT -->
                   </div>
                  <!-- /FORM ITEM -->
                 </div>
                 <div class="form-row split">
                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <!-- FORM SELECT -->
                    <div class="form-select">
                      <label for="profile-status"><i class="fa fa-folder" aria-hidden="true"></i>&nbsp;Category</label>
                      <select id="profile-status" name="categ">
                        <?php
                        $selectdir = $db_con->prepare("SELECT *  FROM cat_dir WHERE  statu=1 AND sub=0 ORDER BY `name` ASC ");
                        $selectdir->execute();
                        while($selrs15=$selectdir->fetch(PDO::FETCH_ASSOC)){
                        echo "<option value=\"{$selrs15['id']}\">{$selrs15['name']}</option>";
                        $sub_cat = $selrs15['id'];
                        $selectsdir = $db_con->prepare("SELECT *  FROM cat_dir WHERE  statu=1 AND sub={$sub_cat} ORDER BY `name` ASC ");
                        $selectsdir->execute();
                        while($selrsu15=$selectsdir->fetch(PDO::FETCH_ASSOC)){
                        echo "<option value=\"{$selrsu15['id']}\">_{$selrsu15['name']}</option>";
                             }

                        } ?>
                      </select>
                      <!-- FORM SELECT ICON -->
                      <svg class="form-select-icon icon-small-arrow">
                        <use xlink:href="#svg-small-arrow"></use>
                      </svg>
                      <!-- /FORM SELECT ICON -->
                    </div>
                    <!-- /FORM SELECT -->
                  </div>
                  <!-- /FORM ITEM -->

                  <!-- FORM ITEM -->
                  <div class="form-item">
                    <div class="form-input social-input small active">
                      <!--  LINK -->
                      <div class="social-link no-hover tag">
                        <!-- ICON  -->
                        <i class="fa fa-tag" aria-hidden="true"></i>
                        <!-- /ICON -->
                      </div>
                      <!-- / LINK -->
                      <label for="tag">Keywords: Place a comma (,) between words</label>
                      <input type="text" id="tag" name="tag" value="<?php if(isset($_GET["tags"])) { echo $_GET["tags"]; }  ?>">
                    </div>
                 </div>
                  <!-- /FORM ITEM -->
                </div>
                <hr />
                <div class="form-item split">
     <?php if(!isset($_COOKIE['user'])){ ?>
              <!-- FORM SELECT -->
              <div class="form-input social-input small active">
                      <!-- name -->
                      <div class="social-link no-hover name">
                        <!-- ICON  -->
                        <?php captcha() ;  ?>=
                        <!-- /ICON  -->
                      </div>
                      <!-- /name -->
                      <label for="capt">verification code</label>
                      <input type="text" id="capt" name="capt" required>
                    </div>
              <!-- /FORM SELECT -->
     <?php }  ?>
               <div class="form-item split">
              <!-- FORM SELECT -->
              <a href="https://www.adstn.gq/kb/myads:Add a new Web site" class="button default" target="_blank" >&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></a>
              <input type="hidden" name="s_type" value="1" />
              <input type="hidden" name="set" value="Publish" />
              <!-- BUTTON -->
              <button type="submit" name="submit_post" value="Publish" class="button primary"><?php lang('spread');  ?></button>
              </div>
              <!-- /BUTTON -->
            </div>
              </form>
                        	</div>
						</div>
					</div>
			</div>
		</div>
<?php }else{ echo"404"; }  ?>