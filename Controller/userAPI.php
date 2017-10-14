<?php
/**
 * User: imyxz
 * Date: 2017-10-14
 * Time: 12:44
 * Github: https://github.com/imyxz/
 */
class userAPI extends SlimvcController{
    function getUnreadNotices()
    {
        try {
            if($this->helper("user_helper")->isLogin()==false)  throw new Exception("请先登录");
            $user_id=$this->helper("user_helper")->getUserID();
            /** @var group_model $group_model */
            $group_model=$this->model("group_model");
            /** @var notice_model $notice_model */
            $notice_model=$this->model("notice_model");
            $notices=$notice_model->getUserUnreadNotice($user_id);
            $groups=array();
            foreach($notices as $one)
            {
                $groups[$one['group_id']]=array();
            }
            unset($one);
            foreach($groups as $key=>&$one)
            {
                $group_info=$group_model->getGroupInfo($key);
                $one=$group_info;
            }
            unset($one);
            $return['notices']=$notices;
            $return['groups']=$groups;
            $return['status'] = 0;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }
    }
    function getReadNotices()
    {
        try {
            if($this->helper("user_helper")->isLogin()==false)  throw new Exception("请先登录");
            $user_id=$this->helper("user_helper")->getUserID();
            /** @var group_model $group_model */
            $group_model=$this->model("group_model");
            /** @var notice_model $notice_model */
            $notice_model=$this->model("notice_model");
            $page=intval(@$_GET['page']);
            if($page<1) $page=1;
            $notices=$notice_model->getUserReadNotice($user_id,$page,7);
            $return['notices']=$notices;
            $return['status'] = 0;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }
    }
    function replyNotice()
    {
        try {
            if($this->helper("user_helper")->isLogin()==false)  throw new Exception("请先登录");
            $user_id=$this->helper("user_helper")->getUserID();
            /** @var group_model $group_model */
            $group_model=$this->model("group_model");
            /** @var notice_model $notice_model */
            $notice_model=$this->model("notice_model");

            $json=$this->getRequestJson();
            $notice_id=intval(@$json['notice_id']);
            $answer=trim(@$json['answer']);
            if($notice_model->isUserInNotice($user_id,$notice_id)==false)   throw new Exception("您无权限读取此通知");
            if(!$notice_model->setUserReadNotice($user_id,$notice_id,$answer))  throw new Exception("系统出错！");
            $return['status'] = 0;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }
    }
    function getPrivilegeGroups()
    {
        try {
            if($this->helper("user_helper")->isLogin()==false)  throw new Exception("请先登录");
            $user_id=$this->helper("user_helper")->getUserID();
            /** @var group_model $group_model */
            $group_model=$this->model("group_model");
            /** @var notice_model $notice_model */
            $notice_model=$this->model("notice_model");
            $return['groups']=$group_model->getUserPrivilegeGroups($user_id);
            $return['status'] = 0;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }
    }
    function getAllGroups()
    {
        try {
            if($this->helper("user_helper")->isLogin()==false)  throw new Exception("请先登录");
            $user_id=$this->helper("user_helper")->getUserID();
            /** @var group_model $group_model */
            $group_model=$this->model("group_model");
            /** @var notice_model $notice_model */
            $notice_model=$this->model("notice_model");
            $return['groups']=$group_model->getAllGroups();
            $return['status'] = 0;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }
    }
    function getUserInfo()
    {
        try {
            if($this->helper("user_helper")->isLogin()==false)  throw new Exception("请先登录");
            $user_id=$this->helper("user_helper")->getUserID();
            /** @var group_model $group_model */
            $group_model=$this->model("group_model");
            /** @var notice_model $notice_model */
            $notice_model=$this->model("notice_model");

            $return['groups']=$group_model->getUserGroups($user_id);
            $user_info=$this->helper("user_helper")->getUserInfo();
            $return['user_info']=array("user_avatar"=>$user_info['user_avatar'],
                "user_nickname"=>$user_info['user_nickname']);
            $return['status'] = 0;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }
    }
    function saveUserInfo()
    {
        try {
            if($this->helper("user_helper")->isLogin()==false)  throw new Exception("请先登录");
            $user_id=$this->helper("user_helper")->getUserID();
            /** @var group_model $group_model */
            $group_model=$this->model("group_model");
            /** @var notice_model $notice_model */
            $notice_model=$this->model("notice_model");

            $json=$this->getRequestJson();
            $user_nickname=trim($json['user_nickname']);
            $update_groups=$json['groups'];
            foreach($update_groups as &$one)
            {
                $one=intval($one);
            }
            unset($one);
            $update_groups=array_unique($update_groups);

            $groups=$group_model->getUserGroups($user_id);
            $cur_groups=array();
            foreach($groups as $one)
            {
                $cur_groups[]=$one['group_id'];
            }
            $groups=$group_model->getAllGroups();
            $all_groups=array();
            foreach($groups as $one)
            {
                $all_groups[]=$one['group_id'];
            }

            foreach($cur_groups as $one)
            {
                if(!in_array($one,$update_groups))
                    $group_model->delUserGroupRelation($user_id,$one);
            }
            foreach($update_groups as $one)
            {
                if(!in_array($one,$cur_groups) && in_array($one,$all_groups))
                    $group_model->addUserGroupRelation($user_id,$one);
            }
            $this->helper("user_helper")->updateUserNickname($user_id,$user_nickname);
            $return['status'] = 0;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }
    }
    function getMyCreateNotice()
    {
        try {
            if($this->helper("user_helper")->isLogin()==false)  throw new Exception("请先登录");
            $user_id=$this->helper("user_helper")->getUserID();
            /** @var group_model $group_model */
            $group_model=$this->model("group_model");
            /** @var notice_model $notice_model */
            $notice_model=$this->model("notice_model");
            $page=intval(@$_GET['page']);
            if($page<1) $page=1;
            $notices=$notice_model->getUserCreateNotice($user_id,$page,30);
            $return['notices']=$notices;
            $return['status'] = 0;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }
    }


}