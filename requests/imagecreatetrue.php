<?PHP

#####################################################################
##                                                                 ##
##                        MYads  v3.1.x                            ##
##                     https://www.adstn.gq                        ##
##                    e-mail: admin@adstn.gq                       ##
##                                                                 ##
##                       copyright (c) 2024                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################

 if(isset($_GET['str1']) AND isset($_GET['str2'])){

    $text = $_GET['str1']." + ".$_GET['str2']." = ";
    
    //Image settings
    $width = 100;
    $height = 30;

    // Create a new image
    $image = imagecreatetruecolor($width, $height);

    // Set colors
    $background_color = imagecolorallocate($image, 255, 255, 255);  // white
    $text_color = imagecolorallocate($image, 0, 0, 0);  // black

    // Fill the background with white
    imagefilledrectangle($image, 0, 0, $width, $height, $background_color);

    // Add text to the image
    imagestring($image, 10, 20, 7, $text, $text_color);

    // Output the image
    header('Content-type: image/png');
    imagepng($image);
    imagedestroy($image);
 }
?>