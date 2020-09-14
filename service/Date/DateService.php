<?php

namespace service\Date;

use think\Exception;

class DateService
{
    /**
     * 不同时间单位转化为秒数
     */
    const YEAR = 31536000;
    const MONTH = 2592000;
    const WEEK = 604800;
    const DAY = 86400;
    const HOUR = 3600;
    const MINUTE = 60;

    /**
     * 计算两个时间戳之间的时差,并按照要求的单位返回
     * @param int $start 开始时间
     * @param int $end 结束时间
     * @param String $result_type 返回结果类型
     * @return bool|float|int
     */
    public static function diff(int $start, int $end, string $result_type = 'days')
    {
        # 检查参数
        if ($end < $start) {
            return false;
        } else if ($start == $end) {
            return 0;
        }

        # 计算时间差
        $seconds_diff = $end - $start;

        switch ($result_type) {
            # 年数
            case 'years':
                return $seconds_diff / self::YEAR;
                break;
            # 月数
            case 'months':
                return $seconds_diff / self::MONTH;
            # 星期数
            case 'weeks':
                return $seconds_diff / self::WEEK;
                break;
            # 天数
            case 'days':
                return $seconds_diff / self::DAY;
                break;
            # 小时数
            case 'hours';
                return $seconds_diff / self::HOUR;
                break;
            # 分钟数
            case 'minutes':
                return $seconds_diff / self::MINUTE;
                break;
            # 秒数
            case 'seconds':
                return $seconds_diff;
            default:
                throw new Exception('暂不支持此种单位换算');
                break;
        }
    }

    /**
     * 计算两个格式化时间之间的时差,并按照要求的单位返回
     * @param String $start 开始时间
     * @param String $end 结束时间
     * @param string $result_type 返回结果类型
     * @return bool|float|int|String
     * @throws Exception
     */
    public static function diff_format(string $start, string $end, string $result_type = 'days')
    {
        # 检查参数
        $preg1           = '/^([12]\d\d\d)-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[0-1])$/';
        $preg2           = '/^([12]\d\d\d)-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[0-1]) ([0-1]\d|2[0-4]):([0-5]\d):([0-5]\d)$/';
        $validate_start1 = preg_match($preg1, $start);
        $validate_start2 = preg_match($preg2, $start);
        $validate_end1   = preg_match($preg1, $end);
        $validate_end2   = preg_match($preg2, $end);

        if ((!$validate_start1 && !$validate_start2) || (!$validate_end1 && !$validate_end2)) {
            throw new Exception('请输入正确的格式化时间');
        }

        if (strtotime($end) < strtotime($start)) {
            return false;
        } else if ($start == $end) {
            return 0;
        } else {
            $start = strtotime($start);
            $end   = strtotime($end);
        }

        # 计算时间差
        $seconds_diff = $end - $start;

        switch ($result_type) {
            # 年数
            case 'years':
                return $seconds_diff / self::YEAR;
                break;
            # 月数
            case 'months':
                return $seconds_diff / self::MONTH;
            # 星期数
            case 'weeks':
                return $seconds_diff / self::WEEK;
                break;
            # 天数
            case 'days':
                return $seconds_diff / self::DAY;
                break;
            # 小时数
            case 'hours';
                return $seconds_diff / self::HOUR;
                break;
            # 分钟数
            case 'minutes':
                return $seconds_diff / self::MINUTE;
                break;
            # 秒数
            case 'seconds':
                return $seconds_diff;
            default:
                throw new Exception('暂不支持此种单位换算');
                break;
        }
    }

    /**
     * 获取一段时间内每天的日期
     * @param $startDate   开始时间
     * @param $endDate     结束时间
     * @return array
     */
    public static function getDateFromRange(string $startDate, string $endDate, string $format = 'Y-m-d')
    {
        $startTimestamp = strtotime($startDate);
        $endTimestamp   = strtotime($endDate);

        # 计算日期段内有多少天
        $days = floor(($endTimestamp - $startTimestamp) / 86400 + 1);

        # 保存每天日期
        $date = [];

        for ($i = 0; $i < $days; $i++) {
            $date[] = date($format, $startTimestamp + (86400 * $i));
        }
        return $date;
    }

    /**
     * 返回今日开始和结束的时间
     * @return array
     */
    public static function today(string $result_type = 'timestamp')
    {
        $start = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $end   = mktime(23, 59, 59, date('m'), date('d'), date('Y'));

        if ($result_type == 'timestamp') {
            return [
                'start' => $start,
                'end'   => $end,
            ];
        } else {
            return [
                'start' => date($result_type, $start),
                'end'   => date($result_type, $end),
            ];
        }
    }

    /**
     * 返回昨日开始和结束的时间
     * @return array
     */
    public static function yesterday(string $result_type = 'timestamp')
    {
        $yesterday = date('d') - 1;
        $start     = mktime(0, 0, 0, date('m'), $yesterday, date('Y'));
        $end       = mktime(23, 59, 59, date('m'), $yesterday, date('Y'));

        if ($result_type == 'timestamp') {
            return [
                'start' => $start,
                'end'   => $end,
            ];
        } else {
            return [
                'start' => date($result_type, $start),
                'end'   => date($result_type, $end),
            ];
        }
    }

    /**
     * 返回本周开始和结束时间
     * @return array
     */
    public static function week(string $result_type = 'timestamp')
    {
        $timestamp = time();
        $start     = strtotime(date('Y-m-d', strtotime("this week Monday", $timestamp)));
        $end       = strtotime(date('Y-m-d', strtotime("this week Sunday", $timestamp))) + 24 * 3600 - 1;
        if ($result_type == 'timestamp') {
            return [
                'start' => $start,
                'end'   => $end,
            ];
        } else {
            return [
                'start' => date($result_type, $start),
                'end'   => date($result_type, $end),
            ];
        }
    }

    /**
     * 返回上周开始和结束的时间
     * @return array
     */
    public static function lastWeek(string $result_type = 'timestamp')
    {
        $timestamp = time();
        $start     = strtotime(date('Y-m-d', strtotime("last week Monday", $timestamp)));
        $end       = strtotime(date('Y-m-d', strtotime("last week Sunday", $timestamp))) + self::DAY - 1;

        if ($result_type == 'timestamp') {
            return [
                'start' => $start,
                'end'   => $end,
            ];
        } else {
            return [
                'start' => date($result_type, $start),
                'end'   => date($result_type, $end),
            ];
        }
    }

    /**
     * 返回本月开始和结束的时间
     * @return array
     */
    public static function month(string $result_type = 'timestamp')
    {
        $start = mktime(0, 0, 0, date('m'), 1, date('Y'));
        $end   = mktime(23, 59, 59, date('m'), date('t'), date('Y'));

        if ($result_type == 'timestamp') {
            return [
                'start' => $start,
                'end'   => $end,
            ];
        } else {
            return [
                'start' => date($result_type, $start),
                'end'   => date($result_type, $end),
            ];
        }
    }

    /**
     * 返回上个月开始和结束的时间
     * @return array
     */
    public static function lastMonth(string $result_type = 'timestamp')
    {
        $start = mktime(0, 0, 0, date('m') - 1, 1, date('Y'));
        $end   = mktime(23, 59, 59, date('m') - 1, date('t', $start), date('Y'));

        if ($result_type == 'timestamp') {
            return [
                'start' => $start,
                'end'   => $end,
            ];
        } else {
            return [
                'start' => date($result_type, $start),
                'end'   => date($result_type, $end),
            ];
        }
    }

    /**
     * 获取剩余时间
     * @param $end_time             结束时间
     * @param string $result_type 返回结果类型
     * @return false|int|string
     * @throws Exception
     */
    public static function remaining($end_time, string $result_type = 'timestamp')
    {
        # 检查参数
        $preg1           = '/^([12]\d\d\d)-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[0-1])$/';
        $preg2           = '/^([12]\d\d\d)-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[0-1]) ([0-1]\d|2[0-4]):([0-5]\d):([0-5]\d)$/';
        $validate_start1 = preg_match($preg1, $end_time);
        $validate_start2 = preg_match($preg2, $end_time);

        if (!$validate_start1 && !$validate_start2) {
            if ((int)$end_time < 0) {
                throw new Exception('请输入正确的格式化时间');
            } else {
                $param_type = 'seconds';
            }
        } else {
            $param_type = 'format';
        }

        # 将时间参数转化为秒 便于处理
        if ($param_type == 'format') {
            $end_time = strtotime($end_time);
        }

        if ($end_time < time()) {
            throw new Exception('请检查时间参数');
        }

        # 生成结果
        if ($result_type == 'timestamp') {
            return $end_time - time();
        } else if ($result_type = 'description') {
            $seconds = (int)$end_time - time();

            if ($seconds > 3600) {
                $days_num = 0;

                # 剩余时间大于一天,计算出天数
                if ($seconds > 24 * 3600) {
                    $days     = (int)($seconds / 86400);
                    $days_num = $days;
                    $seconds  = $seconds % 86400;//取余
                }

                # 计算出剩余的小时数和分钟数
                $hours   = intval($seconds / 3600);
                $minutes = $seconds % 3600;

                # 结果变量
                $result = '';

                if (isset($days_num)) {
                    $result .= $days_num . '天';
                }

                # 拼接成字符串语句
                $result .= $hours
                    . '小时'
                    . gmstrftime('%M分钟%S', $minutes)
                    . '秒';

                return $result;
            } else {
                $result = gmstrftime("%M分钟%S", $seconds) . '秒';
                return $result;
            }
        }
    }

    /**
     * 获取当前时间戳
     * @return int
     */
    public static function now($format = 'timestamp')
    {
        if ($format == 'timestamp') {
            return time();
        } else {
            return date($format, time());
        }
    }

    /**
     * 将中文格式化时间转化为时间戳
     * @param $originalFormat   中文时间格式(格式样例 如:Y年m月d日)
     * @param $date             日期(实际参数)
     * @return false|int
     */
    public static function converDateFormat2timestamp($originalFormat, $date, string $result_type = 'timestamp')
    {
        try {
            $arr = date_parse_from_format("{$originalFormat}", $date);
            $ts  = mktime($arr['hour'], $arr['minute'], $arr['second'], $arr['month'], $arr['day'], $arr['year']);

            if ($result_type == 'timestamp') {
                return $ts;
            } else {
                return date($result_type, $ts);
            }
        } catch (\Exception $exception) {
            return 0;
        }
    }


}
