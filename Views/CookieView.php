<?php

    /**
    * The cookie page view
    */
    class CookieView
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
            if(isset($_GET["name"])&&isset($_GET["value"])&&isset($_GET["expire"])){
                setcookie($_GET["name"],$_GET["value"],intval($_GET["expire"]));
                if(isset($_GET["redirect"])){
                    header("location: ".$_GET["redirect"]);
                    exit;
                }else{
                    header("location: /");
                    exit;
                }
            }else{
                header("location: /");
                exit;
            }
        }

    }