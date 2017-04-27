<?php
if(!defined('HAOFUN'))
{
    exit('Access Denied');
}
?>
<?php
$this->title_name['title']="新增欄位頁面";
$this->css['css']=array('/PhilBlog/Css/bootstrap/css/bootstrap.min.css','/PhilBlog/Css/public.css','/PhilBlog/Css/Common.css');
$this->js['js']=array('/PhilBlog/Js/jquery-3.1.1.js','/PhilBlog/Js/jqueryvalidation/jquery.validate.min.js','/PhilBlog/Js/jqueryvalidation/messages_zh.js');
$this->inc('header');
?>
    <script type="text/javascript">   //jqueryvalidation 插件
        $(function()
        {
            $('#add-type').validate
            ({
                rules:{
                    name:
                    {
                        required:true,
                        rangelength:[1,255]
                    },
                    urlname:
                    {
                        required:true,
                        rangelength:[1,255]
                    },
                    view:
                    {
                        required:true,
                        rangelength:[1,255]
                    },
                    title:
                    {
                        required:true,
                        rangelength:[1,255]
                    },
                    keywords:
                    {
                        required:true,
                        rangelength:[1,255]
                    },
                    description:
                    {
                        required:true,
                        rangelength:[1,500]
                    },
                    sort:
                    {
                        required:true,
                        number:true,
                        range:[0,4294967295]
                    }
                },
                errorPlacement:function(error,element)  //錯誤提示位置
                {
                    $(element).parent().next().html(error);
                },
                highlight:function(element)             //新增CSS效果
                {
                    $(element).parent().addClass('has-error');
                },
                unhighlight:function(element)           //移除CSS效果
                {
                    $(element).parent().removeClass('has-error');
                }
            });
        });
    </script>
</head>
<body>
<div id="header">
    <h1>後台管理</h1>
</div>
<div id="main-body" class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <div class="list-group">
                <a href="/PhilBlog/admin.php/type/index" class="list-group-item">欄位列表</a>
                <a href="/PhilBlog/admin.php/type/add" class="list-group-item active">新增欄位</a>
            </div>
<!--            <div class="list-group">-->
<!--                <a href="#" class="list-group-item">欄位列表</a>-->
<!--                <a href="#" class="list-group-item">添加欄位</a>-->
<!--            </div>-->
        </div>
        <div class="col-md-10">
            <div class="panel panel-default">
                <div class="panel-heading">新增欄位</div>
                <div class="panel-body">
                    <form id="add-type" class="form-horizontal" method="post">
                        <div class="form-group row">
                            <label for="input0" class="col-md-2 control-label">所屬欄位</label>
                            <div class="col-md-8">
                                <select name="pid" class="form-control">
                                    <option value="0">主目錄欄位</option>
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

                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="input1" class="col-md-2 control-label">欄位名稱</label>
                            <div class="col-md-8">
                                <input name="name" type="text" class="form-control" id="input1" placeholder="輸入欄位名稱">
                            </div>
                            <div class="col-md-2">

                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="input2" class="col-md-2 control-label">URL目錄</label>
                            <div class="col-md-8">
                                <input name="urlname" type="text" class="form-control" id="input2" placeholder="輸入URL目錄">
                            </div>
                            <div class="col-md-2">

                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="input3" class="col-md-2 control-label">欄位介面</label>
                            <div class="col-md-8">
                                <input name="view" type="text" class="form-control" id="input3" placeholder="輸入欄位介面">
                            </div>
                            <div class="col-md-2">

                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="input4" class="col-md-2 control-label">SEO標題</label>
                            <div class="col-md-8">
                                <input name="title" type="text" class="form-control" id="input4" placeholder="輸入SEO標題">
                            </div>
                            <div class="col-md-2">

                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="input5" class="col-md-2 control-label">SEO關鍵字</label>
                            <div class="col-md-8">
                                <input name="keywords" type="text" class="form-control" id="input5" placeholder="輸入SEO關鍵字">
                            </div>
                            <div class="col-md-2">

                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="input6" class="col-md-2 control-label">SEO描述</label>
                            <div class="col-md-8">
                                <textarea name="description" class="form-control" rows="3" id="input6" placeholder="輸入SEO描述"></textarea>
                            </div>
                            <div class="col-md-2">

                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="input7" class="col-md-2 control-label">排序</label>
                            <div class="col-md-8">
                                <input name="sort" type="text" class="form-control" id="input7" value="999" placeholder="輸入排序">
                            </div>
                            <div class="col-md-2">

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
<?php
$this->inc('footer');
?>