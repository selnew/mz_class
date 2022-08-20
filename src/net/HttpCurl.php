<?php
namespace mzclass\net;

// use custom\Out;
// use app\common\facade\FOut;
// use app\common\facade\MLog;

/**
 * 接口请求基类: Http Curl
 *
 * @author Mirze <mirzeAdv@163.com>
 *
 */
class HttpCurl {
    // 日志存放目录名称
    // const LOG_DIR_NAME = "http_log";

    /**
     * CURL接口数据
     * @param  string $url  访问的URL
     * @param  array  $post post数据(不填则为GET)
     * @param  array  $conf 设置CURL参数：
     *                          header          CURL头信息：array / string
     *                          timeout         请求超时时长：默认3s
     *                          cookie          提交的$cookies
     *                          returnCookie    是否返回$cookies
     * @return
     *     [0]  状态码：0 失败 1 成功
     *     [1]  返回数据：失败返回错误信息， 成功返回URL数据
     *
     *
     * 例:
     *     $conf = [
     *         'header' => ['Content-Type: application/json; charset=utf-8'],
     *         'timeout' => 3,
     *         'cookie' => '',
     *         'returnCookie' => 0,
     *     ];
     */
    static function getHttpData($url='', $post='', $conf=[]){
        // $urlHeaderInfo = get_headers($url, 1); //获取URLheader信息
        // dump($urlHeaderInfo);exit;

        if(empty($conf['header'])) {
            $headerArr = ['Content-Type:application/x-www-form-urlencoded; charset=UTF-8'];
        } else {
            $headerArr = is_array($conf['header']) ? $conf['header'] : [$conf['header']];
        }
        $timeout = empty($conf['timeout']) ? 3 : trim($conf['timeout']);
        $cookie = isset($conf['cookie']) ? $conf['cookie'] : '';
        $returnCookie = empty($conf['returnCookie']) ? 0 : trim($conf['returnCookie']);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        if($post) {
            $postData = is_array($post) ? http_build_query($post) : $post;

            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            // curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            //     // 'Content-Type: application/json; charset=utf-8',
            //     'Content-Type:application/x-www-form-urlencoded; charset=UTF-8',
            // ));
            // curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArr);
        }

        // 支持GET/POST传header内容
        if(!empty($headerArr)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArr);
        }

        if($cookie) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        curl_setopt($ch, CURLOPT_HEADER, $returnCookie); //输出header信息
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //不显示网页内容
        curl_setopt($ch, CURLOPT_ENCODING, ''); //允许执行gzip

        // ssl 证书：https
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $data = curl_exec($ch);
        // 返回码
        $ch_errno = curl_errno($ch);
        $ch_error = curl_error($ch);

        if ($ch_errno) {
            $msg = "[$ch_errno] " . $ch_error;
            return array(0, $msg);
        }
        curl_close($ch);

        if($returnCookie){
            list($header, $body) = explode("\r\n\r\n", $data, 2);
            preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
            $info['cookie']  = substr($matches[1][0], 1);
            $info['content'] = $body;
            return array(1, $info);
        }else{
            return array(1, $data);
        }
    }

    // /**
    //  * 分析CURL结果：错误记录日志
    //  * @param  array $curlRes [description]
    //  * @return array          [description]
    //  * @author Mirze <2019-04-24>
    //  */
    // function formatHttpData($curlRes=[]) {
    //     list($code, $respondData) = $curlRes;
    //     if($code != 1) {
    //         $msg = (is_string($respondData) && ($respondData != '')) ? trim($respondData) : '接口返回异常';
    //         $msg = "[CURL] ". $msg;
    //         $this->writeLog($msg, __FUNCTION__, SELF::LOG_DIR_NAME, 'notice');

    //         return FOut::arrayCode(400004);
    //     }

    //     // 响应是否为空
    //     if($respondData == '') {
    //         $msg = "[CURL] 接口CURL请求返回响应结果为空";
    //         $this->writeLog($msg, __FUNCTION__, SELF::LOG_DIR_NAME, 'notice');

    //         return FOut::arrayCode(400027);
    //     }
    //     return FOut::arrayCode(200000, $respondData);
    // }

    // /**
    //  * 写入日志
    //  * @param  string $msg     消息体
    //  * @param  string $flag    标识
    //  * @param  string $dirname 存放目录：datalog/
    //  * @param  string $level   日志级别：info、error
    //  * @return [type]          [description]
    //  */
    // public function writeLog($msg='', $flag='', $dirname='http_log', $level='log') {
    //     // write_log($msg, $flag, $dirname, $level);
    //     $data['msg'] = $msg;
    //     $data['flag'] = $flag;
    //     MLog::http($data, $level);
    // }

}