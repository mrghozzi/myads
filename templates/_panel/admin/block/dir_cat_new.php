<?php
include "../../../../dbconfig.php";
if(isset($_COOKIE['admin']) AND isset($_COOKIE['user']))
{
 $stmt = $db_con->prepare("SELECT *  FROM setting   " );
        $stmt->execute();
        $ab=$stmt->fetch(PDO::FETCH_ASSOC);
        $lng=$ab['lang'];
        $url_site   = $ab['url'];
 $s_st="buyfgeufb";
  if( ! ini_get('date.timezone') )
{
    date_default_timezone_set('GMT');
}
   include "../../../../content/languages/$lng.php";
   include "../../../../include/convertTime.php";
echo "
   <div class=\"widget-box\">
    <div class=\"widget-box-content\">
     <form  method=\"post\" class=\"form\" action=\"admincp.php?d_cat_a\">
     <div class=\"form-row\">
      <div class=\"form-item\">
      <!-- FORM INPUT -->
       <div class=\"form-input small active\">
        <label for=\"{$lang['name']}\">{$lang['name']}</label>
        <input type=\"text\" name=\"name\" required>
       </div>
      <!-- /FORM INPUT -->
      </div>
     </div>
     <div class=\"form-row split\">
     <div class=\"form-item\">
      <!-- FORM INPUT -->
       <div class=\"form-select\">
        <label for=\"Folder\"><i class=\"fa-solid fa-folder-open\"></i> Folder</label> 
         <select name=\"sub\" class=\"form-control\" autocomplete=\"off\">
           <option value=\"0\" >--------</option>";
               $stcmut = $db_con->prepare("SELECT *  FROM cat_dir WHERE sub=0 ORDER BY `name` ASC" );
               $stcmut->execute();
               while($ncat_tt=$stcmut->fetch(PDO::FETCH_ASSOC)){
    echo "<option value=\"{$ncat_tt['id']}\" >{$ncat_tt['name']}</option>";
               }
    echo "</select>
          <svg class=\"form-select-icon icon-small-arrow\">
            <use xlink:href=\"#svg-small-arrow\"></use>
          </svg>
        </div>
      </div>
      <div class=\"form-item\">
      <!-- FORM INPUT -->
       <div class=\"form-input small active\">
        <label for=\"Order\"><i class=\"fa-solid fa-arrow-down-1-9\" ></i> Order</label>
        <input type=\"number\"  name=\"ordercat\" value=\"0\" autocomplete=\"off\" required>
       </div>
      <!-- /FORM INPUT -->
      </div>
      <div class=\"form-item\">
      <!-- FORM INPUT -->
       <button type=\"submit\" name=\"ed_submit\" value=\"ed_submit\" class=\"button secondary\">
       <i class=\"fa-solid fa-folder-plus\"></i>  {$lang['add']}
       </button>
     <!-- /FORM INPUT -->
      </div>
      <div class=\"form-item\">
      <!-- FORM INPUT -->
       <button id=\"close\" class=\"button white\">
       <i class=\"fa-solid fa-circle-xmark\"></i>  {$lang['close']}
       </button>
     <!-- /FORM INPUT -->
      </div>
    </div>
  </form>
  </div>
</div>"; ?>
<script>
    $(document).ready(function(){
        $('#close').click(function(e){
          $("#widget_block").html('');
        });
    });
</script>
<?php  }else{ echo"404"; } ?>