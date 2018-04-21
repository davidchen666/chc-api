<?php

/**
 * Created by PhpStorm.
 * User: hyanwang
 * Date: 2015/10/8
 * Time: 11:25
 */
class TestController extends Controller
{
	// const M = "Events";
    function __construct()
    {
    	$this->model = Model::instance(self::M);
    }

    public function index()
    {
        $data = array();
        View::instance('test/index.tpl')->show($data);
    }

    public function getEventsList()
    {
    	echo 123;
        //先验证用户的token值，后续加上
        // echo $this->model->getEventsList();
    }
}
