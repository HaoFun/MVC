<?php
/*
前台
 */
header('Content-type:text/html;charset=utf-8');
define('MODULE','Index');                       //前台MODULE
define('HAOFUN','PhilBlog');                    //建立金鑰，預防其他文件被惡意加載
define('URL_MODE','1');                         //URL PATHINFO模式
include 'Source/Config/Action.inc.php';

?>