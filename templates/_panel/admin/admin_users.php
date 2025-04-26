<?php if (isset($s_st) AND ($s_st == "buyfgeufb")) { ?>
<div class="grid grid-3-9 medium-space">
    <!-- الشريط الجانبي -->
    <div class="grid-column">
        <?php template_mine('admin/admin_nav'); ?>
    </div>

    <!-- المحتوى الرئيسي -->
    <div class="grid-column">
        <!-- صندوق المستخدمين -->
        <div class="widget-box">
            <h2 class="hdg"><?php echo $lang['users']; ?></h2>

            <div class="col-12">
                <!-- جدول المستخدمين -->
                <table id="tablepagination" class="table table-hover">
                    <thead>
                        <tr>
                            <th><center>#<b>ID</b></center></th>
                            <th><center><b><?php echo $lang['username']; ?></b></center></th>
                            <th><center><b><?php echo $lang['online']; ?></b></center></th>
                            <th><center><b><?php echo $lang['check']; ?></b></center></th>
                            <th><center><b><?php echo $lang['pts']; ?></b></center></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // جلب بيانات المستخدمين من قاعدة البيانات
                        $statement = "`users` WHERE id ORDER BY `id` DESC";
                        $results = $db_con->prepare("SELECT * FROM {$statement}");
                        $results->execute();

                        // عرض بيانات كل مستخدم
                        while ($wt = $results->fetch(PDO::FETCH_ASSOC)) {
                            // تقصير اسم المستخدم إذا كان طويلاً
                            $username = mb_strlen($wt['username'], 'utf8') > 15 
                                ? substr($wt['username'], 0, 15) . "&nbsp;..." 
                                : $wt['username'];

                            // عرض صف المستخدم
                            render_user_row($wt, $username, $url_site, $lang);
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th><center>#ID</center></th>
                            <th><center><?php echo $lang['username']; ?></center></th>
                            <th><center><?php echo $lang['online']; ?></center></th>
                            <th><center><?php echo $lang['check']; ?></center></th>
                            <th><center><?php echo $lang['pts']; ?></center></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<?php } else { echo "404"; } ?>

<?php
/**
 * Render a user row in the table.
 *
 * @param array $user بيانات المستخدم.
 * @param string $username اسم المستخدم المختصر.
 * @param string $url_site رابط الموقع.
 * @param array $lang مفردات اللغة.
 */
function render_user_row($user, $username, $url_site, $lang) {
    echo "<tr>
        <td><center>{$user['id']}</center></td>
        <td>
            <center>
                <a href=\"{$url_site}/u/{$user['id']}\">
                    <img class=\"imgu-bordered-sm\" src=\"{$url_site}/{$user['img']}\" style=\"width: 34px;\" alt=\"user image\">
                    &nbsp;<b>{$username}</b>
                </a>
            </center>
            <br />
            <div>
                <a href=\"{$url_site}/admincp?us_edit={$user['id']}\" class='btn btn-success'><i class=\"fa fa-edit\"></i></a>
                <a href=\"#\" data-toggle=\"modal\" data-target=\"#ban{$user['id']}\" class='btn btn-danger'><i class=\"fa fa-ban\"></i></a>
            </div>
        </td>
        <td><center>";
    online_us($user['id']);
    echo "<br /><i class=\"fa fa-clock-o\"></i>&nbsp;{$user['online']}</center></td>
        <td><center>";
    check_us($user['id']);
    echo "<br />{$user['ucheck']}</center></td>
        <td>
            <center>{$user['pts']}</center>
            <br />
            <div>
                <a href=\"{$url_site}/admincp?state&ty=banner&st={$user['id']}\" class='btn btn-warning'><i class=\"fa fa-link\"></i></a>
                <a href=\"{$url_site}/admincp?state&ty=link&st={$user['id']}\" class='btn btn-primary'><i class=\"fa fa-eye\"></i></a>
            </div>
        </td>
    </tr>";

    // نافذة تأكيد الحذف
    render_delete_modal($user, $url_site, $lang);
}

/**
 * Render a delete confirmation modal for a user.
 *
 * @param array $user بيانات المستخدم.
 * @param string $url_site رابط الموقع.
 * @param array $lang مفردات اللغة.
 */
function render_delete_modal($user, $url_site, $lang) {
    echo "<div class=\"modal fade\" id=\"ban{$user['id']}\" data-backdrop=\"\" tabindex=\"-1\" role=\"dialog\">
        <div class=\"modal-dialog modal-dialog-centered\" role=\"document\">
            <div class=\"modal-content modal-info\">
                <div class=\"modal-header\">
                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
                </div>
                <div class=\"modal-body\">
                    <div class=\"more-grids\">
                        <center><h3>{$lang['delete']}&nbsp;!</h3></center>
                        <hr />
                        <center><h4>{$lang['aysywtd']} \"{$user['username']}\" ?</h4></center>
                        <hr />
                        <center>
                            <a href=\"{$url_site}/admincp?us_ban={$user['id']}\" class=\"btn btn-danger\">{$lang['delete']}&nbsp;<i class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></i></a>
                            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">{$lang['close']}</button>
                        </center>
                    </div>
                </div>
            </div>
        </div>
    </div>";
}
?>