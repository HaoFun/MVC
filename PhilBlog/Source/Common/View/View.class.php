<?php
/*
View的父類別
根據不同TypeController方法選擇不同的View頁面
*/
namespace Common;
if(!defined('HAOFUN'))
{
    exit('Access Denied');
}
class View
{
    public  $title_name=array(); //存放頁面title資訊
    public  $css=array();        //存在頁面使用css
    public  $js=array();         //存放頁面使用js
    private $viewPathDefault;    //存放include view的路徑
    private $data;
    /*
    display($path=null)
    這個方法可以傳入一個文件名稱
    預設為NULL不傳值，有傳的話就根據傳入進來的名稱去使用inc()加載對應的VIEW
     */
    public function display($path=null)
    {
        if($path==null)
        {
            $path=$this->getViewPath();
            if(file_exists($path))
            {
                include $path;     //引入對應的VIEW文件
            }
            else
            {
                echo "{$path}--VIEW不存在";
            }
        }
        else
        {
            $this->inc($path);
        }
    }
    public function setData($name,$value)        //set數據
    {
        $this->data[$name]=$value;
    }
    public function getData($name=null)          //通過$name傳入，獲取一個數據值，如不傳通過$name傳入就return整個$data
    {
        if($name==null)
        {
            return $this->data;
        }
        else
        {
            if(isset($this->data[$name]))
            {
                return $this->data[$name];
            }
            else
            {
                echo "getDATA失敗，{$name}不存在";
            }
        }
    }
    public function inc($path)     //可以在View頁面引入其他文件
    {
        $pathArray=array
        (
            $path,
            \PATH_VIEW_SKIN.'/'.Url::getController().'/'.$path.'.view.php',
            \PATH_VIEW_SKIN.'/Common/'.$path.'.view.php',
            null
        );
        foreach ($pathArray as $value)
        {
            if(file_exists($value))
            {
                include $value;
                break;
            }
            if($value==null)
            {
                echo "include失敗，{$path}不存在";
                break;
            }
        }
    }
    //getViewPath() 透過不同Controller及Method 選擇不同的View介面
    private function getViewPath()
    {
        if(!isset($this->viewPathDefault))
        {
            return $this->viewPathDefault=\PATH_VIEW_SKIN.'/'.Url::getController().'/'.Url::getMethod().'.view.php';
        }
        else
        {
            return $this->viewPathDefault;
        }
    }
}

?>