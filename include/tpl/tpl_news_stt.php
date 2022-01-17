<?php
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

 function tpl_news_stt($sutcat)
{
global  $db_con; global  $catsum;  global  $uRow; global $lang; global $url_site; global $title_s; global $hachadmin;
$catusz = $db_con->prepare("SELECT *  FROM `news` WHERE statu=1 AND  id=".$sutcat['tp_id'] );
$catusz->execute();
$sucat=$catusz->fetch(PDO::FETCH_ASSOC);

$dir_text = preg_replace('/ #([^\s]+)/', '<a  href="'.$url_site.'/tag/$1" >#$1</a>', $sucat['text'] );


$time_stt=convertTime($sutcat['date']);
echo "<div class=\"col-md-12 photoday-grid\" style=\"border-radius: 15px;background-color: #7C38BC;\" >
							<div class=\"message-top\">
                                <h3>News&nbsp;{$title_s}</h3>
								<div class=\"message-right\">
								<i class=\"glyphicon glyphicon-list-alt\" aria-hidden=\"true\"></i>
								</div>
								<div class=\"clearfix\"></div>
								</div>
                                <div class=\"clearfix\"></div>
							   <div class=\"photo-text\">
									<h4>|&nbsp;
                                    <a>{$sucat['name']}</a></h4>
									<p>{$dir_text}</p>
                                    <hr>
                                    <p><i class=\"glyphicon glyphicon-time\" aria-hidden=\"true\"></i>منذ {$time_stt}</p>
								</div>
						   <div class=\"clearfix\"></div>
						</div><div class=\"col-md-12 photoday-grid\" >&nbsp;</div>";

}

}else{ echo"404"; }
 ?>