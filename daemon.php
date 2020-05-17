<?php
// 判断运行模式
if(PHP_SAPI != 'cli') {
    header("HTTP/1.1 404 Not Found");
    header("Status: 404 Not Found");
    exit;
}
// 查找共用库地址
if (!@include(str_replace('\\','/',dirname(__FILE__)).'/global.php')) exit('global isn\'t exists!');

ini_set('memory_limit','256M');
set_time_limit(300);

define('BIND_MODULE','admin/Worker');
// 加载框架引导文件
require THINK_PATH.'start.php';
?>