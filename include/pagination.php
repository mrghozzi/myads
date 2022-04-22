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

function pagination($query,$per_page=10,$page=10,$url='?'){
  global  $db_con ;
  $query = "SELECT COUNT(*) as `num` FROM {$query}";
  $rrstmt = $db_con->prepare($query);
 $rrstmt->execute();
 $row=$rrstmt->fetch(PDO::FETCH_ASSOC);

    $total = $row['num'];
    $adjacents = "1";

    $prevlabel = "&lsaquo; prev";
    $nextlabel = "next &rsaquo;";
     
    $page = ($page == 0 ? 1 : $page);
    $start = ($page - 1) * $per_page;                               
     
    $prev = $page - 1;
    $next = $page + 1;
     
    $lastpage = ceil($total/$per_page);
     
    $lpm1 = $lastpage - 1; // //last page minus 1
     
    $pagination = "";
    if($lastpage > 1){
        $pagination .= "<div class=\"page-items\">";

            if ($page > 1) $pagination.= "<a class=\"page-item\" href='{$url}page={$prev}'>{$prevlabel}</a>";

        if ($lastpage < 7 + ($adjacents * 2)){   
            for ($counter = 1; $counter <= $lastpage; $counter++){
                if ($counter == $page)
                    $pagination.= "<a class=\"page-item tag-item secondary\">{$counter}</a>";
                else
                    $pagination.= "<a class=\"page-item\" href='{$url}page={$counter}'>{$counter}</a>";
            }
         
        } elseif($lastpage > 5 + ($adjacents * 2)){
             
            if($page < 1 + ($adjacents * 2)) {
                 
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++){
                    if ($counter == $page)
                        $pagination.= "<a class=\"page-item tag-item secondary\">{$counter}</a>";
                    else
                        $pagination.= "<a class=\"page-item\" href='{$url}page={$counter}'>{$counter}</a>";
                }
                $pagination.= "<p class=\"page-item void\">...</p>";
                $pagination.= "<a class=\"page-item\" href='{$url}page={$lpm1}'>{$lpm1}</a>";
                $pagination.= "<a class=\"page-item\" href='{$url}page={$lastpage}'>{$lastpage}</a>";
                     
            } elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                 
                $pagination.= "<a class=\"page-item\" href='{$url}page=1'>1</a>";
                $pagination.= "<a class=\"page-item\" href='{$url}page=2'>2</a>";
                $pagination.= "<p class=\"page-item void\">...</p>";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                    if ($counter == $page)
                        $pagination.= "<a class=\"page-item tag-item secondary\">{$counter}</a>";
                    else
                        $pagination.= "<a class=\"page-item\" href='{$url}page={$counter}'>{$counter}</a>";
                }
                $pagination.= "<p class=\"page-item void\">...</p>";
                $pagination.= "<a class=\"page-item\" href='{$url}page={$lpm1}'>{$lpm1}</a>";
                $pagination.= "<a class=\"page-item\" href='{$url}page={$lastpage}'>{$lastpage}</a>";
                 
            } else {

                $pagination.= "<a class=\"page-item\" href='{$url}page=1'>1</a>";
                $pagination.= "<a class=\"page-item\" href='{$url}page=2'>2</a>";
                $pagination.= "<p class=\"page-item void\">...</p>";
                for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                    if ($counter == $page)
                        $pagination.= "<a class=\"page-item tag-item secondary\">{$counter}</a></div>";
                    else
                        $pagination.= "<a class=\"page-item\" href='{$url}page={$counter}'>{$counter}</a>";
                }
            }
        }

            if ($page < $counter - 1) $pagination.= "<a class=\"page-item\" href='{$url}page={$next}'>{$nextlabel}</a>";
         
        $pagination.= "</div>";
    }
     
    return $pagination;
}

?>