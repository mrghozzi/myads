<?PHP

#####################################################################
##                                                                 ##
##                        MYads  v3.x.x                            ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2022                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################
if($vrf_License=="65fgh4t8x5fe58v1rt8se9x"){
                  //  Plugins List
   if(isset($_GET['plug']))
{
   if($_COOKIE['admin']==$hachadmin)
{
 function plug_list() {  global  $url_site;  global  $lang; global  $db_con;
 $d = dir("content/plugins");
 $c = 0;
while (false !== ($entry = $d->read())) {
  if($entry!="index.php"){
  if ( $c>=2)   {

  include "content/plugins/".$entry."/extension.php";
  $jsonstr = json_encode($extension);
  $note = json_decode($jsonstr);
  echo "<div class=\"col-xs-6 col-md-4\">
    <a class=\"thumbnail\">
      <img  src=\"{$url_site}/content/plugins/{$entry}/thumbnail.png\" onerror=\"this.src='templates/_panel/images/error_plug.png'\" style=\"width: 280;height: 170;\"  >
      <div class=\"caption\">
        <h3>";
                                    echo $note->{'name'};
                                    echo "</h3>
        "; ?>
<div id="div<?php echo $entry;  ?>" >
<?php
$plug_name = $note->{'name'};
$bnplug = $db_con->prepare("SELECT  * FROM options WHERE name = :name AND o_type='plugins' ");
$bnplug->bindParam(":name", $plug_name);
$bnplug->execute();
$abplug=$bnplug->fetch(PDO::FETCH_ASSOC);

if($abplug['o_valuer']== "1"){
  $plug_st_name = $note->{'uninstall'};
  $plug_st_data = "uninstall";
 ?>
<button type="button" id="my<?php echo $entry;  ?>" value="uninstall" data-complete-text="Loading..." class="btn btn-danger" autocomplete="off"> uninstall
</button>
 <?php }else if($abplug['o_valuer']== "2"){
 $plug_st_name = $note->{'install'};
 $plug_st_data = "install";
 ?>
<button type="button" id="my<?php echo $entry;  ?>" value="install" data-complete-text="Loading..." class="btn btn-primary" autocomplete="off"> install
</button>
 <?php }else{
 $plug_st_name = $note->{'install'};
 $plug_st_data = "install";
  ?>
<button type="button" id="my<?php echo $entry;  ?>" value="install" data-complete-text="Loading..." class="btn btn-primary" autocomplete="off"> install
</button>
<?php }
      echo "  <button data-toggle=\"modal\" data-target=\"#{$entry}\" class=\"btn btn-default\" >Description</button>   </div>
        </div>
    </a>
  </div>";
 ?>

<script>
  $('#my<?php echo $entry;  ?>').on('click', function () {
    $.ajax({
        url : '<?php echo $url_site;  ?>/content/plugins/<?php echo $entry;  ?>/<?php echo $plug_st_name;  ?>',
        data : {
            <?php echo $plug_st_data;  ?> : $("#my<?php echo $entry;  ?>").val()
        },
        datatype : "json",
        type : 'post',
        success : function(result) {
                $("#div<?php echo $entry;  ?>").html(result);
        },
        error : function() {
            alert("Error reaching the server. Check your connection");
        }
    });
  })
</script>
<?php  echo "
  <!-- //modal {$entry} -->
              <div class=\"modal fade\" id=\"{$entry}\" data-backdrop=\"\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog modal-dialog-centered\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-header\">
                        <b>";
           if(isset($note->{'name'})){  echo $note->{'name'};  }
                                    echo "</b>
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">
                               <div class=\"col-md-4 photoday-grid\" style=\"background-color: #DDDDDD\">
                               <center><a class=\"thumbnail\" ><img src=\"{$url_site}/content/plugins/{$entry}/thumbnail.png\" onerror=\"this.src='templates/_panel/images/error_plug.png'\" ></a></center>
								 <center>	";
           if(isset($note->{'name'}))          {  echo $lang["name"]."&nbsp;".$note->{'name'}."<hr />"; }
           if(isset($note->{'version'}))       {  echo "Version : ".$note->{'version'}."<br />";    }
           if(isset($note->{'latest version'})){  echo "Latest version : ".$note->{'latest version'}."<hr />"; }
           if(isset($note->{'v-myads'}))       {  echo "Compatible with : ".$note->{'v-myads'}."<br />";  }
           if(isset($note->{'devlope by'}))    {  echo "Devlope by : ".$note->{'devlope by'}."<br />";    }
                                                  echo "</center></div><div class=\"col-md-8 photoday-grid\">";
           if(isset($note->{'description'}))   {  echo "Description : <br />".$note->{'description'}."<hr />";  }
                                                  echo"</div></div><div class=\"clearfix\"> </div>
						<br /></div> <div class=\"clearfix\"> </div>
					</div>
				</div>
			</div>

			<!-- //modal {$entry} -->";
}
$c++;

}
}
$d->close();
if($c==2){
  echo "<center><div class=\"alert alert-info\" role=\"alert\">The <b>Extensions</b> folder is empty</div></center>";
}
  }
  //  template
 template_mine('header');
 if(!isset($_COOKIE['user'])!="")
{
 template_mine('404');
}else{
 template_mine('admin/admin_header');
 template_mine('admin/admin_plugins');
 }
 template_mine('footer');

 }else{
   header("Location: home");
 }
 }
}else{
 header("Location: .../404.php ") ;
}
  ?>
