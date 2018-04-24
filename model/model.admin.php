<?php

class AdminModel extends AgentModel
{
    public function verifyLogin(){
        $pData = getData();
        // $res = $this->mysqlQuery($sql, "all");
        if($pData['username'] && $pData['password']){
        	$newPwd = md5(md5($pData['password'].'chc'));
        	$sqltotal = "select user_id userid,user_name username FROM user_admin  WHERE 1=1 AND user_name= '{$pData['username']}' AND user_pwd= '{$newPwd}' ";
        	$ret = $this->mysqlQuery($sqltotal, "all");
        	if(count($ret) === 1){
                //生成token
                $nowTime = NOW;
                $randNum = rand(1, 99999);
                $token = md5((string)$ret[0]['userid'].$ret[0]['username'].$nowTime.(string)randNum);
                // return to_success($ret[0]);
                if($this->__saveLogin($ret[0]['userid'],$token,$nowTime)){
                	return to_success(array('token'=>$token));
                }else{
                	return to_error('登录失败，请刷新网页重新登录。');
                }
        	}else{
        		return to_error('用户名或密码错误。');
        	}
        }else{
        	return to_error('用户名或密码不合法。');
        }
        // return $res;
    }

    //获取管理员信息
    public function getAdminInfo(){
        $pData = getData();
        $sql = "SELECT user_id,user_name FROM user_admin where user_id in(select user_id from user_login where user_token='{$pData['token']}' AND login_state=1)";
        $ret = $this->mysqlQuery($sql, "all");
        if(count($ret) === 1){
        	return to_success($ret[0]);
        }else{
        	return to_error('无效的token。请重新登录。');
        }
    }

    //获取管理员列表
    public function getAdminList(){
        $pData = getData();
        $filter = '';
        //当前的页码
        $currentPage = $pData['currentPage'] ? (int)$pData['currentPage'] : 1;
        //每页显示的最大条数
        $pageSize = $pData['pageSize'] ? (int)$pData['pageSize'] : 10;
        //搜索条件
        //user_state=-4表示已删除的用户
        $filter = 'AND user_state <> -4 ';

        if($pData['status']){
            $filter .= " AND user_state='{$pData['status']}' ";
        }
        if($pData['searchVal']){
            $filter .= " AND (user_name like '%{$pData['searchVal']}%' OR user_id like '%{$pData['searchVal']}%' OR user_realName like '%{$pData['searchVal']}%' OR user_mobile like '%{$pData['searchVal']}%' OR user_mail like '%{$pData['searchVal']}%' ) ";
        }
        //总条数
        $res['page']['total'] = $this->__getAdminCount($filter);
        //分页查询
        $pageFilter .= " LIMIT " . ($currentPage-1) * $pageSize . "," . $pageSize;
        $sql = "SELECT user_id, user_name, user_state, user_mobile, user_mail, user_realName, c_date, u_date FROM user_admin WHERE 1=1 {$filter} order by 1 desc {$pageFilter}";
        $res['sql'] = $sql;
        $res['items'] = $this->mysqlQuery($sql, "all");
        return to_success($res);
    }

    /*###########################################################
      #################### PRIVATE METHODS ######################
    */###########################################################

    //存储用户的登录token
    private function __saveLogin($uid,$token,$nowTime){
        //登录用户入库
    	$arrData = array(
            "user_id" => $uid,
            "user_token" => $token,
            "login_state" => 1,
            "c_date" => $nowTime,
            "u_date" => $nowTime,
        );
        return $this->mysqlInsert("user_login", $arrData, 'single', true);
    }

    //获取admin总数目
    public function __getAdminCount($filter){
        $sql = "SELECT COUNT(*) total FROM user_admin WHERE 1=1 {$filter}";
        $res = $this->mysqlQuery($sql, "all");
        return $res[0]['total'];
    }

}