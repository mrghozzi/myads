<?php if($s_st=="buyfgeufb"){  ?>
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
				<h2 class="hdg"><?php lang('forum_cats'); ?></h2>
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
                              <th><center><b>#ID</b></center></th>
                              <th><center><b>Name</b></center></th>
                              <th><center><b>Icons</b></center></th>
                              <th><center><b>Order</b></center></th>
                              <th></th>

                           </tr>
						</thead>
						<tbody>
            <?php 
           $statement = "`f_cat` WHERE id ORDER BY `id` DESC";
           $results =$db_con->prepare("SELECT * FROM {$statement} ");
           $results->execute();
           while($wt=$results->fetch(PDO::FETCH_ASSOC)) {
            
 ?>
            <tr>
  <td><center><b><?php echo $wt['id']; ?></b></center></td>
  <td><center><b><?php echo $wt['name']; ?></b></center></td>
  <td><center><h3><i class="fa <?php echo $wt['icons']; ?>"></i></h3></center></td>
  <td><center><?php echo $wt['ordercat']; ?></center></td>
  <td><a href="#head_block" id="ed<?php echo $wt['id']; ?>" class="btn btn-success"><i class="fa-solid fa-pen-to-square"></i></a>
    <a href="#head_block" id="trash<?php echo $wt['id']; ?>" class="btn btn-danger" ><i class="fa-regular fa-trash-can"></i></a>
</td>
</tr>
<script>
        $(document).ready(function(){
                $("#ed<?php echo $wt['id']; ?>").click(function(e){
                  $("#widget_block").load('<?php url_site();  ?>/templates/_panel/admin/block/f_cat_e.php?id=<?php echo $wt['id']; ?>');
                });
        });
</script>
<script>
        $(document).ready(function(){
                $("#trash<?php echo $wt['id']; ?>").click(function(e){
                  $("#widget_block").load('<?php url_site();  ?>/templates/_panel/admin/block/f_cat_trash.php?id=<?php echo $wt['id']; ?>');
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
        $('#add_cat').click(function(e){
          $("#widget_block").load('<?php url_site();  ?>/templates/_panel/admin/block/f_cat_new.php');
        });
    });
</script>        
<?php }else{ echo"404"; }  ?>