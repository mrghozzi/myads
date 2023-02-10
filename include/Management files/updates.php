<?PHP

#####################################################################
##                                                                 ##
##                        MYads  v3.x.x                            ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2022                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################
if($vrf_License=="65fgh4t8x5fe58v1rt8se9x"){
 //  there_update
   if(isset($_GET['updates']))
{
   if($_COOKIE['admin']==$hachadmin)
{


  //  template
 template_mine('header');
 if(!isset($_COOKIE['user'])!="")
{
 template_mine('404');
}else{
 template_mine('admin/admin_header');
 template_mine('admin/admin_updates');
 }
 template_mine('footer');

 }else{
   header("Location: home");
 }
 }
      // Update now
   if(isset($_GET['e_update']))
{
   if($_COOKIE['admin']==$hachadmin)
{
  	   if($_POST['up_submit']){
        $versionnow = $_POST['versionnow'];
        $myads_last_updates = "https://apikariya.gq/myads/last_updates.txt";
        $last_updates       = @file_get_contents($myads_last_updates);
        $file_get           = @fopen($last_updates, 'r');
        $Tob                = $_SERVER['DOCUMENT_ROOT'];
        $To                 = $Tob."/upload/";
        @file_put_contents($To."Tmpfile.zip", $file_get);
        $zip                = new ZipArchive;
		$file               = $To."Tmpfile.zip";
     // $path               = pathinfo(realpath($file), PATHINFO_DIRNAME);
		if ($zip->open($file) === TRUE) {
		    $zip->extractTo($Tob);
            include "requests/update.php";

        } else {
        $bn_get= "?updates&bnerrMSG=".$lang['not_update'];
        header("Location: admincp{$bn_get}");
		}

    }

 }else {  header("Location: 404");  }
}

}else{
 header("Location: .../404 ") ;
}
  ?>