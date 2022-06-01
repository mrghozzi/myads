<?php if($s_st=="buyfgeufb"){
    function wid_plase() {
    return array(
        '1' => 'portal_left',
        '2' => 'portal_right',
        '3' => 'forum_left',
        '4' => 'forum_right',
        '5' => 'directory_left',
        '6' => 'directory_right',
        '7' => 'profile_left',
        '8' => 'profile_right'
    );
}
 ?>
<div class="grid grid-3-6-3 medium-space" >
<div class="grid-column" >
<?php template_mine('admin/admin_nav');  ?>
</div>
<div class="grid-column" >
				<!--buttons-->
				<div class="widget-box">
					<h2 class="hdg"><?php lang('widgets'); ?></h2>
                    <hr />
                 <div class="form-select">
            <label for="friends-filter-category"><?php lang('add'); ?> <?php lang('widgets'); ?></label>
            <select id="widget_cat" name="widget_cat">
              <option></option>
              <option value="widget_html">Html code</option>
              <option value="widget_members">Suggest Members</option>
            </select>
            <!-- FORM SELECT ICON -->
            <svg class="form-select-icon icon-small-arrow">
              <use xlink:href="#svg-small-arrow"></use>
            </svg>
            <!-- /FORM SELECT ICON -->
          </div>
          <div id="widget_block" ></div>
                </div>

           <div class="widget-box">
            <div class="col-md-12 table-grid">
             <?php if(isset($_GET['bnerrMSG'])){  ?>
                     <div class="alert alert-danger" role="alert"><?php echo $_GET['bnerrMSG'];  ?></div>
                        <?php }  ?>
                <div class="panel panel-widget">

					<table id="tablepagination" class="table table-hover">
						<thead>
						  <tr>
                              <th><center><b>#ID</b></center></th>
                              <th><center><b>Name</b></center></th>
                              <th><center><b>Place</b></center></th>
                              <th><center><b>Order</b></center></th>
                              <th><center><b><?php echo $lang['settings']; ?></b></center></th>
                           </tr>
						</thead>
			   <tbody>
<?php
$o_type = "box_widget";
$bnwidgets = $db_con->prepare("SELECT  * FROM `options` WHERE o_type=:o_type ORDER BY `id` DESC " );
$bnwidgets->bindParam(":o_type", $o_type);
$bnwidgets->execute();
while($abwidgets=$bnwidgets->fetch(PDO::FETCH_ASSOC)){
 $id_widg = $abwidgets['o_parent'];
 $get_plase =  wid_plase();
 $bplase = $get_plase["{$id_widg}"];
?>
                 <tr>
                   <td><center><b>#<?php echo $abwidgets['id']; ?></b></center></td>
                   <td><center><b><?php echo $abwidgets['name']; ?></b></center></td>
                   <td><center><b><?php echo $bplase; ?></b></center></td>
                   <td><center><b><?php echo $abwidgets['o_order']; ?></b></center></td>
                   <td><center><button class="btn btn-success" type="button" id="widget_edit<?php echo $abwidgets['id']; ?>" name="widget_edit" value="<?php echo $abwidgets['id']; ?>" ><i class="fa fa-edit "></i></button></center></td>
                 </tr>
<script>
    $(document).ready(function(){
        $('#widget_edit<?php echo $abwidgets['id']; ?>').click(function(e){
          var wname=$(this).val();
          $("#widget_block").load('<?php url_site();  ?>/templates/_panel/admin/widgets/w_block.php?id='+wname);
        });
    });
</script>
<?php }  ?>    
               </tbody>
					</table>

				</div>
				</div>
                </div>
				</div>
                </div>
<script>
    $(document).ready(function(){
        $('#widget_cat').change(function(e){
          var wname=$(this).val();
          $("#widget_block").load('<?php url_site();  ?>/templates/_panel/admin/widgets/w_block.php?name='+wname);
        });
    });
</script>
<?php }else{ echo"404"; }  ?>