<?php
#####################################################################
##                                                                 ##
##                         MYads  v3.2.x                           ##
##                  https://github.com/mrghozzi                    ##
##                                                                 ##
##                                                                 ##
##                       copyright (c) 2025                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################
if($vrf_License=="65fgh4t8x5fe58v1rt8se9x"){
           //  report List
   if(isset($_GET['widgets']))
{      $admin_page=1;
   if($_COOKIE['admin']==$hachadmin)
{




  //  template
 template_mine('header');
 if(!isset($_COOKIE['user'])!="")
{
 template_mine('404');
}else{
 template_mine('admin/admin_header');
 template_mine('admin/admin_widgets');
 }
 template_mine('footer');

 }else{
   header("Location: home");
 }
 }
}else{
 header("Location: .../404.php ") ;
}
  ?>
