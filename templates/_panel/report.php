<?php if($s_st=="buyfgeufb"){ dinstall_d(); ?>
<style> body{  background-image: url(<?php url_site();  ?>/templates/_panel/img/background.webp); } </style>
<div class="grid grid-12" >
  <div class="grid-column" >
    <div class="widget-box" >
      <!-- PREVIEW TITLE -->
        <p class="widget-box-title"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;<?php echo $lang['report']; ?> - <?php title_site(''); ?></p>
        <hr>
      <!-- /link report TITLE -->
        <?php 
         if(isset($_GET['link'])){ 
          $bn_type = "201";
          $r_link = $db_con->prepare("SELECT * FROM link where id=:a_id " );
          $r_link->bindParam(":a_id", $_GET['link']);
          $r_link->execute();
          $ab_link=$r_link->fetch(PDO::FETCH_ASSOC);
          echo "<div id=\"report{$ab_link['id']}\" >";
          echo "<center><a href=\"{$ab_link['url']}\" class=\"btn btn-warning\" target=\"_blank\">{$ab_link['name']}&nbsp;<b><i class=\"fa fa-external-link\" ></i></b></a></center>";
          ?>
<br />
<textarea class="quicktext" id="txt<?php echo $ab_link['id']; ?>"  ></textarea>
<br /><br />
<center>
<div class="btn-group">
<button  id="btn_edit<?php echo $ab_link['id']; ?>" class="btn btn-danger" >
<?php echo $lang['report']; ?>
</button>
</div>
</center>
<script>
$("document").ready(function() {
   $("#btn_edit<?php echo $ab_link['id']; ?>").click(postedit<?php echo $ab_link['id']; ?>);
});

function postedit<?php echo $ab_link['id']; ?>(){
  var txt<?php echo $ab_link['id']; ?> = $("#txt<?php echo $ab_link['id']; ?>").val();
    $("#report<?php echo $ab_link['id']; ?>").html("posting report ...");

    $.ajax({
        url : '<?php echo $url_site;  ?>/requests/report.php?submit=submit&s_type=<?php echo $bn_type; ?>&tid=<?php echo $ab_link['id']; ?>',
        data : {
            txt : txt<?php echo $ab_link['id']; ?>
        },
        datatype : "json",
        type : 'post',
        success : function(result) {
          $("#report<?php echo $ab_link['id']; ?>").html("<hr /><div class='alert alert-warning alert-dismissible fade show' role='alert'><?php echo $lang['pending']; ?><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>");
        },
        error : function() {
            alert("Error reaching the server. Check your connection");
        }
        });
        }
       </script> 
        <?php 
        echo "</div>";
        echo "<hr>";
         }  ?>
      <!-- /link2 report TITLE -->
      <?php 
         if(isset($_GET['link2'])){ 
          $bn_type = "201";
          $r_link = $db_con->prepare("SELECT * FROM link where id=:a_id " );
          $r_link->bindParam(":a_id", $_GET['link2']);
          $r_link->execute();
          $ab_link=$r_link->fetch(PDO::FETCH_ASSOC);
          echo "<div id=\"report{$ab_link['id']}\" >";
          echo "<center><a href=\"{$ab_link['url']}\" class=\"btn btn-warning\" target=\"_blank\">{$ab_link['name']}&nbsp;<b><i class=\"fa fa-external-link\" ></i></b></a></center>";
          ?>
<br />
<textarea class="quicktext" id="txt<?php echo $ab_link['id']; ?>"  ></textarea>
<br /><br />
<center>
<div class="btn-group">
<button  id="btn_edit<?php echo $ab_link['id']; ?>" class="btn btn-danger" >
<?php echo $lang['report']; ?>
</button>
</div>
</center>
<script>
$("document").ready(function() {
   $("#btn_edit<?php echo $ab_link['id']; ?>").click(postedit<?php echo $ab_link['id']; ?>);
});

function postedit<?php echo $ab_link['id']; ?>(){
  var txt<?php echo $ab_link['id']; ?> = $("#txt<?php echo $ab_link['id']; ?>").val();
    $("#report<?php echo $ab_link['id']; ?>").html("posting report ...");

    $.ajax({
        url : '<?php echo $url_site;  ?>/requests/report.php?submit=submit&s_type=<?php echo $bn_type; ?>&tid=<?php echo $ab_link['id']; ?>',
        data : {
            txt : txt<?php echo $ab_link['id']; ?>
        },
        datatype : "json",
        type : 'post',
        success : function(result) {
          $("#report<?php echo $ab_link['id']; ?>").html("<hr /><div class='alert alert-warning alert-dismissible fade show' role='alert'><?php echo $lang['pending']; ?><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>");
        },
        error : function() {
            alert("Error reaching the server. Check your connection");
        }
        });
        }
       </script> 
        <?php 
        echo "</div>";
        echo "<hr>";
         }  ?> 
      <!-- /banner report TITLE -->
      <?php 
         if(isset($_GET['banner'])){ 
          $bn_type = "202";
          $r_banner = $db_con->prepare("SELECT * FROM banner where id=:a_id " );
          $r_banner->bindParam(":a_id", $_GET['banner']);
          $r_banner->execute();
          $ab_banner=$r_banner->fetch(PDO::FETCH_ASSOC);
          $b_px = $ab_banner['px'];
          if($b_px=="728"){  $w_px =728; $h_px =90; $hh_px=$h_px-10; $d_px =$w_px."x".$h_px; }
          if($b_px=="300"){  $w_px =300; $h_px =250; $hh_px=$h_px-10; $d_px =$w_px."x".$h_px; }
          if($b_px=="160"){  $w_px =160; $h_px =600; $hh_px=$h_px-10; $d_px =$w_px."x".$h_px; }
          if($b_px=="468"){  $w_px =468; $h_px =60; $hh_px=$h_px-10; $d_px =$w_px."x".$h_px; }

          echo "<div id=\"report{$ab_banner['id']}\" >";
          echo "<center><a href=\"{$ab_banner['url']}\" class=\"btn btn-secondary\" target=\"_blank\">{$ab_banner['name']}&nbsp;<b><i class=\"fa fa-external-link\" ></i></b>
          <br><img border=0 src=\"{$ab_banner['img']}\" width=\"{$w_px}\" height=\"{$hh_px}\" ></a></center>";
          ?>
<br />
<textarea class="quicktext" id="txt<?php echo $ab_banner['id']; ?>"  ></textarea>
<br /><br />
<center>
<div class="btn-group">
<button  id="btn_edit<?php echo $ab_banner['id']; ?>" class="btn btn-danger" >
<?php echo $lang['report']; ?>
</button>
</div>
</center>
<script>
$("document").ready(function() {
   $("#btn_edit<?php echo $ab_banner['id']; ?>").click(postedit<?php echo $ab_banner['id']; ?>);
});

function postedit<?php echo $ab_banner['id']; ?>(){
  var txt<?php echo $ab_banner['id']; ?> = $("#txt<?php echo $ab_banner['id']; ?>").val();
    $("#report<?php echo $ab_banner['id']; ?>").html("posting report ...");

    $.ajax({
        url : '<?php echo $url_site;  ?>/requests/report.php?submit=submit&s_type=<?php echo $bn_type; ?>&tid=<?php echo $ab_banner['id']; ?>',
        data : {
            txt : txt<?php echo $ab_banner['id']; ?>
        },
        datatype : "json",
        type : 'post',
        success : function(result) {
          $("#report<?php echo $ab_banner['id']; ?>").html("<hr /><div class='alert alert-warning alert-dismissible fade show' role='alert'><?php echo $lang['pending']; ?><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>");
        },
        error : function() {
            alert("Error reaching the server. Check your connection");
        }
        });
        }
       </script> 
        <?php 
        echo "</div>";
        echo "<hr>";
         }  ?>
      <!-- /visits report TITLE -->
      <?php 
         if(isset($_GET['visits'])){ 
          $bn_type = "203";
          $r_visits = $db_con->prepare("SELECT * FROM visits where id=:a_id " );
          $r_visits->bindParam(":a_id", $_GET['visits']);
          $r_visits->execute();
          $ab_visits=$r_visits->fetch(PDO::FETCH_ASSOC);
          echo "<div id=\"report{$ab_visits['id']}\" >";
          echo "<center><a href=\"{$ab_visits['url']}\" class=\"btn btn-warning\" target=\"_blank\">{$ab_visits['name']}&nbsp;<b><i class=\"fa fa-external-link\" ></i></b></a></center>";
          ?>
<br />
<textarea class="quicktext" id="txt<?php echo $ab_visits['id']; ?>"  ></textarea>
<br /><br />
<center>
<div class="btn-group">
<button  id="btn_edit<?php echo $ab_visits['id']; ?>" class="btn btn-danger" >
<?php echo $lang['report']; ?>
</button>
</div>
</center>
<script>
$("document").ready(function() {
   $("#btn_edit<?php echo $ab_visits['id']; ?>").click(postedit<?php echo $ab_visits['id']; ?>);
});

function postedit<?php echo $ab_visits['id']; ?>(){
  var txt<?php echo $ab_visits['id']; ?> = $("#txt<?php echo $ab_visits['id']; ?>").val();
    $("#report<?php echo $ab_visits['id']; ?>").html("posting report ...");

    $.ajax({
        url : '<?php echo $url_site;  ?>/requests/report.php?submit=submit&s_type=<?php echo $bn_type; ?>&tid=<?php echo $ab_visits['id']; ?>',
        data : {
            txt : txt<?php echo $ab_visits['id']; ?>
        },
        datatype : "json",
        type : 'post',
        success : function(result) {
          $("#report<?php echo $ab_visits['id']; ?>").html("<hr /><div class='alert alert-warning alert-dismissible fade show' role='alert'><?php echo $lang['pending']; ?><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>");
        },
        error : function() {
            alert("Error reaching the server. Check your connection");
        }
        });
        }
       </script> 
        <?php 
        echo "</div>";
        echo "<hr>";
         }  ?> 

    </div>
    <div class="widget-box">
    <center>
    <?php echo "All rights reserved &nbsp;&copy;".date("Y")."&nbsp;"; title_site(''); ?>&trade;
    | <a href="<?php url_site();  ?>/privacy-policy">PRIVACY POLICY</a>
    | `MyAds v<?php myads_version();  ?>`  Devlope by <a href="http://www.krhost.ga/">Kariya Host</a>
    </center>
    </div>
   </div>
</div>
<?php }else{ echo"404"; }  ?>