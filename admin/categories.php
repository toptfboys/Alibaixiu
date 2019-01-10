<?php

require_once '../functions.php';
xiu_get_current_user();


function edit_category()
{
    // 只有当是编辑并点击保存时执行
    global $current_edit_category;
    $id = $current_edit_category['id'];
    $name = empty($_POST['name']) ? $current_edit_category['name'] : $_POST['name'];
    $current_edit_category['name'] = $name;
    $slug = empty($_POST['slug']) ? $current_edit_category['slug'] : $_POST['slug'];
    $current_edit_category['slug'] = $slug;
    $rows = xiu_execute("update categories set slug = '{$slug}',name = '{$name}' where id =  '{$id}' ");
    $GLOBALS['success'] = $rows > 0;
    $GLOBALS['message'] = $rows <= 0 ? '更新失败' : '更新成功';
}

function add_category()
{
    if (empty($_POST['name']) || empty($_POST['slug'])) {
        $GLOBALS['message'] = '请填写完整表单';
        $GLOBALS['success'] = false;
        return;
    }
    $name = $_POST['name'];
    $slug = $_POST['slug'];
    $rows = xiu_execute("insert into  categories values(null, '{$slug}', '{$name}');");
    $GLOBALS['success'] = $rows > 0;
    $GLOBALS['message'] = $rows <= 0 ? '添加失败' : '添加成功';
}

// 判断是编辑主线还是添加主线

if (empty($_GET['id'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 一旦表单提交 并且URL 没有ID就新增
        add_category();
    }
}else{
    $current_edit_category = xiu_fetch_one('select * from categories where id = '
        . $_GET['id']);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 一旦表单提交 并且URL 没有ID就新增
        edit_category() ;
    }
}

// 查询全部的分类数据
$categories = xiu_fetch_all('select * from categories;');


?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>Categories &laquo; Admin</title>
    <link rel="stylesheet" href="/aliBaixiu/static/assets/vendors/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="/aliBaixiu/static/assets/vendors/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="/aliBaixiu/static/assets/vendors/nprogress/nprogress.css">
    <link rel="stylesheet" href="/aliBaixiu/static/assets/css/admin.css">
    <script src="/aliBaixiu/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
<script>NProgress.start()</script>

<div class="main">
    <?php $current_page = 'categories' ?>
    <?php include "inc/navbar.php"; ?>
    <div class="container-fluid">
        <div class="page-title">
            <h1>分类目录</h1>
        </div>
        <!-- 有正确信息时展示 -->
        <?php if (isset($message)): ?>
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <strong>正确 ! </strong><?php echo $message; ?> !
                </div>
            <?php else: ?>
                <div class="alert alert-danger">
                    <strong>错误 ! </strong><?php echo $message; ?> !
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <?php if (isset($current_edit_category)): ?>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $current_edit_category['id']; ?>"
                          method="post">
                        <h2>编辑<<<?php echo $current_edit_category['name']; ?>>></h2>
                        <div class="form-group">
                            <label for="name">名称</label>
                            <input id="name" class="form-control" name="name" type="text" placeholder="分类名称"
                                   value="<?php echo $current_edit_category['name']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="slug">别名</label>
                            <input id="slug" class="form-control" name="slug" type="text" placeholder="slug"
                                   value="<?php echo $current_edit_category['slug']; ?>">
                            <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">保存</button>
                        </div>
                    </form>
                <?php else: ?>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <h2>添加新分类目录</h2>
                        <div class="form-group">
                            <label for="name">名称</label>
                            <input id="name" class="form-control" name="name" type="text" placeholder="分类名称">
                        </div>
                        <div class="form-group">
                            <label for="slug">别名</label>
                            <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
                            <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">添加</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
            <div class="col-md-8">
                <div class="page-action">
                    <!-- show when multiple checked -->
                    <a id="btn_delete" class="btn  btn-danger btn-sm" href="/aliBaixiu/admin/categories-delete.php"
                       style="display: none">批量删除</a>
                </div>
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th class="text-center" width="40"><input type="checkbox"></th>
                        <th>名称</th>
                        <th>Slug</th>
                        <th class="text-center" width="100">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($categories as $item): ?>
                        <tr>
                            <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id']; ?>"></td>
                            <td><?php echo $item['name'] ?></td>
                            <td><?php echo $item['slug'] ?></td>
                            <td class="text-center">
                                <a href="/aliBaixiu/admin/categories.php?id=<?php echo $item['id']; ?>"
                                   class="btn btn-info btn-xs">编辑</a>
                                <a href="/aliBaixiu/admin/categories-delete.php?id=<?php echo $item['id']; ?>"
                                   class="btn btn-danger btn-xs">删除</a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'inc/sidebai.php' ?>

<script src="/aliBaixiu/static/assets/vendors/jquery/jquery.js"></script>
<script src="/aliBaixiu/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
<script>
    // 在表格中的任何一个行被选中
    $(function ($) {
        var $tbodyCheckboxs = $("tbody input");
        var $deleteCheck = $("#btn_delete");
        // 定义一个被选中的数组
        var allCheckeds = [];
        $tbodyCheckboxs.on('change', function () {
            var id = $(this).data('id');
            if ($(this).prop('checked')) {
                allCheckeds.includes(id) || allCheckeds.push(id);
            } else {20
                allCheckeds.splice(allCheckeds.indexOf(id), 1);
            }
            allCheckeds.length ? $deleteCheck.fadeIn() : $deleteCheck.fadeOut();
            $deleteCheck.prop('search', '?id=' + allCheckeds);
        });

        // 文本框全选和全部选
        $('thead input').on('change',function () {
            // 1. 获取当前选中状态
            var checked = $(this).prop('checked');
            // 2. 设置给标题中的每一个
            $tbodyCheckboxs.prop('checked',checked).trigger('change');
        })

    })
</script>
</body>
</html>
