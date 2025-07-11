<?php if (isset($s_st) AND ($s_st == "buyfgeufb")) { ?>
<div class="grid grid-3-9 medium-space">
    <!-- Sidebar -->
    <div class="grid-column">
        <?php template_mine('admin/admin_nav'); ?>
    </div>

    <!-- Site Settings -->
    <div class="grid-column">
        <div class="widget-box">
            <div class="col-md-12 validation-grid">
                <!-- Site Settings Title -->
                <p class="widget-box-title">
                    <h4><span><?php lang('settings_site'); ?></span></h4>
                </p>

                <div class="widget-box-content">
                    <!-- Show error message if exists -->
                    <?php if (isset($_GET['bnerrMSG'])) { ?>
                        <div class="alert alert-danger" role="alert"><?php echo $_GET['bnerrMSG']; ?></div>
                    <?php } ?>

                    <!-- Site Settings Form -->
                    <form id="defaultForm" method="post" class="form-horizontal" action="admincp.php?settings">
                        <div class="form-row split">
                            <!-- Site Name Input -->
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="Site-Name"><?php lang('site_name'); ?></label>
                                    <input type="text" id="Site-Name" name="name" value="<?php bnr_echo('titer'); ?>">
                                </div>
                            </div>

                            <!-- Website URL Input -->
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="Url-website"><?php lang('url_link'); ?></label>
                                    <input type="text" id="Url-website" name="url" value="<?php bnr_echo('url'); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-row split">
                            <!-- Site Description Input -->
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="Description"><?php lang('description'); ?></label>
                                    <textarea id="profile-desc" name="desc"><?php bnr_echo('description'); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-row split">
                            <!-- Template Name Input -->
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="Template-name"><?php lang('template'); ?></label>
                                    <input type="text" id="Template-name" name="styles" value="<?php bnr_echo('styles'); ?>">
                                </div>
                            </div>

                            <!-- Default Language Input -->
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="Language-Default"><?php lang('language_default'); ?></label>
                                    <input type="text" id="Language-Default" name="lang" value="<?php bnr_echo('lang'); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-row split">
                            <!-- Educational Links Selection -->
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
                            <!-- Timezone Selection -->
                            <div class="form-item">
                                <div class="form-select">
                                    <label for="site-Timezone"><?php lang('timezone'); ?></label>
                                    <select id="site-Timezone" name="timezone">
                                        <option value="<?php bnr_echo('timezone'); ?>"><?php bnr_echo('timezone'); ?></option>
                                        <!-- Timezone List -->
                                        <option value="Etc/GMT+12">(GMT-12:00) International Date Line West</option>
                                        <option value="Pacific/Midway">(GMT-11:00) Midway Island, Samoa</option>
                                        <option value="Pacific/Honolulu">(GMT-10:00) Hawaii</option>
                                        <option value="US/Alaska">(GMT-09:00) Alaska</option>
                                        <option value="America/Los_Angeles">(GMT-08:00) Pacific Time (US & Canada)</option>
                                        <option value="America/Tijuana">(GMT-08:00) Baja California</option>
                                        <option value="America/Denver">(GMT-07:00) Mountain Time (US & Canada)</option>
                                        <option value="America/Chihuahua">(GMT-07:00) Chihuahua, La Paz, Mazatlan</option>
                                        <option value="America/Phoenix">(GMT-07:00) Arizona</option>
                                        <option value="America/Chicago">(GMT-06:00) Central Time (US & Canada)</option>
                                        <option value="America/Regina">(GMT-06:00) Saskatchewan</option>
                                        <option value="America/Mexico_City">(GMT-06:00) Guadalajara, Mexico City, Monterrey</option>
                                        <option value="America/New_York">(GMT-05:00) Eastern Time (US & Canada)</option>
                                        <option value="America/Indiana/Indianapolis">(GMT-05:00) Indiana (East)</option>
                                        <option value="America/Bogota">(GMT-05:00) Bogota, Lima, Quito</option>
                                        <option value="America/Caracas">(GMT-04:30) Caracas</option>
                                        <option value="America/Halifax">(GMT-04:00) Atlantic Time (Canada)</option>
                                        <option value="America/La_Paz">(GMT-04:00) Georgetown, La Paz, San Juan</option>
                                        <option value="America/Santiago">(GMT-04:00) Santiago</option>
                                        <option value="America/St_Johns">(GMT-03:30) Newfoundland</option>
                                        <option value="America/Sao_Paulo">(GMT-03:00) Brasilia</option>
                                        <option value="America/Argentina/Buenos_Aires">(GMT-03:00) Buenos Aires</option>
                                        <option value="America/Godthab">(GMT-03:00) Greenland</option>
                                        <option value="Atlantic/Cape_Verde">(GMT-01:00) Cape Verde Is.</option>
                                        <option value="Atlantic/Azores">(GMT-01:00) Azores</option>
                                        <option value="Europe/London">(GMT+00:00) Dublin, Edinburgh, Lisbon, London</option>
                                        <option value="Africa/Casablanca">(GMT+00:00) Casablanca, Monrovia</option>
                                        <option value="Europe/Berlin">(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna</option>
                                        <option value="Europe/Belgrade">(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague</option>
                                        <option value="Europe/Paris">(GMT+01:00) Brussels, Copenhagen, Madrid, Paris</option>
                                        <option value="Africa/Lagos">(GMT+01:00) West Central Africa</option>
                                        <option value="Europe/Istanbul">(GMT+02:00) Athens, Bucharest, Istanbul</option>
                                        <option value="Asia/Jerusalem">(GMT+02:00) Jerusalem</option>
                                        <option value="Asia/Amman">(GMT+02:00) Amman</option>
                                        <option value="Asia/Beirut">(GMT+02:00) Beirut</option>
                                        <option value="Africa/Cairo">(GMT+02:00) Cairo</option>
                                        <option value="Asia/Damascus">(GMT+02:00) Damascus</option>
                                        <option value="Africa/Johannesburg">(GMT+02:00) Harare, Pretoria</option>
                                        <option value="Asia/Baghdad">(GMT+03:00) Baghdad</option>
                                        <option value="Asia/Riyadh">(GMT+03:00) Kuwait, Riyadh</option>
                                        <option value="Asia/Tehran">(GMT+03:30) Tehran</option>
                                        <option value="Asia/Dubai">(GMT+04:00) Abu Dhabi, Muscat</option>
                                        <option value="Asia/Baku">(GMT+04:00) Baku</option>
                                        <option value="Asia/Yerevan">(GMT+04:00) Yerevan</option>
                                        <option value="Asia/Kabul">(GMT+04:30) Kabul</option>
                                        <option value="Asia/Karachi">(GMT+05:00) Islamabad, Karachi</option>
                                        <option value="Asia/Tashkent">(GMT+05:00) Tashkent</option>
                                        <option value="Asia/Kolkata">(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi</option>
                                        <option value="Asia/Kathmandu">(GMT+05:45) Kathmandu</option>
                                        <option value="Asia/Dhaka">(GMT+06:00) Astana, Dhaka</option>
                                        <option value="Asia/Colombo">(GMT+06:00) Sri Jayawardenepura</option>
                                        <option value="Asia/Almaty">(GMT+06:00) Almaty, Novosibirsk</option>
                                        <option value="Asia/Rangoon">(GMT+06:30) Yangon (Rangoon)</option>
                                        <option value="Asia/Bangkok">(GMT+07:00) Bangkok, Hanoi, Jakarta</option>
                                        <option value="Asia/Shanghai">(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi</option>
                                        <option value="Asia/Kuala_Lumpur">(GMT+08:00) Kuala Lumpur, Singapore</option>
                                        <option value="Asia/Taipei">(GMT+08:00) Taipei</option>
                                        <option value="Australia/Perth">(GMT+08:00) Perth</option>
                                        <option value="Asia/Seoul">(GMT+09:00) Seoul</option>
                                        <option value="Asia/Tokyo">(GMT+09:00) Osaka, Sapporo, Tokyo</option>
                                        <option value="Australia/Darwin">(GMT+09:30) Darwin</option>
                                        <option value="Australia/Adelaide">(GMT+09:30) Adelaide</option>
                                        <option value="Australia/Sydney">(GMT+10:00) Canberra, Melbourne, Sydney</option>
                                        <option value="Australia/Brisbane">(GMT+10:00) Brisbane</option>
                                        <option value="Australia/Hobart">(GMT+10:00) Hobart</option>
                                        <option value="Asia/Vladivostok">(GMT+10:00) Vladivostok</option>
                                        <option value="Pacific/Guam">(GMT+10:00) Guam, Port Moresby</option>
                                        <option value="Asia/Magadan">(GMT+11:00) Magadan, Solomon Is., New Caledonia</option>
                                        <option value="Pacific/Auckland">(GMT+12:00) Auckland, Wellington</option>
                                        <option value="Pacific/Fiji">(GMT+12:00) Fiji, Kamchatka, Marshall Is.</option>
                                        <option value="Pacific/Tongatapu">(GMT+13:00) Nuku'alofa</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Admin Email Input -->
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="Admin-Email"><?php lang('admin_email'); ?></label>
                                    <input type="text" id="Admin-Email" name="a_mail" value="<?php bnr_echo('a_mail'); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <!-- Save Button -->
                            <div class="form-item">
                                <button type="submit" name="ed_submit" value="ed_submit" class="btn btn-primary"><?php lang('edit'); ?></button>
                            </div>
                        </div>
                    </form>
                    <!-- /Site Settings Form -->
                </div>
            </div>
        </div>
    </div>
</div>
<?php } else { echo "404"; } ?>