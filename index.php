<?php
// 查找共用库地址
if (!@include(str_replace('\\','/',dirname(__FILE__)).'/global.php')) exit('global isn\'t exists!');
// 加载框架引导文件
require THINK_PATH.'start.php';
?>