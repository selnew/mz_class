<?php
namespace mzclass\library;

/**
 * 安全：加密、解密、私钥
 * @author Mirze <mirzeAdv@163.com>
 *
 *
 */
class Safe
{
    //加密函数
    function lock_url($txt,$key='mirze')
    {
        $txt = $txt.$key;
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+";
        $nh = rand(0,64);
        $ch = $chars[$nh];
        $mdKey = md5($key.$ch);
        $mdKey = substr($mdKey,$nh%8, $nh%8+7);
        $txt = base64_encode($txt);
        $tmp = '';
        $i=0;$j=0;$k = 0;
        for ($i=0; $i<strlen($txt); $i++) {
            $k = $k == strlen($mdKey) ? 0 : $k;
            $j = ($nh+strpos($chars,$txt[$i])+ord($mdKey[$k++]))%64;
            $tmp .= $chars[$j];
        }
        return urlencode(base64_encode($ch.$tmp));
    }
    //解密函数
    function unlock_url($txt,$key='mirze')
    {
        $txt = base64_decode(urldecode($txt));
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+";
        $ch = $txt[0];
        $nh = strpos($chars,$ch);
        $mdKey = md5($key.$ch);
        $mdKey = substr($mdKey,$nh%8, $nh%8+7);
        $txt = substr($txt,1);
        $tmp = '';
        $i=0;$j=0; $k = 0;
        for ($i=0; $i<strlen($txt); $i++) {
            $k = $k == strlen($mdKey) ? 0 : $k;
            $j = strpos($chars,$txt[$i])-$nh - ord($mdKey[$k++]);
            while ($j<0) $j+=64;
            $tmp .= $chars[$j];
        }
        return trim(base64_decode($tmp),$key);
    }

    /**
     * UTF8字符串加密
     * @param  [type] $string [description]
     * @return [type]         [description]
     */
    function encode($string) {
        $chars = '';
        $len = strlen($string = iconv('utf-8', 'gbk', $string));
        for ($i = 0; $i < $len; $i++) {
            $chars .= str_pad(base_convert(ord($string[$i]), 10, 36), 2, 0, 0);
        }
        return strtoupper($chars);
    }
    /**
     * UTF8字符串解密
     * @param  [type] $string [description]
     * @return [type]         [description]
     */
    function decode($string) {
        $chars = '';
        foreach (str_split($string, 2) as $char) {
            $chars .= chr(intval(base_convert($char, 36, 10)));
        }
        return iconv('gbk', 'utf-8', $chars);
    }

    //url base64编码: 传参替换
    function urlsafe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }
    //url base64解码: 传参替换
    function urlsafe_b64decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }




}