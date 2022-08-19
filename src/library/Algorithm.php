<?php
namespace mzclass\library;

/**
 * 算法：排序、
 *
 * @author Mirze <mirzeAdv@163.com>
 *
 */
class Algorithm
{
    /**
     * [bubble_sort 冒泡排序]
     * @param  [array] $data [需要排序的数据（相邻的数据进行比较调换位置）]
     * @return [array]       [排序好的数据]
     */
    function bubble_sort($data)
    {
        if(!empty($data) && is_array($data))
        {
            $len = count($data);
            for($i=0; $i<$len; $i++)
            {
                for($k=0; $k<$len-1; $k++)
                {
                    if($data[$k] > $data[$i])
                    {
                        $data[$i] = $data[$i] ^ $data[$k];
                        $data[$k] = $data[$i] ^ $data[$k];
                        $data[$i] = $data[$i] ^ $data[$k];
                    }
                }
            }
        }
        return $data;
    }

    /**
     * [select_sort 选择排序]
     * @param  [array] $data [需要排序的数据（选择最小的值与第一个调换位置）]
     * @return [array]       [排序好的数据]
     */
    function select_sort($data)
    {
        if(!empty($data) && is_array($data))
        {
            $len = count($data);
            for($i=0; $i<$len; $i++)
            {
                $t = $i;
                for($j=$i+1; $j<$len; $j++)
                {
                    if($data[$t] > $data[$j])
                    {
                        $t = $j;
                    }
                }
                if($t != $i)
                {
                    $data[$i] = $data[$i] ^ $data[$t];
                    $data[$t] = $data[$i] ^ $data[$t];
                    $data[$i] = $data[$i] ^ $data[$t];
                }
            }
        }
        return $data;
    }

    /**
     * [insert_sort 插入排序]
     * @param  [array] $data [需要排序的数据（把第n个数插到前面的有序数组中，以此反复循环直到排序好）]
     * @return [array]       [排序好的数据]
     */
    function insert_sort($data)
    {
        if(!empty($data) && is_array($data))
        {
            $len = count($data);
            for($i=1; $i<$len; $i++)
            {
                $tmp = $data[$i];
                for($j=$i-1; $j>=0; $j--)
                {
                    if($data[$j] > $tmp)
                    {
                        $data[$j+1] = $data[$j];
                        $data[$j]   = $tmp;
                    } else {
                        break;
                    }
                }
            }
        }
        return $data;
    }

    /**
     * [quick_sort 快速排序]
     * @param  [array] $data [需要排序的数据（选择一个基准元素，将待排序分成小和打两罐部分，以此类推递归的排序划分两罐部分）]
     * @return [array]       [排序好的数据]
     */
    function quick_sort($data)
    {
        if(!empty($data) && is_array($data))
        {
            $len = count($data);
            if($len <= 1) return $data;

            $base = $data[0];
            $left_array = array();
            $right_array = array();
            for($i=1; $i<$len; $i++)
            {
                if($base > $data[$i])
                {
                    $left_array[] = $data[$i];
                } else {
                    $right_array[] = $data[$i];
                }
            }
            if(!empty($left_array)) $left_array = quick_sort($left_array);
            if(!empty($right_array)) $right_array = quick_sort($right_array);

            return array_merge($left_array, array($base), $right_array);
        }
    }

    /**
     * [GetDistance 计算两个进纬度之间的距离]
     * @param [float] $lat1 [纬度1]
     * @param [float] $lng1 [经度1]
     * @param [float] $lat2 [纬度2]
     * @param [float] $lng2 [经度2]
     * @return[float]       [两个坐标距离值]
     */
    function GetDistance($lat1, $lng1, $lat2, $lng2)
    {
        $pi = 3.1415926535898;
        $earth_radius = 6378.137;

        $radLat1 = $lat1 * ($pi / 180);
        $radLat2 = $lat2 * ($pi / 180);
       
        $a = $radLat1 - $radLat2; 
        $b = ($lng1 * ($pi / 180)) - ($lng2 * ($pi / 180)); 
       
        $s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1)*cos($radLat2)*pow(sin($b/2),2))); 
        $s = $s * $earth_radius; 
        $s = round($s * 10000) / 10000; 
        return $s; 
    }



}