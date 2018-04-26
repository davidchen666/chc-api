<?php
/**
 * Copyright © 大猩猩
 * events api
 * Author 大猩猩
 * Create 18-02-22 14:34
 */
class EventsController extends Controller
{
    private $model;
    private $_api;
    private $title;
    const M = "Events";

    function __construct()
    {
        $this->model = Model::instance(self::M);
    }

    /**
     * add
     */
    function addMSignUp()
    {
        echo $this->model->addMSignUp();
    }

    //路演报名
    function addRSignUp()
    {
        echo $this->model->addRSignUp();
    }

    /*
    ###########################################
    ############## 后台管理接口 ################
    ###########################################
    */
    //获取会议列表
    function getEventsList()
    {
        //先验证用户的token值，后续加上
        echo $this->model->getEventsList();
    }
}

?>