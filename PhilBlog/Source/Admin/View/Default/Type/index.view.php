<?php
if(!defined('HAOFUN'))
{
    exit('Access Denied');
}
?>
<?php
$this->title_name['title']="欄位頁面";
$this->css['css']=array('/PhilBlog/Css/bootstrap/css/bootstrap.min.css','/PhilBlog/Css/public.css','/PhilBlog/Css/Common.css');
$this->js['js']=array('/PhilBlog/Js/jquery-3.1.1.js','/PhilBlog/Js/jqueryvalidation/jquery.validate.min.js','/PhilBlog/Js/jqueryvalidation/messages_zh.js');
$this->inc('header');
?>
</head>
<body>
<div id="header">
    <h1>欄位頁面 index....</h1>
</div>
<div id="main-body" class="container-fluid">

</div>
<?php
$this->inc('footer');
?>
