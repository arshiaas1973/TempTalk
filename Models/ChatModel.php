<?php

    /**
    * The chat page model
    */
    class ChatModel
    {
        private $host="127.0.0.1", 
               $user="temptalksite", 
               $pass="temptalksite@2023", 
               $db="temptalk", 
               $port=3307;
        function __construct()
        {

        }

        public function check_if_logged_in(){
            if (isset($_COOKIE["islogined"])&&isset($_COOKIE["loginbate"])){
                return true;
            }else{
                return false;
            }
        }

        public function createConnection(){
            $connection = mysqli_connect($this->host,$this->user,$this->pass,$this->db,$this->port);
            mysqli_query($connection,"SET CHARACTER SET UTF8;");
            return $connection;
        }


        public function getuserinfofromsession(){
            $con = $this->createConnection();
            $fixedcookie = str_replace("%7B","{",$_COOKIE["loginbate"]);
            $fixedcookie = str_replace("%22","\"",$fixedcookie);
            $fixedcookie = str_replace("%3A",":",$fixedcookie);
            $fixedcookie = str_replace("%2C",",",$fixedcookie);
            $fixedcookie = str_replace("%7D","}",$fixedcookie);
            
            $q1 = mysqli_query($con,"SELECT * FROM `sessions` WHERE `session_code`='"."SESSION_".json_decode($fixedcookie)->code."'");
            if($q1){
                $uid = mysqli_fetch_assoc($q1)["u_id"];
                $q2 = mysqli_query($con,"SELECT * FROM `users` WHERE `u_id`=".$uid);
                if($q2){
                    return mysqli_fetch_assoc($q2);
                }else{
                    return "false";
                }
            }else{
                return "false";
            }
        }

    }