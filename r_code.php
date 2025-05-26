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



// Include database configuration and functions
include "dbconfig.php";
include "include/function.php";

// Set the page title using language variables
$title_page = $lang['codes']."&nbsp;".$lang['referal'];

// Display the header template
template_mine('header');

// Check if the user is logged in
if(!isset($_COOKIE['user'])!="")
{
    // If not logged in, show 404 page
    template_mine('404');
}else{
    // If logged in, show referral code page
    template_mine('r_code');
}

// Display the footer template
template_mine('footer');


?>

