<?php
/*
Model Type子類
自訂義驗證等等
*/
namespace Admin\Model;
use Common\Model;
if(!defined('HAOFUN'))
{
    exit('Access Denied');
}
class TypeModel extends Model
{
    public function __construct()
    {
        $this->tableName = 'arctype';  //設置使用資料庫 表名

        /*
        數據是否可以不傳值    null
        數據的類型           type
        數據的區間           between
        數據不能在某個區間
        數據在某個範圍       in
        數據不能在某個範圍
        數據的長度           length
        數據是否可以重複(唯一性) unique
        數據是否符合正則      preg
        數據是否符合某個自定義函數  function   //'function'=>array('test'),  使用方法
        數據是否符合某個自定義方法  method     //'method'=>array('test'),  使用方法
        數據是否等於某個值     equal
        數據是否不等於某個值
        數據兩條輸入的是否一致 consistent
        */
        $this->validate = array  //設置自定義驗證規則
        (
            'id' => array
            (
                'between' => '0,4294967295',  //int UNSIGNED最大值42944967295
                'unique' => true,
            ),
            'pid' => array
            (
                'between' => '0,4294967295',  //int UNSIGNED最大值42944967295
                'method' => array
                (
                    'checkPid',
                ),
            ),
            'name' => array
            (
                'length' => '1,255',          //欄位名稱最小1位、最大255位
                'unique' => true,
            ),
            'urlname' => array
            (
                'length' => '1,255',          //URL目錄最小1位、最大255位
                'unique' => true,
            ),
            'view' => array
            (
                'length' => '1,255',          //欄位介面最小1位、最大255位
            ),
            'title' => array
            (
                'length' => '1,255',          //SEO標題最小1位、最大255位
            ),
            'keywords' => array
            (
                'length' => '1,255',          //SEO關鍵字最小1位、最大255位
            ),
            'description' => array
            (
                'length' => '1,500',          //SEO描述最小1位、最大500位
            ),
            'sort' => array
            (
                'between' => '0,4294967295',  //int UNSIGNED最大值42944967295
            ),
        );
        $this->validateMessage = array  //設置自定義驗證提示訊息
        (
            'pid' => array
            (
                'method' => '所選擇的父欄位名稱不存在，請確認',
            ),
        );
        $this->validateFieldsAlias = array   //設置自定義$fields欄位名稱的別名
        (
            'pid' => '父欄位名稱',
            'name' => '欄位名稱',
            'urlname' => 'URL目錄',
            'view' => '欄位介面',
            'title' => 'SEO標題',
            'keywords' => 'SEO關鍵字',
            'description' => 'SEO描述',
            'sort' => '排序',
        );
        /*
        $this->validateTime=array  //設置自定義驗證時機
        (
            'id'=>array
            (
                'type'=>array(self::INSERT,self::DELETE,self::UPDATE,self::SELECT,self::UPDATE_CHECK_FIELDKEY,self::DELETE_CHECK_FIELDKEY),
            ),
        );
        */
        parent::__construct();
    }

    public function checkPid($pid) //check驗證，驗證父欄位$pid方法
    {
        //如$pid等於0，表示為最上層目錄，這邊就不需要驗證
        if ($pid > 0) {
            $param = array
            (
                //傳送所選的父欄位，SQL語句查詢判斷父欄目這個ID是否存在
                'sql' => "SELECT pid FROM {$this->getTableName()} WHERE id=?",
                'bind' => array($this->getFieldsType('id'), array($pid)),
            );
            return count($this->db->execute($param));  //返回查詢到的筆數
        } else {
            return true;
        }
    }

    //傳入一個父欄位ID，查找這個ID的tid為哪個值
    public function getTid($id)
    {
        $param = array
        (
            'sql' => "SELECT tid FROM {$this->getTableName()} WHERE id=?",
            'bind' => array($this->getFieldsType('id'), array($id)),
        );
        $data = $this->db->execute($param);
        if (isset($data[0]['tid'])) {
            return $data[0]['tid'];
        } else {
            return false;
        }
    }

    //改寫父類的add()
    public function add($data, $autoValidation = true)
    {
        //如$_POST內父欄位名稱為0，表示為最上層欄位，這邊需獲取到下一條自動遞增的ID值，
        //否則該$_POST的tid與該$_POST父欄位的tid為同一個
        //設置好tid後就執行父類的add方法
        if (post('pid') == 0) {
            $data['tid'] = $this->getNextAutoID();
        } else {
            $data['tid'] = $this->getTid(post('pid'));
        }
        return parent::add($data, $autoValidation = true);
    }

    public function getAll()  //查詢該資料表所有欄位資訊
    {
        $param=array
        (
            'sql'=>"SELECT * FROM {$this->getTableName()} ORDER BY sort ASC",
        );
        return $this->db->execute($param);
    }
}