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


include "dbconfig.php";
include "include/function.php";

    if($_COOKIE['admin']==$hachadmin)
{
   ob_start();
$new="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<urlset
      xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"
      xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
      xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">
<url>
  <loc>{$url_site}/</loc>
  <changefreq>weekly</changefreq>
  <priority>1.00</priority>
</url>
<url>
  <loc>{$url_site}/portal</loc>
  <changefreq>weekly</changefreq>
  <priority>0.80</priority>
</url>
<url>
  <loc>{$url_site}/directory</loc>
  <changefreq>weekly</changefreq>
  <priority>0.80</priority>
</url>
<url>
  <loc>{$url_site}/add-site.html</loc>
  <changefreq>monthly</changefreq>
  <priority>0.80</priority>
</url>
<url>
  <loc>{$url_site}/forum</loc>
  <changefreq>weekly</changefreq>
  <priority>0.80</priority>
</url>
<url>
  <loc>{$url_site}/store</loc>
  <changefreq>weekly</changefreq>
  <priority>0.80</priority>
</url>
<url>
  <loc>{$url_site}/login</loc>
  <changefreq>monthly</changefreq>
  <priority>0.80</priority>
</url>
<url>
  <loc>{$url_site}/register</loc>
  <changefreq>monthly</changefreq>
  <priority>0.80</priority>
</url>
";
$stmtcat = $db_con->prepare("SELECT *  FROM forum WHERE id ORDER BY `id` DESC " );
   $stmtcat->execute();
   while($s_post=$stmtcat->fetch(PDO::FETCH_ASSOC) ) {

    $nn_art_s="<url>
  <loc>{$url_site}/t".$s_post['id']."</loc>
  <changefreq>weekly</changefreq>
  <priority>0.50</priority>
</url>".$nn_art_s ;

   }
$stmtcat = $db_con->prepare("SELECT *  FROM directory WHERE id ORDER BY `id` DESC " );
   $stmtcat->execute();
   while($s_post=$stmtcat->fetch(PDO::FETCH_ASSOC) ) {

    $nn_art_s="<url>
  <loc>{$url_site}/dr".$s_post['id']."</loc>
  <changefreq>weekly</changefreq>
  <priority>0.50</priority>
</url>".$nn_art_s ;

   }
$k_type = "knowledgebase";
$storknow = $db_con->prepare("SELECT *  FROM options WHERE  o_type=:o_type AND o_order=0 ORDER BY `id` " );
$storknow->bindParam(":o_type", $k_type);
$storknow->execute();
while($sknowled=$storknow->fetch(PDO::FETCH_ASSOC) ) {
    $nn_art_s="<url>
  <loc>{$url_site}/kb/{$sknowled['o_mode']}:{$sknowled['name']}</loc>
  <changefreq>weekly</changefreq>
  <priority>0.50</priority>
</url>".$nn_art_s ;
}
  $stmtnews = $db_con->prepare("SELECT *  FROM cat_dir WHERE id ORDER BY `id` DESC " );
   $stmtnews->execute();
   while($s_news=$stmtnews->fetch(PDO::FETCH_ASSOC) ) {

$nn_news_s="<url>
  <loc>{$url_site}/cat/".$s_news['id']."</loc>
  <changefreq>weekly</changefreq>
  <priority>0.50</priority>
</url>".$nn_news_s ;

   }
    $stmtplans = $db_con->prepare("SELECT *  FROM users WHERE id ORDER BY `id` DESC " );
   $stmtplans->execute();
   while($s_plans=$stmtplans->fetch(PDO::FETCH_ASSOC) ) {
   $stausr = $db_con->prepare("SELECT * FROM `options` WHERE `o_type` = 'user' AND `o_order` = :o_order ");
$stausr->bindParam(":o_order", $s_plans['id']);
 $stausr->execute();
 $usrRow=$stausr->fetch(PDO::FETCH_ASSOC);
    $nn_plans_s="<url>
  <loc>{$url_site}/u/".$usrRow['o_valuer']."</loc>
  <changefreq>weekly</changefreq>
  <priority>0.50</priority>
</url>".$nn_plans_s ;

   }
$new2="</urlset>";
   $new_nid= $new.$nn_plans_s.$nn_art_s.$nn_news_s.$new2;
   $open = fopen("sitemap.xml","w");
    fwrite($open,$new_nid);
    fclose($open);
     header("Location: {$url_site}/admincp?home&sitemap") ;
     }else{
       template_mine('header');
       template_mine('404');
       template_mine('footer');
     }
    ?>