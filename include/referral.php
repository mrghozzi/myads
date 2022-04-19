<?PHP

#####################################################################
##                                                                 ##
##                        My ads v2.4.x                            ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2022                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################
if($s_st=="buyfgeufb"){ 
include_once('include/pagination.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$uidss = $_SESSION['user'];

$statement = "`referral` WHERE uid={$uidss} ORDER BY `id` DESC";
$results = $db_con->prepare("SELECT  * FROM {$statement} " );
$results->execute();
while($wt=$results->fetch(PDO::FETCH_ASSOC)) {


$ssus = $db_con->prepare("SELECT * FROM users WHERE id=".$wt['ruid'] );
$ssus->execute();
$wus=$ssus->fetch(PDO::FETCH_ASSOC);

echo "<tr>
  <td>{$wus['id']}</td>
  <td><div class=\"table-column\">
                      <!-- USER STATUS -->
                      <div class=\"user-status\">
                        <!-- USER STATUS AVATAR -->
                        <a class=\"user-status-avatar\" href=\"{$url_site}/u/{$wus['id']}\">
                          <!-- USER AVATAR -->
                          <div class=\"user-avatar small no-outline "; online_us($wus['id']); echo " \">
                            <!-- USER AVATAR CONTENT -->
                            <div class=\"user-avatar-content\">
                              <!-- HEXAGON -->
                              <div class=\"hexagon-image-30-32\" data-src=\"{$url_site}/{$wus['img']}\" style=\"width: 30px; height: 32px; position: relative;\"><canvas style=\"position: absolute; top: 0px; left: 0px;\" width=\"30\" height=\"32\"></canvas></div>
                              <!-- /HEXAGON -->
                            </div>
                            <!-- /USER AVATAR CONTENT -->

                            <!-- /USER AVATAR PROGRESS -->

                            <!-- USER AVATAR PROGRESS BORDER -->
                            <div class=\"user-avatar-progress-border\">
                              <!-- HEXAGON -->
                              <div class=\"hexagon-border-40-44\" style=\"width: 40px; height: 44px; position: relative;\"></div>
                              <!-- /HEXAGON -->
                            </div>
                            <!-- /USER AVATAR PROGRESS BORDER -->  ";
                            if(check_us($wus['id'],1)==1){
 echo                   " <!-- USER AVATAR BADGE -->
                            <div class=\"user-avatar-badge\">
                              <!-- USER AVATAR BADGE BORDER -->
                              <div class=\"user-avatar-badge-border\">
                                <!-- HEXAGON -->
                                <div class=\"hexagon-22-24\" style=\"width: 22px; height: 24px; position: relative;\"></div>
                                <!-- /HEXAGON -->
                              </div>
                              <!-- /USER AVATAR BADGE BORDER -->

                              <!-- USER AVATAR BADGE CONTENT -->
                              <div class=\"user-avatar-badge-content\">
                                <!-- HEXAGON -->
                                <div class=\"hexagon-dark-16-18\" style=\"width: 16px; height: 18px; position: relative;\"></div>
                                <!-- /HEXAGON -->
                              </div>
                              <!-- /USER AVATAR BADGE CONTENT -->

                              <!-- USER AVATAR BADGE TEXT -->
                              <p class=\"user-avatar-badge-text\"><i class=\"fa fa-fw fa-check\" ></i></p>
                              <!-- /USER AVATAR BADGE TEXT -->
                            </div>
                            <!-- /USER AVATAR BADGE -->       ";
                              }
echo                 " </div>
                          <!-- /USER AVATAR -->
                        </a>
                        <!-- /USER STATUS AVATAR -->

                        <!-- USER STATUS TITLE -->
                        <p class=\"user-status-title\"><a class=\"bold\" href=\"{$url_site}/u/{$wus['id']}\">{$wus['username']}</a></p>
                        <!-- /USER STATUS TITLE -->

                        <!-- USER STATUS TEXT -->
                        <p class=\"user-status-text small\">@{$wus['username']}</p>
                        <!-- /USER STATUS TEXT -->
                      </div>
                      <!-- /USER STATUS -->
                    </div>
 </td>
  <td>{$wt['date']}</td>
  <td>{$wus['pts']}</td>
</tr>";
   }


}else{ echo"404"; }
 ?>