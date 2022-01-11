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
					<h2 class="hdg">Banner Code</h2>

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
    <a class="nav-link active" data-toggle="tab" href="#h" role="tab" aria-controls="home">728x90</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#p" role="tab" aria-controls="profile">300x250</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#m" role="tab" aria-controls="messages">160x600</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#s" role="tab" aria-controls="settings">468x60</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#r" role="tab" aria-controls="settings">Responsive<span class="badge badge-info"><font face="Comic Sans MS">beta<br></font></span></a>
  </li>
</ul>

<div class="tab-content">
  <div class="tab-pane active" id="h" role="tabpanel">
  <div class="panel panel-primary"><div class="panel-heading">
  Your promotion tags 728x90  (1 point)

         <div class="well" style="color: black;" > <?php bnr_mine('728','90');  ?><?php echo htmlspecialchars($extensions_code);  ?></div>
           <br />
	 <center><?php cc_bnr('728','90');  ?></center>

  </div></div>
  </div>
  <div class="tab-pane" id="p" role="tabpanel">
  <div class="panel panel-primary"><div class="panel-heading">
  Your promotion tags 300x250  (1 point)

       <div class="well" style="color: black;" ><?php bnr_mine('300','250');  ?><?php echo htmlspecialchars($extensions_code);  ?></div>
            <br />
	 <center><?php cc_bnr('300','250');  ?></center>

  </div></div></div>
  <div class="tab-pane" id="m" role="tabpanel">
  <div class="panel panel-primary"><div class="panel-heading">
  Your promotion tags 160x600 (1 point)

       <div class="well" style="color: black;" ><?php bnr_mine('160','600');  ?><?php echo htmlspecialchars($extensions_code);  ?></div>
            <br />
	 <center><?php cc_bnr('160','600');  ?></center>

  </div></div></div>
  <div class="tab-pane" id="s" role="tabpanel">
  <div class="panel panel-primary"><div class="panel-heading">
  Your promotion tags 468x60  (1 point)

       <div class="well" style="color: black;" ><?php bnr_mine('468','60');  ?><?php echo htmlspecialchars($extensions_code);  ?></div>
             <br />
	 <center><?php cc_bnr('468','60');  ?></center>

  </div></div></div>
  <div class="tab-pane" id="r" role="tabpanel">
  <div class="panel panel-primary"><div class="panel-heading">
  Your promotion tags Responsive  (1 point)

       <div class="well" style="color: black;" ><?php bnr_mine('responsive','60');  ?><?php echo htmlspecialchars($extensions_code);  ?></div>
             <br />
	 <center><?php cc_bnr('responsive','60');  ?></center>

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