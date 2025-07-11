<?PHP

#####################################################################
##                                                                 ##
##                        MYads  v3.2.x                            ##
##                  https://github.com/mrghozzi                    ##
##                                                                 ##
##                                                                 ##
##                       copyright (c) 2025                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################

if ($vrf_License == "65fgh4t8x5fe58v1rt8se9x") {
    // Banners ads List
    if (isset($_GET['b_list'])) {
        $admin_page = 1;
        if ($_COOKIE['admin'] == $hachadmin) {
            $statement = "`banner` WHERE id ORDER BY 'id' DESC";
            $results = $db_con->prepare("SELECT * FROM {$statement}");
            $results->execute();

            function bnr_list()
            {
                global $results, $db_con, $url_site, $lang;
                while ($wt = $results->fetch(PDO::FETCH_ASSOC)) {
                    $lus_id = $wt['uid'];
                    $stmht_select = $db_con->prepare('SELECT * FROM users WHERE id=:id ');
                    $stmht_select->execute(array(':id' => $lus_id));
                    $lusRow = $stmht_select->fetch(PDO::FETCH_ASSOC);

                    $fgft = ($wt['statu'] == "1") ? "ON" : "OFF";

                    $str_name = mb_strlen($wt['name'], 'utf8');
                    $bnname = ($str_name > 25) ? substr($wt['name'], 0, 25) . "&nbsp;..." : $wt['name'];

                    echo "<tr>
                        <td>{$wt['id']}&nbsp;-&nbsp;<a href=\"{$url_site}/u/{$lusRow['id']}\">{$lusRow['username']}</a>
                        <hr />
                        <a href=\"admincp?b_edit={$wt['id']}\" class='btn btn-success'><i class=\"fa fa-edit\"></i></a>
                        <a href=\"#\" data-toggle=\"modal\" data-target=\"#ban{$wt['id']}\" class='btn btn-danger'><i class=\"fa fa-ban\"></i></a>
                        </td>
                        <td>{$bnname}</td>
                        <td><a href=\"admincp?state&ty=banner&id={$wt['id']}\" class='btn btn-warning'>{$wt['vu']}</a></td>
                        <td><a href=\"admincp?state&ty=vu&id={$wt['id']}\" class='btn btn-primary'>{$wt['clik']}</a></td>
                        <td>{$wt['px']}</td>
                        <td>{$fgft}</td>
                    </tr>";

                    // نافذة تأكيد الحذف
                    echo "<div class=\"modal fade\" id=\"ban{$wt['id']}\" data-backdrop=\"\" tabindex=\"-1\" role=\"dialog\">
                        <div class=\"modal-dialog modal-dialog-centered\" role=\"document\">
                            <div class=\"modal-content modal-info\">
                                <div class=\"modal-header\">
                                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
                                </div>
                                <div class=\"modal-body\">
                                    <div class=\"more-grids\">
                                        <h3>{$lang['delete']}</h3>
                                        <p>{$lang['sure_to_delete']} {$wt['id']} ?</p><br />
                                        <center><a href=\"admincp?b_ban={$wt['id']}\" class=\"btn btn-danger\">{$lang['delete']}</a></center>
                                        <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">{$lang['close']}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>";
                }
            }

            // Template
            template_mine('header');
            if (!isset($_COOKIE['user']) != "") {
                template_mine('404');
            } else {
                template_mine('b_list');
            }
            template_mine('footer');
        } else {
            header("Location: home");
        }
    }

    // Banners ADS edit
    if (isset($_GET['b_edit'])) {
        if ($_COOKIE['admin'] == $hachadmin) {
            $id = $_GET['b_edit'];

            // Select image from db to delete
            $stmht_select = $db_con->prepare('SELECT * FROM banner WHERE id=:did ');
            $stmht_select->execute(array(':did' => $id));
            $bnRow = $stmht_select->fetch(PDO::FETCH_ASSOC);

            if ($bnRow['id'] == $id) {
                function bnr_echo($name)
                {
                    global $bnRow;
                    echo $bnRow["{$name}"];
                }

                $slctRow = $bnRow['px'];
                $statuRow = $bnRow['statu'];

                if (isset($_POST['bn_submit'])) {
                    $bn_name = $_POST['name'];
                    $bn_url = $_POST['url'];
                    $bn_img = $_POST['img'];
                    $bn_px = $_POST['bn_px'];
                    $bn_statu = $_POST['statu'];

                    if (empty($bn_name)) {
                        $bnerrMSG = $lang['please_enter_name'];
                    }
                    if (empty($bn_url)) {
                        $bnerrMSG = $lang['please_enter_url'];
                    }
                    if (isset($bnerrMSG)) {
                        $bn_get = "?b_edit=" . $id . "&bnerrMSG=" . $bnerrMSG;
                    }
                    if (!isset($bnerrMSG)) {
                        $stmsb = $db_con->prepare("UPDATE banner SET name=:a_da,url=:opm,img=:ptdk,px=:bn_px,statu=:statu WHERE id=:ertb ");
                        $stmsb->bindParam(":opm", $bn_url);
                        $stmsb->bindParam(":a_da", $bn_name);
                        $stmsb->bindParam(":ptdk", $bn_img);
                        $stmsb->bindParam(":bn_px", $bn_px);
                        $stmsb->bindParam(":statu", $bn_statu);
                        $stmsb->bindParam(":ertb", $id);

                        if ($stmsb->execute()) {
                            // Check if status changed
                            $nurl = "b_edit?id=".$id;
                            $time = time();
                            if ($statuRow != $bn_statu) {
                                $notif_stmt = $db_con->prepare("INSERT INTO notif (uid, name, nurl, logo, time, state) VALUES (:uid, :name, :nurl, 'overview', :time, 1)");
                                $notif_stmt->bindParam(':uid', $bnRow['uid']);
                                $notif_stmt->bindParam(':time', $time);
                                $notif_stmt->bindParam(':nurl', $nurl);
                                if ($bn_statu == 1) {
                                    $notif_stmt->bindParam(':name', $lang['your_ad_has_been_activated']);
                                } else {
                                    $notif_stmt->bindParam(':name', $lang['your_ad_as_been_blocked']);
                                }
                                $notif_stmt->execute();
                            }
                            header("Location: admincp?b_list");
                        }
                    } else {
                        header("Location: admincp{$bn_get}");
                    }
                }
            } else {
                header("Location: 404");
            }
        } else {
            header("Location: 404");
        }

        // Template
        template_mine('header');
        if (!isset($_COOKIE['user']) != "") {
            template_mine('404');
        } else {
            template_mine('b_edit');
        }
        template_mine('footer');
    }

    // Ban Banners
    if (isset($_GET['b_ban'])) {
        if ($_COOKIE['admin'] == $hachadmin) {
            $bn_id = $_GET['b_ban'];
           // select banner from db to delete
         $stmht_select = $db_con->prepare('SELECT * FROM banner WHERE  id=:did ');
         $stmht_select->execute(array(':did'=>$bn_id));
         $bnRow=$stmht_select->fetch(PDO::FETCH_ASSOC);
         // delete banner
            $stmt = $db_con->prepare("DELETE FROM banner WHERE id=:id");
            $stmt->execute(array(':id' => $bn_id));
         // Check if status changed
         $nurl = "b_list";
         $time = time();
         $notif_stmt = $db_con->prepare("INSERT INTO notif (uid, name, nurl, logo, time, state) VALUES (:uid, :name, :nurl, 'delete', :time, 1)");
         $notif_stmt->bindParam(':uid', $bnRow['uid']);
         $notif_stmt->bindParam(':time', $time);
         $notif_stmt->bindParam(':nurl', $nurl);
         $notif_stmt->bindParam(':name', $lang['your_ad_has_been_deleted']);
         $notif_stmt->execute();
            header("Location: admincp?b_list");
        } else {
            header("Location: home");
        }
    }
} else {
    header("Location: .../404.php");
}
?>
