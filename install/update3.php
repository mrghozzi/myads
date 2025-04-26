<?php
require_once '../dbconfig.php';
include "header.php";
?>

<div class="main-content">
    <div class="form">
        <div class="sap_tabs">
            <div id="horizontalTab" style="display: block; width: 100%; margin: 0px;">
                <div class="facts">
                    <div class="register">
                        <h4>Update Completed Successfully</h4>
                        <br />
                        <h3>
                            <p style='color:#FF0000'>DELETE THE "INSTALL" FOLDER OR RENAME IT IMMEDIATELY!</p>
                        </h3>
                        <div class="sign-up">
                            <a href="../index.php" class="btn btn-primary">Preview Site</a>
                            <a href="../login.php" class="btn btn-secondary">Administration Panel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="right">
        <h4>Final Step</h4>
        <ul>
            <li><p>This is the last step of the update process.</p></li>
            <li><p>Welcome to your updated site!</p></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>

<?php include "footer.php"; ?>
