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
			<div class="widget-box">
                  <p class="widget-box-title"><h2 class="hdg">Plugins</h2> </p>
             <div class="widget-box-content">
             <?php if(isset($_GET['bnerrMSG'])){  ?>
             <div class="alert alert-danger" role="alert"><?php echo $_GET['bnerrMSG'];  ?></div>
             <?php }  ?>
             <hr />
                <div class="panel panel-widget">
                    <table class="table table-hover">
                       <div class="row">
                        <?php plug_list();  ?>
                       </div>
					</table>
                </div>
				</div>
                </div>
				</div>  
				</div>
<?php }else{ echo"404"; }  ?>