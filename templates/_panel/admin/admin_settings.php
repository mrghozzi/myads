<?php if (isset($s_st) AND ($s_st == "buyfgeufb")) { ?>
<div class="grid grid-3-9 medium-space">
    <!-- الشريط الجانبي -->
    <div class="grid-column">
        <?php template_mine('admin/admin_nav'); ?>
    </div>

    <!-- إعدادات الموقع -->
    <div class="grid-column">
        <div class="widget-box">
            <div class="col-md-12 validation-grid">
                <!-- عنوان إعدادات الموقع -->
                <p class="widget-box-title">
                    <h4><span><?php lang('settings_site'); ?></span></h4>
                </p>

                <div class="widget-box-content">
                    <!-- عرض رسالة خطأ إذا كانت موجودة -->
                    <?php if (isset($_GET['bnerrMSG'])) { ?>
                        <div class="alert alert-danger" role="alert"><?php echo $_GET['bnerrMSG']; ?></div>
                    <?php } ?>

                    <!-- نموذج إعدادات الموقع -->
                    <form id="defaultForm" method="post" class="form-horizontal" action="admincp.php?settings">
                        <div class="form-row split">
                            <!-- إدخال اسم الموقع -->
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="Site-Name"><?php lang('site_name'); ?></label>
                                    <input type="text" id="Site-Name" name="name" value="<?php bnr_echo('titer'); ?>">
                                </div>
                            </div>

                            <!-- إدخال رابط الموقع -->
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="Url-website"><?php lang('url_link'); ?></label>
                                    <input type="text" id="Url-website" name="url" value="<?php bnr_echo('url'); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-row split">
                            <!-- إدخال وصف الموقع -->
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="Description"><?php lang('description'); ?></label>
                                    <textarea id="profile-desc" name="desc"><?php bnr_echo('description'); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-row split">
                            <!-- إدخال اسم القالب -->
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="Template-name"><?php lang('template'); ?></label>
                                    <input type="text" id="Template-name" name="styles" value="<?php bnr_echo('styles'); ?>">
                                </div>
                            </div>

                            <!-- إدخال اللغة الافتراضية -->
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="Language-Default"><?php lang('language_default'); ?></label>
                                    <input type="text" id="Language-Default" name="lang" value="<?php bnr_echo('lang'); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-row split">
                            <!-- اختيار الروابط التعليمية -->
                            <div class="form-item">
                                <div class="form-select small active">
                                    <label for="Educational-Links"><?php lang('educational_links'); ?></label>
                                    <select id="Educational-Links" name="e_links">
                                        <option value="1"><?php lang('activate'); ?></option>
                                        <option value="0"><?php lang('close'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-row split">
                            <!-- اختيار المنطقة الزمنية -->
                            <div class="form-item">
                                <div class="form-select">
                                    <label for="site-Timezone"><?php lang('timezone'); ?></label>
                                    <select id="site-Timezone" name="timezone">
                                        <option value="<?php bnr_echo('timezone'); ?>"><?php bnr_echo('timezone'); ?></option>
                                        <!-- قائمة المناطق الزمنية -->
                                        <option value="Etc/GMT+12">(GMT-12:00) International Date Line West</option>
                                        <option value="Pacific/Midway">(GMT-11:00) Midway Island, Samoa</option>
                                        <option value="Pacific/Honolulu">(GMT-10:00) Hawaii</option>
                                        <option value="US/Alaska">(GMT-09:00) Alaska</option>
                                        <option value="America/Los_Angeles">(GMT-08:00) Pacific Time (US & Canada)</option>
                                        <!-- يمكن إضافة المزيد من المناطق الزمنية هنا -->
                                    </select>
                                </div>
                            </div>

                            <!-- إدخال البريد الإلكتروني للمسؤول -->
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="Admin-Email"><?php lang('admin_email'); ?></label>
                                    <input type="text" id="Admin-Email" name="a_mail" value="<?php bnr_echo('a_mail'); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <!-- زر الحفظ -->
                            <div class="form-item">
                                <button type="submit" name="ed_submit" value="ed_submit" class="btn btn-primary"><?php lang('edit'); ?></button>
                            </div>
                        </div>
                    </form>
                    <!-- /نموذج إعدادات الموقع -->
                </div>
            </div>
        </div>
    </div>
</div>
<?php } else { echo "404"; } ?>