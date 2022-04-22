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
   //  ADS List
   if(isset($_GET['ads']))
{
   if($_COOKIE['admin']==$hachadmin)
{

 $page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;
$per_page = 9; // Records per page.
$startpoint = ($page * $per_page) - $per_page;
$statement = "`ads` WHERE id ORDER BY `id` ASC";
$results =$db_con->prepare("SELECT * FROM {$statement} LIMIT {$startpoint} , {$per_page}");
$results->execute();
function lnk_list() {  global  $results;  global  $statement; global  $per_page; global  $page;  global  $db_con;  global $url_site;
while($wt=$results->fetch(PDO::FETCH_ASSOC)) {
$ads_id =  $wt['id'];
$ads_name[1] ="Home Page";
$ads_name[2] ="User Dashboard";
$ads_name[3] ="Header Exchange";
$ads_name[4] ="Forum";
$ads_name[5] ="Topic";
echo "<form id=\"defaultForm\" method=\"post\" class=\"form-horizontal\" action=\"admincp?e_ads={$wt['id']}\">
  <tr>
  <td><center><h3>{$ads_name[$ads_id]}</h3></center><br />
  <textarea id=\"code{$ads_id}\" name=\"code_ads\" class=\"form-control\"  >{$wt['code_ads']}</textarea><br />
 <center> <button type=\"submit\" name=\"ed_submit\" value=\"ed_submit\" class=\"btn btn-success\"><i class=\"fa fa-edit \"></i></button></center></td>
</tr>
</form> ";
 echo "<script type='text/javascript' src='{$url_site}/templates/_panel/js/codemirror.js'></script>
<script>
var editor = CodeMirror.fromTextArea(document.getElementById('code{$ads_id}'), {
  lineNumbers: true,
  extraKeys: {'Ctrl-Space': 'autocomplete'},
  mode: {name: 'javascript', globalVars: true}
});

if (typeof Promise !== 'undefined') {
  var comp = [
    ['here', 'hither'],
    ['asynchronous', 'nonsynchronous'],
    ['completion', 'achievement', 'conclusion', 'culmination', 'expirations'],
    ['hinting', 'advise', 'broach', 'imply'],
    ['function','action'],
    ['provide', 'add', 'bring', 'give'],
    ['synonyms', 'equivalents'],
    ['words', 'token'],
    ['each', 'every'],
  ]

  function synonyms(cm, option) {
    return new Promise(function(accept) {
      setTimeout(function() {
        var cursor = cm.getCursor(), line = cm.getLine(cursor.line)
        var start = cursor.ch, end = cursor.ch
        while (start && /\w/.test(line.charAt(start - 1))) --start
        while (end < line.length && /\w/.test(line.charAt(end))) ++end
        var word = line.slice(start, end).toLowerCase()
        for (var i = 0; i < comp.length; i++) if (comp[i].indexOf(word) != -1)
          return accept({list: comp[i],
                         from: CodeMirror.Pos(cursor.line, start),
                         to: CodeMirror.Pos(cursor.line, end)})
        return accept(null)
      }, 100)
    })
  }

  var editor2 = CodeMirror.fromTextArea(document.getElementById('synonyms'), {
    extraKeys: {'Ctrl-Space': 'autocomplete'},
    lineNumbers: true,
    lineWrapping: true,
    mode: 'text/x-markdown',
    hintOptions: {hint: synonyms}
  })
}
</script>";
   } if(isset($_SERVER["HTTP_REFERER"])){
    $url_sitea =$_SERVER["HTTP_REFERER"];
   }else{
     $url_sitea = "";
   }
   $url=$url_sitea.$_SERVER["REQUEST_URI"]."&";
   echo pagination($statement,$per_page,$page,$url);
      }
  //  template
 template_mine('header');
 if(!isset($_COOKIE['user'])!="")
{
 template_mine('404');
}else{
 template_mine('admin/admin_header');
 template_mine('admin/admin_ads');
 }
 template_mine('footer');

 }else{
   header("Location: home");
 }
 }
       // Ads edite
   if(isset($_GET['e_ads']))
{
   if($_COOKIE['admin']==$hachadmin)
{
  		$id = $_GET['e_ads'];
		// select image from db to delete
	    if($_POST['ed_submit']){

           $code_ads = $_POST['code_ads'];

        if(!isset($bnerrMSG))
		{

            $stmsb = $db_con->prepare("UPDATE ads SET code_ads=:opm
            WHERE id=:ertb ");
			$stmsb->bindParam(":opm", $code_ads);

            $stmsb->bindParam(":ertb", $id);
         	if($stmsb->execute()){
             header("Location: admincp?ads");
         	}
    }else{
      header("Location: admincp?ads");
    }
    }

 }else {  header("Location: 404");  }
}

}else{
 header("Location: .../404 ") ;
}
  ?>
