<?PHP
if(!defined('HAOFUN'))
{
    exit('Access Denied');
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?PHP echo $this->title_name['title']; ?></title>
    <?PHP
    foreach ($this->css['css'] as $value)
    {
        echo "<link rel='stylesheet' type='text/css' href='{$value}' />";
    }
    foreach ($this->js['js'] as $value)
    {
        echo "<script type='text/javascript' src='{$value}'></script>";
    }
    ?>

