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
    function addRSignUp()
    {
        echo $this->model->addRSignUp();
    }
    
}

?>