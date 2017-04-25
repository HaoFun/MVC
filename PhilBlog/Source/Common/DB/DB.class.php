<?php
/*
Mysql資料庫 操作類
 */
namespace Common;
if(!defined('HAOFUN'))
{
    exit('Access Denied');
}
class DB extends \mysqli     //繼承mysqli
{
    private $lastInsertID;  //存放 上次insert的ID屬性
    public function __construct()   //預設為null可不傳(使用默認配置)，可接受一個參數(陣列類型)，可與默認配置進行array_merge()合併
    {
        @parent::__construct(DB_HOST,DB_USERNAME,DB_PASSWORD,DB_DATABASE,DB_PORT);
        if($this->connect_errno)              //如發生連接錯誤
        {
            $this->dError($this->connect_error);
        }
        $this->set_charset(DB_CHARSET);    //設置編碼UTF8
    }
    /*
    mysqli預處理
    防止SQL注入(防止特殊SQL語句，例如 1 or 1=1 用預處理方式?內的數據不會被當成SQL語句，只是純數據)
    execute($param) $param參數為陣列
    array
    (
        'sql'=>'select * from test where id=?',
        'bind'=>array('i',array(10)),  或者  'bind'=>array('i',array('id'=>10)),
    );
    */
    public function execute($param)
    {
        $stmt=$this->stmt_init();  //獲取一個stmt物件
        if($stmt->prepare($param['sql']))  //prepare() 準備一條SQL語句至mysql預處理
        {
            if(isset($param['bind']))
            {
                foreach ($param['bind'][1] as $key => $value)
                {
                    //bind_param 需用變數的方式傳值，如需直接傳據值需用引入的方式，這邊需加上&
                    //如使用$value會有問題，因$value為臨時變數使用&$value foreach會將值都變成最後一個
                    //故使用$param['bind'][1][$key] <= $key 為0開始，可以針對陣列中第幾個參數開始賦給$tmp
                    $tmp[]=&$param['bind'][1][$key];
                }
                array_unshift($tmp,$param['bind'][0]);
                if(!@call_user_func_array(array($stmt,'bind_param'),$tmp))
                {
                    $this->dError('參數綁定失敗');
                }
            }
            if($stmt->execute())
            {
                if($stmt->result_metadata())   //result_metadata()判斷 是否為一個結果集 如select語句
                {
                    $result=$stmt->get_result();    //get_result()將查詢到的MYSQL數據取回PHP處理
                    return $result->fetch_all(MYSQLI_ASSOC);  //返回查詢的數據 MYSQLI_ASSOC欄位名稱模式
                }
                else                          //無結果集 SQL語句  insert、delete、update 就返回影響的行數
                {
                    $this->lastInsertID=$stmt->insert_id;
                    return $stmt->affected_rows;
                }
            }
            else
            {
                $this->dError($stmt->error);
            }
        }
        else
        {
            $this->dError($stmt->error);
        }
    }
    public function getLastInsertID()  //返回上次最後insert的ID
    {
        return $this->lastInsertID;
    }
    /*
    escape($data)
    轉義
    資料入庫之前進行轉義，確認資料能夠順利入庫，使用遞迴
    */
    public function escape($data)
    {
        if(is_string($data))
        {
            return $this->real_escape_string($data);
        }
        if(is_array($data))
        {
            foreach ($data as $key=>$value)
            {
                $data[$key]=$this->escape($value);
            }
        }
        return $data;
    }

    public function __destruct()     //關閉資料庫
    {
        @$this->close();
    }
    private function dError($error)  //Mysql錯誤處理
    {
        throw new \Exception($error);
    }
}

?>