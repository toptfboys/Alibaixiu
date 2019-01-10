<?php

require_once "../functions.php";
xiu_get_current_user();

// 筛选文章类型
$where = '1 = 1';
$search = '';
if(isset($_GET['category']) && $_GET['category'] !== 'all'){
    $where  .=  ' and posts.category_id = ' . $_GET['category'];
    $search .= '&category='. $_GET['category'];
}

// 筛选文章发布状态
if(isset($_GET['status']) && $_GET['status'] !== 'all'){
    $where  .=  "  and posts.status = '{$_GET['status']}' ";
    $search .= '&status='. $_GET['status'];
}

// 处理分页参数

$size = 20;

$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];

if($page < 1 ){
    header("Location:/aliBaixiu/admin/posts.php?page=1".$search);
}


// 求出最大页码以及总页数
$total_count = (int)xiu_fetch_one("select count(1) as num from posts inner join
categories  on posts.category_id = categories.id
inner join users on posts.user_id = users.id
where {$where};")['num'];
$total_page = (int)ceil($total_count/$size);
$offset = ($page - 1)*$size;

if($page > $total_page ){
    header("Location:/aliBaixiu/admin/posts.php?page=".$total_page .$search);
}



// 获取全部数据
// 获取全部文章数据并分页显示


$posts = xiu_fetch_all("select 
posts.id,
posts.title,
users.nickname as users_name,
categories.name as categories_name,
posts.created,
posts.status 
from posts 
inner join
categories  on posts.category_id = categories.id
inner join users on posts.user_id = users.id
where {$where}  
order by posts.created asc
limit {$offset},{$size};");
// 查询全部的分类数据
$categories = xiu_fetch_all('select * from categories;');


$visible = 5;
$begin = $page - ($visible-1)/2; //开始页码
$end = $begin + $visible -1; //结束页码

//判断页码不合理范围
$begin = $begin < 1 ? 1:$begin; //确保 begin不会小于1
$end = $begin + $visible -1;
$end = $end > $total_page ? $total_page :$end;
$begin = $end -$visible +1;
$begin = $begin < 1 ? 1:$begin; //确保 begin不会小于1

function convert_status($status){
    $dict = array( 'published'=>'已发布',
        'drafted' => '草稿',
        'trashed' => '回收站');
    return isset($dict[$status])? $dict[$status] : '未知';
}
function convert_date($created){
    $timestamp = strtotime($created);
    return date('Y年m月d日<b\r>H:i:s',$timestamp);
}


?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>Posts &laquo; Admin</title>
    <link rel="stylesheet" href="/aliBaixiu/static/assets/vendors/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="/aliBaixiu/static/assets/vendors/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="/aliBaixiu/static/assets/vendors/nprogress/nprogress.css">
    <link rel="stylesheet" href="/aliBaixiu/static/assets/css/admin.css">
    <script src="/aliBaixiu/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
<script>NProgress.start()</script>

<div class="main">
    <?php $current_page = 'posts'?>
    <?php include "inc/navbar.php"; ?>
    <div class="container-fluid">
        <div class="page-title">
            <h1>所有文章</h1>
            <a href="post-add.php" class="btn btn-primary btn-xs">写文章</a>
        </div>
        <!-- 有错误信息时展示 -->
        <!-- <div class="alert alert-danger">
          <strong>错误！</strong>发生XXX错误
        </div> -->
        <div class="page-action">
            <!-- show when multiple checked -->
            <a id = "btn_delete" class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
            <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
                <select name="category" class="form-control input-sm">
                    <option value="all">所有分类</option>
                    <?php foreach ($categories as $item):?>
                        <option value="<?php echo $item['id'];?>"
                            <?php echo isset($_GET['category']) && $_GET['category'] === $item['id'] ? 'selected':''?>><?php echo $item['name'];?></option>
                    <?php endforeach;?>
                </select>
                <select name="status" class="form-control input-sm">
                    <option value="all">所有状态</option>
                    <option value="drafted"
                        <?php echo isset($_GET['status']) && $_GET['status'] === 'drafted' ? 'selected':''?>>草稿</option>
                    <option value="published"
                        <?php echo isset($_GET['status']) && $_GET['status'] === 'published' ? 'selected':''?>> 已发布 </option>
                    <option value="trashed"
                        <?php echo isset($_GET['status']) && $_GET['status'] === 'trashed' ? 'selected':''?>>回收站</option>
                </select>
                <button class="btn btn-default btn-sm">筛选</button>
            </form>
            <ul class="pagination pagination-sm pull-right">
                <li><a href="#">上一页</a></li>
                <?php for ($i = $begin;$i <= $end; $i++):?>
                    <li class="<?php echo $i===$page? 'active':'';?>"><a href="?page=<?php echo $i . $search;?>"><?php echo $i;?></a></li>
                <?php endfor;?>
                <li><a href="#">下一页</a></li>
            </ul>
        </div>
        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>标题</th>
                <th>作者</th>
                <th>分类</th>
                <th class="text-center">发表时间</th>
                <th class="text-center">状态</th>
                <th class="text-center" width="100">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($posts as $items):?>
                <tr>
                    <td class="text-center"><input type="checkbox"></td>
                    <td><?php echo $items['title'];?></td>
<!--                    <td>--><?php //echo get_user($items['user_id']);?><!--</td>-->
<!--                    <td>--><?php //echo get_category($items['category_id']);?><!--</td>-->
                    <td><?php echo  $items['users_name'] ;?></td>
                    <td><?php echo  $items['categories_name'] ;?></td>
                    <td class="text-center"><?php echo convert_date($items['created']);?></td>
                    <td class="text-center"><?php echo convert_status($items['status']);?></td>
                    <td class="text-center">
                        <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
                        <a href="/aliBaixiu/admin/posts-delete.php?id=<?php echo $items['id'];?>" class="btn btn-danger btn-xs">删除</a>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'inc/sidebai.php' ?>

<script src="/aliBaixiu/static/assets/vendors/jquery/jquery.js"></script>
<script src="/aliBaixiu/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
<script>
    $(function ($) {
        var $tbodyCheckboxs = $("tbody input");
        var $deleteCheck = $("#btn_delete");
        var allCheckeds = [];
        $tbodyCheckboxs.on('change',function () {
            var id = $(this).data('id');
            if($(this).prop('checked')){
                allCheckeds.push(id);
            }else{
                allCheckeds.splice(allCheckeds.indexOf(id),1);
            }
            allCheckeds.length ? $deleteCheck.fadeIn():$deleteCheck.fadeOut();
        })
    })
</script>
</body>
</html>
