<?php
header("Content-type:text/html;charset=utf-8"); //设置框架编码

ini_set("date.timezone","Asia/Shanghai"); //设置时区

define('APP_PATH',__DIR__.'/'); //定义项目路径常量

define('Lib','../YzmPHP'); //定义框架目录的常量

define('Resource', APP_PATH.'Resource'); //定义资源目录常量

define('APP_DEBUG',true);

ini_set("display_errors",0); //是否抛出错误

require Lib.'/YzmPHP.php';

$app = new YzmPHP();

$app->run();

$app = null;