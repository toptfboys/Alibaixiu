<?php
/**
 * 接受客户端ID删除对应数据
 *
 */

require_once '../functions.php';
if (empty($_GET['id'])){
    exit("缺少必要参数");
}

$id = $_GET['id'];

$rows = xiu_execute('delete from categories where id in ('. $id .');');

header('Location:/aliBaixiu/admin/categories.php');