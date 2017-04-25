<?php
/*
Controller的父類別
*/
namespace Common;
if(!defined('HAOFUN'))
{
    exit('Access Denied');
}
class Controller
{
    protected $view;     //實例化View()用屬性
    protected $model;    //實例化Model()用屬性
    public function __construct()
    {
        $this->view=new View();   //實例化對應的View()類
    }
    //__call 魔術方法，如使用到不存在的方法時，會自動使用__call方法
    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        echo "{$name} Method不存在　:((";
    }
}

?>