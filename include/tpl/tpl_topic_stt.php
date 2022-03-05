<?PHP

#####################################################################
##                                                                 ##
##                        My ads v2.4.x                            ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2022                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################
if($s_st=="buyfgeufb"){

 //  Get Browser
function tpl_topic_stt($sutcat,$Suggestion)
{
global  $db_con; global  $catsum;  global  $uRow; global $lang; global $url_site; global $title_s; global $hachadmin;
$catusz = $db_con->prepare("SELECT *  FROM `forum` WHERE statu=1 AND  id=".$sutcat['tp_id'] );
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);

$catdid=$sucat['id'];
$catus = $db_con->prepare("SELECT *  FROM users WHERE  id='{$sucat['uid']}'");
$catus->execute();
$catuss=$catus->fetch(PDO::FETCH_ASSOC);
$catusc = $db_con->prepare("SELECT *  FROM f_cat WHERE  id='{$sucat['cat']}'");
$catusc->execute();
$catussc=$catusc->fetch(PDO::FETCH_ASSOC);
$catdnb = $db_con->prepare("SELECT  COUNT(id) as nbr FROM status WHERE tp_id='{$catdid}' AND s_type=2 " );
$catdnb->execute();
$abdnb=$catdnb->fetch(PDO::FETCH_ASSOC);
$share_nbr = $abdnb['nbr']-1;
if($share_nbr==0){ $share_nbr=""; }
$catdnbcm = $db_con->prepare("SELECT  COUNT(id) as nbr FROM f_coment WHERE tid='{$catdid}' " );
$catdnbcm->execute();
$abdcmnt=$catdnbcm->fetch(PDO::FETCH_ASSOC);
$likenbcm = $db_con->prepare("SELECT  COUNT(id) as nbr FROM `like` WHERE sid='{$catdid}' AND  type=2 " );
$likenbcm->execute();
$abdlike=$likenbcm->fetch(PDO::FETCH_ASSOC);
$likeuscm = $db_con->prepare("SELECT  * FROM `like` WHERE uid='{$uRow['id']}' AND sid='{$catdid}' AND  type=2 " );
$likeuscm->execute();
$uslike=$likeuscm->fetch(PDO::FETCH_ASSOC);
$time_stt=convertTime($sutcat['date']);

$namesher =  "{$sucat['name']} - {$title_s}";
$namesher = strip_tags($namesher, '');
$linksher =  "{$url_site}/t{$catdid}";
$linksher = strip_tags($linksher, '');

$comtxt = preg_replace('/ #([^\s]+)/', '<a  href="'.$url_site.'/tag/$1" >#$1</a>', $sucat['txt'] );
$comtxt = strip_tags($comtxt, '<a><br><iframe>');

$comtxt=substr($comtxt,0,480);
if($sucat['uid']==$sutcat['uid']){
$usecho = "";
   }else{
$catrus = $db_con->prepare("SELECT *  FROM users WHERE  id='{$sutcat['uid']}'");
$catrus->execute();
$catruss=$catrus->fetch(PDO::FETCH_ASSOC);
$usecho =  "<b>{$catruss['username']}</b> <i class=\"fa fa-retweet\" aria-hidden=\"true\"></i>   " ;
}

echo "<div id=\"tpcid{$sucat['id']}\" class=\"col-md-12 photoday-grid\" style=\"border-radius: 15px;background-color: #A9A9A9;\" >
							<div class=\"photoday\">
                                <div class=\"photo1\">";
            if((isset($uRow['id']) AND ($uRow['id']==$sucat['uid'])) OR (isset($uRow['id']) AND ($uRow['id']==$sutcat['uid'])) OR (isset($_COOKIE['admin']) AND ($_COOKIE['admin']== $hachadmin))){
               echo "<div class=\"col-md-2 phot-grid\">
										<a href=\"#\" data-toggle=\"modal\" data-target=\"#trash{$sucat['id']}\" >{$lang['delete']}&nbsp;<i class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></i> </a>
                                     </div>";
                                      }
          if((isset($uRow['id']) AND ($uRow['id']==$sucat['uid'])) OR (isset($uRow['id']) AND ($uRow['id']==$sutcat['uid'])) OR (isset($_COOKIE['admin']) AND ($_COOKIE['admin']== $hachadmin))){
             echo "<div class=\"col-md-6 phot-grid\">";
              }else{
                echo "<div class=\"col-md-8 phot-grid\">"; }
                if($sucat['uid']==0){
                 echo "<a  ><i class=\"fa fa-user\" aria-hidden=\"true\"></i>Guest</a>  ";
               }else if($Suggestion==1){
                 echo "<a  href=\"#\" data-toggle=\"modal\" data-target=\"#user{$sucat['id']}\"  ><i class=\"fa fa-random\" aria-hidden=\"true\"></i>Suggestion</a>  ";
                }else if($Suggestion==2){
                 echo "<a  href=\"#\" data-toggle=\"modal\" data-target=\"#user{$sucat['id']}\"  ><i class=\"fa fa-bullhorn\" aria-hidden=\"true\"></i>Sponsoring</a>  ";
                }else{
                   echo  $usecho."<a  href=\"#\" data-toggle=\"modal\" data-target=\"#user{$sucat['id']}\"  ><img class=\"imgu-bordered-sm\" src=\"{$url_site}/{$catuss['img']}\" style=\"width: 35px;\" alt=\"user image\"> {$catuss['username']} ";
            online_us($catuss['id']);
            check_us($catuss['id']);
            echo "</a>  " ;
                }

		    echo "</a>
									</div>
                                    <div class=\"col-md-2 phot-grid\" id=\"heart{$sucat['id']}\">
										";
                                      if(isset($_COOKIE['user']) OR (isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin) )  ){
                                       if($uslike['sid']==$catdid){
                                       echo "<a style=\"color: #FF0000;\"  href=\"javascript:void(0);\" id=\"ulike{$sucat['id']}\" ><i class=\"fa fa-heart\" style=\"color: #FF0000;\"  aria-hidden=\"true\"></i>{$abdlike['nbr']}</a>
                                       <input type=\"hidden\" id=\"lval\" value=\"test_like\" />";
                                       }else{
                                       echo "<a href=\"javascript:void(0);\" id=\"like{$sucat['id']}\"   ><i class=\"fa fa-heart-o\" aria-hidden=\"true\"></i>{$abdlike['nbr']}</a>
                                       <input type=\"hidden\" id=\"lval\" value=\"test_like\" />";
                                       } }else{
                                       echo "<a href=\"javascript:void(0);\" data-toggle=\"modal\" data-target=\"#mlike{$sucat['id']}\" ><i class=\"fa fa-heart-o\" aria-hidden=\"true\"></i>{$abdlike['nbr']}</a>";
                                       }
                                       echo "</div>
									<div class=\"col-md-2 phot-grid\">
										<p><i class=\"glyphicon glyphicon-comment\" aria-hidden=\"true\"></i>{$abdcmnt['nbr']}</p>
									</div>
									<div class=\"clearfix\"></div>
								</div>
							<div class=\"photo1\">
<div class=\"col-md-4 phot-grid\">
									<h4><font color=\"#B10DC9\"><i class=\"fa {$catussc['icons']}\" aria-hidden=\"true\"></i> {$catussc['name']}</font></h4>
</div>
                                 <div class=\"col-md-8 phot-grid\">
                                  <h4>  <a href=\"{$url_site}/t{$sucat['id']}\"  >{$sucat['name']}</a></h4>
</div><div class=\"clearfix\"></div></div>

									<div class=\"photo-text\">
                                     <p>{$comtxt} ... </p>
                                     <hr>
                                    <p><i class=\"glyphicon glyphicon-time\" aria-hidden=\"true\"></i> منذ {$time_stt}</p>
								</div>
								<div class=\"photo1\">
									<div class=\"col-md-4 phot-grid\">";
           if((isset($uRow['id'])AND isset($sucat['uid']) AND ($uRow['id']==$sucat['uid'])) OR (isset($_COOKIE['admin'])  AND ($_COOKIE['admin']==$hachadmin) )){
           echo "<a href=\"#\" data-toggle=\"modal\" data-target=\"#edit{$sucat['id']}\" ><i class=\"glyphicon glyphicon-edit\" aria-hidden=\"true\"></i> {$lang['edit']} </a>";
           }else{
           echo "<a href=\"#\" data-toggle=\"modal\" data-target=\"#Report{$sucat['id']}\" ><i class=\"glyphicon glyphicon-flag\" aria-hidden=\"true\"></i> {$lang['report']} </a>";
           }
           echo " </div>
									<div class=\"col-md-4 phot-grid\">
										<a href=\"#\" data-toggle=\"modal\" data-target=\"#share{$sucat['id']}\" >{$lang['share']}&nbsp;<i class=\"glyphicon glyphicon-share\" aria-hidden=\"true\"></i>{$share_nbr} </a>
									</div>
									<div class=\"col-md-4 phot-grid\">
										<p><a href=\"{$url_site}/t{$sucat['id']}\" >{$lang['preview']}&nbsp;<i class=\"glyphicon glyphicon-chevron-right\" aria-hidden=\"true\"></i></a></p>
									</div>
                         <script>
     \$(\"document\").ready(function() {
   \$(\"#like{$sucat['id']}\").click(postlike{$sucat['id']});

});

function postlike{$sucat['id']}(){
    \$(\"#heart{$sucat['id']}\").html(\"<i class='fa fa-thumbs-up' aria-hidden='true'></i>\");
    \$.ajax({
        url : '{$url_site}/requests/f_like.php?id={$sucat['id']}&f_like=like_up&t=f',
        data : {
            test_like : \$(\"#lval\").val()
        },
        datatype : \"json\",
        type : 'post',
        success : function(result) {
               $(\"#heart{$sucat['id']}\").html(result);
        },
        error : function() {
            alert(\"Error reaching the server. Check your connection\");
        }
    });
}
 \$(\"document\").ready(function() {
   \$(\"#ulike{$sucat['id']}\").click(postulike{$sucat['id']});

});

function postulike{$sucat['id']}(){
    \$(\"#heart{$sucat['id']}\").html(\"<i class='fa fa-thumbs-down' aria-hidden='true'></i>\");
    \$.ajax({
        url : '{$url_site}/requests/f_like.php?id={$sucat['id']}&f_like=like_down&t=f',
        data : {
            test_like : \$(\"#lval\").val()
        },
        datatype : \"json\",
        type : 'post',
        success : function(result) {
               $(\"#heart{$sucat['id']}\").html(result);
        },
        error : function() {
            alert(\"Error reaching the server. Check your connection\");
        }
    });
}
     </script>
      <!-- //modal like {$sucat['id']} -->
              <div class=\"modal fade\" id=\"mlike{$sucat['id']}\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-header\">
                        You do not have an account!
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">
                                 <center>
                                   <a href=\"{$url_site}/login\" class=\"btn btn-success\" ><i class=\"fa fa-sign-in\"></i>{$lang['login']}</a>
                        <a href=\"{$url_site}/register\" class=\"btn btn-danger\" ><i class=\"fa fa-user-plus\"></i>{$lang['sign_up']}</a>
                                    </center>
                            <br />

							</div>
						</div>
					</div>
				</div>
			</div>

	   <!-- //modal like {$sucat['id']} -->
      <!-- //modal user {$sucat['id']} -->
              <div class=\"modal fade\" id=\"user{$sucat['id']}\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-header\">
                <h3>   <a href=\"{$url_site}/u/{$sucat['uid']}\" ><img class=\"imgu-bordered-sm\" src=\"{$url_site}/{$catuss['img']}\" style=\"width: 35px;\" alt=\"user image\">   {$catuss['username']}  ";
            online_us($catuss['id']);
            check_us($catuss['id']);
            echo "</a> </h3>
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">
	<div class=\"col-md-4 phot-grid\">
									    <img class=\"img-responsive\" src=\"{$url_site}/{$catuss['img']}\" width=\"32\" >
									</div>
									<div class=\"col-md-4 phot-grid\">


                                  ";
echo "منذ".convertTime($catuss['online']);
if(isset($_COOKIE['user']) OR (isset($_COOKIE['admin']) AND ($_COOKIE['admin']==$hachadmin) )  ){
  if($_COOKIE['user']==$catuss['id']){
      //  btn user
              }else{
  echo "</div>
									<div class=\"col-md-4 phot-grid\">

<a href=\"{$url_site}/message/{$catuss['id']}\" class=\"btn btn-info\"><i class=\"fa fa-envelope\" aria-hidden=\"true\"></i></a>";
} }
echo "   </div> 	<div class=\"clearfix\"></div>
							</div>
						</div>
					</div>
				</div>
			</div>

	   <!-- //modal user {$sucat['id']} -->
       <!-- //modal share {$sucat['id']} -->
              <div class=\"modal fade\" id=\"share{$sucat['id']}\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-header\">
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">
									<h3 style=\"color: #313437\" >{$lang['share']}</h3>
                              <div class=\"alert alert-success\" role=\"alert\" style=\"display: none;\">
                               The publication was shared
                              </div>
                              <div class=\"alert alert-danger\" role=\"alert\" style=\"display: none;\">
                              There error. Try again
                               </div>
									<p>
                                    <form  method=\"POST\">
                                    <a onclick=\"ourl('https://www.facebook.com/sharer/sharer.php?u={$linksher}');\" href=\"javascript:void(0);\" class=\"btn btn-primary\" >
                                    <i class=\"fa fa-facebook nav_icon\"></i></a>
                                    <a onclick=\"ourl('https://twitter.com/intent/tweet?text={$namesher}&url={$linksher}');\" href=\"javascript:void(0);\" class=\"btn btn-info\" >
                                    <i class=\"fa fa-twitter nav_icon\"></i></a>
                                    <a onclick=\"ourl('https://www.linkedin.com/sharing/share-offsite/?url={$linksher}');\" href=\"javascript:void(0);\" class=\"btn btn-primary\" >
                                    <i class=\"fa fa-linkedin nav_icon\"></i></a>
                                    <a onclick=\"ourl('https://www.wasp.gq/sharer?url={$linksher}');\" href=\"javascript:void(0);\" class=\"btn btn-danger\" >
                                    <i class=\"fa fa-wikipedia-w nav_icon\"></i></a>
                                    <a onclick=\"ourl('https://www.adstn.gq/directory?p&url={$linksher}&tags=');\" href=\"javascript:void(0);\" class=\"btn btn-danger\" >
                                    <i class=\"fa fa-bullhorn nav_icon\"></i></a>
                                    ";
                                    if(isset($_COOKIE['user'])){
                                echo "
                                    <!-- form -->
                                    <input type=\"hidden\" name=\"tid\" value=\"{$sucat['id']}\" />
                                    <input type=\"hidden\" name=\"s_type\" value=\"2\" />
                                    <input type=\"hidden\" name=\"set\" value=\"share\" />
                                    <button  type=\"submit\" name=\"submit\" value=\"share\" class=\"btn btn-success\" >
                                    <i class=\"fa fa-share nav_icon\"></i></button>
                                    <!-- END form -->
                           "; }
                           echo "
                                     </form>
                                    </p>
                                    <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">{$lang['close']}</button>

							</div>
						</div>
					</div>
				</div>
			</div>
	   <!-- //modal share {$sucat['id']} -->
       <!-- //modal Report {$sucat['id']} -->
              <div class=\"modal fade\" id=\"Report{$sucat['id']}\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-header\">
                        <i class=\"glyphicon glyphicon-flag\" aria-hidden=\"true\"></i>&nbsp;{$lang['report']}
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">
                                 <h4>{$sucat['name']}</h4>
                                 <div class=\"alert alert-success\" role=\"alert\" style=\"display: none;\">
                               has been sent
                              </div>
                              <div class=\"alert alert-danger\" role=\"alert\" style=\"display: none;\">
                              There error. Try again
                               </div>
                               <form method=\"POST\">
                                 <hr>
                                 <textarea name=\"txt\" class=\"form-control\" required></textarea>
                                 <hr>
                                 <input type=\"hidden\" name=\"tid\" value=\"{$sucat['id']}\" />
                                 <input type=\"hidden\" name=\"s_type\" value=\"2\" />
                                 <input type=\"hidden\" name=\"set\" value=\"Report\" />
                                 <input type=\"submit\" name=\"submit\" value=\"{$lang['spread']}\" class=\"btn btn-primary\" />
                                 <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">{$lang['close']}</button>
</form>
							</div>
						</div>
					</div>
				</div>
			</div>

	   <!-- //modal Report {$sucat['id']} -->
       <!-- //modal trash {$sucat['id']} -->
              <div class=\"modal fade\" id=\"trash{$sucat['id']}\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-header\">
                        {$lang['delete']}&nbsp;!
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">
                            <br /> <h4>{$lang['aysywtd']} \"{$sucat['name']}\"?</h4>
                            <div class=\"alert alert-success\" role=\"alert\" style=\"display: none;\">
                               Deleted
                              </div>
                              <div class=\"alert alert-danger\" role=\"alert\" style=\"display: none;\">
                              There error. Try again
                               </div>
								   <center>
                                   <!-- form -->
                                   <form  method=\"POST\">
                                    <input type=\"hidden\" name=\"did\" value=\"{$sutcat['id']}\" />
                                    <input type=\"hidden\" name=\"set\" value=\"delete\" />
                                    <button  type=\"submit\" name=\"submit\" value=\"{$lang['delete']}\" class=\"btn btn-danger\" >
                                    <i class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></i></button>
                                    </form>
                                    <!-- END form -->
                                     </center>
                            <br />
                                    <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">{$lang['close']}</button>

							</div>
						</div>
					</div>
				</div>
			</div>

	   <!-- //modal trash {$sucat['id']} -->
   <!-- //modal edit {$sucat['id']} -->
              <div class=\"modal fade\" id=\"edit{$sucat['id']}\" tabindex=\"-1\" role=\"dialog\">
				<div class=\"modal-dialog\" role=\"document\">
					<div class=\"modal-content modal-info\">
						<div class=\"modal-header\">
                        <h2>{$lang['e_topic']}</h2>
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						</div>
						<div class=\"modal-body\">
							<div class=\"more-grids\">
                            <div class=\"alert alert-success\" role=\"alert\" style=\"display: none;\">
                               Modified

                              </div>
                              <div class=\"alert alert-danger\" role=\"alert\" style=\"display: none;\">
                              There error. Try again
                               </div>
                      <form  method=\"POST\">
                       <div class=\"input-group\">
                        <span class=\"input-group-addon\" id=\"basic-addon1\"><i class=\"fa fa-edit\" aria-hidden=\"true\"></i></span>
                       <input type=\"text\" class=\"form-control\" name=\"name\" value=\"{$sucat['name']}\" placeholder=\"Web site name\" aria-describedby=\"basic-addon1\" required>
                       </div>
                       <div class=\"input-group\">
                        <span class=\"input-group-addon\" id=\"basic-addon1\"><i class=\"fa fa-text-width\" aria-hidden=\"true\"></i></span>
                       <textarea name=\"txt\" id=\"txt{$sucat['id']}\" class=\"form-control\"   placeholder=\"Site Description\" required>{$sucat['txt']}</textarea>
                       </div>
                       <div class=\"input-group\">
                       <span class=\"input-group-addon\" id=\"basic-addon1\"><i class=\"fa fa-folder\" aria-hidden=\"true\"></i></span>
                       <select class=\"form-control\" name=\"categ\" >
                       ";
                       $selectdir = $db_con->prepare("SELECT *  FROM f_cat ORDER BY `name` ASC ");
                       $selectdir->execute();
                       while($selrs15=$selectdir->fetch(PDO::FETCH_ASSOC)){
                             echo "<option value=\"{$selrs15['id']}\"
                             ";
                             if($selrs15['id']==$sucat['cat'])  {
                               echo "selected=\"selected\"";
                             }
                             echo "
                             >{$selrs15['name']}</option>";
                             }
                     echo "
                       </select>
                       </div>
             <input type=\"hidden\" name=\"tid\" value=\"{$sucat['id']}\" />
                           <input type=\"hidden\" name=\"s_type\" value=\"2\" />
                           <input type=\"hidden\" name=\"set\" value=\"edit\" />
                           <button  type=\"submit\" name=\"submit\" value=\"edit\" class=\"btn btn-primary\" >
                              {$lang['spread']}</button>
                              <a href=\"{$url_site}/editor/{$sucat['id']}\" class=\"btn btn-default\" >&nbsp;<i class=\"fa fa-arrows-alt\" aria-hidden=\"true\"></i>&nbsp;</a>
                            
                           <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">{$lang['close']}</button>
                           </form>
							</div>
						</div>
					</div>
				</div>
			</div>
        <script>
    \$(function ()
    {
        \$('#txt{$sucat['id']}').keyup(function (e){
            if(e.keyCode == 13){
                var curr = getCaret(this);
                var val = \$(this).val();
                var end = val.length;

                \$(this).val( val.substr(0, curr) + '<br>' + val.substr(curr, end));
            }

        })
    });

    function getCaret(el) {
        if (el.selectionStart) {
            return el.selectionStart;
        }
        else if (document.selection) {
            el.focus();

            var r = document.selection.createRange();
            if (r == null) {
                return 0;
            }

            var re = el.createTextRange(),
            rc = re.duplicate();
            re.moveToBookmark(r.getBookmark());
            rc.setEndPoint('EndToStart', re);

            return rc.text.length;
        }
        return 0;
    }
</script>
	   <!-- //modal edit {$sucat['id']} -->

									<div class=\"clearfix\"></div>
								</div>
							</div>
                            <div class=\"clearfix\"></div>
						</div><div class=\"col-md-12 photoday-grid\" >&nbsp;</div>";

}

}else{ echo"404"; }
 ?>