<?php
/*
Config基本配置
*/
if(!defined('HAOFUN'))
{
    exit('Access Denied');
}
define('PATH_APP',dirname(dirname(dirname(__FILE__))));   //Project 絕對路徑
define('PATH_SOURCE',PATH_APP.'/Source');                 //Project Source絕對路徑
define('PATH_MODULE',PATH_SOURCE.'/'.MODULE);             //Project Module絕對路徑
define('PATH_COMMON',PATH_SOURCE.'/Common');              //Project Common絕對路徑


define('PATH_VIEW',PATH_MODULE.'/View');                  //Project View的絕對路徑
defined('PATH_VIEW_SKIN')?null:define('PATH_VIEW_SKIN',PATH_VIEW.'/Default');    //Project View\Default的絕對路徑

define('GET_CONTROLLER','controller');                    //用於Url分析  controller=?
define('GET_METHOD','method');                            //用於Url分析  method=?


//URL模式
//0.一般模式   1.PATHINFO模式
defined('URL_MODE')? null:define('URL_MODE','0');


//資料庫基本配置
define('DB_HOST','localhost');
define('DB_USERNAME','root');
define('DB_PASSWORD','0926');
define('DB_DATABASE','philblog');
define('DB_PORT',3306);
define('DB_CHARSET','utf8');
define('DB_PREFIX','phil_');
?>