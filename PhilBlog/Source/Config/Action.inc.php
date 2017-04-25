<?php
/*
不同的功能由不同的控制器類創建出來的物件實現
*/
if(!defined('HAOFUN'))
{
    exit('Access Denied');
}
include 'Config.inc.php';        //加載共用config配置
include 'functions.inc.php';     //加載共用函數

spl_autoload_register('loadNameSpace');          //自動載入
spl_autoload_register('loadCommon');             //自動載入

try
{
    //獲取當前執行對應的Controller裡方法名稱
    $method=Common\Url::getMethod();
    //創建指定類型的Controller，執行指定的方法
    Common\ControllerFactory::create()->$method();
}
catch(Exception $e)       //捕獲異常
{
    exit($e->getMessage());
}
?>