<?php

class IndexModel extends AgentModel
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
                if($this->saveLogin($ret[0]['userid'],$token,$nowTime)){
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

    //存储用户的登录token
    private function saveLogin($uid,$token,$nowTime){
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

}