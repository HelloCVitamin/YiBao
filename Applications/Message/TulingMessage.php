<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17-11-30
 * Time: 上午11:06
 */

namespace TulingMessage;

class TulingMessage
{
    const   TULINGAPIURL = 'http://www.tuling123.com/openapi/api';

    private $key;
    private $secret;

    public function __construct($key, $secret = '')
    {
        $this->key = $key;
        $this->secret = $secret;
    }


    /**
     * @param $userId
     * @param $info
     * @param string $loc
     * @return mixed
     */
    public function onMessage($userId, $info, $loc = '')
    {
        $param = array(
            'key' => $this->key,
            'info' => $info,
            "userid" => $userId
        );
        $loc != '' ? $param['loc'] = $loc : $param;
        $msg = self::curl($param);
        return $msg;
    }


    //返回true，即是，否则不是
    public static function isJson($str)
    {
        return !is_null(json_decode($str));
    }

    /**
     * 创建http header参数
     * @param array $data
     * @return array
     */
    private function createHttpHeader()
    {
        $timeStamp = time();
        return array(
            'RC-Timestamp:' . $timeStamp
        );
    }


    /**
     * 重写实现 http_build_query 提交实现(同名key)key=val1&key=val2
     * @param array $formData 数据数组
     * @param string $numericPrefix 数字索引时附加的Key前缀
     * @param string $argSeparator 参数分隔符(默认为&)
     * @param string $prefixKey Key 数组参数，实现同名方式调用接口
     * @return string
     */
    private function build_query($formData, $numericPrefix = '', $argSeparator = '&', $prefixKey = '')
    {
        $str = '';
        foreach ($formData as $key => $val) {
            if (!is_array($val)) {
                $str .= $argSeparator;
                if ($prefixKey === '') {
                    if (is_int($key)) {
                        $str .= $numericPrefix;
                    }
                    $str .= urlencode($key) . '=' . urlencode($val);
                } else {
                    $str .= urlencode($prefixKey) . '=' . urlencode($val);
                }
            } else {
                if ($prefixKey == '') {
                    $prefixKey .= $key;
                }
                if (isset($val[0]) && is_array($val[0])) {
                    $arr = array();
                    $arr[$key] = $val[0];
                    $str .= $argSeparator . http_build_query($arr);
                } else {
                    $str .= $argSeparator . $this->build_query($val, $numericPrefix, $argSeparator, $prefixKey);
                }
                $prefixKey = '';
            }
        }
        return substr($str, strlen($argSeparator));
    }

    /**
     * 发起 server 请求
     * @param $action
     * @param $params
     * @param $httpHeader
     * @return mixed
     */
    private function curl($params, $contentType = 'json', $httpMethod = 'POST')
    {
        $action = self::TULINGAPIURL;
        $httpHeader = $this->createHttpHeader();
        $ch = curl_init();
        if ($httpMethod == 'POST' && $contentType == 'urlencoded') {
            $httpHeader[] = 'Content-Type:application/x-www-form-urlencoded';
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->build_query($params));
        }
        if ($httpMethod == 'POST' && $contentType == 'json') {
            $httpHeader[] = 'Content-Type:Application/json';
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }
        if ($httpMethod == 'GET' && $contentType == 'urlencoded') {
            $action .= strpos($action, '?') === false ? '?' : '&';
            $action .= $this->build_query($params);
        }
        curl_setopt($ch, CURLOPT_URL, $action);
        curl_setopt($ch, CURLOPT_POST, $httpMethod == 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //处理http证书问题
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $ret = curl_exec($ch);
        if (false === $ret) {
            $ret = curl_errno($ch);
        }
        curl_close($ch);
        return $ret;
    }

}