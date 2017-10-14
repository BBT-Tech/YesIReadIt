<?php
/**
 * User: imyxz
 * Date: 2017-10-14
 * Time: 12:13
 * Github: https://github.com/imyxz/
 */
class group_model extends SlimvcModel
{
    function addGroup($group_name,$group_desc)
    {
        if(!$this->queryStmt("insert into group_info set group_name=?,group_desc=?,people_count=0",
            "ss",
            $group_name,
            $group_desc))   return false;
        return $this->InsertId;
    }
    function getGroupPeople($group_id)
    {
        return $this->queryStmt("select user_info.* from user_info,user_group_relation where user_group_relation.group_id=? and user_info.user_id=user_group_relation.user_id",
            "i",
            $group_id)->all();
    }
    function getGroupInfo($group_id)
    {
        return $this->queryStmt("select * from group_info where group_id=?",
            "i",
            $group_id)->row();
    }
    function addNoticeGroupRelation($notice_id,$group_id)
    {
        return $this->queryStmt("insert into notice_group_relation set notice_id=?,group_id=?,bind_time=now() on duplicate key update bind_time=now()",
            "ii",
            $notice_id,
            $group_id);
    }
    function getNoticeGroups($notice_id)
    {
        return $this->queryStmt("select * from group_info,notice_group_relation where notice_group_relation.notice_id=? and group_info.group_id=notice_group_relation.group_id",
            "i",
            $notice_id)->all();
    }
    function delNoticeGroupRelation($notice_id,$group_id)
    {
        return $this->queryStmt("delete from notice_group_relation where notice_id=? and group_id=?",
            "ii",
            $notice_id,
            $group_id);
    }

    function addUserGroupRelation($user_id,$group_id)
    {
        if(!$this->queryStmt("update group_info set people_count=people_count+1 where group_id=?",
            "i",
            $group_id)) return false;
        return $this->queryStmt("insert into user_group_relation set user_id=?,group_id=?,bind_time=now() on duplicate key update bind_time=now()",
            "ii",
            $user_id,
            $group_id);
    }
    function getUserGroups($user_id)
    {
        return $this->queryStmt("select * from group_info,user_group_relation where user_group_relation.user_id=? and group_info.group_id=user_group_relation.group_id",
            "i",
            $user_id)->all();
    }
    function getUserPrivilegeGroups($user_id)
    {
        return $this->queryStmt("select * from user_group_privilege,group_info where user_group_privilege.user_id=? and group_info.group_id=user_group_privilege.group_id",
            "i",
            $user_id)->all();
    }
    function delUserGroupRelation($user_id,$group_id)
    {
        if(!$this->queryStmt("update group_info set people_count=people_count-1 where group_id=?",
            "i",
            $group_id)) return false;
        return $this->queryStmt("delete from user_group_relation where user_id=? and group_id=?",
            "ii",
            $user_id,
            $group_id);
    }
    function getAllGroups()
    {
        return $this->query("select * from group_info")->all();
    }
}