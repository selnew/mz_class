<?php
namespace mzclass\library;

/**
 * 安全过滤校验：
 *      Input           输入校验
 *      htmlTags        过滤防XSS注入
 *      checkSign       校验签名
 *      regularVerify   正则验证
 * 
 * @author Mirze <mirzeAdv@163.com>
 */
class FilterCheck
{
    /**
     * 请求参数安全过滤
     * @param  array  $post 需校验数据
     * @return [type]       [description]
     */
    function checkInput($post=[]) {
        if(empty($post) || !is_array($post)) return false;

        $filterStr = @"/and|or|exec|execute|insert|select|delete|update|alter|create|drop|count|\*|chr|char|asc|mid|substring|master|truncate|declare|xp_cmdshell|restore|backup|net +user|net +localgroup +administrators/i";

        foreach ($post as $key => $value) {
            if(preg_match($filterStr, $value, $matches)){
                return false;
                break;
            }
        }
        return true;
    }

    /**
     * 过滤HTML标签，防XSS注入等
     * @param  string  $str  过滤字符串
     * @param  integer $type 过滤类型
     * @return [type]        [description]
     * @author Mirze <2018-11-14>
     */
    function htmlTags($str='', $type=1)
    {
        $rule1 = "/<(\/?)(script|i?frame|style|html|body|title|link|meta|object|img|input|div|span|applet|embed||font|table|b|a|p|\?|\%)([^>]*?)>/isU";
        // 保留<a><p>
        $rule2 = "/<(\/?)(script|i?frame|style|html|body|title|link|meta|object|img|input|div|span|applet|embed|b|font|table|\?|\%)([^>]*?)>/isU";
        // 过滤没有">"闭合的标签
        $rule3 = "/<(\/?)(script|!--|i?frame|style|html|body|title|link|meta|object|img|input|div|span|applet|embed|b|font|table)/isU";
        $rule4 = "/(style\ +\=\ +\".*[^\"].*)\"/isU";

        switch ($type) {
            case 1:
                $retStr = preg_replace($rule1, '', $str);
                $retStr = htmlspecialchars($retStr);
                break;
            case 2: // 保留 <a><p>
                $retStr = preg_replace($rule2, '', $str);
                $retStr = preg_replace($rule3, '', $retStr);
                $retStr = preg_replace($rule4, '', $retStr);
                break;
            default: // 不过滤
                $retStr = $str;
                break;
        }
        return $retStr;
    }

    /**
     * 签名校验：请求参数令牌校权：MD5()
     * @param  array  $post      请求参数数组
     * @param  string $cert      请求校权验证口令
     * @param  string $delimiter 分隔符: 默认为空，可自定义如"&"
     * @param  string $secret    私钥
     * @return [type]            [description]
     */
    function checkSign($post=[], $cert='', $delimiter='', $secret='')
    {
        if(empty($post)) return false;

        ksort($post);
        $str = '';
        foreach ($post as $k => $v) {
            $str .= ($k.'='.$v . $delimiter);
        }
        if($delimiter != '') {
            $str = rtrim($str, $delimiter);        
        }
        if($secret != '') {
            $str .= trim($secret);        
        }
        $genCert = md5($str);
        // halt($genCert);
        return $cert == $genCert ? true : false;
    }

    /**
     * 正则验证：
     * @param  string $str  [description]
     * @param  string $type [description]
     * @return boolean
     */
    function regularVerify($str='', $type='') {
        if('' == $str) return false;

        $ruleArr=array(
            'require' => '/.+/',
            'email' => '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
            'url' => '/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/',
            'currency' => '/^\d+(\.\d+)?$/',
            'number' => '/^\d+$/',
            'zip' => '/^\d{6}$/',
            'integer' => '/^[-\+]?\d+$/',
            'double' => '/^[-\+]?\d+(\.\d+)?$/',
            'english' => '/^[A-Za-z]+$/',
            'qq' => '/^\d{5,11}$/',
            // 'mobile' => '/^1(3|4|5|6|7|8)\d{9}$/',
            'mobile' => '/^1[2-9]\d{9}$/',
            'birthday' => '/^(19|20)(\d){2}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[0-1])$/',
            'amount' => '/^([1-9]\d{0,9}|0)(\.\d{1,2})?$/', // 金额：最多保留2位小数
        );
        $rule = array_key_exists($type, $ruleArr) ? $ruleArr[$type] : '';

        if(preg_match($rule, $str)){
            return true;
        } else {
            return false;
        }
    }



}