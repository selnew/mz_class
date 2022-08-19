<?php
namespace mzclass\library;

/**
 * 格式化数据输出
 * @author Mirze <mirzeAdv@163.com>
 *
 */
class Out
{
    //对象转数组,使用get_object_vars返回对象属性组成的数组
    static function object_to_array($obj){
        $arr = is_object($obj) ? get_object_vars($obj) : $obj;
        if(is_array($arr)){
            return array_map(__FUNCTION__, $arr);
        }else{
            return $arr;
        }
    }

    //数组转对象
    static function array_to_object($arr){
        if(is_array($arr)){
            return (object) array_map(__FUNCTION__, $arr);
        }else{
            return $arr;
        }
    }

    // 检测PHP版本5.4及以上
    static function check_phpver_5_4() {
        if(version_compare(PHP_VERSION,'5.4','<')) {
            return false;
        }
        return true;
    }

    // 解决JSON中文UNICODE转码问题
    static function json_encode_zh($data) {
        $phpver = SELF::check_phpver_5_4(); // 检测PHP版本5.4及以上
        return $phpver ? json_encode($data,JSON_UNESCAPED_UNICODE) : json_encode($data);
    }

    // JSON不自动转义"/"
    static function json_encode_all($data) {
        $phpver = SELF::check_phpver_5_4(); // 检测PHP版本5.4及以上
        // JSON_UNESCAPED_UNICODE(256) + JSON_UNESCAPED_SLASHES(64) = 320
        return $phpver ? json_encode($data, 320) : json_encode($data);
    }

    /**
     * 输出标准格式化
     * @param  integer $code    提示码：1 正确 0 错误
     * @param  string  $msg     提示语话术
     * @param  array   $data    返回数据
     * @param  integer $type    输出类型：1 数组 2 json字符串 3 callback回调 4 数组强制转对象JSON输出
     * @return [type]           [description]
     */
    static function formatOut($code = 0, $msg = '', $data = array(), $type=1) {
        $code = is_int($code) ? (string) $code : $code; // 转换为字符串
        $format = ['code'=>$code, 'msg'=>$msg, 'data'=>$data];
        switch ($type) {
            case 1: // return array
                return $format;
                break;
            case 2: // echo json
                echo SELF::json_encode_zh($format);
                break;
            case 3: // echo json callback
                echo 'callback(' . SELF::json_encode_zh($format) .')';
                break;
            case 4: // 数组强制转对象，JSON输出
                $format = SELF::array_to_object($format); // 数组强制转换对象
                echo SELF::json_encode_zh($format);
                break;
            case 5: // echo json 不转义
                echo SELF::json_encode_all($format);
                break;
            default:
                return $format;
                break;
        }
        exit();
    }

    /**
     * 输出标准格式化
     * @param  integer $code    提示码：1 正确 0 错误
     * @param  string  $msg     提示语话术
     * @param  array   $data    返回数据
     * @param  integer $type    输出类型：1 数组 2 json字符串 3 callback回调 4 数组强制转对象JSON输出
     * @return [type]           [description]
     */
    protected static function msg($code, $msg='', $data=[], $type=2) {
        // return format_out($code, $msg, $data, $type);
        return SELF::formatOut($code, $msg, $data, $type);
    }

    // 编码输出
    protected static function code($code, $data=[], $type=2) {
        // 获取状态码信息: config/codemsg.php
        $codemsg = config("codemsg");
        $msg = isset($codemsg[$code]) ? $codemsg[$code] : '未知操作';

        return self::msg($code, $msg, $data, $type);
    }

    // 返回：数组
    public static function arrayMsg($code, $msg='', $data=[]) {
        return SELF::msg($code,$msg,$data,1);
    }
    // 返回：数组
    public static function arrayCode($code, $data=[]) {
        return SELF::code($code, $data,1);
    }

    // 返回：JSON
    public static function jsonMsg($code, $msg='', $data=[]) {
        return SELF::msg($code,$msg,$data,2);
    }
    // 返回：JSON
    public static function jsonCode($code, $data=[]) {
        return SELF::code($code, $data,2);
    }

    // 返回：JSON OBJECT
    public static function jsonObjMsg($code, $msg='', $data=[]) {
        return SELF::msg($code,$msg,$data,4);
    }
    // 返回：JSON OBJECT
    public static function jsonObjCode($code, $data=[]) {
        return SELF::code($code, $data,4);
    }

    // 返回：callback 回调
    public static function callMsg($code, $msg='', $data=[]) {
        return SELF::msg($code,$msg,$data,3);
    }
    // 返回：callback 回调
    public static function callCode($code, $data=[]) {
        return SELF::code($code, $data,3);
    }

    // 中文且不转义
    public static function jsonMsgAll($code, $msg='', $data=[]) {
        return SELF::msg($code,$msg,$data,5);
    }



}