<?php
include_once '../dbconfig.php';
include "update.php";
include "header.php";
?>

<div class="main-content">
    <div class="form">
        <div class="sap_tabs">
            <div id="horizontalTab" style="display: block; width: 100%; margin: 0px;">
                <div class="facts">
                    <div class="register">
                        <form>
                            <p>
                                <?php
                                // عرض رسالة التحديث
                                if (isset($echoup)) {
                                    echo $echoup;
                                } else {
                                    echo "Preparing for the update...";
                                }
                                ?>
                            </p>
                            <div class="sign-up">
                                <a href="update3.php" class="btn btn-primary">Next</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="right">
        <h4>Update Step 1</h4>
        <ul>
            <li><p>Install the required tables in the database.</p></li>
            <li><p>Click on the "Next" button to proceed.</p></li>
        </ul>
    </div>
    <div class="clear"></div>
</div>

<?php include "footer.php"; ?>