<?php
namespace service;

class DateService
{

    /**
     * @param int $start
     * @param int $end
     * @param String $result_type
     */
    public static function diff(int $start,int $end,String $result_type = 'days')
    {
        # 检查参数
        if($end < $start) {
            return false;
        } else if ($start == $end) {
            return 0;
        }

        # 计算时间差
        $seconds_diff = $end - $start;







    }










}
