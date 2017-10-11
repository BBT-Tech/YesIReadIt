<?php
/**
 * User: imyxz
 * Date: 2017/5/26
 * Time: 15:32
 * Github: https://github.com/imyxz/
 */
class user_helper extends SlimvcHelper
{
    private $user_id;
    private $user_info;
    private $is_login;
    function __construct()
    {
        $this->user_id=$this->helper("session_helper")->get("user_id");
        if(empty($this->user_id))
        {
            $this->user_id=0;
            $this->is_login=false;
            $this->user_info=array();
        }
        else
        {
            $this->is_login=true;
            $this->user_info=$this->model("user_model")->getUserInfo($this->user_id);
        }
    }
    function getUserInfo()
    {
        return $this->user_info;
    }
    function getUserID()
    {
        return $this->user_id;
    }
    function isLogin()
    {
        return $this->is_login;
    }
    function newUser($username,$password,$email,$nickname,$reg_ip)
    {
        return $this->model("user_model")->newUser($username,$password,$email,$nickname,$reg_ip);
    }
    function checkUserPassword($username,$password)
    {
        return $this->model("user_model")->checkUserPassword($username,$password);
    }
    function updateUserNickname($user_id=0,$nickname)
    {
        if($user_id==0)
            $user_id=$this->user_id;
        return $this->model("user_model")->updateUserNickname($user_id,$nickname);
    }
    function updateUserAvatar($user_id=0,$avatar)
    {
        if($user_id==0)
            $user_id=$this->user_id;
        return $this->model("user_model")->updateUserAvatar($user_id,$avatar);
    }

    function loginUser($userid)
    {
        $this->helper("session_helper")->set("user_id",$userid);
        $this->is_login=true;
        $this->user_id=$userid;
        $this->user_info=$this->model("user_model")->getUserInfo($userid);
    }


    function logoutUser($userid=0)
    {

        if($userid==0)
            $userid=$this->user_id;
        if($this->is_login=true && $userid>0)
        {
            $this->helper("session_helper")->destroySession();
            $this->user_id=0;
            $this->is_login=false;
            $this->user_info=array();
        }
    }
    function isPlayer()
    {
        return $this->isLogin()&&$this->user_info['player_id']>0;
    }
    function getPlayerID()
    {
        return $this->user_info['player_id'];
    }
}