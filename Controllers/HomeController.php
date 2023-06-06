<?php

    /**
    * The home page controller
    */
    class HomeController
    {
        private $model;

        function __construct($model)
        {
            $this->model = $model;
        }

        public function checkCookie(){
            $expirationdate = json_decode($_COOKIE["loginbate"])->expire;
            if (($expirationdate-time())<60*60*24) {
                header("location: /home/logout");
                exit;
            }
        }

    }