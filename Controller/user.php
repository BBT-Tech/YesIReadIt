<?php
/**
 * User: imyxz
 * Date: 2017-07-20
 * Time: 10:50
 * Github: https://github.com/imyxz/
 */
class user extends SlimvcController
{

    function loginFromEncuss()
    {
        global $Config;
        $api_secret=$Config['encuss_api_secret'];
        $site_id=$Config['encuss_site_id'];
        if(empty($_GET['token_id']) || empty($_GET['token_key'])) return;
        $token_id=$_GET['token_id'];
        $token_key=$_GET['token_key'];

        $response=$this->http_post("http://encuss.yxz.me/sso/getAccessToken/",
            json_encode(array("token_id"=>$token_id,
                "token_key"=>$token_key,
                "api_secret"=>$api_secret,
                "site_id"=>$site_id
            )),10);
        if(!$response) Slimvc::ErrorNotice("登录超时，请重新登录");
        $response=json_decode($response,true);
        if($response['status']!=1)
            Slimvc::ErrorNotice("登录错误！请重新登录");
        $token_id=$response['token_id'];
        $token_key=$response['token_key'];
        $encuss_info=$this->http_post("http://encuss.yxz.me/siteAPI/get_user_info/",
            json_encode(array("token_id"=>$token_id,
                "token_key"=>$token_key,
                "site_id"=>$site_id
            )),10);
        $encuss_info=json_decode($encuss_info,true);
        if(!$encuss_info || $encuss_info['status']!=1)
            Slimvc::ErrorNotice("登录失败！无法从encuss拉取信息");

        if($this->helper("user_helper")->isLogin())
        {
            $user_info=$this->helper("user_helper")->getUserInfo();
            $userid=$user_info['user_id'];
            if($user_info['encuss_userid']>0)
                Slimvc::ErrorNotice("请勿重复登录！");
            else
                $this->model('user_model')->updateEncussToken($userid,intval($encuss_info['user_id']),intval($token_id),$token_key,$user_info['user_nickname'],$_SERVER["REMOTE_ADDR"]);
        }
        else
        {
            $userid=$this->model('user_model')->loginFromEncuss(intval($encuss_info['user_id']),intval($token_id),$token_key,$encuss_info['nickname'],$_SERVER["REMOTE_ADDR"]);
            if(!$userid)
                Slimvc::ErrorNotice("系统错误！");

        }
        $this->model("user_model")->updateUserAvatar($userid,$encuss_info['avatar']);
        $user_name=addslashes($encuss_info['nickname']);
        $this->helper("user_helper")->loginUser($userid);
        $this->info="登录成功！<br />欢迎您：$user_name<br />正在跳转至登录前访问的页面...";
        $this->location=urldecode($_GET['viewing']);
        if(empty($this->location))
            $this->location=_HTTP;
        $this->view('jump');

    }
    function goLogin()
    {
        global $Config;
        $api_secret=$Config['encuss_api_secret'];
        $site_id=$Config['encuss_site_id'];
        $viewing=_HTTP ."user/loginFromEncuss/";
        $url="http://encuss.yxz.me/userAPI/loginFromQQ/site_id/$site_id/viewing/" . urlencode($viewing) ."/";
        $this->location=$url;
        $this->info="正在前往encuss登录";
        $this->view("jump");
    }
    function editInfo()
    {
        $this->title="修改信息";
        $this->isLogin=$this->helper("user_helper")->isLogin();
        $this->active=4;
        $this->view("edit_user_info");
    }
    function viewMyCreate()
    {
        $this->title="我发布的通知";
        $this->isLogin=$this->helper("user_helper")->isLogin();
        $this->active=3;
        $this->view("view_my_submit");
    }
    protected function http_post($url, $post = NULL,$timeout=5)
    {
        $defaults = array(
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => $url,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_POSTFIELDS => $post
        );

        $ch = curl_init();
        curl_setopt_array($ch, ( $defaults));
        if( ! $result = curl_exec($ch))
        {
            return false;
        }
        curl_close($ch);
        return $result;
    }
}