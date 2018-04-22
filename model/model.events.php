<?php
class EventsModel extends AgentModel
{
    //报名
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

    //路演报名
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

    //获取会议列表-admin
    public function getEventsList(){
        $pData = getData();
        $filter = '';
        //当前的页码
        $currentPage = $pData['currentPage'] ? (int)$pData['currentPage'] : 1;
        //每页显示的最大条数
        $pageSize = $pData['pageSize'] ? (int)$pData['pageSize'] : 10;
        //搜索条件
        if($pData['eventStatus']){
        	$filter .= " AND events_state='{$pData['eventStatus']}' ";
        }
        if($pData['searchVal']){
        	$filter .= " AND (events_id like '%{$pData['searchVal']}%' OR events_name like '%{$pData['searchVal']}%' OR events_date like '%{$pData['searchVal']}%' OR events_city like '%{$pData['searchVal']}%' OR events_url like '%{$pData['searchVal']}%') ";
        }
        //总条数
        $res['page']['total'] = $this->__getEventsCount($filter);
        //分页查询
        $pageFilter .= " LIMIT " . ($currentPage-1) * $pageSize . "," . $pageSize;
        $sql = "SELECT * FROM events_list WHERE 1=1 {$filter} order by 1 desc {$pageFilter}";
        $res['sql'] = $sql;
        $res['items'] = $this->mysqlQuery($sql, "all");
        return to_success($res);
    }

    private function __getEventsCount($filter){
    	$sql = "SELECT COUNT(*) total FROM events_list WHERE 1=1 {$filter}";
        $res = $this->mysqlQuery($sql, "all");
        return $res[0]['total'];
    }

}