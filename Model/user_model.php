<?php
class user_model extends SlimvcModel
{

    function isUserExist($user_name)
    {

        return $this->queryStmt("select user_id from user_info WHERE user_name=? limit 1",
            "s",
            $user_name)->sum() >=1 ;
    }


    function checkUserPassword($username,$password)
    {
        $row=$this->queryStmt("select user_id from user_info WHERE user_name=? and user_password=? limit 1",
            "ss",
            $username,
            $password)->row();
        if($row)
            return $row['user_id'];
        else
            return false;
    }


    function getUserId($username)
    {
        $result=$this->queryStmt("select user_id from user_info where user_name=? limit 1",
            "s",
            $username)->row();
        if(!$result)    return false;
        return $result['user_id'];
    }


    function getUserInfo($userid)
    {
        return $this->queryStmt("select * from user_info where user_id=? limit 1",
            "i",
            $userid)->row();
    }

    function newUser($username,$password,$email,$nickname,$reg_ip)
    {
        if(!$this->queryStmt("insert into user_info set user_name=?,user_password=?,user_nickname=?,user_email=?,reg_time=now(),login_time=now(),reg_ip=?,user_avatar=''",
            "sssss",
            $username,
            $password,
            $nickname,
            $email,
            $reg_ip))
            return false;
        return $this->InsertId;
    }
    function updateUserAvatar($user_id,$user_avatar)
    {
        return $this->queryStmt("update user_info set user_avatar=? where user_id=? limit 1",
            "si",
            $user_avatar,
            $user_id);
    }
    function updateUserNickname($user_id,$nickname)
    {
        return $this->queryStmt("update user_info set user_nickname=? where user_id=? limit 1",
            "si",
            $nickname,
            $user_id);
    }

    function loginFromEncuss($encuss_userid,$encuss_token_id,$encuss_token_key,$user_name,$login_ip)
    {
        $now=time();
        $row=$this->queryStmt("select user_id from user_info where encuss_userid=?",
            "i",
            $encuss_userid)->row();
        if($row)
        {
            $this->queryStmt("update user_info set login_ip=?,login_time=? where user_id=?",
                "sii",
                $login_ip,
                $now,
                $row['user_id']);
            return $row['user_id'];
        }

        $this->queryStmt("insert into user_info set encuss_userid=?,encuss_token_id=?,encuss_token_key=?,user_nickname=?,login_ip=?,login_time=?,user_name=?",
        "iisssis",
            $encuss_userid,
            $encuss_token_id,
            $encuss_token_key,
            $user_name,
            $login_ip,
            $now,
            '_encuss_' . $encuss_userid);
        return $this->InsertId;
    }
    function updateEncussToken($user_id,$encuss_userid,$encuss_token_id,$encuss_token_key,$user_name,$login_ip)
    {
        $now=time();
        return $this->queryStmt("update user_info set encuss_userid=?,encuss_token_id=?,encuss_token_key=?,user_nickname=?,login_ip=?,login_time=? where user_id=?",
            $encuss_userid,
            $encuss_token_id,
            $encuss_token_key,
            $user_name,
            $login_ip,
            $now,
            $user_id);

    }
}