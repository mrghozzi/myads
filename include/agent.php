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

 //  Get Browser
function getBrowser($u_agent)
{
    $bname = 'Unknown';
    $platform = 'Unknown';

    $ub = "Unknown";

    //First get the platform?
    if (preg_match('/Windows Phone/i', $u_agent)) {
        $platform = 'Windows Phone';
    }else if (preg_match('/Android/i', $u_agent)) {
        $platform = 'Android';
    }
    elseif (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    }
    elseif (preg_match('/iPhone/i', $u_agent)) {
        $platform = 'iPhone';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }
    elseif (preg_match('/J2ME/i', $u_agent)) {
        $platform = 'Java Platform';
    }

    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    }
    elseif(preg_match('/UBrowser/i',$u_agent))
    {
        $bname = 'UC Browser';
        $ub = "UBrowser";
    }
    elseif(preg_match('/UCBrowser/i',$u_agent))
    {
        $bname = 'UC Browser';
        $ub = "UCBrowser";
    }
    elseif(preg_match('/Microsoft/i',$u_agent))
    {
        $bname = 'Microsoft Lumia';
        $ub = "Lumia";
    }
    elseif(preg_match('/Firefox/i',$u_agent))
    {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    }
    elseif(preg_match('/Chrome/i',$u_agent))
    {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    }
    elseif(preg_match('/Lenovo A536/i',$u_agent))
    {
        $bname = 'Lenovo A536';
        $ub = "Android";
    }
    elseif(preg_match('/IEMobile/i',$u_agent))
    {
        $bname = 'IEMobile';
        $ub = "IEMobile";
    }
    elseif(preg_match('/Mobile/i',$u_agent))
    {
        $bname = 'Mobile';
        $ub = "Version";
    }
    elseif(preg_match('/Safari/i',$u_agent))
    {
        $bname = 'Apple Safari';
        $ub = "Safari";
    }
    elseif(preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Opera';
        $ub = "Opera";
    }
    elseif(preg_match('/Netscape/i',$u_agent))
    {
        $bname = 'Netscape';
        $ub = "Netscape";
    }
    elseif(preg_match('/CLR/i',$u_agent))
    {
        $bname = 'CLR Browser';
        $ub = "CLR";
    }
    elseif(preg_match('/Lumia 430/i',$u_agent))
    {
        $bname = 'Lumia 430';
        $ub = "Trident";
    }


    // finally get the correct version number
  $known = array('Version', $ub, 'other');
     $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
    $version= "";
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }

    }
    else {
        $version= $matches['version'][0];
    }

    // check if we have a number
    if ($version==null || $version=="") {$version="?";}

    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
}

}else{ echo"404"; }
 ?>