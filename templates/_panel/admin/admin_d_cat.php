<?php if(isset($s_st) AND ($s_st=="buyfgeufb")){  ?>
<style>
select {
  font-family: 'FontAwesome', 'sans-serif';
}</style>
<div class="grid grid-3-9 medium-space" >
<div class="grid-column" >
<?php template_mine('admin/admin_nav');  ?>
</div>
<div class="grid-column" >
				<!--buttons-->
				<div id="head_block" class="widget-box">
					<h2 class="hdg"><?php lang('dir_cats'); ?></h2>
          <div class="widget-box-settings">
          <p id="add_cat" class="btn btn-info" ><i class="fa fa-plus"></i>&nbsp;<?php lang('add'); ?></p>
          </div>
        </div>
        <div id="widget_block" ></div>
              <div class="widget-box">
            <div class="col-md-12 table-grid">
             <?php if(isset($_GET['bnerrMSG'])){  ?>
                     <div class="alert alert-danger" role="alert"><?php echo $_GET['bnerrMSG'];  ?></div>
                        <?php }  ?>
                <div class="panel panel-widget">
					<table id="tablepagination" class="table table-hover">
						<thead>
							<tr>
                              <th><center>#ID</center></th>
                              <th><center>Name</center></th>
                              <th><center>Order</center></th>
                              <th></th>
                           </tr>
						</thead>
						<tbody>
 <?php 
           $statement = "`cat_dir` WHERE id ORDER BY `id` DESC";
           $results =$db_con->prepare("SELECT * FROM {$statement} ");
           $results->execute();
           while($wt=$results->fetch(PDO::FETCH_ASSOC)) {
 ?>
                   <tr>
  <td>#<?php echo $wt['id']; ?></td>
  <td><center><?php echo $wt['name']; ?></center></td>
  <td><center><b><?php echo $wt['ordercat']; ?></b></center></td>
  <td><center>
    <a href="<?php url_site();  ?>/cat/<?php echo $wt['id']; ?>" class="btn btn-primary" target="_blank" ><i class="fa-solid fa-arrow-up-right-from-square fa-beat"></i></a>
    <a href="#head_block" id="ed<?php echo $wt['id']; ?>" class="btn btn-success"><i class="fa-solid fa-pen-to-square"></i></a>
    <a href="#head_block" id="trash<?php echo $wt['id']; ?>" class="btn btn-danger" ><i class="fa-regular fa-trash-can"></i></a>
  </center></td>
</tr>
<script>
        $(document).ready(function(){
                $("#ed<?php echo $wt['id']; ?>").click(function(e){
                  $("#widget_block").load('<?php url_site();  ?>/templates/_panel/admin/block/dir_cat_e.php?id=<?php echo $wt['id']; ?>');
                });
        });
</script>
<script>
        $(document).ready(function(){
                $("#trash<?php echo $wt['id']; ?>").click(function(e){
                  $("#widget_block").load('<?php url_site();  ?>/templates/_panel/admin/block/dir_cat_trash.php?id=<?php echo $wt['id']; ?>');
                });
        });
</script>
<?php }  ?>
               </tbody>
               <tfoot>
							<tr>
                              <th><center>#ID</center></th>
                              <th><center>Name</center></th>
                              <th><center>Order</center></th>
                              <th></th>
                           </tr>
			  </tfoot>
					</table>

				</div>
				</div>
                </div>
				</div>
				</div>
<script>
    $(document).ready(function(){
        $('#add_cat').click(function(e){
          $("#widget_block").load('<?php url_site();  ?>/templates/_panel/admin/block/dir_cat_new.php');
        });
    });
</script>
<?php }else{ echo"404"; }  ?>