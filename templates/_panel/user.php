<?php
// التحقق من صحة المتغير $s_st
if (isset($s_st) AND ($s_st == "buyfgeufb")) {
    // استدعاء دالة التثبيت
    dinstall_d();

    // التحقق من وجود ملف تعريف الارتباط للمستخدم
    if (isset($_COOKIE['user'])) {
        $my_user_1 = $_COOKIE['user'];
    }

    // جلب بيانات المستخدم من قاعدة البيانات
    $usz = $db_con->prepare("SELECT * FROM `users` WHERE id = :u_id");
    $usz->bindParam(":u_id", $usrRow['o_order']);
    $usz->execute();

    // التحقق من وجود المستخدم
    if ($sus = $usz->fetch(PDO::FETCH_ASSOC)) {
        // التحقق من حالة المتابعة بين المستخدمين
        $flusz = $db_con->prepare("SELECT * FROM `like` WHERE uid = :u_id AND sid = :s_id AND type = 1");
        $flusz->bindParam(":u_id", $my_user_1);
        $flusz->bindParam(":s_id", $usrRow['o_order']);
        $flusz->execute();

        // تحديد حالة المتابعة (متابعة أو إلغاء متابعة)
        if ($flsus = $flusz->fetch(PDO::FETCH_ASSOC)) {
            $follow = "<a class=\"profile-header-info-action button tertiary\" href=\"{$url_site}/requests/follow.php?unfollow={$sus['id']}\" >
                        <span class=\"hide-text-mobile\">" . $lang['unfollow'] . "</span>&nbsp;<i class=\"fa fa-user-times\" aria-hidden=\"true\"></i></a>";
        } else {
            $follow = "<a class=\"profile-header-info-action button secondary\" href=\"{$url_site}/requests/follow.php?follow={$sus['id']}\" style=\"color: #fff;\" >
                        <span class=\"hide-text-mobile\">" . $lang['follow'] . "</span>&nbsp;<i class=\"fa fa-user-plus\" aria-hidden=\"true\"></i></a>";
        }
?>
        <!-- عرض واجهة المستخدم -->
        <div class="profile-header">
            <!-- صورة الغلاف -->
            <figure class="profile-header-cover liquid" style="background: rgba(0, 0, 0, 0) url(<?php echo $us_cover; ?>) no-repeat scroll center center / cover;">
                <img src="<?php echo $us_cover; ?>" alt="cover-<?php echo $sus['username']; ?>" style="display: none;">
            </figure>
            <!-- /صورة الغلاف -->

            <!-- معلومات المستخدم -->
            <div class="profile-header-info">
                <!-- وصف المستخدم -->
                <div class="user-short-description big">
                    <!-- صورة المستخدم -->
                    <a class="user-short-description-avatar user-avatar big <?php online_us($sus['id']); ?>" href="<?php url_site(); ?>/u/<?php echo $usrRow['o_valuer']; ?>">
                        <div class="user-avatar-border">
                            <div class="hexagon-148-164" style="width: 148px; height: 164px; position: relative;">
                                <canvas style="position: absolute; top: 0px; left: 0px;" width="148" height="164"></canvas>
                            </div>
                        </div>
                        <div class="user-avatar-content">
                            <div class="hexagon-image-100-110" data-src="<?php url_site(); ?>/<?php echo $sus['img']; ?>" style="width: 100px; height: 110px; position: relative;">
                                <canvas style="position: absolute; top: 0px; left: 0px;" width="100" height="110"></canvas>
                            </div>
                        </div>
                        <div class="user-avatar-progress-border">
                            <div class="hexagon-border-124-136" style="width: 124px; height: 136px; position: relative;">
                                <canvas style="position: absolute; top: 0px; left: 0px;" width="124" height="136"></canvas>
                            </div>
                        </div>
                        <?php
                        // التحقق من حالة المستخدم (موثوق أم لا)
                        if (check_us($sus['id'], 1) == 1) {
                            echo "<div class=\"user-avatar-badge\">
                                    <div class=\"user-avatar-badge-border\">
                                        <div class=\"hexagon-40-44\" style=\"width: 22px; height: 24px; position: relative;\"></div>
                                    </div>
                                    <div class=\"user-avatar-badge-content\">
                                        <div class=\"hexagon-dark-32-34\" style=\"width: 16px; height: 18px; position: relative;\"></div>
                                    </div>
                                    <p class=\"user-avatar-badge-text\"><i class=\"fa fa-fw fa-check\"></i></p>
                                  </div>";
                        }
                        ?>
                    </a>
                    <!-- /صورة المستخدم -->

                    <!-- اسم المستخدم -->
                    <a class="user-short-description-avatar user-short-description-avatar-mobile user-avatar medium <?php online_us($sus['id']); ?>" href="<?php url_site(); ?>/u/<?php echo $usrRow['o_valuer']; ?>">
                        <div class="user-avatar-border">
                            <div class="hexagon-120-132" style="width: 120px; height: 132px; position: relative;">
                                <canvas style="position: absolute; top: 0px; left: 0px;" width="120" height="132"></canvas>
                            </div>
                        </div>
                        <div class="user-avatar-content">
                            <div class="hexagon-image-82-90" data-src="<?php url_site(); ?>/<?php echo $sus['img']; ?>" style="width: 82px; height: 90px; position: relative;">
                                <canvas style="position: absolute; top: 0px; left: 0px;" width="82" height="90"></canvas>
                            </div>
                        </div>
                        <div class="user-avatar-progress-border">
                            <div class="hexagon-border-100-110" style="width: 100px; height: 110px; position: relative;">
                                <canvas style="position: absolute; top: 0px; left: 0px;" width="100" height="110"></canvas>
                            </div>
                        </div>
                        <?php
                        if (check_us($sus['id'], 1) == 1) {
                            echo "<div class=\"user-avatar-badge\">
                                    <div class=\"user-avatar-badge-border\">
                                        <div class=\"hexagon-32-34\" style=\"width: 22px; height: 24px; position: relative;\"></div>
                                    </div>
                                    <div class=\"user-avatar-badge-content\">
                                        <div class=\"hexagon-dark-26-28\" style=\"width: 16px; height: 18px; position: relative;\"></div>
                                    </div>
                                    <p class=\"user-avatar-badge-text\"><i class=\"fa fa-fw fa-check\"></i></p>
                                  </div>";
                        }
                        ?>
                    </a>
                    <!-- /صورة المستخدم -->

                    <!-- اسم المستخدم -->
                    <p class="user-short-description-title">
                        <a href="<?php url_site(); ?>/u/<?php echo $usrRow['o_valuer']; ?>"><?php echo $sus['username']; ?></a>
                    </p>
                    <!-- /اسم المستخدم -->

                    <!-- آخر تواجد للمستخدم -->
                    <p class="user-short-description-text"><?php echo $lang['lastcontact'] . "&nbsp;" . convertTime($sus['online']); ?></p>
                    <!-- /آخر تواجد للمستخدم -->
                </div>
                <!-- /وصف المستخدم -->

                <!-- إحصائيات المستخدم -->
                <div class="user-stats">
                    <div class="user-stat big">
                        <p class="user-stat-title"><a href="<?php url_site(); ?>/followers/<?php echo $sus['id']; ?>"><?php nbr_follow($sus['id'], "sid"); ?></a></p>
                        <p class="user-stat-text"><?php lang('Followers'); ?></p>
                    </div>
                    <div class="user-stat big">
                        <p class="user-stat-title"><a href="<?php url_site(); ?>/following/<?php echo $sus['id']; ?>"><?php nbr_follow($sus['id'], "uid"); ?></a></p>
                        <p class="user-stat-text"><?php lang('Following'); ?></p>
                    </div>
                    <div class="user-stat big">
                        <p class="user-stat-title"><?php nbr_posts($sus['id']); ?></p>
                        <p class="user-stat-text"><?php lang('Posts'); ?></p>
                    </div>
                    <div class="user-stat big">
                        <?php if (isset($_COOKIE['user']) AND isset($_COOKIE['admin']) AND ($_COOKIE['admin'] == $hachadmin)) { ?>
                            <a class="social-link patreon" href="<?php url_site(); ?>/admincp?us_edit=<?php echo $sus['id']; ?>" style="color: #fff;">
                                <i class="fa fa-edit" aria-hidden="true"></i>
                            </a>
                        <?php } ?>
                    </div>
                </div>
                <!-- /إحصائيات المستخدم -->

                <!-- أزرار الإجراءات -->
                <div class="profile-header-info-actions">
                    <?php if (isset($_COOKIE['user']) AND ($_COOKIE['user'] == $usrRow['o_order'])) { ?>
                        <a href="<?php url_site(); ?>/e<?php echo $sus['id']; ?>" class="profile-header-info-action button secondary" style="color: #fff;">
                            <span class="hide-text-mobile"><?php echo $lang['edit']; ?></span>&nbsp;<i class="fa fa-edit" aria-hidden="true"></i>
                        </a>
                    <?php } else { ?>
                        <?php echo $follow; ?>
                    <?php } ?>
                    <?php if (isset($_COOKIE['user']) AND ($_COOKIE['user'] != $usrRow['o_order'])) { ?>
                        <a class="profile-header-info-action button primary" href="<?php url_site(); ?>/message/<?php echo $sus['id']; ?>">
                            <span class="hide-text-mobile"><?php echo $lang['send_message']; ?></span>&nbsp;<i class="fa fa-envelope" aria-hidden="true"></i>
                        </a>
                    <?php } ?>
                </div>
                <!-- /أزرار الإجراءات -->
            </div>
            <!-- /معلومات المستخدم -->
        </div>
        <!-- /واجهة المستخدم -->

        <!-- عرض القوالب -->
        <?php template_mine('users_templates/user_navigation'); ?>
        <div class="grid grid-3-6-3 mobile-prefer-content">
            <div class="grid-column">
                <?php widgets(7); ?>
            </div>
            <div class="grid-column">
                <?php if (isset($_COOKIE['user']) AND ($_COOKIE['user'] == $usrRow['o_order'])) { ?>
                    <?php template_mine('status/add_post'); ?>
                <?php } ?>
                <?php forum_tpc_list(); ?>
            </div>
            <div class="grid-column">
                <?php widgets(8); ?>
            </div>
        </div>
    <?php } else {
        template_mine('404'); // عرض صفحة الخطأ إذا لم يتم العثور على المستخدم
    }
} else {
    echo "404"; // عرض رسالة خطأ إذا لم يتم التحقق من $s_st
}
?>