<?php

class IndexModel extends AgentModel
{
    public function verifyLogin(){
        $pData = getData();
        // $res = $this->mysqlQuery($sql, "all");
        if($pData['username'] && $pData['password']){
        	$newPwd = md5(md5($pData['password'].'chc'));
        	$sqltotal = "select id userid,user_name username FROM user_admin  WHERE 1=1 AND user_name= '{$pData['username']}' AND user_pwd= '{$newPwd}' ";
        	$ret = $this->mysqlQuery($sqltotal, "all");
        	if(count($ret) === 1){
        		return to_success($ret[0]);
        	}else{
        		return to_error('用户名或密码错误。');
        	}
        }else{
        	return to_error('用户名或密码不合法。');
        }
        // return $res;
    }

}