<?php
class EventsModel extends AgentModel
{
    public function addMSignUp(){
    	$pData = getData();
    	if(!$pData || !$pData['uData']){
    		return to_error('缺少参数');
    	}
    	$nowTime  = NOW;
    	//添加公司信息
    	$arrData = array(
            "com_name" => "{$pData['com_name']}",
            "com_Invoices_title" => "{$pData['com_Invoices_title']}",
            "com_duty_num" => "{$pData['com_duty_num']}",
            "com_phone" => "{$pData['com_phone']}",
            "com_fax" => "{$pData['com_fax']}",
            "com_postal_addr" => "{$pData['com_postal_addr']}",
            "com_postal_code" => "{$pData['com_postal_code']}",
            "com_field" => "{$pData['com_field']}",
            "com_from" => "{$pData['from']}",
            "c_date" => "{$nowTime}"
        );
        $cid = $this->mysqlInsert("events_com_sign_up", $arrData, 'single', true);
        $res['cid'] = $cid;
        //添加用户信息
        foreach ($pData['uData'] as $k => $v) {
        	$arrUser = array(
        		'com_id'=>$cid,
        		'user_name'=>$v['uname'],
        		'user_job'=>$v['ujob'],
        		'user_mobile'=>$v['umobile'],
        		'user_email'=>$v['uemail'],
        		"c_date" => "{$nowTime}"
        	);
        	$uid = $this->mysqlInsert("events_user_sign_up", $arrUser, 'single', true);
        	$res['user'][] = $uid;
        }
        return to_success($res);
    }

    public function addRSignUp(){
    	$pData = getData();
    	if(!$pData){
    		return to_error('缺少参数');
    	}
    	$nowTime  = NOW;
    	//添加公司信息
    	$arrData = array(
            "com_name" => "{$pData['cname']}",
            "user_name" => "{$pData['uname']}",
            "user_job" => "{$pData['ujob']}",
            "user_email" => "{$pData['uemail']}",
            "user_mobile" => "{$pData['umobile']}",
            "file_name" => "{$pData['fname']}",
            "c_date" => "{$nowTime}"
        );
        $cid = $this->mysqlInsert("events_road_show_sign_up", $arrData, 'single', true);
        $res['cid'] = $cid;
        return to_success($res);
    }

}