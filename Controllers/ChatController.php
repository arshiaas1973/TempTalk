<?php

    /**
    * The chat page controller
    */
    class ChatController
    {
        private $model;

        function __construct($model)
        {
            $this->model = $model;
        }

        public function checkCookie(){
            $expirationdate = json_decode($_COOKIE["loginbate"])->expire;
            echo "<script>console.log(\"".$expirationdate."\");</script>";
            echo "<script>console.log(\"".time()."\");</script>";
            // if (($expirationdate-time())<86400) {
            //     header("location: /home/logout");
            //     exit;
            // }
        }

        public function isLoggedin(){
            return $this->model->check_if_logged_in();
        }
    }