<?php
/**
 * User: imyxz
 * Date: 2017-10-14
 * Time: 14:28
 * Github: https://github.com/imyxz/
 */
class indexs extends SlimvcController
{
    function IndexAction()
    {
        $this->title="主页";
        $this->isLogin=$this->helper("user_helper")->isLogin();
        $this->active=1;
        $this->view("user_unread");
    }
}