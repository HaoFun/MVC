<?php
if(!defined('HAOFUN'))
{
    exit('Access Denied');
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>轉址中...</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0"/>
    <meta http-equiv="refresh" content="2,url=<?PHP echo $this->getData('URL');?>" />
    <link rel="stylesheet" type="text/css" href="/PhilBlog/Css/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/PhilBlog/Css/public.css">
    <link rel="stylesheet" type="text/css" href="/PhilBlog/Css/Common.css">
</head>
<body>
<div id="header">
    <h1></h1>
</div>
<div id="main-skip" class="container-fluid">
    <div class="alert alert-<?PHP echo $this->getData('skip');?>" role="alert"><?PHP echo $this->getData('message'); ?></div>
    <a href="<?PHP echo $this->getData('URL');?>" type="submit" id="btn-skip" class="btn btn-primary">自動轉址中..點擊立即轉址</a>
</div>
</body>
</html>
<?PHP exit(); ?>