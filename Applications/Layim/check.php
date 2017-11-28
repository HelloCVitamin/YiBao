<?php
/**
 * Created by PhpStorm.
 * User: Kilingzhang
 * Date: 17-11-28
 * Time: 上午2:28
 */
session_start();
$user_id = isset($_POST['uid']) ? $_POST['uid'] : "";
$user_name =  isset($_POST['uname']) ? $_POST['uname'] : "";
if ($user_id != "" && $user_name != "") {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $user_name;
    exit('{"code":0}');
} else {
    exit('{"code":-1}');
}