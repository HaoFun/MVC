<?php
/*
Tree
處理樹狀結構的數據 相關方法
*/
namespace Common;
class Tree
{
    private $treeDataOriginal;  //樹狀結構接收的原始數據
    private $treeDataId;        //按照ID為陣列索引的數據
    private $treeDataPid;       //按照Pid與id為陣列索引的數據
    private $treePosterity;     //存放整理過後的欄位的屬性(依階層排列)
    public function __construct($data)
    {
        $this->treeDataOriginal=$data;
        //透過foreach將數據改為ID為陣列索引、以及Pid為陣列索引的數據
        foreach ($this->treeDataOriginal as $value)
        {
            $this->treeDataId[$value['id']]=$value;
            //因pid有共同的數據、故這裡第二層鍵名再加上id索引
            $this->treeDataPid[$value['pid']][$value['id']]=$value;
        }
    }

    //根據$id獲取該id的子欄位
    public function getChildren($id)
    {
        if(isset($this->treeDataPid[$id]))
        {
            return$this->treeDataPid[$id];
        }
        else
        {
            return false;
        }
    }

    //根據$id 獲取該$id的父欄位
    public function getParent($id)
    {
        if(isset($this->treeDataId[$id]['pid']))
        {
            if(isset($this->treeDataId[$this->treeDataId[$id]['pid']]))
            {
                return $this->treeDataId[$this->treeDataId[$id]['pid']];
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    //獲取$id的最上層欄位(這裡主要用於計算階層)
    public function getAncestor($id)
    {
        $tmp=array();
        while ($parent=$this->getParent($id))
        {
            //將父欄位的id重新賦給$id
            $id=$parent['id'];
            $tmp[$parent['id']]=$parent;
        }
        return $tmp;
    }

    //獲取所有最上層欄位
    public function getTop()
    {
        if(isset($this->treeDataPid[0]))
        {
            return $this->treeDataPid[0];
        }
        else
        {
            return array();
        }
    }

    //獲取該數據 樹狀結構數據(階層)
    public function getTreeAll()
    {
        //清空$treePosterity，防止重複賦值
        $this->treePosterity=null;
        foreach ($this->getTop() as $value)
        {
            //因parsePosterity()方法只會獲取全部的子欄位，故這邊先把最上層欄位加入$treePosterity裡
            $this->treePosterity[$value['id']]=$value;
            $this->parsePosterity($value['id']);
        }
        return $this->treePosterity;
    }

    //遞迴獲取$id子欄位
    private function parsePosterity($id)
    {
        $children=$this->getChildren($id);
        //$children為false結束單輪foreach
        if($children)
        {
            foreach ($children as $value)
            {
                $value['level']=count($this->getAncestor($value['id']));
                //implode將陣列轉為字串，style作為顯示第幾層欄位輸出額外空格用
                $value['style']=implode('',array_fill(0,$value['level'],'　　'));
                $this->treePosterity[$value['id']]=$value;
                $this->parsePosterity($value['id']);
            }
        }
        return $this->treePosterity;
    }

    //外部使用 獲取$id的所有子欄位
    public function getPosterity($id)
    {
        //清空$treePosterity，防止重複賦值
        $this->treePosterity=null;
        $this->parsePosterity($id);
        return $this->treePosterity;
    }
}
?>