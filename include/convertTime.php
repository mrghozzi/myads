<?PHP

#####################################################################
##                                                                 ##
##                        My ads v3.0.5(+)                         ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2023                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################
if($s_st=="buyfgeufb"){

//   convertTime
function convertTime($ptime)
{
  global $lang;

  if(isset($ptime)AND is_numeric($ptime)) {
    $etime = time() - $ptime;

    if ($etime < 1)
    {
        return $lang['now'];
    }
    $a = array(  2 * 365* 24 * 60 * 60            =>  $lang['years'].'<br /> '.date("Y-m-d H:i",$ptime),
                     365* 24 * 60 * 60            =>  $lang['year'].'<br /> '.date("Y-m-d H:i",$ptime),
                     30 * 24 * 60 * 60            =>  $lang['month'],
                      7 * 24 * 60 * 60            =>  $lang['weeks'],
                          24 * 60 * 60            =>  $lang['days'],
                               60 * 60            =>  $lang['hour'],
                                    60            =>  $lang['minute'],
                                     1            =>  $lang['second']
                );
    foreach ($a as $secs => $str)
    {
        $d = $etime / $secs;
        if ($d >= 1)
        {
            $r = round($d);
            return $r . ' ' . $str . ($r > 1 ? '' : '');
        }
    }
    }
}

}else{ echo"404"; }
 ?>