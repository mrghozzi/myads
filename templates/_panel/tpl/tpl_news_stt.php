<?PHP

#####################################################################
##                                                                 ##
##                        My ads v3.0.5(+)                         ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2023                        ##
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
echo "        <div class=\"post-preview\">
          <!-- POST PREVIEW IMAGE -->
          <figure class=\"post-preview-image liquid\" style=\"background: rgba(0, 0, 0, 0) url({$url_site}/templates/_panel/img/cover_news.jpg) no-repeat scroll center center / cover;\">
            <img src=\"{$url_site}/templates/_panel/img/cover_news.jpg\" alt=\"cover-19\" style=\"display: none;\">
          </figure>
          <!-- /POST PREVIEW IMAGE -->

          <!-- POST PREVIEW INFO -->
          <div class=\"post-preview-info fixed-height\">
            <!-- POST PREVIEW INFO TOP -->
            <div class=\"post-preview-info-top\">
              <!-- POST PREVIEW TIMESTAMP -->
              <p class=\"post-preview-timestamp\"><i class=\"fa fa-clock-o\" aria-hidden=\"true\"></i>&nbsp;منذ {$time_stt}</p>
              <!-- /POST PREVIEW TIMESTAMP -->

              <!-- POST PREVIEW TITLE -->
              <p class=\"post-preview-title\">{$sucat['name']}</p>
              <!-- /POST PREVIEW TITLE -->
            </div>
            <!-- /POST PREVIEW INFO TOP -->

            <!-- POST PREVIEW INFO BOTTOM -->
            <div class=\"post-preview-info-bottom\">
              <!-- POST PREVIEW TEXT -->
              <p class=\"post-preview-text\">{$dir_text}</p>
              <!-- /POST PREVIEW TEXT -->

             </div>
            <!-- /POST PREVIEW INFO BOTTOM -->
          </div>
          <!-- /POST PREVIEW INFO -->
        </div>";

}

}else{ echo"404"; }
 ?>