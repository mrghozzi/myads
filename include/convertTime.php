<?php
#####################################################################
##                                                                 ##
##                        My ads v2.3.x                            ##
##                     http://www.krhost.ga                        ##
##                   e-mail: admin@krhost.ga                       ##
##                                                                 ##
##                       copyright (c) 2021                        ##
##                                                                 ##
##                    This script is freeware                      ##
##                                                                 ##
#####################################################################
if($s_st=="buyfgeufb"){

//   convertTime
function convertTime($ptime)
{
  if(isset($ptime)AND is_numeric($ptime)) {
    $etime = time() - $ptime;

    if ($etime < 1)
    {
        return '  الأن  ';
    }
    $a = array(  2 * 365* 24 * 60 * 60            =>   '  سنوات <br /> '.date("Y-m-d H:i",$ptime),
365* 24 * 60 * 60            =>   '  سنة <br /> '.date("Y-m-d H:i",$ptime),
30 * 24 * 60 * 60            =>  'شهر',
7 * 24 * 60 * 60            =>  'أسابيع',
24 * 60 * 60            =>  'أيام',
                60 * 60                 =>  'ساعة',
                60                      =>  'دقيقة',
                1                       =>  'ثانيه'
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