<?php
namespace mzclass\library;

/**
 * 相关函数使用
 *
 * @Author Mirze
 */
class Func
{
    // 检测PHP版本5.4及以上
    function check_phpver_5_4() {
        if(version_compare(PHP_VERSION,'5.4','<')) {
            return false;
        }
        return true;
    }
    // 解决JSON中文UNICODE转码问题
    function json_encode_zh($data) {
        // $phpver = check_phpver_5_4(); // 检测PHP版本5.4及以上
        $check = version_compare(PHP_VERSION,'5.4','<');
        $phpver = $check ? false : true;
        return $phpver ? json_encode($data,JSON_UNESCAPED_UNICODE) : json_encode($data);
    }

    // 判断数据不是JSON格式
    function not_json($str) {
        return is_null(json_decode($str));
    }

    // PHP >5.3
    function is_json($str) {
        json_decode($str);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    //随机参数
    function uri_rand(){
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec.(float)$sec);
    }

    /**
     * 正则验证：
     * @param  string $str  [description]
     * @param  string $type [description]
     * @return boolean
     */
    function regular_verify($str='', $type='') {
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

    /**
     * 全角字符截取函数
     * @param string str 被截取的字符串
     * @param number len 截取长度
     * @param boolen haspoint 是否显示省略号
     * @param number start 开始位置
     * @param string code 字符编码
     *
     * @return string
     */
    function cut_str($str,$len=1,$haspoint=false,$start=0,$code='UTF-8') {
        //替换特殊字符
        $str = @str_replace("&","＆",$str);
        $str = @str_replace(">","＞",$str);
        $str = @str_replace("<","＜",$str);
        $str = @str_replace("\'","’",$str);
        $str = @str_replace("\"","”",$str);
        $tmpPoint = "";
        if($haspoint){
            $tmpPoint = "...";
        }
        if($code == 'UTF-8')
        {
            $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
            preg_match_all($pa, $str, $t_str);
            if(count($t_str[0]) - $start > $len) return join('', array_slice($t_str[0], $start, $len)).$tmpPoint;
            return join('', array_slice($t_str[0], $start, $len));
        }
        else
        {
            $start = $start*2;
            $len = $len*2;
            $strlen = strlen($str);
            $tmpstr = '';
            for($i=0; $i< $strlen; $i++)
            {
                if($i>=$start && $i< ($start+$len))
                {
                    if(ord(substr($str, $i, 1))>129)
                    {
                        $tmpstr.= substr($str, $i, 2);
                    }
                    else
                    {
                        $tmpstr.= substr($str, $i, 1);
                    }
                }
                if(ord(substr($str, $i, 1))>129) $i++;
            }
            if(strlen($tmpstr)< $strlen ) $tmpstr.= "";
            return $tmpstr.$tmpPoint;
        }
    }

    /**
     * 字符串截取，支持中文和其他编码
     * @static
     * @access public
     * @param string $str 需要转换的字符串
     * @param string $start 开始位置
     * @param string $length 截取长度
     * @param string $charset 编码格式
     * @param string $suffix 截断显示字符
     * @return string
     */
    function msubstr($str, $start, $length, $charset="utf-8", $suffix=true) {
        if(function_exists("mb_substr"))
            $slice = mb_substr($str, $start, $length, $charset);
        elseif(function_exists('iconv_substr')) {
            $slice = iconv_substr($str,$start,$length,$charset);
            if(false === $slice) {
                $slice = '';
            }
        }else{
            $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("",array_slice($match[0], $start, $length));
        }
        return $suffix ? $slice.'...' : $slice;
    }

    /**
     * 分析URL获取请求GET参数
     * @param  string $uri 分析URL
     * @return array
     */
    function url_replace_build($uri='' , $replaceArr=array()) {
        if(empty($uri) || !is_string($uri)) return '';
        if(empty($replaceArr) || !is_array($replaceArr)) return $uri;

        $uriArr = parse_url($uri);
        $query = empty($uriArr['query']) ? '' : trim($uriArr['query']);
        if(empty($query)) return $uri;

        // 解析参数
        $queryParts = explode('&', $query);
        $params = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }
        // 替换URL参数
        $query = array_replace($params, $replaceArr);
        $uriArr['query'] = $query;

        // 合并新URL
        $scheme = isset($uriArr['scheme']) ? trim($uriArr['scheme']) : 'http';
        $user = isset($uriArr['user']) ? trim($uriArr['user']) : '';
        $pass = isset($uriArr['pass']) ? trim($uriArr['pass']) : '';
        $host = isset($uriArr['host']) ? trim($uriArr['host']) : '';
        $path = isset($uriArr['path']) ? trim($uriArr['path']) : '';
        $fragment = isset($uriArr['fragment']) ? trim($uriArr['fragment']) : '';

        $buildUrl = $scheme . "://";
        if($host !='' || $pass !='') {
            $buildUrl .= $user.":".$pass."@".$host. $path;
        } else {
            $buildUrl .= $host. $path;
        }
        $buildUrl .= "?".http_build_query($query);
        if($fragment !='') {
            $buildUrl .= "#" . $fragment;
        }
        // return $uriArr;
        return $buildUrl;
    }

    /**
     * 无限级分类格式化：
     *
     * // 1 => array('id' => 1, 'pid' => 0, 'name' => '江西省'),
     *
     */
    function gen_tree($items, $pk='id') {
        foreach ($items as $item) {
            $items[$item['pid']]['son'][$item[$pk]] = &$items[$item[$pk]];
        }
        return isset($items[0]['son']) ? $items[0]['son'] : array();
    }

    /**
     * 把返回的数据集转换成Tree
     * @param array $list 要转换的数据集
     * @param string $pid parent标记字段
     * @param string $level level标记字段
     * @return array
     */
    function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0) {
        // 创建Tree
        $tree = array();
        if(is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] =& $list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId =  $data[$pid];
                if ($root == $parentId) {
                    $tree[] =& $list[$key];
                }else{
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }
            }
        }
        return $tree;
    }

    /**
     * 将list_to_tree的树还原成列表
     * @param  array $tree  原来的树
     * @param  string $child 孩子节点的键
     * @param  string $order 排序显示的键，一般是主键 升序排列
     * @param  array  $list  过渡用的中间数组，
     * @return array        返回排过序的列表数组
     */
    function tree_to_list($tree, $child = '_child', $order='id', &$list = array()){
        if(is_array($tree)) {
            foreach ($tree as $key => $value) {
                $reffer = $value;
                if(isset($reffer[$child])){
                    unset($reffer[$child]);
                    tree_to_list($value[$child], $child, $order, $list);
                }
                $list[] = $reffer;
            }
            $list = list_sort_by($list, $order, $sortby='asc');
        }
        return $list;
    }

    /**
     * 多维数组按某个字段值排序
     * @param  array $multi_array  多维数组
     * @param  string $sort_key    排序字段
     * @param  [type] $sort        排序方式：SORT_DESC/SORT_ASC
     * @return [type]              [description]
     */
    function multi_array_sort($multi_array,$sort_key,$sort=SORT_DESC){
        if(empty($multi_array) || !is_array($multi_array)) {
            return $multi_array;
        }
        foreach ($multi_array as $row_array){
            if(is_array($row_array)){
                $key_array[] = $row_array[$sort_key];
            }else{
                return $multi_array;
            }
        }
        array_multisort($key_array,$sort,$multi_array);
        return $multi_array;
    }

    /**
     * 将list_to_tree的树还原成列表
     * @param  array $tree  原来的树
     * @return array 返回列表数组
     */
    function tree_to_array($tree){
        static $arr = [];
        foreach($tree as $val){
            $arr[] = ['id'=>$val['id'],'name'=>$val['name'],'pid'=>$val['pid']];
            if(isset($val['_child']) && !empty($val['_child'])){
                tree_to_array($val['_child']);
            }
        }
        return $arr;
    }
    /**
     * 高效率计算文件行数:
     *     最快的方法是多行统计，每次读取N个字节，然后再统计行数，这样比逐行效率高多了。
     * @param  [type] $file [description]
     * @return [type]       [description]
     */
    function file_line_num($file){
        $fp=fopen($file, "r");
        $i=0;
        while(!feof($fp)) {
            //每次读取2M
            if($data = fread($fp,1024*1024*2)){
                //计算读取到的行数
                $num = substr_count($data,"\n");
                $i+=$num;
            }
        }
        fclose($fp);
        return $i;
    }

    /**
     * 删除目录及目录下所有文件
     * @param  [type] $dirName [description]
     * @return [type]          [description]
     */
    function rm_dir($dirName)
    {
        if(! is_dir($dirName)) {
            return true;
        }
        $handle = @opendir($dirName);
        while(($file = @readdir($handle)) !== false){
            if($file != '.' && $file != '..'){
                $dir = $dirName . '/' . $file;
                is_dir($dir) ? rm_dir($dir) : @unlink($dir);
            }
        }
        closedir($handle);

        return rmdir($dirName) ;
    }

    /**
     * 仅删除目录下所有文件
     * @param  [type] $dir       目录路径
     * @param  string $file_type 指定文件类型
     * @return [type]            [description]
     */
    function del_dir($dir,$file_type='') {
        if(is_dir($dir)){
            $files = @scandir($dir);
            //打开目录 //列出目录中的所有文件并去掉 . 和 ..
            foreach($files as $filename){
                if($filename != '.' && $filename != '..'){
                    if(!is_dir($dir.'/'.$filename)){
                        if(empty($file_type)){
                            @unlink($dir.'/'.$filename);
                        }else{
                            if(is_array($file_type)){
                                //正则匹配指定文件
                                if(preg_match($file_type[0],$filename)){
                                    @unlink($dir.'/'.$filename);
                                }
                            }else{
                                //指定包含某些字符串的文件
                                if(false!=stristr($filename,$file_type)){
                                    @unlink($dir.'/'.$filename);
                                }
                            }
                        }
                    }else{
                        del_dir($dir.'/'.$filename);

                        @rmdir($dir.'/'.$filename);
                    }
                }
            }
            return true;
        }else{
            if(file_exists($dir)) @unlink($dir);
        }
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

    /**
     * 签名校验：请求参数令牌校权：MD5()
     * @param  array  $post      请求参数数组
     * @param  string $cert      请求校权验证口令
     * @param  string $delimiter 分隔符: 默认为空，可自定义如"&"
     * @param  string $secret    私钥
     * @return [type]            [description]
     * @author Mirze <2018-12-10>
     */
    function check_sign($post=[], $cert='', $delimiter='', $secret='')
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
     * 时间戳校验：允许前后3分钟内时间差
     * @param  integer $timestamp 校验时间戳：客户端时间戳
     * @param  integer $minute    误差前后时间：默认前后3分钟
     * @return [type]             [description]
     * @author Mirze <2019-01-04>
     */
    function check_timestamp($timestamp=0, $minute=3)
    {
        // JAVA时间戳13位，PHP时间戳10位
        $len = strlen($timestamp);
        $timestamp = ($len > 10) ? substr($timestamp,0, 10) : $timestamp;

        $time = time();
        $diff = 60 * $minute;

        $minTime = $time - $diff;
        $maxTime = $time + $diff;

        return ($timestamp > $minTime && $timestamp < $maxTime) ? true : false;
    }

    /**
     * 请求参数安全过滤
     * @param  array  $post 需校验数据
     * @return [type]       [description]
     * @author Mirze <2018-11-14>
     */
    function safe_input($post=[]) {
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
    function filter_tags($str='', $type=1)
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
     * 写入日志
     * @param  string $msg     消息体内容
     * @param  string $flag    标识消息体
     * @param  string $dirname 存放目录：runtime/datalog/*
     * @param  string $level   消息级别(低到高)： debug, info, notice, warning, error, critical, alert, emergency
     *                                     标准：'log', 'error', 'info', 'sql', 'notice', 'alert', 'debug'
     * @return [type]          [description]
     */
    function write_log($msg='', $flag='', $dirname='', $level='log') {
        $dirname = ($dirname == '') ? 'common' : trim($dirname);
        $path = env('runtime_path') . "/datalog/{$dirname}/";

        $conf = [
            'type'  =>  'File',
            'path' => $path, // 日志路径
            'apart_level' =>  [$level], // 独立日志
            'file_size' => 1024*1024*5, //单个日志文件的大小限制，超过后会自动记录到第二个文件
            'time_format'   =>'Y-m-d H:i:s' // 日志的时间格式，默认是` c `
        ];
        Log::init($conf);

        if(is_array($msg) || is_object($msg)) {
            $txt = json_encode($msg);
        } else {
            $txt = $msg;
        }
        $logMsg = ($flag == '') ? $txt : "[$flag] ".$txt;
        Log::write($logMsg, $level);
    }

    /**
     * 获取前一页地址中设置的返回url
     * @return array
     */
    function get_back_url()
    {
        if (isset($_SERVER["HTTP_REFERER"]) && !empty($_SERVER["HTTP_REFERER"])) {
            $queryStr = explode('?', $_SERVER["HTTP_REFERER"]);
            if (count($queryStr) == 2) {
                parse_str($queryStr[1], $queryArr);
                if (isset($queryArr['back_url']) && !empty($queryArr['back_url'])) {
                    $backUrl = explode("&", urldecode($queryArr['back_url']));
                    foreach ($backUrl as $k => $v) {
                        $v = explode("=", $v);
                        if (isset($v[1]) && !empty($v[1])) {
                            $backArr[$v[0]] = $v[1];
                        }
                    }
                }
            }
        }
        return $backArr ?? [];
    }

    // 百分比值标准化
    function percent_format($val=100, $decimals=1)
    {
        if($val > 100) return 100;
        if($val < 0) return 0;
        return number_format($val, $decimals, ".","");
    }

    // 手机号中间四位替换为星号****
    function mask_phone($phone, $str='****') {
        return preg_replace("/(\d{3})\d{4}(\d{4})/", "$1{$str}$2", $phone);
    }

}