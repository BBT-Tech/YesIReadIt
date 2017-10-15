<?php
/**
 * User: imyxz
 * Date: 2017-10-14
 * Time: 12:38
 * Github: https://github.com/imyxz/
 */
class noticeAPI extends SlimvcController
{
    function getNoticeInfo()
    {
        try {
            if($this->helper("user_helper")->isLogin()==false)  throw new Exception("请先登录");
            $user_id=$this->helper("user_helper")->getUserID();
            /** @var group_model $group_model */
            $group_model=$this->model("group_model");
            /** @var notice_model $notice_model */
            $notice_model=$this->model("notice_model");
            $notice_id=intval(@$_GET['id']);
            if(!($notice_model->isUserInNotice($user_id,$notice_id)|| $notice_model->isUserHasNoticePrivilege($user_id,$notice_id)))   throw new Exception("您无权限读取此通知");
            $return['notice_info']=$notice_model->getNoticeInfo($notice_id);
            unset($return['notice_info']['notice_markdown']);
            $groups=$notice_model->getNoticeGroupsName($notice_id);
            $return['groups']=array();
            foreach($groups as $one)
            {
                $return['groups'][]=$one['group_name'];
            }
            $return['status'] = 0;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }
    }
    function getNoticeAnswerStatus()
    {
        try {
            if($this->helper("user_helper")->isLogin()==false)  throw new Exception("请先登录");
            $user_id=$this->helper("user_helper")->getUserID();
            /** @var group_model $group_model */
            $group_model=$this->model("group_model");
            /** @var notice_model $notice_model */
            $notice_model=$this->model("notice_model");
            $notice_id=intval(@$_GET['id']);
            if(!($notice_model->isUserInNotice($user_id,$notice_id)|| $notice_model->isUserHasNoticePrivilege($user_id,$notice_id)))   throw new Exception("您无权限读取此通知");
            $return['notice_answers']=$notice_model->getNoticeAnswers($notice_id);
            $return['unread']=array();
            $unreads=$notice_model->getNoticeUnreadUser($notice_id);
            foreach($unreads as $one)
            {
                $return['unread'][]=$one['user_nickname'];
            }
            $return['status'] = 0;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }
    }
    function newNotice()
    {
        require _Root . 'vendor/autoload.php';
        try {
            if($this->helper("user_helper")->isLogin()==false)  throw new Exception("请先登录");
            $user_id=$this->helper("user_helper")->getUserID();
            /** @var group_model $group_model */
            $group_model=$this->model("group_model");
            /** @var notice_model $notice_model */
            $notice_model=$this->model("notice_model");
            $json=$this->getRequestJson();
            $notice_title=trim($json['notice_title']);
            $notice_markdown=trim($json['notice_markdown']);
            $notice_groups=$json['notice_groups'];
            $notice_end_time=intval($json['notice_end_time']);
            if(empty($notice_title) || empty($notice_markdown) ||count($notice_groups)==0 ||$notice_end_time<time() )
                throw new Exception("相关信息填写错误！");
            $groups=$group_model->getUserPrivilegeGroups($user_id);
            $ok_groups=array();
            foreach($groups as $one)
            {
                $ok_groups[]=$one['group_id'];
            }
            foreach($notice_groups as &$one)
            {
                $one=intval($one);
                if(!in_array($one,$ok_groups))throw new Exception("您无权在某个组创建通知！");
            }
            $notice_groups=array_unique($notice_groups);
            unset($one);
            $mardown_parser=new Parsedown();
            $html=$mardown_parser->parse($notice_markdown);
            if(!$html)
                throw new Exception("无法解析markdown");
            $purifier = new HTMLPurifier();
            $clean_html = $purifier->purify($html);
            if(!($notice_id=$notice_model->newNotice($user_id,$notice_title,$clean_html,$notice_markdown,time(),$notice_end_time)))
                throw new Exception("系统出错！");
            foreach($notice_groups as $one)
            {
                $group_model->addNoticeGroupRelation($notice_id,$one);
            }
            $return['notice_id']=$notice_id;
            $return['status'] = 0;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }
    }
}