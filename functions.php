<?php

require_once 'config.php';


session_start();
function xiu_get_current_user(){
    if(empty($_SESSION['current_login_user'])){
        // 没有当前用户信息
        header('Location:/aliBaixiu/admin/login.php');
        exit();
    }
    return $_SESSION['current_login_user'];
}

/**
 *
 *
 */
function xiu_fetch_all($sql){
    $conn = mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);
    if(!$conn){
        exit('连接失败');
    }
    $query =mysqli_query($conn,$sql);
    if(!$query){
        return false;
    }
    while ($row = mysqli_fetch_assoc($query)){
        $result[] = $row;

    }
    mysqli_free_result($query);
    mysqli_close($conn);
    return $result;
}

/**
 * 获取单条数据
 *
 */
function xiu_fetch_one($sql){
    $res = xiu_fetch_all($sql);
    return isset($res[0])?$res[0]:null;
}

/**
 * 执行一个增删改操作
 */
function xiu_execute($sql){
    $conn = mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);
    if(!$conn){
        exit('连接失败');
    }
    $query =mysqli_query($conn,$sql);
    if(!$query){
        return false;
    }
    // 对于增删改类操作都是获取受影响行数
    $affected_rows = mysqli_affected_rows($conn);
    mysqli_close($conn);
    return $affected_rows;
}