<?php
/**
 * User: imyxz
 * Date: 2017-10-14
 * Time: 16:35
 * Github: https://github.com/imyxz/
 */
class notice extends SlimvcController
{
    function read()
    {
        $this->title="查看通知";
        $this->notice_id=intval($_GET['id']);
        $this->isLogin=$this->helper("user_helper")->isLogin();
        $this->active=1;
        $this->view("view_notice");
    }
    function add()
    {
        $this->title="发布通知";
        $this->notice_id=intval($_GET['id']);
        $this->isLogin=$this->helper("user_helper")->isLogin();
        $this->active=2;
        $this->view("add_notice");
    }
}