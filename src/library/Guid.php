<?php
namespace mzclass\library;

/**
 * 生成码：唯一码，订单号
 * 
 * @Author Mirze
 */
class Guid
{
    //密码字典
    protected static $dic = [
        0,1,2,3,4,5,6,7,8,9
        ,'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'
    ];
    
    // 格式化
    static function encodeID($int, $format=8, $dnum=36) {
        $dics = self::$dic;
        // $dnum = 36; //进制数
        $arr = array ();
        $loop = true;
        while ($loop) {
            $arr[] = $dics[bcmod($int, $dnum)];
            $int = bcdiv($int, $dnum, 0);
            if ($int == "0") {
                $loop = false;
            }
        }
        if (count($arr) < $format)
            $arr = array_pad($arr, $format, $dics[0]);
 
        return implode("", array_reverse($arr));
    }
 
    // 转码还原
    static function decodeID($ids, $dnum=36) {
        $dics = self::$dic;
        // $dnum = 36; //进制数
        //键值交换
        $dedic = array_flip($dics);
        //去零
        $id = ltrim($ids, $dics[0]);
        //反转
        $id = strrev($id);
        $v = 0;
        for ($i = 0, $j = strlen($id); $i < $j; $i++) {
            $v = bcadd(bcmul($dedic[$id[$i]], bcpow($dnum, $i, 0), 0), $v, 0);
        }
        return $v;
    }

    // 随机数
    static function randomNum($len=6){
        $d = substr(base_convert(md5(uniqid(md5(microtime(true)),true)), 16, 10), 0, $len);
        return $d;
    }

    // 唯一码
    static function uuid()
    {
        mt_srand((double)microtime() * 10000);
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $uuid = substr($charid, 0, 8)
            . substr($charid, 8, 4)
            . substr($charid, 12, 4)
            . substr($charid, 16, 4)
            . substr($charid, 20, 12);
        return $uuid;
    }

    // 生成随机码
    static function genRandStr($length = 6, $prefix = '', $suffix = '')
    {
        for($i = 0; $i < $length; $i++){
            $prefix .= random_int(0,1) ? chr(random_int(65, 90)) : random_int(0, 9);
        }
        return $prefix . $suffix;
    }

    // 唯一订单号：
    static function orderSnOne($len = 20, $prefix = '', $suffix = '')
    {
        $subLen = $len > 13 ? $len-8 : $len-6;
        $res = substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, $subLen);
        return $prefix. $res. $suffix;
    }

    // 唯一订单号：适用大型电商
    static function orderSnTwo($prefix = '', $suffix = '')
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $diff = date('Y') - 2022;
        $k = $diff % 10;
        $res = $yCode[$k] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
        return $prefix. $res. $suffix;
    }

    // 唯一订单号：适用大型电商
    static function orderSnThree($prefix = '') {
        $order_id_main = $prefix . rand(10000000, 99999999);
        $order_id_len = strlen($order_id_main);
        $order_id_sum = 0;
        for ($i = 0; $i < $order_id_len; $i++) {
            $order_id_sum += (int)(substr($order_id_main, $i, 1)); //这里对生成的随机序列进行加法运算使重复率降低
        }
        $nid = $order_id_main . str_pad((100 - $order_id_sum % 100) % 100, 2, '0', STR_PAD_LEFT);
        return $nid;
    }
    
    
    
    


}