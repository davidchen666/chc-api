<?php
/**
 * Copyright © 大猩猩
 * SDK中间api类
 * Author 大猩猩
 * Create 18-02-22 14:34
 */
class AdminController extends Controller
{
    private $model;
    private $_api;
    private $title;
    const M = "Admin";

    function __construct()
    {
        $this->model = Model::instance(self::M);
    }

    function verifyLogin(){
        echo $this->model->verifyLogin();
    }

    //验证token
    //获取管理员信息
    function getAdminInfo(){
        echo $this->model->getAdminInfo();
    }

    //验证token
    //获取管理员列表
    function getAdminList(){
        echo $this->model->getAdminList();
    }

}

?>