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

        $ruleArr = array(
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

    /**
     * 输出内容正则匹配校验
     * 密码设计参考：https://vimsky.com/article/3603.html
     *
     * @param string $str       比较内容
     * @param string $type      匹配类型规则KEY
     * @param integer $minLen   最小长度：0 不限
     * @param integer $maxLen   最大长度：0 不限
     * @return void
     * @Author Mirze
     * @DateTime 2024-03-19
     */
    function regularInput($str='', $type='', $minLen=5, $maxLen=30) {
        if('' == $str) return false;

        // 规则前部配置
        $ruleArr = array(
            // 字母和数字_
            '1' => '/^(?=.*\w)',
            // 必须包含数字、字母
            '2' => '/^(?=.*\d)(?=.*[a-zA-Z])',
            // 必须包含数字、字母和符号
            '3' => '/^(?=.*[0-9])(?=.*[a-zA-Z])(?=.*[!@$%^&*])',
            // 至少一个大写字母，一个小写字母，一个数字和一个特殊字符
            '4' => '/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[!@$%^&*])',
            // 包含至少一个小写字母、一个大写字母和一个数字。排除了所有的非字母数字字符，并忽略了大小写
            '5' => '/^(?!.[\W_])(?=.[a-z])(?=.[A-Z])(?=.\d)',
            // 必须含有大写字母、小写字母和数字
            '6' => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*d)',
            // 匹配汉字: /^[u4e00-u9fa5]+$/
            '7' => '/^(?=.*[u4e00-u9fa5])',
        );
        // 规则前部
        $rule = array_key_exists($type, $ruleArr) ? $ruleArr[$type] : '';
        if($rule == '') return false;

        // 规则长度
        if($minLen > 0) {
            if($maxLen > 0) {
                $rule .= '.{$minLen, $maxLen}';
            } else {
                $rule .= '.{$minLen, }';
            }
        } else {
            if($maxLen > 0) {
                $rule .= '.{, $maxLen}';
            }
        }
        // 规则尾部
        $rule .= '$/';

        if(preg_match($rule, $str)){
            return true;
        } else {
            return false;
        }
    }



}