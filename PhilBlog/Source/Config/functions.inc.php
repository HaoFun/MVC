<?php
/*
存放共用函數
*/
if(!defined('HAOFUN'))
{
    exit('Access Denied');
}
/*
loadNameSpace()函數  使用命名空間加載$path
*/
function loadNameSpace($classname)
{
    $path=PATH_SOURCE.'/'.str_replace('\\','/',$classname).'.class.php';
    if(file_exists($path))
    {
        require $path;
    }
}
/*
loadCommon()函數  加載Common下面的類，可使用命名空間加載
用於情況:因Common下有目錄，如直接使用 Common\ControllerFactory會找不到Common\Factory\ControllerFactory這個路徑
scandir()列出指定路徑中的文件及目錄，函數返回為陣列
is_file()判斷是否為文件
is_dir() 判斷是否為目錄
file_exists() 判斷是否為目錄或文件
*/
function loadCommon($classname)
{
    foreach (scandir(PATH_COMMON) as $value)
    {
        if($value=='.' || $value=='..' || is_file(PATH_COMMON.'/'.$value))
        {
            continue;
        }
        else
        {
            $classAttr=explode('\\',$classname);                     //先將Common\ControllerFactory 以\符號 拆成陣列
            $class=$classAttr[count($classAttr)-1];                  //取陣列中最後一個值ControllerFactory
            $path=PATH_COMMON.'/'.$value.'/'.$class.'.class.php';
            if(file_exists($path))
            {
                require $path;
                return true;                                         //如已經找到且require $path，就reture true不用再找了
            }
        }
    }
}

//post() 傳入一個鍵名，判斷$_POST裡是否有該鍵名
function post($index)
{
    if(isset($_POST[$index]))
    {
        return $_POST[$index];
    }
}
//get() 傳入一個鍵名，判斷$_GET裡是否有該鍵名
function get($index)
{
    if(isset($_GET[$index]))
    {
        return $_GET[$index];
    }
}

function test($a,$b,$c,$d) //測試check()用
{
    var_dump($a);
    var_dump($b);
    var_dump($c);
    var_dump($d);
    return true;
}
?>