<?php
if(!defined('HAOFUN'))
{
    exit('Access Denied');
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>bootstrap</title>
    <link rel="stylesheet" type="text/css" href="/PhilBlog/Css/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/PhilBlog/Css/public.css">
    <style type="text/css">
        #header
        {
            width: 100%;
            height: 40px;
            border-bottom: 1px solid #e3e3e3;
        }
        #header h1
        {
            font-size: 40px;
            line-height: 40px;
            margin: 0 0 0 20px;
            color: #333;
            font-weight: bold;
        }
        #main-body
        {
            margin: 10px;
        }
    </style>
</head>
<body>
<div id="header">
    <h1>後台管理</h1>
</div>
<div id="main-body" class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <div class="list-group">
                <a href="#" class="list-group-item active">欄位列表</a>
                <a href="#" class="list-group-item">添加欄位</a>
            </div>
            <div class="list-group">
                <a href="#" class="list-group-item">欄位列表</a>
                <a href="#" class="list-group-item">添加欄位</a>
            </div>
        </div>
        <div class="col-md-10">
            <div class="panel panel-default">
                <div class="panel-heading">添加欄位</div>
                <div class="panel-body">
                    <form class="form-horizontal" method="post">
                        <div class="form-group row">
                            <label for="input0" class="col-md-2 control-label">所屬欄位</label>
                            <div class="col-md-8">
                                <select name="pid" class="form-control">
                                    <option value="0">最上層欄位</option>
                                    <?PHP
                                        //獲取樹狀結構處理後的所有欄位
                                        foreach ($this->getData('arctype') as $value)
                                        {
                                            echo "<option value='{$value['id']}'>{$value['style']}{$value['name']}</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                提示訊息
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="input1" class="col-md-2 control-label">欄位名稱</label>
                            <div class="col-md-8">
                                <input name="name" type="text" class="form-control" id="input1" placeholder="輸入欄位名稱">
                            </div>
                            <div class="col-md-2">
                                提示訊息
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="input2" class="col-md-2 control-label">URL目錄</label>
                            <div class="col-md-8">
                                <input name="urlname" type="text" class="form-control" id="input2" placeholder="輸入URL目錄">
                            </div>
                            <div class="col-md-2">
                                提示訊息
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="input3" class="col-md-2 control-label">欄位介面</label>
                            <div class="col-md-8">
                                <input name="view" type="text" class="form-control" id="input3" placeholder="輸入欄位介面">
                            </div>
                            <div class="col-md-2">
                                提示訊息
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="input4" class="col-md-2 control-label">SEO標題</label>
                            <div class="col-md-8">
                                <input name="title" type="text" class="form-control" id="input4" placeholder="輸入SEO標題">
                            </div>
                            <div class="col-md-2">
                                提示訊息
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="input5" class="col-md-2 control-label">SEO關鍵字</label>
                            <div class="col-md-8">
                                <input name="keywords" type="text" class="form-control" id="input5" placeholder="輸入SEO關鍵字">
                            </div>
                            <div class="col-md-2">
                                提示訊息
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="input6" class="col-md-2 control-label">SEO描述</label>
                            <div class="col-md-8">
                                <textarea name="description" class="form-control" rows="3" id="input6" placeholder="輸入SEO描述"></textarea>
                            </div>
                            <div class="col-md-2">
                                提示訊息
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="input7" class="col-md-2 control-label">排序</label>
                            <div class="col-md-8">
                                <input name="sort" type="text" class="form-control" id="input7" value="999" placeholder="輸入排序">
                            </div>
                            <div class="col-md-2">
                                提示訊息
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-offset-2 col-md-4">
                                <button type="submit" class="btn btn-primary">&nbsp;&nbsp;送出&nbsp;&nbsp;</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>