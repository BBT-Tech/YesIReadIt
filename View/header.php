<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1,minimum-scale=1.0,maximum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="<?php echo _HTTP;?>css/materialize-icon.css">
    <link rel="stylesheet" href="<?php echo _HTTP;?>lib/materialize/dist/css/materialize.min.css">
    <script src="<?php echo _HTTP;?>lib/jquery/dist/jquery.min.js"></script>
    <script src="<?php echo _HTTP;?>lib/materialize/dist/js/materialize.min.js"></script>
    <script src="<?php echo _HTTP;?>lib/axios/dist/axios.min.js"></script>
    <script src="<?php echo _HTTP;?>lib/vue/dist/vue.min.js"></script>
    <script src="<?php echo _HTTP;?>lib/showdown/dist/showdown.min.js"></script>
    <script src="<?php echo _HTTP;?>lib/lodash/dist/lodash.min.js"></script>
    <script src="<?php echo _HTTP;?>js/common.js"></script>
    <link rel="stylesheet" href="<?php echo _HTTP;?>lib/github-markdown-css/github-markdown.css">
    <link rel="stylesheet" href="<?php echo _HTTP;?>css/style.css?20171015">
    <title><?php echo $title;?> - Yes I Read It</title>

</head>
<body>
<?php global $Config;?>
<div class="">
    <nav class="nav-extended">
        <div class="nav-wrapper light-blue">
            <a href="<?php echo _HTTP?>" class="brand-logo" style="margin-left: 20px;">通知中心</a>
            <a href="#" data-activates="mobile-demo" class="button-collapse"><i class="material-icons">menu</i></a>
            <ul class="right hide-on-med-and-down ">
                <?php $index=1;?>
                <li <?php echo $active == $index ? "class=\"active\"" : ' ';
                $index++; ?>><a href="<?php echo _HTTP;?>" class="">首页</a>
                </li>
                <li <?php echo $active == $index ? "class=\"active\"" : ' ';
                $index++; ?>><a href="<?php echo _HTTP;?>notice/add/" class="">创建通知</a>
                </li>
                <li <?php echo $active == $index ? "class=\"active\"" : ' ';
                $index++; ?>><a href="<?php echo _HTTP;?>user/viewMyCreate/" class="">我创建的通知</a>
                <li <?php echo $active == $index ? "class=\"active\"" : ' ';
                $index++; ?>><a href="<?php echo _HTTP;?>user/editInfo/" class="">修改个人信息</a>
                </li>
            </ul>
            <ul class="side-nav" id="mobile-demo">
                <?php $index=1;?>
                <li <?php echo $active == $index ? "class=\"active\"" : ' ';
                $index++; ?>><a href="<?php echo _HTTP;?>" class="">首页</a>
                </li>
                <li <?php echo $active == $index ? "class=\"active\"" : ' ';
                $index++; ?>><a href="<?php echo _HTTP;?>notice/add/" class="">创建通知</a>
                </li>
                <li <?php echo $active == $index ? "class=\"active\"" : ' ';
                $index++; ?>><a href="<?php echo _HTTP;?>user/viewMyCreate/" class="">我创建的通知</a>
                <li <?php echo $active == $index ? "class=\"active\"" : ' ';
                $index++; ?>><a href="<?php echo _HTTP;?>user/editInfo/" class="">修改个人信息</a>
                </li>
            </ul>
        </div>
        <?php
        if(isset($addon_header))
            include($addon_header);
        ?>
    </nav>
</div>



