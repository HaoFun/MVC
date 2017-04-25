<?php
namespace Admin\Controller;
use Common\Controller;
if(!defined('HAOFUN'))
{
    exit('Access Denied');
}
class IndexController extends Controller
{
    public function index()
    {
        var_dump('index1111');
        var_dump($_GET);
    }
}
?>