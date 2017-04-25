<?php
/*
分析URL地址用，靜態類  靜態方法
*/
namespace Common;
if(!defined('HAOFUN'))
{
    exit('Access Denied');
}
class Url
{
    static private $controller;             //存放$_GET[GET_CONTROLLER] 屬性
    static private $method;                 //存放$_GET[GET_METHOD] 屬性
    static private function init()          //初始化
    {
        switch (URL_MODE)
        {
            case 0:
                break;
            case 1:
                if(isset($_SERVER['PATH_INFO']))
                {
                    self::parsePathinfo();
                }
                break;
        }
        self::parseUrl();
    }
    /*
    parsePathinfo()
    分析Pathinfo模式
    admin.php/type/index/id/100
    */
    static private function parsePathinfo()
    {
        preg_match_all('/([^\/]+)\/([^\/]+)/',$_SERVER['PATH_INFO'],$data);
        if(count($data[0]))
        {
            foreach ($data[0] as $key=>$value)
            {
                $tmp=explode('/',$value);
                if($key==0)                          //配對出來的第一組數據比較特別，$tmp[0]賦給Controller、$tmp[1]賦給Method
                {
                    $_GET[\GET_CONTROLLER]=$tmp[0];
                    $_GET[\GET_METHOD]=$tmp[1];
                }
                else                                 //其餘的$_GET[$tmp[0]]=$tmp[1]，如id=100
                {
                    $_GET[$tmp[0]]=$tmp[1];
                }
            }
        }
        else
        {
            $tmp=explode('/',$_SERVER['PATH_INFO']); //沒有配對道的情況如 admin.php/type 或 admin.php/type/ 將$tmp[1]賦給Controller即可
            if(isset($tmp[1]))
            {
                $_GET[\GET_CONTROLLER]=$tmp[1];
            }
        }
    }
    /*
    靜態方法 parseUrl()
    分析$_GET方式取到的數值，如沒有或為空值，則將$_GET賦予index
    */
    static private function parseUrl()
    {
        if(!isset($_GET[\GET_CONTROLLER]) || $_GET[\GET_CONTROLLER]=='')
        {
            $_GET[\GET_CONTROLLER]='index';
        }
        if(!isset($_GET[\GET_METHOD]) || $_GET[\GET_METHOD]=='')
        {
            $_GET[\GET_METHOD]='index';
        }
        self::$controller=ucfirst($_GET[\GET_CONTROLLER]);    //ucfirst() 將Controller 第一個字大寫
        self::$method=$_GET[\GET_METHOD];
    }
    /*
    靜態方法 getController($type=false)
    可傳入一個參數$type
    為true時後墜會加上Controller
    供外部調用使用
    */
    static public function getController($type=false)
    {
        if(!isset(self::$controller))
        {
            self::init();
        }
        if($type)
        {
            return self::$controller.'Controller';
        }
        else
        {
            return self::$controller;
        }
    }
    /*
    靜態方法 getMethod()
    供外部調用使用
    */
    static public function getMethod()
    {
        if(!isset(self::$method))
        {
            self::init();
        }
        return self::$method;
    }
}
?>