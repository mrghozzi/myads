<?php if($s_st=="buyfgeufb"){  ?>
 <style>

.grid-inbox i {
    color: #FFFFFF

}

}
           </style>
		<div id="page-wrapper">
           <div class="main-page">

<br>
   <?php ads_site(4); ?>
</hr>


     <div class="col-md-8 photoday-grid">
       <script async src="https://cse.google.com/cse.js?cx=011368158097811702264:cye7f07j9vs"></script>
       <div class="gcse-search"></div>
 </div>
    <div class="col-md-4 photoday-grid">
        <div class="photoday">

        <div class="col-md-12 inbox-grid">


        <div class="grid-inbox">
        <div class="inbox-bottom">
								<ul>
  <li><a href="<?php url_site();  ?>/forum" class="compose" ><i class="fa fa-comments-o"></i> Forum</a></li>
  <li><a href="<?php url_site();  ?>/directory" class="compose" ><i class="fa fa-globe"></i> Directory</a></li>
</ul>
         </div>
          <div class="online">
									<h4 style="color: #000099;" >Suggestions</h4>
									<ul>
                                       <?php
     $stt = $db_con->prepare("SELECT *,MD5(RAND()) AS m FROM users where  NOT(id = :id)  ORDER BY m LIMIT 30 " );
     $stt->execute(array(':id'=>$uRow['id']));
     while($usb=$stt->fetch(PDO::FETCH_ASSOC)){ ?>
		   <li><a href="<?php url_site();  ?>/u/<?php echo $usb['id']; ?>"> <b><?php check_us($usb['id']); echo $usb['username']; ?></b> <?php online_us($usb['id']); ?></a></li>
     <?php } ?>
								   </ul>
								</div>
                            
                               
                           </div>


             </div></div>
        </div>
  
             <div class="clearfix"></div>
			 </div>
                <div class="clearfix"> </div>
           </div>
           </div>
<?php }else{ echo"404"; }  ?>