<?php
/*
Controller工廠  創建Controller
 */
namespace Common;
if(!defined('HAOFUN'))
{
    exit('Access Denied');
}
class ControllerFactory extends Factory
{
    static public function create($type=null)  //重寫父類別方法，這邊$type是必須的，預設設為null就不用傳值進來
    {
        $ControllerPath='\\'.MODULE.'\\Controller\\'.Url::getController(true);
        return parent::create($ControllerPath);
    }
}
?>