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

        public function ContainsLetters($str){
            $words = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789~`_-+={}[]|\":\\/.";
            $strarray = str_split($str);
            $wordsarray = str_split($words);
            for ($i = 0; $i < strlen($str); $i++) {
                for ($j = 0; $j < strlen($words); $j++) {
                    if($strarray[$i] == $wordsarray[$j])
                        return true;
                }
            }
            return false;
        }

        public function userexists(string $username){
            $con = $this->createConnection();
            $query = mysqli_query($con,"SELECT * FROM `users` WHERE `u_username`='$username'");
            if(mysqli_num_rows($query)>0)
                return true;
            else
                return false;
        }

        public function userexistsbyid($uid){
            $con = $this->createConnection();
            $query = mysqli_query($con,"SELECT * FROM `users` WHERE `u_id`=$uid");
            if(mysqli_num_rows($query)>0)
                return true;
            else
                return false;
        }

        public function getuserbyusername($username){
            $con = $this->createConnection();
            $query = mysqli_query($con,"SELECT * FROM `users` WHERE `u_username`='$username'");
            if(mysqli_num_rows($query)>0)
                return mysqli_fetch_assoc($query);
            else
                return false;
        }

        public function checkifconversationexists($sender_id, $reciver_id){
            $con = $this->createConnection();
            $query = mysqli_query($con,"SELECT * FROM `conversations` WHERE (`u_starter`=$sender_id AND `u_destination`=$reciver_id) OR (`u_starter`=$reciver_id AND `u_destination`=$sender_id)");
            if(mysqli_num_rows($query)>0)
                return true;
            else
                return false;
        }

        public function getcidformuids($sender_id, $reciver_id){
            $con = $this->createConnection();
            $query = mysqli_query($con,"SELECT * FROM `conversations` WHERE (`u_starter`=$sender_id AND `u_destination`=$reciver_id) OR (`u_starter`=$reciver_id AND `u_destination`=$sender_id)");
            if(mysqli_num_rows($query)>0)
                return mysqli_fetch_assoc($query)["c_id"];
            else
                return false;
        }

        public function createMessagesConnection(){
            $dsn = 'pgsql:host=localhost;port=5432;dbname=temptalk';
            $user="root"; $pass="admin";
            try {
                $pdo = new PDO($dsn,$user,$pass);
                return $pdo;
            } catch (PDOException $ex) {
                return null;
            }
        }
    }