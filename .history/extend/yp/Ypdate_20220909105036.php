<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-07-22 20:33:00
 * @LastEditTime: 2022-07-23 08:56:48
 * @FilePath: \web\extend\yp\Ypdate.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

declare(strict_types=1);

namespace yp;

use Nyg\Holiday;

class Ypdate
{
    public static function getNow($ypdate = '2020-02-29')
    {
        $ypdate = $ypdate ? $ypdate : date('Ymd');
        $time = strtotime($ypdate);

        $month = date('j', $time);
        $days = self::getRow($time);
        return $days;
    }
    public static function getWeek()
    {
        $week = [
            '星期日',
            '星期一',
            '星期二',
            '星期三',
            '星期四',
            '星期五',
            '星期六'
        ];
        return $week;
    }
    public static function getRow($ypdate)
    {
        $holiday = new Holiday();
        //当月天数
        $days = date('t', $ypdate);
        $enddays = 0;
        //1号星期
        $formatDate = date('Y-m-d', $ypdate);
        $firstDay = date("Y-m-d", strtotime("first day of {$formatDate}"));
        //当前月第一天星期
        $firstDayWeek = date("w", strtotime($firstDay));
        //上个月最后一天
        $preMonth =  strtotime("{$formatDate} last day of -1 month");
        //本月中上月开始
        $preMonthStart = strtotime("- {$firstDayWeek}day", $preMonth);
        $preMonthStart = strtotime("+ 1day", $preMonthStart);
        //本月最后一天星期
        $lastDay = strtotime("last day of {$formatDate}");
        $lastDayWeek = date("w", $lastDay);
        $enddays = $lastDay;
        if ($lastDayWeek != 6) {
            $nextMonthDays = 6 - $lastDayWeek - 1;
            //下个月第一天
            $nextMonth =   strtotime("{$formatDate} first day of +1 month");
            //本月中下月结束
            $nextMonthEnd = strtotime("+ {$nextMonthDays}day", $nextMonth);
            $enddays = $nextMonthEnd;
        } else {
            $enddays = $lastDay;
        }


        $week = self::getWeek();
        $calendar['today'] = date('Y-m-d', $ypdate);
        $calendar['premonth'] = date('Y-m-d', strtotime("{$formatDate} first day of -1 month"));
        $calendar['nextmonth'] = date('Y-m-d', strtotime("{$formatDate} first day of +1 month"));
        $calendar['preyear'] = date('Y-m-d', strtotime("{$formatDate} -1 year"));
        $calendar['nextyear'] = date('Y-m-d', strtotime("{$formatDate} next year"));
        $calendar['week'] = $week;
        $s = true;
        $di = 0;
        while ($s) {
            $di++;
            $nowtime = $preMonthStart;
            $arr['week'] =  $week[date('w', $nowtime)];
            $arr['date'] = date('Y-m-d', $nowtime);
            $arr['year'] = date('Y', $nowtime);
            $arr['month'] = date('m', $nowtime);
            $arr['day'] = date('d', $nowtime);
            $arr['holidays'] = $holiday->nowHoliday($nowtime);

            $calendar['day'][] =  $arr;

            $preMonthStart = strtotime("+ 1day", $preMonthStart);
            if ($preMonthStart > $enddays) {
                $s = false;
            }


            if ($di > 63) {
                $s = false;
            }
        }

        return $calendar;
    }
}
