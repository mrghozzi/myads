 <?php
 include '../dbconfig.php';
$q=$db_con->prepare("CREATE TABLE IF NOT EXISTS `ads` (
  `id` int(11) NOT NULL,
  `code_ads` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;");
  $q->execute();

$q2=$db_con->prepare("INSERT INTO `ads` (`id`, `code_ads`) VALUES
(1, '<!-- MyAds code begin -->'),
(2, '<!-- MyAds code begin -->'),
(3, '<!-- MyAds code begin -->'),
(4, '<!-- MyAds code begin -->'),
(5, '<!-- MyAds code begin -->');");
  $q2->execute();
$q3=$db_con->prepare("CREATE TABLE IF NOT EXISTS `banner` (
  `id` int(15) NOT NULL,
  `uid` int(15) NOT NULL,
  `name` varchar(255) NOT NULL,
  `statu` int(15) NOT NULL DEFAULT '1',
  `vu` int(15) NOT NULL DEFAULT '0',
  `clik` int(15) NOT NULL DEFAULT '0',
  `url` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL,
  `px` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;");
  $q3->execute();
$q4=$db_con->prepare("CREATE TABLE IF NOT EXISTS `link` (
  `id` int(15) NOT NULL,
  `uid` int(15) NOT NULL,
  `statu` int(7) NOT NULL DEFAULT '1',
  `clik` float NOT NULL DEFAULT '0',
  `url` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `txt` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;");
  $q4->execute();
$q5=$db_con->prepare("CREATE TABLE IF NOT EXISTS `menu` (
  `id_m` int(9) NOT NULL,
  `name` varchar(100) NOT NULL,
  `dir` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
  $q5->execute();
$q24=$db_con->prepare("CREATE TABLE IF NOT EXISTS `messages` (
  `id_msg` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `us_env` int(11) NOT NULL,
  `us_rec` int(11) NOT NULL,
  `msg` text NOT NULL,
  `time` varchar(255) NOT NULL,
  `state` int(9) NOT NULL DEFAULT '1'
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;");
  $q24->execute();
$q25=$db_con->prepare("CREATE TABLE IF NOT EXISTS `news` (
  `id` int(15) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `statu` INT(15) NOT NULL DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;");
  $q25->execute();
$q26=$db_con->prepare("CREATE TABLE IF NOT EXISTS `notif` (
  `id` int(15) NOT NULL,
  `uid` int(15) NOT NULL,
  `name` varchar(255) NOT NULL,
  `nurl` varchar(255) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `state` int(15) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;");
  $q26->execute();
$q23=$db_con->prepare("CREATE TABLE IF NOT EXISTS `referral` (
  `id` int(15) NOT NULL,
  `uid` int(15) NOT NULL,
  `ruid` int(15) NOT NULL DEFAULT '1',
  `date` date NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;");
  $q23->execute();
$q6=$db_con->prepare("CREATE TABLE IF NOT EXISTS `setting` (
  `id` int(15) NOT NULL,
  `titer` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'MyAds',
  `description` text NOT NULL DEFAULT '',
  `url` varchar(250) NOT NULL DEFAULT '',
  `styles` varchar(255) NOT NULL DEFAULT 'default',
  `lang` varchar(25) NOT NULL DEFAULT 'ar',
  `timezone` VARCHAR(255) NOT NULL DEFAULT 'Etc/Greenwich',
  `close` int(9) NOT NULL DEFAULT '1',
  `close_text` text NOT NULL DEFAULT '',
  `a_mail` varchar(250) NOT NULL DEFAULT '',
  `a_not` text NOT NULL DEFAULT '',
  `facebook` varchar(320) NOT NULL DEFAULT '#facebook',
  `twitter` varchar(320) NOT NULL DEFAULT '#twitter',
  `linkedin` varchar(320) NOT NULL DEFAULT '#wasp'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;");
  $q6->execute();
$q8=$db_con->prepare("CREATE TABLE IF NOT EXISTS `short` (
  `id` int(15) NOT NULL,
  `uid` int(15) NOT NULL,
  `sho` varchar(255) NOT NULL DEFAULT '0',
  `url` varchar(255) NOT NULL,
  `clik` int(15) NOT NULL DEFAULT '0',
  `sh_type` int(15) NOT NULL DEFAULT '0',
  `tp_id` int(15) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;");
  $q8->execute();
$q9=$db_con->prepare("CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `img` VARCHAR(255) NOT NULL DEFAULT 'upload/avatar.png',
  `vu` int(11) NOT NULL DEFAULT '0',
  `nvu` float NOT NULL DEFAULT '0',
  `nlink` float NOT NULL DEFAULT '0',
  `pts` int(11) NOT NULL DEFAULT '10',
  `online` varchar(255) NOT NULL DEFAULT '0',
  `ucheck` INT(15) NOT NULL  DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;");
  $q9->execute();
$q10=$db_con->prepare("CREATE TABLE IF NOT EXISTS `visits` (
  `id` int(15) NOT NULL,
  `uid` int(15) NOT NULL,
  `statu` int(7) NOT NULL DEFAULT '1',
  `vu` int(15) NOT NULL DEFAULT '0',
  `url` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `tims` int(7) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;");
  $q10->execute();
$q42=$db_con->prepare("CREATE TABLE IF NOT EXISTS `state` (
  `id` int(15) NOT NULL,
  `sid` int(15) NOT NULL DEFAULT '0',
  `pid` int(15) NOT NULL DEFAULT '0',
  `t_name` varchar(255) NOT NULL DEFAULT '0',
  `r_link` varchar(255) NOT NULL DEFAULT '0',
  `r_date` varchar(255) NOT NULL DEFAULT '0',
  `visitor_Agent` varchar(255) NOT NULL DEFAULT '0',
  `v_ip` varchar(255) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;");
$q42->execute();
$q45=$db_con->prepare("CREATE TABLE `directory`
( `id` INT(15) NOT NULL AUTO_INCREMENT ,
`uid` INT(15) NOT NULL ,
`name` VARCHAR(255) NOT NULL ,
`url` VARCHAR(255) NOT NULL ,
`txt` TEXT NOT NULL ,
`metakeywords` VARCHAR(255) NOT NULL  DEFAULT '0',
`cat` INT(15) NOT NULL  DEFAULT '0',
`vu` VARCHAR(255) NOT NULL DEFAULT '0' ,
`statu` INT(15) NOT NULL DEFAULT '0' ,
 PRIMARY KEY (`id`)) ENGINE = InnoDB;");
 $q45->execute();
$q46=$db_con->prepare("CREATE TABLE `cat_dir`
 ( `id` INT(15) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `txt` TEXT NOT NULL ,
  `metakeywords` VARCHAR(255) NOT NULL DEFAULT '0' ,
  `sub` INT(15) NOT NULL DEFAULT '0' ,
  `ordercat` INT(15) NOT NULL DEFAULT '0' ,
  `statu` INT(15) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`)) ENGINE = InnoDB;");
  $q46->execute();
$q47=$db_con->prepare("CREATE TABLE `status`
 ( `id` INT(15) NOT NULL AUTO_INCREMENT ,
  `uid` INT(15) NOT NULL DEFAULT '0' ,
  `date` VARCHAR(255) NOT NULL DEFAULT '0' ,
  `s_type` INT(15) NOT NULL DEFAULT '0' ,
  `tp_id` INT(15) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)) ENGINE = InnoDB;");
  $q47->execute();
$q48=$db_con->prepare("CREATE TABLE `report`
 ( `id` INT(15) NOT NULL AUTO_INCREMENT ,
  `uid` INT(15) NOT NULL DEFAULT '0' ,
  `txt` TEXT NOT NULL DEFAULT '' ,
  `s_type` INT(15) NOT NULL DEFAULT '0' ,
  `tp_id` INT(15) NOT NULL DEFAULT '0' ,
  `statu` INT(15) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`)) ENGINE = InnoDB;");
  $q48->execute();
$q49=$db_con->prepare("CREATE TABLE `forum`
  ( `id` INT(15) NOT NULL AUTO_INCREMENT ,
   `uid` INT(15) NOT NULL ,
    `name` VARCHAR(255) NOT NULL DEFAULT '0' ,
    `txt` TEXT NOT NULL ,
    `cat` INT(15) NOT NULL DEFAULT '0' ,
    `statu` INT(15) NOT NULL DEFAULT '0' ,
    PRIMARY KEY (`id`)) ENGINE = InnoDB;");
    $q49->execute();
$q50=$db_con->prepare("CREATE TABLE `f_cat` ( `id` INT(15) NOT NULL AUTO_INCREMENT ,
 `name` VARCHAR(255) NOT NULL ,
 `icons` VARCHAR(255) NOT NULL ,
 PRIMARY KEY (`id`)) ENGINE = InnoDB;");
 $q50->execute();
$q51=$db_con->prepare("CREATE TABLE `f_coment` (
 `id` INT(15) NOT NULL AUTO_INCREMENT ,
 `uid` INT(15) NOT NULL ,
 `tid` INT(15) NOT NULL DEFAULT '0' ,
 `txt` TEXT NOT NULL ,
 `date` VARCHAR(255) NOT NULL DEFAULT '0' ,
 PRIMARY KEY (`id`)) ENGINE = InnoDB;");
 $q51->execute();
 $q52=$db_con->prepare("CREATE TABLE `emojis` (
 `id` INT(15) NOT NULL AUTO_INCREMENT ,
 `name` VARCHAR(255) NOT NULL ,
 `img` VARCHAR(255) NOT NULL ,
 PRIMARY KEY (`id`)) ENGINE = InnoDB;");
 $q52->execute();
 $q53= $db_con->prepare("CREATE TABLE `like` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `sid` int(11) NOT NULL DEFAULT '0',
  `type` int(2) NOT NULL DEFAULT '0',
  `time_t` VARCHAR(255) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;" );
$q53->execute();
$q54= $db_con->prepare("CREATE TABLE `options`
 ( `id` INT(15) NOT NULL  PRIMARY KEY AUTO_INCREMENT
 , `name` VARCHAR(255) NOT NULL DEFAULT '0'
 , `o_valuer`  TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
 , `o_type` VARCHAR(255) NOT NULL DEFAULT '0'
 , `o_parent` INT(15) NOT NULL DEFAULT '0'
 , `o_order` INT(15) NOT NULL DEFAULT '0'
 , `o_mode` VARCHAR(255) NOT NULL DEFAULT '0'
 ) ENGINE = InnoDB;" );
 $q54->execute();
$q11=$db_con->prepare("ALTER TABLE `ads`
  ADD PRIMARY KEY (`id`);");
  $q11->execute();
$q12=$db_con->prepare("ALTER TABLE `banner`
  ADD PRIMARY KEY (`id`);");
  $q12->execute();
$q13=$db_con->prepare("ALTER TABLE `link`
  ADD PRIMARY KEY (`id`);");
  $q13->execute();
$q14=$db_con->prepare("ALTER TABLE `menu`
  ADD PRIMARY KEY (`id_m`);");
  $q14->execute();
$q15=$db_con->prepare("ALTER TABLE `messages`
  ADD PRIMARY KEY (`id_msg`);");
  $q15->execute();
$q16=$db_con->prepare("ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);");
  $q16->execute();
$q17=$db_con->prepare("ALTER TABLE `notif`
  ADD PRIMARY KEY (`id`);");
  $q17->execute();
$q18=$db_con->prepare("ALTER TABLE `referral`
  ADD PRIMARY KEY (`id`);");
  $q18->execute();
$q19=$db_con->prepare("ALTER TABLE `setting`
  ADD PRIMARY KEY (`id`);");
  $q19->execute();
$q20=$db_con->prepare("ALTER TABLE `short`
  ADD PRIMARY KEY (`id`);");
  $q20->execute();
$q21=$db_con->prepare("ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);");
  $q21->execute();
$q22=$db_con->prepare("ALTER TABLE `visits`
  ADD PRIMARY KEY (`id`);");
  $q22->execute();
$q43=$db_con->prepare("ALTER TABLE `state`
  ADD PRIMARY KEY (`id`);");
  $q43->execute();
$q30=$db_con->prepare("ALTER TABLE `ads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;");
  $q30->execute();
$q31=$db_con->prepare("ALTER TABLE `banner`
  MODIFY `id` int(15) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;");
  $q31->execute();
$q32=$db_con->prepare("ALTER TABLE `link`
  MODIFY `id` int(15) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;");
  $q32->execute();
$q33=$db_con->prepare("ALTER TABLE `menu`
  MODIFY `id_m` int(9) NOT NULL AUTO_INCREMENT;");
  $q33->execute();
$q34=$db_con->prepare("ALTER TABLE `messages`
  MODIFY `id_msg` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;");
  $q34->execute();
$q35=$db_con->prepare("ALTER TABLE `news`
  MODIFY `id` int(15) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;");
  $q35->execute();
$q36=$db_con->prepare("ALTER TABLE `notif`
  MODIFY `id` int(15) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;");
  $q36->execute();
$q37=$db_con->prepare("ALTER TABLE `referral`
  MODIFY `id` int(15) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;");
  $q37->execute();
$q38=$db_con->prepare("ALTER TABLE `setting`
  MODIFY `id` int(15) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;");
  $q38->execute();
$q39=$db_con->prepare("ALTER TABLE `short`
  MODIFY `id` int(15) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;");
  $q39->execute();
$q40=$db_con->prepare("ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;");
  $q40->execute();
$q41=$db_con->prepare("ALTER TABLE `visits`
  MODIFY `id` int(15) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;");
  $q41->execute();
$q44=$db_con->prepare("ALTER TABLE `state`
  MODIFY `id` int(15) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;");
  $q44->execute();
$q55=$db_con->prepare("INSERT INTO options (id, name, o_valuer, o_type, o_parent, o_order, o_mode) VALUES (NULL, 'script', '0', 'storecat', '0', '0', 'script'), (NULL, 'plugins', '0', 'storecat', '0', '0', 'plugins'), (NULL, 'templates', '0', 'storecat', '0', '0', 'templates'), (NULL, 'blogs', '0', 'scriptcat', '0', '0', 'blogs'), (NULL, 'cms', '0', 'scriptcat', '0', '0', 'cms'), (NULL, 'forums', '0', 'scriptcat', '0', '0', 'forums'), (NULL, 'socialnetwor', '0', 'scriptcat', '0', '0', 'socialnetwor'), (NULL, 'admanager', '0', 'scriptcat', '0', '0', 'admanager'), (NULL, 'games', '0', 'scriptcat', '0', '0', 'games'), (NULL, 'ecommerce', '0', 'scriptcat', '0', '0', 'ecommerce'), (NULL, 'educational', '0', 'scriptcat', '0', '0', 'educational'), (NULL, 'directory', '0', 'scriptcat', '0', '0', 'directory'), (NULL, 'others', '0', 'scriptcat', '0', '0', 'others')" );
$q55->execute();
$q7=$db_con->prepare("INSERT INTO `setting` ( `titer`, `description`, `url`, `styles`, `lang`, `close`, `close_text`, `a_mail`, `a_not`, `facebook`, `twitter`, `linkedin`) VALUES
('MyAds', 'Description Sit web', 'http://mysite.com', 'default', 'ar', 1, '', 'mail@maysit.com', '', '#facebook', '#twitter', '#wasp');");
  $q7->execute();
 include "header.php";
    ?>

    <div class="main-content">
		<div class="form">
			<div class="sap_tabs">
				<div id="horizontalTab" style="display: block; width: 100%; margin: 0px;">



				        <div class="facts">
					        <div class="register">
						         <form>
                                 <p  >
                                 <?php
                                  if($q)
                                   {
	                               echo "<p style='color:#04B404' >CREATE TABLE 'ads'</p>";
                                  }	else{
		                          echo "<p style='color:#FF0000' >CREATE TABLE 'ads'</p>";
	                              }
                                  if($q3)
                                   {
	                               echo "<p style='color:#04B404' >CREATE TABLE 'banner'</p>";
                                  }	else{
		                          echo "<p style='color:#FF0000' >CREATE TABLE 'banner'</p>";
	                              }
                                  if($q4)
                                   {
	                               echo "<p style='color:#04B404' >CREATE TABLE 'link'</p>";
                                  }	else{
		                          echo "<p style='color:#FF0000' >CREATE TABLE 'link'</p>";
	                              }
                                  if($q5)
                                   {
	                               echo "<p style='color:#04B404' >CREATE TABLE 'menu'</p>";
                                  }	else{
		                          echo "<p style='color:#FF0000' >CREATE TABLE 'menu'</p>";
	                              }
                                  if($q6)
                                   {
	                               echo "<p style='color:#04B404' >CREATE TABLE 'setting'</p>";
                                  }	else{
		                          echo "<p style='color:#FF0000' >CREATE TABLE 'setting'</p>";
	                              }
                                  if($q8)
                                   {
	                               echo "<p style='color:#04B404' >CREATE TABLE 'short'</p>";
                                  }	else{
		                          echo "<p style='color:#FF0000' >CREATE TABLE 'short'</p>";
	                              }
                                  if($q9)
                                   {
	                               echo "<p style='color:#04B404' >CREATE TABLE 'users'</p>";
                                  }	else{
		                          echo "<p style='color:#FF0000' >CREATE TABLE 'users'</p>";
	                              }
                                  if($q10)
                                   {
	                               echo "<p style='color:#04B404' >CREATE TABLE 'visitors'</p>";
                                  }	else{
		                          echo "<p style='color:#FF0000' >CREATE TABLE 'visitors'</p>";
	                              }
                                  if($q23)
                                   {
	                               echo "<p style='color:#04B404' >CREATE TABLE 'referral'</p>";
                                  }	else{
		                          echo "<p style='color:#FF0000' >CREATE TABLE 'referral'</p>";
	                              }
                                  if($q24)
                                   {
	                               echo "<p style='color:#04B404' >CREATE TABLE 'messages'</p>";
                                  }	else{
		                          echo "<p style='color:#FF0000' >CREATE TABLE 'messages'</p>";
	                              }
                                  if($q25)
                                   {
	                               echo "<p style='color:#04B404' >CREATE TABLE 'news'</p>";
                                  }	else{
		                          echo "<p style='color:#FF0000' >CREATE TABLE 'news'</p>";
	                              }
                                  if($q26)
                                   {
	                               echo "<p style='color:#04B404' >CREATE TABLE 'notif'</p>";
                                  }	else{
		                          echo "<p style='color:#FF0000' >CREATE TABLE 'notif'</p>";
	                              }
                                   if($q42)
                                   {
	                               echo "<p style='color:#04B404' >CREATE TABLE 'state'</p>";
                                  }	else{
		                          echo "<p style='color:#FF0000' >CREATE TABLE 'state'</p>";
	                              }
                                   if($q45)
                                   {
	                               echo "<p style='color:#04B404' >CREATE TABLE 'directory'</p>";
                                  }	else{
		                          echo "<p style='color:#FF0000' >CREATE TABLE 'directory'</p>";
	                              }
                                  if($q47)
                                   {
	                               echo "<p style='color:#04B404' >CREATE TABLE 'status'</p>";
                                  }	else{
		                          echo "<p style='color:#FF0000' >CREATE TABLE 'status'</p>";
	                              }
                                  if($q49)
                                   {
	                               echo "<p style='color:#04B404' >CREATE TABLE 'forum'</p>";
                                  }	else{
		                           echo "<p style='color:#FF0000' >CREATE TABLE 'forum'</p>";
	                              }
                                  if($q52)
                                   {
	                               echo "<p style='color:#04B404' >CREATE TABLE 'emojis'</p>";
                                  }	else{
		                           echo "<p style='color:#FF0000' >CREATE TABLE 'emojis'</p>";
                                  }
                                  if($q53)
                                   {
	                               echo "<p style='color:#04B404' >CREATE TABLE 'like'</p>";
                                  }	else{
		                           echo "<p style='color:#FF0000' >CREATE TABLE 'like'</p>";
                                  }
                                  if($q54)
                                   {
	                               echo "<p style='color:#04B404' >CREATE TABLE 'options'</p>";
                                  }	else{
		                          echo "<p style='color:#FF0000' >CREATE TABLE '<b>options</b>'</p>";
	                              }
                                  if($q55)
                                   {
	                               echo "<p style='color:#04B404' >CREATE 'store'</p>";
                                  }	else{
		                          echo "<p style='color:#FF0000' >CREATE '<b>store</b>'</p>";
	                              }

                                 ?>
                                 </p>
							        <div class="sign-up">
								        <a href="install3.php" type="next" />next</a>
							        </div>
                                </form>
						    </div>
				        </div>

			 	</div>
		    </div>
        </div>
        <div class="right">
			<h4>Step 1</h4>
			<ul>
				<li><p>Install the tables in the database </p></li>
				<li><p>Click on the next button</p></li>

			</ul>


		</div>
		<div class="clear"></div>
	</div>



   <?php  include "footer.php";   ?>
