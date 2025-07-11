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
require "dbconfig.php"; // إعدادات قاعدة البيانات
require "include/function.php"; // الوظائف المساعدة

// التحقق من صحة المتغير $s_st
if (!isset($s_st) || $s_st !== "buyfgeufb") {
    http_response_code(401); // إرسال رمز HTTP 401 (غير مصرح)
    echo "Unauthorized access."; // عرض رسالة خطأ
    exit; // إنهاء التنفيذ
}

// تأمين المتغير $s_st لمنع هجمات XSS
$s_st = htmlspecialchars($s_st, ENT_QUOTES, 'UTF-8');

// عرض القوالب
template_mine('header'); // عرض رأس الصفحة
if (isset($_COOKIE['user']) == "") {
    template_mine('index'); // عرض الصفحة الرئيسية إذا لم يكن المستخدم مسجلاً
} else {
    template_mine('home'); // عرض صفحة المستخدم إذا كان مسجلاً
}
template_mine('footer'); // عرض تذييل الصفحة

?>