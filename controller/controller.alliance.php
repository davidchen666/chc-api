<?php
/**
 * Copyright © 大猩猩
 * Banking api
 * Author 大猩猩
 * Create 18-05-27 20:46
 */
class AllianceController extends Controller
{
    private $model;
    private $_api;
    private $title;
    const M = "Alliance";

    function __construct()
    {
        $this->model = Model::instance(self::M);
    }

    //提交申请书-----前台
    function addAllianceSignUp()
    {
        echo $this->model->addAllianceSignUp();
    }
    /*
    ###########################################
    ############## 后台管理接口 ################
    ###########################################
    */
    //获取报名列表
    function getAllianceRegisterList()
    {
        //先验证用户的token值，后续加上
        echo $this->model->getAllianceRegisterList();
    }

}

?>