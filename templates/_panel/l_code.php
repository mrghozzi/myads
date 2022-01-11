<?php if($s_st=="buyfgeufb"){
   $o_type =  "extensions_code";
 $bnextensions = $db_con->prepare("SELECT  * FROM `options` WHERE o_type=:o_type " );
$bnextensions->bindParam(":o_type", $o_type);
$bnextensions->execute();
$abextensions=$bnextensions->fetch(PDO::FETCH_ASSOC);
$extensions_code = $abextensions['o_valuer'];
?>
<div id="page-wrapper">
			<div class="main-page">
				<!--buttons-->
				<div class="grids-section">
					<h2 class="hdg">links Code</h2>

			<div class="clearfix"></div>
			</div>
           <div class="panel panel-success">
    <div class="panel-heading">Your referral link <span class="input-group-addon" id="basic-addon3"><?php ref_url(); ?></span>  </div>

  <div class="panel-body">
    <center>  <?php lang('ryffyrly'); ?></center>
     </div> </div>

 <div class="panel panel-primary">
 <div class="panel-body">
<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" data-toggle="tab" href="#h" role="tab" aria-controls="home">468x60</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#p" role="tab" aria-controls="profile">Responsive</a>
  </li>
</ul>

<div class="tab-content">
  <div class="tab-pane active" id="h" role="tabpanel">
  <div class="panel panel-body"><div class="panel-heading">
  Your promotion tags 468x60  (1 point)

       <div class="well" style="color: black;" ><?php lnk_mine('468','60');  ?><?php echo htmlspecialchars($extensions_code);  ?></div>
            <br />
	<div class="panel-heading"> <center><?php cc_lnk('468','60');  ?></center> </div>

  </div></div>
  </div>
  <div class="tab-pane" id="p" role="tabpanel">
  <div class="panel panel-body"><div class="panel-heading">
  Your promotion tags Responsive  (1 point)

       <div class="well" style="color: black;" ><?php lnk_mine('510','320');  ?><?php echo htmlspecialchars($extensions_code);  ?></div>
            <br />
	 <div class="panel-heading"><center><?php cc_lnk('510','320');  ?></center>  </div>

  </div></div></div>
</div>

<script>
  $(function () {
    $('#myTab a:last').tab('show')
  })
</script>   </div> </div>

				</div>
				</div>
<?php }else{ echo"404"; }  ?>