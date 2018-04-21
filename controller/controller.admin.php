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

    function getAdminInfo(){
        echo $this->model->getAdminInfo();
    }

}

?>