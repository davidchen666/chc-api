<?php

class HotelModel extends AgentModel
{
    
    //获取酒店列表
    public function getHotelList(){
        $pData = getData();
        $filter = '';
        //当前的页码
        $currentPage = $pData['currentPage'] ? (int)$pData['currentPage'] : 1;
        //每页显示的最大条数
        $pageSize = $pData['pageSize'] ? (int)$pData['pageSize'] : 10;
        //user_state=-4表示已删除的
        $filter = 'AND hotel_state <> -4 ';
        //搜索条件
        if($pData['status']){
            $filter .= " AND hotel_state='{$pData['status']}' ";
        }
        if($pData['searchVal']){
            $filter .= " AND (hotel_name like '%{$pData['searchVal']}%' OR hotel_info like '%{$pData['searchVal']}%' OR arrive_info like '%{$pData['searchVal']}%' OR hotel_remark like '%{$pData['searchVal']}%' ) ";
        }
        //总条数
        $res['page']['total'] = $this->__getHotelCount($filter);
        //分页查询
        $pageFilter .= " LIMIT " . ($currentPage-1) * $pageSize . "," . $pageSize;
        $sql = "SELECT hotel_id, hotel_name, hotel_state, hotel_info, hotel_pic, arrive_info, arrive_pic, c_date, u_date FROM events_hotel WHERE 1=1 {$filter} order by 1 desc {$pageFilter}";
        $res['sql'] = $sql;
        $res['items'] = $this->mysqlQuery($sql, "all");
        return to_success($res);
    }

    //添加酒店
    public function addHotel(){
        $pData = getData();
        //验证数据
        if(!$pData['hotelname']){
            return to_error('酒店名称不能为空。');
        }
        $arrData = array(
            "hotel_name" => $pData['hotelname'],
            "hotel_state" => 1,
            "hotel_info" => $pData['hotelinfo'],
            "hotel_pic" => json_encode($pData['hotelpic']),
            "arrive_info" => $pData['arriveinfo'],
            "arrive_pic" => $pData['arrivepic'],
            "hotel_remark" => $pData['remark'],
            "c_date" => NOW,
            "u_date" => NOW
        );
        return to_success($this->mysqlInsert("events_hotel", $arrData, 'single', true));
    }

    //编辑酒店
    public function editHotel(){
        $pData = getData();
        //验证数据
        if(!$pData['hotelid']){
            return to_error('操作失败,非法数据，不能获取酒店ID。');
        }
        //查看用户是否存在
        $filter = " hotel_id='{$pData['hotelid']}' ";
        if($this->__getHotelCount(' AND '.$filter) === 0){
            return to_error('操作失败！该酒店不存在。');
        }else if($this->__getHotelCount(' AND '.$filter) > 1){
            return to_error('操作失败！存在多个酒店。');
        }
        $arrData = array(
            "hotel_name" => $pData['hotelname'],
            "hotel_state" => $pData['state'],
            "hotel_info" => $pData['hotelinfo'],
            "hotel_pic" => json_encode($pData['hotelpic']),
            "arrive_info" => $pData['arriveinfo'],
            "arrive_pic" => $pData['arrivepic'],
            "hotel_remark" => $pData['remark'],
            "c_date" => NOW,
            "u_date" => NOW
        );
        return to_success($this->mysqlEdit("events_hotel", $arrData, $filter));
    }

    //更改酒店状态
    public function editHotelState(){
        $pData = getData();
        $rightStateArr = array('1','-1','-4');
        //查看用户是否存在
        $filter = " hotel_id='{$pData['hotelid']}' ";
        if($this->__getHotelCount(' AND '.$filter) === 0){
            return to_error('操作失败！该酒店不存在。');
        }else if($this->__getHotelCount(' AND '.$filter) > 1){
            return to_error('操作失败！非法用户。');
        }
        //检查用户状态值是否合法
        if(!in_array($pData['state'], $rightStateArr)){
            return to_error('操作失败！非法状态值。');
        }
        $arrData = array(
            "hotel_state" => $pData['state'],
            "u_date" => NOW
        );
        return to_success($this->mysqlEdit("events_hotel", $arrData, $filter));
    }

    //上传图片
    public function uploadFile(){
        $file = $_FILES['file'];
        $name = $file['name'];
        $type = $file['type'];
        $size = $file['size'];
        $tmp_name = $file['tmp_name'];
        $url = dirname(dirname(__FILE__))."/uploads/hotel/";//文件路径
        $tpname = substr(strrchr($name,'.'),1);//获取文件后缀
        $pre_str = date("YmdHis",time()).'-'.rand(1,99999);
        // $tmp_url = $url.$pre_str.$name;
        //重新生成不包含中文的文件名称（要求不重合）
        $name = $pre_str.'.'.$tpname;
        $tmp_url = $url.$pre_str.$name;
        // date("YmdHis",time())+rand(1,99999);
        $types = array('jpg','png','jpeg','bmp','gif');
        $filesize = 1024 * 1024 * 100;
        if($size > $filesize){
            //              echo "<script>alert('退出成功!');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
            // echo "'文件过大!";
            echo to_error("文件过大!");
            exit;
        }else if(!in_array($tpname,$types)){
            // echo "文件类型不符合!";
            echo to_error("文件类型不符合!");
            exit;
        }else if(!move_uploaded_file($tmp_name,$tmp_url)){
            // echo "移动文件失败!";
            var_dump($tmp_name,$tmp_url);
            echo to_error("移动文件失败!(请检查文件名是否合法)");
            exit;
        }else{
            move_uploaded_file($tmp_name,$tmp_url);
            $size = round($size/1024/1024,2); //转换成Mb
            $upload = array('size' => $size, 'url' => $tmp_url, 'name' => $name,'newname'=>$pre_str.$name, 'type' => $tpname);
            // var_dump($upload);
            // return $upload;
            echo to_success($upload);
        }
    }
    /*###########################################################
      #################### PRIVATE METHODS ######################
    */###########################################################

    //获取酒店总数目
    private function __getHotelCount($filter){
        $sql = "SELECT COUNT(*) total FROM events_hotel WHERE 1=1 {$filter}";
        $res = $this->mysqlQuery($sql, "all");
        return (int)$res[0]['total'];
    }

}