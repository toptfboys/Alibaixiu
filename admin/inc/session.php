<?php
/**
 * 校验属于当前用户访问ID
 */
session_start();
if(empty($_SESSION['current_login_user'])){
    // 没有当前用户信息
    header('Location:/aliBaixiu/admin/login.php');
}

?>