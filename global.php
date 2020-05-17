<?php
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.4.0','<')) die('require PHP > 5.4.0 !');
// 开启调试模式
define('APP_DEBUG', true);
// 应用入口文件
error_reporting(E_ALL | E_STRICT);
// 网站主目录
define('BASE_ROOT_PATH', str_replace('\\', '/', dirname(__FILE__)).'/');
// 全局变量
//define('BASE_INCLUDE_PATH', BASE_ROOT_PATH.'include/');
define('BASE_DATA_PATH', BASE_ROOT_PATH.'data/');
define('ADDON_PATH', BASE_ROOT_PATH.'addon/');
// Thinkphp变量
define('RUNTIME_PATH', BASE_DATA_PATH.'runtime/');
define('LANG_PATH', BASE_DATA_PATH.'lang/');
define('THINK_PATH', BASE_ROOT_PATH.'vendor/thinkphp/');
define('APP_PATH', BASE_ROOT_PATH.'app/');// 定义配置文件位置
define('CONF_PATH', BASE_DATA_PATH.'config/');
// 缓存分隔符
define('CDS', ':');
?>