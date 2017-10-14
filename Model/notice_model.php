<?php
/**
 * User: imyxz
 * Date: 2017-10-14
 * Time: 10:52
 * Github: https://github.com/imyxz/
 */
class notice_model extends SlimvcModel
{
    function getUserUnreadNotice($user_id)
    {
        return $this->queryStmt("select  ni.*,ng.group_id
                                  from notice_info as ni,user_group_relation as ug,notice_group_relation as ng
                                  where ug.user_id=?
                                    and ng.group_id=ug.group_id
                                    and ni.notice_id=ng.notice_id
                                    and ni.notice_pub_time<=now()
                                    and ni.notice_end_time>=now()
                                    and not EXISTS (select ns.notice_id from user_notice_status as ns where ns.user_id=? and ns.notice_id=ni.notice_id)
                                  group by ni.notice_id
                                  order by ng.group_id asc , ni.notice_id desc",
            "ii",
            $user_id,
            $user_id)->all();
        /*
         * select  ni.*,ug.group_id from notice_info as ni
                                  inner join user_group_relation as ug on ug.user_id=?,
                                  inner join notice_group_relation as ng on ng.group_id=ug.group_id
                                  where ni.notice_id=ng.notice_id and not EXISTS (select ns.notice_id from user_notice_status as ns where ns.user_id=? and ns.notice_id=ni.notice_id)
                                  order by ng.group_id asc , ni.notice_id desc
         */
    }
    function getUserReadNotice($user_id,$page,$limit)
    {
        $page--;
        if($page>0)
            $start=$page*$limit;
        else
            $start=0;
        return $this->queryStmt("select notice_info.* from notice_info,user_notice_status where user_notice_status.user_id=? and notice_info.notice_id=user_notice_status.notice_id order by user_notice_status.answer_time desc limit ?,?",
                "iii",
                $user_id,
                $start,
                $limit)->all();
    }
    function getUserCreateNotice($user_id,$page,$limit)
    {
        $page--;
        if($page>0)
            $start=$page*$limit;
        else
            $start=0;
        return $this->queryStmt("select notice_info.* from notice_info where pub_user_id=? order by notice_id desc limit ?,?",
            "iii",
            $user_id,
            $start,
            $limit)->all();
    }
    function getNoticeUnreadUser($notice_id)
    {
        return $this->queryStmt("select ui.user_nickname
                                  from notice_info as ni,user_group_relation as ug,notice_group_relation as ng,user_info as ui
                                  where ni.notice_id=?
                                    and ng.notice_id=ni.notice_id
                                    and ug.group_id=ng.group_id
                                    and not EXISTS (select ns.user_id from user_notice_status as ns where ns.notice_id=? and ns.user_id=ug.user_id)
                                    and ui.user_id=ug.user_id
                                  group by ui.user_id",
            "ii",
            $notice_id,
            $notice_id)->all();
    }
    function newNotice($pub_user_id,$notice_title,$notice_content,$notice_pub_time,$notice_end_time)
    {
        if(!$this->queryStmt("insert into notice_info set pub_user_id=?,
                              notice_title=?,
                              notice_content=?,
                              notice_pub_time=from_unixtime(?),
                              notice_end_time=from_unixtime(?)",
            "issii",
            $pub_user_id,
            $notice_title,
            $notice_content,
            $notice_pub_time,
            $notice_end_time))
            return false;
        return $this->InsertId;
    }
    function delNotice($notice_id)
    {
        if(!$this->queryStmt("delete from notice_group_relation where notice_id=?",
            "i",
            $notice_id))return false;
        return $this->queryStmt("delete from notice_info where notice_id=?",
            "i",
            $notice_id);
    }
    function getNoticeInfo($notice_id)
    {
        return $this->queryStmt("select * from notice_info where notice_id=?",
            "i",
            $notice_id)->row();
    }
    function setUserReadNotice($user_id,$notice_id,$answer)
    {
        return $this->queryStmt("insert into user_notice_status set user_id=?,notice_id=?,answer_info=?,answer_time=now() on duplicate key update answer_info=?,answer_time=now()",
            "iiss",
            $user_id,
            $notice_id,
            $answer,
            $answer);
    }
    function getNoticeAnswers($notice_id)
    {
        return $this->queryStmt("select user_notice_status.*,user_info.user_nickname,user_info.user_avatar from user_notice_status,user_info where user_notice_status.notice_id=? and user_info.user_id=user_notice_status.user_id order by user_notice_status.answer_time desc","i",$notice_id)->all();
    }
    function isUserInNotice($user_id,$notice_id)
    {
        return $this->queryStmt("select ni.notice_id
                                  from notice_info as ni,user_group_relation as ug,notice_group_relation as ng
                                  where ug.user_id=?
                                    and ni.notice_id=?
                                    and ng.group_id=ug.group_id
                                    and ni.notice_id=ng.notice_id
                                  limit 1",
            "ii",
            $user_id,
            $notice_id)->sum()>0;
    }
    function isUserHasNoticePrivilege($user_id,$notice_id)
    {
        return $this->queryStmt("select ni.notice_id
                                  from notice_info as ni,user_group_privilege as ug,notice_group_relation as ng
                                  where ug.user_id=?
                                    and ni.notice_id=?
                                    and ng.group_id=ug.group_id
                                    and ni.notice_id=ng.notice_id
                                  limit 1",
            "ii",
            $user_id,
            $notice_id)->sum()>0;
    }
    function getNoticeGroupsName($notice_id)
    {
        return $this->queryStmt("select group_info.group_name from notice_group_relation,group_info where notice_group_relation.notice_id=? and group_info.group_id=notice_group_relation.group_id",
            "i",
            $notice_id)->all();
    }


}