<?php

    /**
    * The account page controller
    */
    class AccountController
    {
        private $model;

        function __construct($model)
        {
            $this->model = $model;
        }

        public function checkCookie(){
            $fixedcookie = str_replace("%7B","{",$_COOKIE["loginbate"]);
            $fixedcookie = str_replace("%22","\"",$fixedcookie);
            $fixedcookie = str_replace("%3A",":",$fixedcookie);
            $fixedcookie = str_replace("%2C",",",$fixedcookie);
            $fixedcookie = str_replace("%7D","}",$fixedcookie);
            $expirationdate = json_decode($fixedcookie)->expire;
            if (($expirationdate-time())<86400) {
                header("location: /home/logout");
                exit;
            }
        }

        public function isLoggedin(){
            return $this->model->check_if_logged_in();
        }

        public function setProfile(string $imagepath){
            $user = $this->model->getuserinfofromsession();
            $con = $this->model->createConnection();
            $query = mysqli_query($con,"UPDATE `users` SET `u_profilepic`='$imagepath' WHERE `u_id`=".$user["u_id"]);
            if($query){
                setcookie("choosenpfp","done",time()+(60*60*24*7),'/');
                header("location: /account/setup?step=1");
                exit;
            }else{
                header("location: /account/setup?error=Err 010: Had problem with changing your information. Please contact the administrator.");
                exit;
            }
        }
    }