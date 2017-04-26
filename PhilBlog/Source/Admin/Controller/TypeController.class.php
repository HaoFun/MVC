<?php
/*
TypeController
根據不同TypeController使用的方法選擇不同的欄位處理方式
*/
namespace Admin\Controller;
use Admin\Model\TypeModel;
use Common\Controller;
use Common\Tree;
if(!defined('HAOFUN'))
{
    exit('Access Denied');
}
class TypeController extends Controller
{
    public function __construct()
    {
        $this->model=new TypeModel(); //實例化TypeModel
        parent::__construct();
    }
    public function index()
    {
        $this->view->display();
    }
    public function add()
    {
        if(!empty($_POST))
        {
            if($this->model->add($_POST))
            {
                $this->view->setData('message','新增成功');
                $this->view->setData('skip','success');
                $this->view->setData('URL','/PhilBlog/admin.php/type/index');
            }
            else
            {
                $this->view->setData('message',$this->model->getError());
                $this->view->setData('skip','danger');
                $this->view->setData('URL','/PhilBlog/admin.php/type/add');
            }
            $this->view->display('skip');
        }
        $tree=new Tree($this->model->getAll());  //實例化樹狀結構
        $this->view->setData('arctype',$tree->getTreeAll());
        $this->view->display();
    }
    public function update()
    {
        //$data=array('id'=>100,'name'=>'Tone','pic'=>'men');
        //var_dump($this->model->update($data,32));
        //var_dump($this->model->getError());
    }
    public function delete()
    {
        //var_dump($this->model->delete(26));
        //var_dump($this->model->getError());
    }
}
?>