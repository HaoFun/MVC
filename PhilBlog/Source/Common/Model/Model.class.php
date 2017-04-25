<?php
/*
Model父類
驗證、增刪改查方法
*/
namespace Common;
if(!defined('HAOFUN'))
{
    exit('Access Denied');
}
class Model
{
    protected $db;            // db物件屬性
    protected $error;         // 錯誤訊息屬性

    //資料表名稱相關屬性
    protected $tableName;     // mysql 表名屬性
    protected $tablePrefix;   // 表名前綴屬性  phil
    protected $trueTableName; // 加上前綴的表名

    //資料庫欄位相關屬性
    protected $fields;        //資料庫欄位名稱
    protected $fieldsType;    //資料庫欄位類型
    protected $fieldsKey;     //資料庫表的主鍵

    //驗證方法類用屬性
    protected $validate;          //欄位需驗證的規則(自定義)  (該屬性在對應的子類去實作賦值)
    protected $validateDefault;   //欄位需驗證的規則(默認)  靠Model類裡面的方法判斷默認需驗證的規則
    protected $trueValidate;      //驗證規則(最後版本)   由自定義與默認規則整合而成的規則

    //驗證方法時機(增、刪、改、查)用屬性
    protected $validateTime;                 //自定義的驗證時機   (該屬性在對應的子類去實作賦值)
    protected $validateTimeDefault = array(self::INSERT, self::DELETE, self::UPDATE, self::SELECT);  //默認的驗證時機
    protected $currenttTime;                 //當前設置的驗證時機
    protected $validateMessage;              //自定義的驗證提示訊息 (該屬性在對應的子類去實作賦值)
    protected $validateMessageDefault = array  //默認的驗證提示訊息
    (
        'null' => '{field}沒有數據',
        'type' => '{field}數據類型錯誤',
        'between' => '{field}數值必須介於{rule}之間',
        'notbetween' => '{field}數值不能介於{rule}之間',
        'in' => '{field}必須在{rule}裡面',
        'notin' => '{field}不能在{rule}裡面',
        'length' => '{field}長度必須介於{rule}',
        'unique' => '{field}已經存在{value}',
        'preg' => '{field}中包含非法字元',
        'function' => '{field}沒有通過{function}驗證',
        'method' => '{field}沒有通過{method}驗證',
        'equal' => '{field}必須等於{rule}',
        'notequal' => '{field}必須不等於{rule}',
        'consistent' => '{field}與{rule}輸入不一致',
    );
    protected $validateFieldsAlias;     //驗證提示的別名  (該屬性在對應的子類去實作賦值) 例:'name' 顯示為 名字
    const INSERT = 1;
    const DELETE = 2;
    const UPDATE = 3;
    const SELECT = 4;
    const UPDATE_CHECK_FIELDKEY = 5;    //此常數是用於update時確認主鍵時用的
    const DELETE_CHECK_FIELDKEY = 6;    //此常數是用於delete時確認主鍵時用的


    public function __construct()
    {
        $this->db = new DB();   //實例化 DB()
    }
    /*
    以下方法為獲取資料庫表方法
========================================================================================================================
     */
    protected function getTablePrefix()  //獲取表前綴
    {
        if (!isset($this->tablePrefix))
        {
            if (defined('DB_PREFIX'))
            {
                $this->tablePrefix = \DB_PREFIX;
            }
            else
            {
                $this->tablePrefix = '';
            }
        }
        return $this->tablePrefix;
    }

    protected function getTableName()  //獲取完整表名稱 (前綴+表名)
    {
        if (!isset($this->trueTableName))
        {
            if (!isset($this->tableName))
            {
                exit('請設置使用的表名稱');
            }
            $this->trueTableName = $this->getTablePrefix() . $this->tableName;
        }
        return $this->trueTableName;
    }
    /*
    以下方法為獲取時機方法
========================================================================================================================
     */
    protected function setCurrenttTime($time) //設置當前時機
    {
        $this->currenttTime=$time;
    }
    protected function getValidateTime($field,$ruleType)  //傳入欄位名稱、欄位型態獲取相對應的自定義時機與默認時機
    {
        //判斷是否有自定義時機
        if(isset($this->validateTime[$field][$ruleType]))
        {
            //如果有的話，判斷自定義時機是否為陣列，因後面處理以陣列方式處理，不是的話需轉為陣列
            if(is_array($this->validateTime[$field][$ruleType]))
            {
                return $this->validateTime[$field][$ruleType];
            }
            else
            {
                return array($this->validateTime[$field][$ruleType]);
            }
        }
        else
        {
            return $this->validateTimeDefault;
        }
    }

    /*
    以下方法為獲取資料庫鍵名、欄位名稱、欄位類型、該資料表下一條自動遞增ID值等
========================================================================================================================
     */
    public function parseFields()  //分析該資料表中各個欄位資訊並將值賦給對應的屬性，默認驗證規則也由此方法賦予
    {
        $param = array
        (
            'sql' => "SHOW COLUMNS FROM {$this->getTableName()}",
        );
        $fields = $this->db->execute($param);
        foreach ($fields as $value)
        {
            if ($value['Key'] == 'PRI')
            {
                $this->fieldsKey = $value['Field'];
            }
            if (strpos($value['Type'], 'int') !== false)
            {
                $this->fieldsType[$value['Field']] = 'i';
                //判斷欄位類型時順便賦給默認規則對應的Type類型，之後驗證用
                $this->validateDefault[$value['Field']]['type'] = 'i';
            }
            elseif (strpos($value['Type'], 'double') !== false)
            {
                $this->fieldsType[$value['Field']] = 'd';
                //判斷欄位類型時順便賦給默認規則對應的Type類型，之後驗證用
                $this->validateDefault[$value['Field']]['type'] = 'd';
            }
            else
            {
                $this->fieldsType[$value['Field']] = 's';
                //判斷欄位類型時順便賦給默認規則對應的Type類型，之後驗證用
                $this->validateDefault[$value['Field']]['type'] = 's';
            }
            $this->fields[] = $value['Field'];


            //判斷欄位名稱中'Null'是否為YES 或 是否為自動遞增，如結果為是，默認規則該欄位就可以不傳值
            if ($value['Null'] == 'YES' || $value['Extra'] == 'auto_increment')
            {
                $this->validateDefault[$value['Field']]['null'] = true;
            }
            else
            {
                $this->validateDefault[$value['Field']]['null'] = false;
            }
        }
    }

    protected function getFields()   //獲取該資料表中的欄位名稱
    {
        if (!isset($this->fields))
        {
            $this->parseFields();
        }
        return $this->fields;
    }

    protected function getFieldsType($field = null)   //獲取該資料表中的欄位類型，可傳一個欄位名稱獲取該欄位名稱的類型
    {
        if (!isset($this->fieldsType))
        {
            $this->parseFields();
        }
        if ($field)
        {
            return $this->fieldsType[$field];
        }
        else
        {
            return $this->fieldsType;
        }
    }

    protected function getFieldsKey()   //獲取該資料表中的主鍵
    {
        if (!isset($this->fieldsKey))
        {
            $this->parseFields();
        }
        return $this->fieldsKey;
    }

    protected function getNextAutoID() //獲取表內下一個自動遞增的ID值
    {
        $param=array
        (
            //shhow table status whewe name=表名
            //內容有此表的一些設定，這邊最重要是要獲取Auto_increment欄位:下一個Auto_increment的值
            'sql'=>"show table status where name='{$this->getTableName()}'",
        );
        $data=$this->db->execute($param);
        if(isset($data[0]['Auto_increment']))
        {
            return $data[0]['Auto_increment'];
        }
    }
    /*
    以下方法為獲取默認驗證規則、最後的驗證規則、驗證失敗提示訊息、提示訊息中替換別名等
========================================================================================================================
     */
    protected function getValidateDefault()  //獲取默認的驗證規則
    {
        if (!isset($this->validateDefault))
        {
            $this->parseFields();
        }
        return $this->validateDefault;
    }

    protected function getValidate()   //獲取最後的驗證規則
    {
        if (!isset($this->trueValidate))
        {
            //這邊遍歷"默認的驗證規則"，因自定義的驗證規則可能沒有定義到全部的欄位
            //$key   = 規則的欄位名稱  id
            //$value = 規則           array('type'=>'i','null'=>true)
            foreach ($this->getValidateDefault() as $key => $value)
            {
                if (isset($this->validate[$key]))  //判斷如有自定義規則則將自定義驗證規則覆蓋默認的驗證規則
                {
                    $this->trueValidate[$key] = array_merge($value, $this->validate[$key]);
                }
                else
                {
                    $this->trueValidate[$key] = $value;
                }
            }
        }
        return $this->trueValidate;
    }
    protected function getValidateMessage($field,$ruleType,$rule,$value)  //獲取驗證的提示訊息
    {
        /*
        $field    欄位名稱              id
        $ruleType 欄位類型              type
        $rule     規則的值              i
        $value    外部傳進來要驗證的值   100
        */
        if(isset($this->validateMessage[$field][$ruleType]))   //判斷是否有自定義的提示訊息
        {
            $message=$this->validateMessage[$field][$ruleType];
        }
        else
        {
            $message=$this->validateMessageDefault[$ruleType];
        }
        //判斷$message內有沒有{field}、{rule}、{value}、{function}、{method}，有的話就做替換
        if(strpos($message,'{field}')!==false)
        {
            //欄位名稱看是否有別名  getValidateFieldsAlias()
            $message=str_replace('{field}',$this->getValidateFieldsAlias($field),$message);
        }
        if(strpos($message,'{rule}')!==false)
        {
            $message=str_replace('{rule}',$rule,$message);
        }
        if(strpos($message,'{value}')!==false)
        {
            $message=str_replace('{value}',$value,$message);
        }
        if(strpos($message,'{function}')!==false)
        {
            $message=str_replace('{function}',$rule[0],$message);
        }
        if(strpos($message,'{method}')!==false)
        {
            $message=str_replace('{method}',$rule[0],$message);
        }
        return strtoupper($message); //換成大寫看起來比較清楚
    }
    protected function getValidateFieldsAlias($field)  //獲取一個欄位名稱的別名
    {
        if(isset($this->validateFieldsAlias[$field]))
        {
            return $this->validateFieldsAlias[$field];
        }
        else
        {
            return $field;
        }
    }
     /*
     以下方法為處理驗證、處理錯誤方法
========================================================================================================================
     */
     protected function validation($data)  //實際處理驗證方法
     {
         //$key   = 規則的欄位名稱  id
         //$value = 規則           array('type'=>'i','null'=>true)
         //遍歷完整的驗證規則getValidate()
        foreach ($this->getValidate() as $key => $value)
        {
            if(!isset($this->currenttTime))  //判斷是否有設置當前時機
            {
                $this->error='操作時機沒有設置';
                return false;
            }
            //判斷當前$key(欄位名稱)，$data裡是不是有同一個欄位名稱，如果沒有表示要判斷傳進來的$data要判斷該欄位名稱($key)是否能夠不傳值
            if(array_key_exists($key,$data))
            {
                //$ruleType   規則名稱   'type'
                //$rule       規則的值   'i'
                foreach ($value as $ruleType => $rule)
                {
                    if($ruleType=='null')  //$ruleType=='null'這個已經驗證過了，這邊就跳過
                    {
                        continue;
                    }
                    //獲取當前驗證規則類型它的getValidateTime驗證"時機"
                    //獲取當前currenttTime"時機"，兩者相比較
                    //這裡會依序每次循環將對應的欄位名稱及欄位類型去獲取對應的驗證時機，判斷當前時機是否包含在內
                    //判斷當前時機currenttTime是否包含在getValidateTime內
                    if(in_array($this->currenttTime,$this->getValidateTime($key,$ruleType)))
                    {
                        $tmp=$data[$key];
                        //因後面check()判斷unique時需要用到欄位名稱($key)的值，故這邊重新更改$tmp
                        if($ruleType=='unique')
                        {
                            $tmp=array
                            (
                                'fieldName'=>$key,
                                'fieldValue'=>$data[$key],
                            );
                        }
                        //因後面check()判斷consistent時需要$data[$rule]的值
                        //假設TypeModel設置'consistent'=>'test',$data(array('id' => '20','test'=>'20'))裡也有這個數字
                        //$data[$rule] = 20
                        if($ruleType=='consistent')
                        {
                            $tmp=array
                            (
                                $data[$rule],
                                $data[$key],
                            );
                        }
                        //$data[$key]  id值為100，$data[$key]=100
                        //判斷是否通過驗證
                        if(!$this->check($tmp,$ruleType,$rule))
                        {
                            //$key       欄位名稱              id
                            //$ruleType  欄位類型              type
                            //$rule      規則的值              i
                            //$tmp       外部傳進來要驗證的值   100  ('unique'時為array('id'=>'100'))
                            $this->error=$this->getValidateMessage($key,$ruleType,$rule,$data[$key]);
                            return false;
                        }
                    }
                }
            }
            else
            {
                if(in_array($this->currenttTime,$this->getValidateTime($key,'null')))
                {
                    if (!$value['null'])
                    {
                        //這裡判斷$value['null']是否為false，如果是false的話，將欄位名稱、規則類型傳到getValidateMessage
                        //後兩個null因傳的值為false故以null代替(也用不到這兩個參數)
                        $this->error = $this->getValidateMessage($key, 'null', null, null);
                        return false;
                    }
                }
            }
        }
        return true; //驗證通過
     }
     protected function check($value,$ruleType,$rule)  //check()驗證規則是否通過
     {
         //switch根據 規則類型
         switch ($ruleType)
         {
             case 'type':
                 if($rule=='i')
                 {
                     return is_numeric($value);
                 }
                 elseif ($rule=='d')
                 {
                    return is_double($value);
                 }
                 elseif ($rule=='s')
                 {
                     return is_string($value);
                 }
                 else
                 {
                    return true;
                 }
                 break;
             case 'between':
                 $between=explode(',',$rule);
                 return $value>=$between[0] && $value<=$between[1];
                 break;
             case 'notbetween':
                 $notbetween=explode(',',$rule);
                 return $value<$notbetween[0] || $value>$notbetween[1];
                 break;
             case 'in':
                 $in=explode(',',$rule);
                 return in_array($value,$in);
                 break;
             case 'notin':
                 $notin=explode(',',$rule);
                 return !in_array($value,$notin);
                 break;
             case 'length':
                 $length=explode(',',$rule);
                 //判斷$rule傳進來的值是單個數字，還是兩個數字(區間)
                 if(count($length)==1)  //單個數字
                 {
                     return mb_strlen($value,'utf-8')>=$length[0];
                 }
                 elseif(count($length)==2)  //兩個數字
                 {
                     return mb_strlen($value,'utf-8')>=$length[0] && mb_strlen($value,'utf-8')<=$length[1];
                 }
                 break;
             case 'unique':
                 if($rule)  //判斷$rule為true or false，true要驗證是否唯一、false不用驗證是否唯一
                 {
                     $param=array
                     (
                         'sql'=>"SELECT {$value['fieldName']} FROM {$this->getTableName()} WHERE {$value['fieldName']}=?",
                         'bind'=>array($this->getFieldsType($value['fieldName']),array($value['fieldValue'])),
                     );
                     //計算查詢出來得值，如果有就代表不是唯一性了，返回false
                     if(count($this->db->execute($param)))
                     {
                        return false;
                     }
                     else
                     {
                         return true;
                     }
                 }
                 else
                 {
                     return true; //不須驗證，返回true
                 }
                 break;
             case 'preg':
                 return preg_match($rule,$value);
                 break;
             case 'function':
                 //TypeModel 內設置 'function'=>array('test'),
                 //使用方法 如有$rule[1] 將$value值 array_unshift到陣列第一個
                 //否則就將$value設為陣列，傳到對應的方法內執行
                 //函數 應寫在functions內
                 if(isset($rule[1]))
                 {
                     $param=$rule[1];
                     array_unshift($param,$value);
                 }
                 else
                 {
                     $param=array($value);
                 }
                 return call_user_func_array($rule[0],$param);
                 break;
             case 'method':
                 //TypeModel 內設置 'method'=>array('test'),
                 //使用方法 同上
                 //方法應寫在Model父類或子類內
                 if(isset($rule[1]))
                 {
                     $param=$rule[1];
                     array_unshift($param,$value);
                 }
                 else
                 {
                     $param=array($value);
                 }
                 return call_user_func_array(array($this,$rule[0]),$param);
                 break;
             case 'equal':
                 return $value==$rule;
                 break;
             case 'notequal':
                 return $value!=$rule;
                 break;
             case 'consistent':
                 return $value[0]==$value[1];
                 break;
             default:
                 break;
         }
     }
     public function getError()  //獲取錯誤訊息
     {
         return $this->error;
     }
    /*
    以下方法為資料插入、修改、刪除方法
========================================================================================================================
    */
    public function add($data,$autoValidation=true) //add()插入方法
    {
        if($autoValidation)
        {
            $this->setCurrenttTime(self::INSERT);
            if(!$this->validation($data))
            {
                return false;
            }
        }
        //插入方法中如主鍵有傳值，將它unset()掉，用默認自動遞增的就好
        if(isset($data[$this->getFieldsKey()]))
        {
            unset($data[$this->getFieldsKey()]);
        }
        $type='';
        //foreach內需判斷傳進來$data的鍵名是否在該資料表中也有相同的欄位名稱，沒有的話unset掉(in_array判斷)
        foreach ($data as $key=>$value)
        {
            if (!in_array($key,$this->getFields()))
            {
                unset($data[$key]);
                continue;
            }
            $type.=$this->getFieldsType($key);
        }
        //判斷$data移除主鍵&unset該資料表中沒有的鍵名後，如果已經沒有值了，就return false
        if(!count($data)>0)
        {
            $this->error='插入數據異常';
            return false;
        }
        $fieldsName=implode(',',array_keys($data));                //array_keys獲取陣列中的鍵名
        $fieldsfill=implode(',',array_fill(0,count($data),'?'));   //array_fill從第0個開始用?填充陣列
        $param=array
        (
            'sql'=>"INSERT INTO {$this->getTableName()}({$fieldsName}) VALUES ({$fieldsfill})",
            'bind'=>array($type,$data),
        );
        return $this->db->execute($param);
    }
    public function update($data,$fieldKey,$autoValidation=true)  //update()方法
    {
        //修改方法中如主鍵有傳值，將它unset()掉，用原本的主鍵就好
        if(isset($data[$this->getFieldsKey()]))
        {
            unset($data[$this->getFieldsKey()]);
        }
        if($autoValidation)
        {
            //因驗證會驗證是否唯一性，故這邊驗證時機有新建一個專門用於UPDATE_CHECK_FIELDKEY的常數
            $this->setCurrenttTime(self::UPDATE_CHECK_FIELDKEY);
            //validation驗證需傳入陣列，這邊將傳進來的主鍵ID改為陣列
            if(!$this->validation(array($this->getFieldsKey()=>$fieldKey)))
            {
                return false;
            }
            //更改回來UPDATE時機
            $this->setCurrenttTime(self::UPDATE);
            if(!$this->validation($data))
            {
                return false;
            }
        }
        $fieldsName='';
        $type='';
        foreach ($data as $key => $value)
        {
            if(!in_array($key,$this->getFields()))
            {
                unset($data[$key]);
                continue;
            }
            //$fieldsName  例:name=?,pic=?
            $fieldsName.="{$key}=?,";
            //$type  例ssi
            $type.="{$this->getFieldsType($key)}";
        }
        if(!count($data))
        {
            $this->error='修改數據異常';
        }
        //$type因SQL語句後面還有一個id=?的值須加上
        $type.="{$this->getFieldsType($this->getFieldsKey())}";
        //$fieldsName也因SQL語句綁定需求須將主鍵的值array_push到最後
        array_push($data,$fieldKey);
        $fieldsName=rtrim($fieldsName,',');
        $param=array
        (
            //UPDATE test SET name=?,pic=? WHERE id=?
            'sql'=>"UPDATE {$this->getTableName()} SET {$fieldsName}  WHERE {$this->getFieldsKey()}=?",
            'bind'=>array($type,$data),
        );
        return $this->db->execute($param);
    }
    public function delete($fieldkey)
    {
        $this->setCurrenttTime(self::DELETE_CHECK_FIELDKEY);
        if(is_array($fieldkey))
        {
            $tmp='';
            $type='';
            foreach ($fieldkey as $value)
            {
                if(!$this->validation(array($this->getFieldsKey()=>$value)))
                {
                    return false;
                }
                $tmp.='?,';
                $type.="{$this->getFieldsType($this->getFieldsKey())}";
            }
            $tmp=rtrim($tmp,',');
            $sql="DELETE FROM {$this->getTableName()} WHERE {$this->getFieldsKey()} in({$tmp})";
        }
        else
        {
            if(!$this->validation(array($this->getFieldsKey()=>$fieldkey)))
            {
                return false;
            }
            $sql="DELETE FROM {$this->getTableName()} WHERE {$this->getFieldsKey()}=?";
            $type=$this->getFieldsType($this->getFieldsKey());
            $fieldkey=array($fieldkey);
        }
        $param=array
        (
            'sql'=>$sql,
            'bind'=>array($type,$fieldkey),
        );
        return $this->db->execute($param);
    }
}
?>