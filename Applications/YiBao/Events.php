<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */

//declare(ticks=1);
include dirname(__DIR__). "/Config/config.php";
include dirname(__DIR__). "SendRequest.php";
use \GatewayWorker\Lib\Gateway;
/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{

    /**
     * 新建一个类的静态成员，用来保存数据库实例
     */
    public static $db = null;
    public static $sendRequest = null;

    /**
     * 进程启动后初始化数据库连接
     */
    public static function onWorkerStart($worker)
    {
        self::$db = new Workerman\MySQL\Connection(DB_HOST, DB_PORT, DB_USER, DB_PWD, DB_NAME);
        self::$sendRequest = new SendRequest();
    }


    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     *
     * @param int $client_id 连接id
     */
    public static function onConnect($client_id)
    {
        //TODO Bind Sid to Client_id
        // 向当前client_id发送数据 
        Gateway::sendToClient($client_id, "{\"msg\":\"You Connect Successfuly ... \"}");
    }

    /**
     * 当客户端发来消息时触发
     * @param int $client_id 连接id
     * @param mixed $message 具体消息
     */
    public static function onMessage($client_id, $message)
    {
        //TODO
        var_dump("onMessage:->" . $message);
        if (self::isJson($message)) {
            $message = json_decode($message, true);
            if (array_key_exists('type', $message)) {
                $type = $message['type'];
                switch ($type) {
                    case "ping":
                        Gateway::sendToClient($client_id, "{\"msg\":\"You Ping Successfuly ... \"}");
                        break;
                    case "kefu":
                        $msg = "";
                        $param = array(
                            'key' => TULING_API_KEY,
                            'info' => $message['data']['mine']['content'],
                            "userid" => $message['data']['mine']['id']
                        );
                        $msg = self::$sendRequest->curl("", $param);
                        $msg = json_decode($msg,true);
                        if($msg['code'] == 100000){
                            $msg = $msg['text'];
                        }else{

                        }
                        $response['emit'] = 'chatMessage';
                        $response['data']['username'] = $message['data']['to']['name'];
                        $response['data']['avatar'] = $message['data']['to']['avatar'];
                        $response['data']['id'] = $message['data']['to']['id'];
                        $response['data']['type'] = $message['data']['to']['type'];
                        $response['data']['content'] = $msg;
                        $response['data']['cid'] = 0;
                        $response['data']['mine'] = false;
                        $response['data']['fromid'] = $message['data']['to']['id'];
                        $response['data']['timestamp'] = time() * 1000;
                        $response = json_encode($response, JSON_UNESCAPED_UNICODE);
                        Gateway::sendToClient($client_id, $response);
                        break;
                }
            }

        } else {
            Gateway::sendToClient($client_id, "{\"msg\":$message}");
        }

    }

    //返回true，即是，否则不是
    public static function isJson($str)
    {
        return !is_null(json_decode($str));
    }

    /**
     * 当用户断开连接时触发
     * @param int $client_id 连接id
     */
    public static function onClose($client_id)
    {
        // 向所有人发送

        Gateway::sendToClient($client_id, "{\"msg\":\"You Logout Successfuly ... \"}");

    }
}
