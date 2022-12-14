<?php
namespace mzclass\library;

/**
 * 日期时间处理类
 *
 * @Author Mirze
 */
class DTime
{
    // 服务器日期时间
    public function sys()
    {
        $time = time();
        $res['timestamp'] = $time;
        $res['timestamps'] = $time.'000';
        $res['datetime'] = date('Y-m-d H:i:s', $time);
        $res['date'] = date('Y-m-d', $time);
        $res['time'] = date('H:i:s', $time);
        $res['year'] = date('Y', $time);
        $res['month'] = date('m', $time);
        $res['week'] = date('w', $time);
        return $res;
    }

    // 年份合法化：起始年-当前年有效
    function vaildYear($year=0, $startYear=0)
    {
        $curYear = date('Y');
        if($year == 0 || $year > $curYear || $year < $startYear) return $curYear;
        return $year;
    }

    // 获取年度查询日期范围
    function yearStartEndDate($year, $sDate='', $eDate='')
    {
        $curDate = date('Y-m-d');
        $firstDate = $year . '-01-01'; // 年度第一天
        $endDate = $year . '-12-31'; // 年度最后一天
        
        if($sDate == '' && $eDate == '') {
            return [$firstDate, $endDate];
        }

        if($sDate != '' && $eDate != '') {
            return [$sDate, $eDate];
        }

        if($sDate != '') {
            return [$sDate, $endDate];
        }

        if($eDate != '') {
            return [$firstDate, $eDate];
        }
    }

    /**
     * 查询日期所在周聚合数据：周一到周天
     *      week_sday: 周起始日期：周一
     *      week_eday: 周结束日期：周天
     *      week_num: 年度第几周（第1周从不含上年日期开始统计）
     *      week_day: 周几(1-7)
     * @param string $date
     * @return void 周第一天，
     *      
     * @Author Mirze
     */
    function dayWeekGnn($date='') 
    {
        // $strat_week =  mktime(0, 0 , 0,date("m"),date("d")-(date("w")==0?7:date("w"))+1,date("Y"));
        // $end_week =  mktime(23,59,59,date("m"),date("d")-(date("w")==0?7:date("w"))+7,date("Y"));

        $timestamp = empty($date) ? time() : strtotime($date);
        $sdate = date('Y-m-d', $timestamp-(date('N',$timestamp)-1)*86400); // 周第一天(周一)
        $edate = date('Y-m-d', $timestamp + (7-date('N',$timestamp))*86400); // 周最后一天(周日)

        $weekNum = date('W', $timestamp); // 年度第几周
        $weekDay = date('N', $timestamp); // 周几

        $data['week_sday'] = $sdate; // 周起始日期：周一
        $data['week_eday'] = $edate; // 周结束日期：周天
        $data['week_num'] = $weekNum; // 年度第几周（第1周从不含上年日期开始统计）0-53
        $data['week_day'] = $weekDay; // 周几(1-7)
        return $data;
    }

    /**
     * 根据年度周数获取当前周的开始日期和结束日期
     *
     * @param integer $year
     * @param integer $weeknum
     * @return void
     * @Author Mirze
     */
    function weekNumSEDay($year=0, $weeknum=0){ 
        $firstdayofyear=mktime(0,0,0,1,1,$year);
        $firstweekday=date('N',$firstdayofyear); 
        $firstweenum=date('W',$firstdayofyear); 
        if($firstweenum == 1){ 
            $day = (1-($firstweekday-1))+7*($weeknum-1); 
            $startdate = date('Y-m-d',mktime(0,0,0,1,$day,$year)); 
            $enddate = date('Y-m-d',mktime(0,0,0,1,$day+6,$year)); 
        }else{ 
            $day=(9-$firstweekday)+7*($weeknum-1); 
            $startdate=date('Y-m-d',mktime(0,0,0,1,$day,$year)); 
            $enddate=date('Y-m-d',mktime(0,0,0,1,$day+6,$year)); 
        }         
        return [$startdate,$enddate];
    }


    /**
     * 获取上个季度的开始和结束日期
     * @param int $ts 时间戳
     * @return array 第一个元素为开始日期，第二个元素为结束日期
     */
    function lastQuarter($ts) {
        $ts = intval($ts);
    
        $threeMonthAgo = mktime(0, 0, 0, date('n', $ts) - 3, 1, date('Y', $ts));
        $year = date('Y', $threeMonthAgo);
        $month = date('n', $threeMonthAgo);
        $startMonth = intval(($month - 1)/3)*3 + 1; // 上季度开始月份
        $endMonth = $startMonth + 2; // 上季度结束月份
        return array(
            date('Y-m-1', strtotime($year . "-{$startMonth}-1")),
            date('Y-m-t', strtotime($year . "-{$endMonth}-1"))
        );
    }
    
    /**
     * 获取上个月的开始和结束
     * @param int $ts 时间戳
     * @return array 第一个元素为开始日期，第二个元素为结束日期
     */
    function lastMonth($ts) {
        $ts = intval($ts);
    
        $oneMonthAgo = mktime(0, 0, 0, date('n', $ts) - 1, 1, date('Y', $ts));
        $year = date('Y', $oneMonthAgo);
        $month = date('n', $oneMonthAgo);
        return array(
            date('Y-m-1', strtotime($year . "-{$month}-1")),
            date('Y-m-t', strtotime($year . "-{$month}-1"))
        );
    }
    
    /**
     * 获取上n周的开始和结束，每周从周一开始，周日结束日期
     * @param int $ts 时间戳
     * @param int $n 你懂的(前多少周)
     * @param string $format 默认为'%Y-%m-%d',比如"2012-12-18"
     * @return array 第一个元素为开始日期，第二个元素为结束日期
     */
    function lastNWeek($ts, $n, $format = '%Y-%m-%d') {
        $ts = intval($ts);
        $n  = abs(intval($n));
    
        // 周一到周日分别为1-7
        $dayOfWeek = date('w', $ts);
        if (0 == $dayOfWeek)
        {
            $dayOfWeek = 7;
        }
    
        $lastNMonday = 7 * $n + $dayOfWeek - 1;
        $lastNSunday = 7 * ($n - 1) + $dayOfWeek;
        return array(
            strftime($format, strtotime("-{$lastNMonday} day", $ts)),
            strftime($format, strtotime("-{$lastNSunday} day", $ts))
        );
    }

    /**
     * 时间戳转换话术
     *
     * @param integer $time 时间戳
     * @return void
     */
    function commentTime($time=0) {
        //$time = strtotime($time);
        $nowTime = time();
        $message = '';
        if(empty($time)) {
            $message='很早以前';
            return $message;
        }
        //一年前
        $year = idate ( 'Y', $nowTime ) - idate ( 'Y', $time );
        if ($year>0) {
            if($year==1){
                $message = "1年前";
            }else{
                  $message = date ( 'Y.m.d', $time );
            }
        }else{
            //同一年
            $days = idate ( 'z', $nowTime ) - idate ( 'z', $time );
            switch(true){
                //一天内
                case (0 == $days):
                    $seconds = $nowTime - $time;
                    //一小时内
                    if ($seconds < 3600) {
                        //一分钟内
                        if ($seconds < 60) {
                            $message = '刚刚';
                        }else{
                            $message = intval ( $seconds / 60 ) . '分钟前';
                        }
                    }else{
                        $message = idate ( 'H', $nowTime ) - idate ( 'H', $time ) . '小时前';
                    }
                    break;
                    //昨天
                case (1 == $days):
                    $message = '昨天' . date ( 'H:i', $time );
                    break;
                    //前天
                case (2 == $days):
                    $message = '前天 ' . date ( 'H:i', $time );
                    break;
                    //7天内
                case (7 > $days):
                    $message = $days . '天前';
                    break;
                case (60 > $days):
                    $message = '1月前';
                    break;
                case (120 > $days):
                    $message = '3月前';
                    break;
                case (360 > $days):
                    $message = '半年前';
                    break;
                default:
                    $message = date ( 'n月j日 H:i', $time );
                    break;
            }
        }
        return $message;
    }
    
    /**
     * 校验两个时间戳误差值范围
     *
     * @param integer $timestamp    校验时间戳：客户端时间戳
     * @param integer $minute       误差前后时间：默认前后3分钟
     * @param integer $compare      比较时间戳：默认当前时间
     * @return void bool
     */
    function compareMinute($timestamp=0, $minute=3, $compare=0)
    {
        // JAVA时间戳13位，PHP时间戳10位
        $len = strlen($timestamp);
        $timestamp = ($len > 10) ? substr($timestamp,0, 10) : $timestamp;

        if($compare > 0) {
            $cLen = strlen($compare);
            $time = ($cLen > 10) ? substr($compare,0, 10) : $compare;
        } else {
            $time = time();
        }

        $diff = 60 * $minute; // 差值

        $minTime = $time - $diff;
        $maxTime = $time + $diff;

        return ($timestamp > $minTime && $timestamp < $maxTime) ? true : false;
    }
    
    

}