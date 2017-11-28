<?php
session_start();
if(!isset($_SESSION["user_id"]) || $_SESSION["user_id"] == ""){
    echo "<script>window.parent.location.href='login.php';</script>";
}else{
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'];
}
