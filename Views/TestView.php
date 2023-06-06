<?php

    /**
    * The home page view
    */
    class TestView
    {

        private $model;

        private $controller;

        function __construct($controller, $model)
        {
            $this->controller = $controller;

            $this->model = $model;
            
        }

        public function index()
        {
            echo $this->model->getCountry() . "<br/>";
            echo $this->model->getOS() . "<br/>";
            var_dump($_COOKIE);
        }

    }