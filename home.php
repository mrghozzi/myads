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

// Include configuration and function files
include "dbconfig.php"; // Database settings
include "include/function.php"; // Helper functions

// Set page title
$title_page = $lang['home'];

// Check form submission
if (isset($_POST['bt_pts'])) {

    // Receive inputs from the form
    $le_name = $_POST['pts']; // Number of points
    $le_type = $_POST['to'];  // Type of operation

    // Validate inputs
    if ($le_name < 0) {
        $le_name = 0; // Prevent negative values
    } else if (!is_numeric($le_name)) {
        $le_name = 0; // Prevent non-numeric values
    }

    // Check available points for the user
    if (isset($uRow['pts']) && ($uRow['pts'] < $le_name)) {
        $errMSG = $lang['tnopmtrnon'] . " : " . $uRow['pts'] . "</b>";
    } else if ($le_name == "0") {
        $errMSG = $lang['cnc0p'];
    }

    // If there is an error message, prepare the link with the message
    if (isset($errMSG)) {
        $le_get = "?errMSG=" . $errMSG;
    }

    // If there are no errors, execute the operation
    if (!isset($errMSG)) {
        $o_type = "hest_pts"; // Type of operation
        $bn_desc = "-" . $le_name; // Description of operation

        // Determine the type of operation based on inputs
        if ($le_type == "link") {
            $bn_name = "tostads";
        } else if ($le_type == "banners") {
            $bn_name = "towthbaner";
        } else if ($le_type == "exchv") {
            $bn_name = "toexchvisi";
        }

        // Prepare data for database entry
        $bn_uid = $uRow['id'];
        $bn_sid = "0";
        $o_time = time();

        // Insert operation into options table
        $inshest = $db_con->prepare("INSERT INTO options (name, o_valuer, o_type, o_parent, o_order, o_mode)
            VALUES (:name, :o_valuer, :o_type, :o_parent, :o_order, :o_mode)");
        $inshest->bindParam(":name", $bn_name);
        $inshest->bindParam(":o_valuer", $bn_desc);
        $inshest->bindParam(":o_type", $o_type);
        $inshest->bindParam(":o_parent", $bn_uid);
        $inshest->bindParam(":o_order", $bn_sid);
        $inshest->bindParam(":o_mode", $o_time);

        // Execute the operation
        if ($inshest->execute()) {
            // Update user data based on operation type
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

// Display templates
template_mine('header'); // Display page header
if (isset($_COOKIE['user']) == "") {
    template_mine('404'); // Display error page if user is not logged in
} else {
    template_mine('home'); // Display user's home page
}
template_mine('footer'); // Display page footer

?>

