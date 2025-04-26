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

// تضمين ملفات الإعدادات والوظائف
include "dbconfig.php"; // إعدادات قاعدة البيانات
include "include/function.php"; // الوظائف المساعدة

// تحديد عنوان الصفحة
$title_page = $lang['home'];

// التحقق من إرسال النموذج
if (isset($_POST['bt_pts'])) {

    // استلام المدخلات من النموذج
    $le_name = $_POST['pts']; // عدد النقاط
    $le_type = $_POST['to'];  // نوع العملية

    // التحقق من صحة المدخلات
    if ($le_name < 0) {
        $le_name = 0; // منع القيم السالبة
    } else if (!is_numeric($le_name)) {
        $le_name = 0; // منع القيم غير الرقمية
    }

    // التحقق من النقاط المتاحة للمستخدم
    if (isset($uRow['pts']) && ($uRow['pts'] < $le_name)) {
        $errMSG = $lang['tnopmtrnon'] . " : " . $uRow['pts'] . "</b>";
    } else if ($le_name == "0") {
        $errMSG = $lang['cnc0p'];
    }

    // إذا كانت هناك رسالة خطأ، يتم إعداد الرابط مع الرسالة
    if (isset($errMSG)) {
        $le_get = "?errMSG=" . $errMSG;
    }

    // إذا لم تكن هناك أخطاء، يتم تنفيذ العملية
    if (!isset($errMSG)) {
        $o_type = "hest_pts"; // نوع العملية
        $bn_desc = "-" . $le_name; // وصف العملية

        // تحديد نوع العملية بناءً على المدخلات
        if ($le_type == "link") {
            $bn_name = "tostads";
        } else if ($le_type == "banners") {
            $bn_name = "towthbaner";
        } else if ($le_type == "exchv") {
            $bn_name = "toexchvisi";
        }

        // إعداد البيانات للإدخال في قاعدة البيانات
        $bn_uid = $uRow['id'];
        $bn_sid = "0";
        $o_time = time();

        // إدخال العملية في جدول الخيارات
        $inshest = $db_con->prepare("INSERT INTO options (name, o_valuer, o_type, o_parent, o_order, o_mode)
            VALUES (:name, :o_valuer, :o_type, :o_parent, :o_order, :o_mode)");
        $inshest->bindParam(":name", $bn_name);
        $inshest->bindParam(":o_valuer", $bn_desc);
        $inshest->bindParam(":o_type", $o_type);
        $inshest->bindParam(":o_parent", $bn_uid);
        $inshest->bindParam(":o_order", $bn_sid);
        $inshest->bindParam(":o_mode", $o_time);

        // تنفيذ العملية
        if ($inshest->execute()) {
            // تحديث بيانات المستخدم بناءً على نوع العملية
            if ($le_type == "link") {
                $le_go = $le_name / 2;
                $stms = $db_con->prepare("UPDATE users SET nlink = nlink + :a_da, pts = pts - :pts WHERE id = :uid");
                $stms->bindParam(":pts", $le_name, PDO::PARAM_INT);
                $stms->bindParam(":a_da", $le_go);
                $stms->bindParam(":uid", $uRow['id']);
                if ($stms->execute()) {
                    $comment = str_replace("[le_go]", $le_go, $lang['phbdp']);
                    $comment = str_replace("[le_name]", $le_name, $comment);
                    $MSG = $comment;
                    $le_get = "?MSG=" . $MSG;
                    header("Location: home.php{$le_get}");
                }
            } else if ($le_type == "banners") {
                $le_go = $le_name / 2;
                $stms = $db_con->prepare("UPDATE users SET nvu = nvu + :a_da, pts = pts - :pts WHERE id = :uid");
                $stms->bindParam(":pts", $le_name, PDO::PARAM_INT);
                $stms->bindParam(":a_da", $le_go);
                $stms->bindParam(":uid", $uRow['id']);
                if ($stms->execute()) {
                    $comment = str_replace("[le_go]", $le_go, $lang['phbdb']);
                    $comment = str_replace("[le_name]", $le_name, $comment);
                    $MSG = $comment;
                    $le_get = "?MSG=" . $MSG;
                    header("Location: home.php{$le_get}");
                }
            } else if ($le_type == "exchv") {
                $le_go = $le_name / 4;
                $stms = $db_con->prepare("UPDATE users SET vu = vu + :a_da, pts = pts - :pts WHERE id = :uid");
                $stms->bindParam(":pts", $le_name, PDO::PARAM_INT);
                $stms->bindParam(":a_da", $le_go);
                $stms->bindParam(":uid", $uRow['id']);
                if ($stms->execute()) {
                    $comment = str_replace("[le_go]", $le_go, $lang['phbdv']);
                    $comment = str_replace("[le_name]", $le_name, $comment);
                    $MSG = $comment;
                    $le_get = "?MSG=" . $MSG;
                    header("Location: home.php{$le_get}");
                }
            }
        } else {
            header("Location: home.php{$le_get}");
        }
    }
}

// عرض القوالب
template_mine('header'); // عرض رأس الصفحة
if (isset($_COOKIE['user']) == "") {
    template_mine('404'); // عرض صفحة الخطأ إذا لم يكن المستخدم مسجلاً
} else {
    template_mine('home'); // عرض الصفحة الرئيسية للمستخدم
}
template_mine('footer'); // عرض تذييل الصفحة

?>

