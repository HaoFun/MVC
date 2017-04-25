<?php
/*
工廠
實例化 Controller返回給Action
 */
namespace Common;
if(!defined('HAOFUN'))
{
    exit('Access Denied');
}
class Factory
{
    static public function create($type)
    {
        if (class_exists($type))         //class_exists() 默認會將嘗試調用 __autoload 或者 spl_autoload_register
        {
            return new $type();
        }
        else
        {
            throw new \Exception("{$type}不存在");
        }
    }
}
?>