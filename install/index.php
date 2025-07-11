<?php
include_once '../dbconfig.php';
include "header.php";
?>

<div class="main-content">
    <div class="form">
        <div class="sap_tabs">
            <div id="horizontalTab" style="display: block; width: 100%; margin: 0px;">
                <div class="facts">
                    <div class="register">
                        <form>
                            <h4>Install</h4>
                            <h4>MyAds <?php echo $version; ?></h4>
                            <div class="sign-up">
                                <a href="install2.php" class="btn btn-primary">Start Installation</a>
                            </div>
                            <div class="sign-up">
                                <a href="update1.php" class="btn btn-secondary">Start Update</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="right">
        <h4>Welcome!</h4>
        <ul>
            <li><p>Edit the dbconfig.php file and add the database name, username, and password.</p></li>
            <li><p>Click the install button to proceed.</p></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>

<?php include "footer.php"; ?>