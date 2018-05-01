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
        //单条
        if($pData['eventsid']){
             $filter .= " AND events_id='{$pData['eventsid']}' ";
        }
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
        // $res['sql'] = $sql;
        $res['items'] = $this->mysqlQuery($sql, "all");
        return to_success($res);
    }

    //添加会议信息
    public function addEvents(){
        $pData = getData();
        //验证数据
        if(!$pData['events_name']){
            return to_error('会议标题不能为空。');
        }
        $arrData = array(
            "events_name" => $pData['events_name'],
            "events_begin_date" => $pData['events_begin_date'],
            "events_end_date" => $pData['events_end_date'],
            "events_city" => $pData['events_city'],
            "events_pic" => $pData['events_pic'],
            "events_menu" => json_encode($pData['events_menu']),
            "events_state" => $pData['events_state'],
            "events_remark" => $pData['events_remark'],
            "create_date" => NOW,
            "update_date" => NOW
        );
        $id = $this->mysqlInsert("events_list", $arrData, 'single', true);
        if($id){
            $id_info = $this->mysqlInsert("events_list_detail", array("events_id" => $id), 'single', true);
            return to_success(array('events_id'=>$id));
        }else {
            return to_error('添加失败');
        }
        
    }

    //编辑会议信息
    public function editEvents(){
        $pData = getData();
        //验证数据
        if(!$pData['events_id']){
            return to_error('操作失败,非法数据，不能获取ID。');
        }
        //查看会议id是否存在
        $filter = " events_id='{$pData['events_id']}' ";
        if($this->__getEventsCount(' AND '.$filter) === 0){
            return to_error('操作失败！该会议id不存在。');
        }else if($this->__getEventsCount(' AND '.$filter) > 1){
            return to_error('操作失败！存在多个会议id');
        }
        $arrData = array(
            "events_name" => $pData['events_name'],
            "events_begin_date" => $pData['events_begin_date'],
            "events_end_date" => $pData['events_end_date'],
            "events_city" => $pData['events_city'],
            "events_pic" => $pData['events_pic'],
            "events_menu" => json_encode($pData['events_menu']),
            "events_state" => $pData['events_state'],
            "events_remark" => $pData['events_remark'],
            "update_date" => NOW
        );
        return to_success($this->mysqlEdit("events_list", $arrData, $filter));
    }

    //获取会议详情-admin
    public function getEventsInfo(){
        $pData = getData();
        $filter = '';
        $query = '*';
        $query = $pData['query'] ? 'events_id,'.$pData['query'] : '*';
        //单条
        if($pData['events_id']){
             $filter .= " AND events_id='{$pData['events_id']}' ";
        }else{
            return '参数不正确！不能获取会议id。';
        }
        $sql = "SELECT {$query} FROM events_list_detail WHERE 1=1 {$filter} order by 1 desc {$pageFilter}";
        // $res['sql'] = $sql;
        $res['items'] = $this->mysqlQuery($sql, "all");
        return to_success($res);
    }

    //编辑会议详情-admin
    public function editEventsInfo(){
        $pData = getData();
        //验证数据
        if(!$pData['events_id']){
            return to_error('操作失败,非法数据，不能获取ID。');
        }
        //查看会议id是否存在
        $filter = " events_id='{$pData['events_id']}' ";
        if($this->__getEventsDetailCount(' AND '.$filter) === 0){
            return to_error('操作失败！该会议id不存在。');
        }else if($this->__getEventsDetailCount(' AND '.$filter) > 1){
            return to_error('操作失败！存在多个会议id');
        }
        if($pData['query']){
            $arrData = array();
            $queryArr =  explode(",",$pData['query']);
            foreach ($queryArr as $k => $v) {
                $arrData[$v] = $pData[$v];
            }
            return to_success($this->mysqlEdit("events_list_detail", $arrData, $filter));
        }else{
            return to_error('不能获取query值');
        }
    }

    //获取会议菜单列表-admin
    public function getEventsMenuList(){
        $pData = getData();
        $sql = "SELECT * FROM events_menu_list";
        // $res['sql'] = $sql;
        $res['items'] = $this->mysqlQuery($sql, "all");
        return to_success($res);
    }

    //获取会议报名列表-admin
    public function getEventsRegisterList(){
        $pData = getData();
        $filter = '';
        //单条
        // if($pData['mediaid']){
        //      $filter .= " AND media_id='{$pData['mediaid']}' ";
        // }
        //当前的页码
        $currentPage = $pData['currentPage'] ? (int)$pData['currentPage'] : 1;
        //每页显示的最大条数
        $pageSize = $pData['pageSize'] ? (int)$pData['pageSize'] : 10;
        //user_state=-4表示已删除的
        // $filter .= 'AND media_state <> -4 ';
        //搜索条件
        if($pData['events_id']){
            $filter .= " AND events_id='{$pData['events_id']}' ";
        }
        //com_id    events_id   com_name    com_Invoices_title  com_duty_num    com_phone   com_fax com_postal_addr com_postal_code com_field   com_from    create_date
        if($pData['searchVal']){
            $filter .= " AND (com_id like '%{$pData['searchVal']}%' OR events_id like '%{$pData['searchVal']}%' OR com_name like '%{$pData['searchVal']}%' OR com_Invoices_title like '%{$pData['searchVal']}%' OR com_phone like '%{$pData['searchVal']}%' OR com_fax like '%{$pData['searchVal']}%' OR com_postal_addr like '%{$pData['searchVal']}%' OR pay_method like '%{$pData['searchVal']}%' OR remark like '%{$pData['searchVal']}%' ) ";
        }
        //总条数
        $res['page']['total'] = $this->__getEventsRegisterCount($filter);
        //分页查询
        $pageFilter .= " LIMIT " . ($currentPage-1) * $pageSize . "," . $pageSize;
        $sql = "SELECT aa.com_id,aa.events_id,aa.com_name,aa.com_Invoices_title,aa.com_duty_num,aa.com_phone,aa.com_fax, aa.com_postal_addr,aa.com_postal_code,aa.com_field,aa.com_from,aa.create_date,aa.update_date, aa.pay_price,aa.pay_method,aa.invoice_state,aa.remark,
            bb.events_name 
            FROM events_com_sign_up AS aa 
            LEFT JOIN events_list AS bb
            ON aa.events_id = bb.events_id
            WHERE 1=1 {$filter} order by 1 desc {$pageFilter}";
        $res['sql'] = $sql;
        $res['items'] = $this->mysqlQuery($sql, "all");
        foreach ($res['items'] as $key => $value) {
             $res['items'][$key]['users']= $this->__getEventsRegisterUsers($value['com_id']);
        }
        return to_success($res);
    }

    //更改会议报名信息-> 报名费用，发票状态，付费渠道，备注信息
    public function editEventsRegister(){
        $pData = getData();
        $rightStateArr = array('1','-1');
        //查看报名是否存在
        $filter = " com_id='{$pData['com_id']}' ";
        if($this->__getEventsRegisterCount(' AND '.$filter) === 0){
            return to_error('操作失败！该报名ID不存在。');
        }else if($this->__getEventsRegisterCount(' AND '.$filter) > 1){
            return to_error('操作失败！数据有误。');
        }
        //检查用户状态值是否合法
        if(!in_array($pData['invoice_state'], $rightStateArr)){
            return to_error('操作失败！非法参数--发票状态。');
        }
        //判断金额是不是数字
        if($pData['pay_price'] && !is_numeric($pData['pay_price'])){
            return to_error('操作失败！付费价格参数不正确。');
        }
        $arrData = array(
            "pay_price" => $pData['pay_price'],
            "pay_method" => $pData['pay_method'],
            "invoice_state" => $pData['invoice_state'],
            "remark" => $pData['remark'],
            "update_date" => NOW
        );
        return to_success($this->mysqlEdit("events_com_sign_up", $arrData, $filter,''));
    }

    /*###########################################################
      #################### PRIVATE METHODS ######################
    */###########################################################

    private function __getEventsCount($filter){
    	$sql = "SELECT COUNT(*) total FROM events_list WHERE 1=1 {$filter}";
        $res = $this->mysqlQuery($sql, "all");
        return $res[0]['total'];
    }

    private function __getEventsDetailCount($filter){
        $sql = "SELECT COUNT(*) total FROM events_list_detail WHERE 1=1 {$filter}";
        $res = $this->mysqlQuery($sql, "all");
        return $res[0]['total'];
    }
    
    private function __getEventsRegisterCount($filter){
        $sql = "SELECT COUNT(*) total FROM events_com_sign_up WHERE 1=1 {$filter}";
        $res = $this->mysqlQuery($sql, "all");
        return $res[0]['total'];
    }

    private function __getEventsRegisterUsers($com_id){
        $sql = "SELECT *  FROM events_user_sign_up WHERE com_id='{$com_id}' ";
        return $this->mysqlQuery($sql, "all");
    }

}