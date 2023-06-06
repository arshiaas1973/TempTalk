<?php

    /**
    * The login page controller
    */
    class LoginController
    {
        private $model;

        function __construct($model)
        {
            $this->model = $model;
        }

        public function ProcessLogin($POST){
                $user = htmlspecialchars($POST["username"]);
                $pass = md5(htmlspecialchars($POST["password"]));
                $con = $this->model->createConnection();
                if($con->connect_errno){
                    header("location: /login?error=Err 003: we have problems with database.");
                    exit;
                }else{
                    $q1 = mysqli_query($con,"SELECT * FROM `users` WHERE `u_email`='".$user."' AND `u_password`='".$pass."'");
                    if(mysqli_num_rows($q1)>0){
                        $expire = time()+(60*60*24*7);
                        $data = array("code"=>$_POST["passcode"],"expire"=>$expire);
                        setcookie("islogined",1,$expire,'/');
                        setcookie("loginbate",json_encode($data),$expire,'/');
                        $uid = mysqli_fetch_assoc($q1)["u_id"];
                        $qsession = mysqli_query($con,"SELECT * FROM `sessions` WHERE `u_id`=".$uid);
                        if(mysqli_num_rows($qsession)>0){
                            $newpasscode = md5($user."|".$pass."|".$this->model->getCountry()."|".$this->model->getOS());
                            $data = array("code"=>$newpasscode,"expire"=>$expire);
                            setcookie("loginbate",json_encode($data),$expire,'/');
                            $qsession = mysqli_query($con, "INSERT INTO `sessions`(`u_id`, `session_code`, `device_location`, `device_os`) VALUES ('".$uid."','"."SESSION_".$newpasscode."','".$this->model->getCountry()."','".$this->model->getOS()."');");
                            if($qsession){
                                header("location: /");
                                exit;
                            }else{
                                header("location: /login?error=Err 005: we were not able to define this device as account main identity.");
                                exit;
                            }
                        }else{
                            $qsession = mysqli_query($con, "INSERT INTO `sessions`(`u_id`, `session_code`, `device_location`, `device_os`) VALUES ('".$uid."','"."SESSION_".$POST["passcode"]."','".$this->model->getCountry()."','".$this->model->getOS()."');");
                            if($qsession){
                                header("location: /");
                                exit;
                            }else{
                                header("location: /login?error=Err 005: we were not able to define this device as account main identity.");
                                exit;
                            }
                        }
                    }else{
                        $q2 = mysqli_query($con,"SELECT * FROM `users` WHERE `u_username`='".$user."' AND `u_password`='".$pass."'");
                        if(mysqli_num_rows($q2)>0){
                            $expire = time()+(60*60*24*7);
                            $data = array("code"=>$_POST["passcode"],"expire"=>$expire);
                            setcookie("islogined",1,$expire);
                            setcookie("loginbate",json_encode($data),$expire);
                            $uid = mysqli_fetch_assoc($q2)["u_id"];
                            $qsession = mysqli_query($con,"SELECT * FROM `sessions` WHERE `u_id`=".$uid);
                            if(mysqli_num_rows($qsession)>0){
                                $newpasscode = md5($user."|".$pass."|".$this->model->getCountry()."|".$this->model->getOS());
                                $data = array("code"=>$newpasscode,"expire"=>$expire);
                                setcookie("loginbate",json_encode($data),$expire);
                                // setcookie("loginbate",$POST["passcode"],604800);
                                $qsession = mysqli_query($con, "INSERT INTO `sessions`(`u_id`, `session_code`, `device_location`, `device_os`) VALUES ('".$uid."','"."SESSION_".$newpasscode."','".$this->model->getCountry()."','".$this->model->getOS()."');");
                                if($qsession){
                                    header("location: /");
                                    exit;
                                }else{
                                    header("location: /login?error=Err 005: we were not able to define this device as account main identity.");
                                    exit;
                                }
                            }else{
                                $qsession = mysqli_query($con, "INSERT INTO `sessions`(`u_id`, `session_code`, `device_location`, `device_os`) VALUES ('".$uid."','"."SESSION_".$POST["passcode"]."','".$this->model->getCountry()."','".$this->model->getOS()."');");
                                if($qsession){
                                    header("location: /");
                                    exit;
                                }else{
                                    header("location: /login?error=Err 005: we were not able to define this device as account main identity.");
                                    exit;
                                }
                            }
                        }else{
                            header("location: /login?error=Err 007: you have entered wrong login credentials.");
                            exit;
                        }
                    }
                }
            
        }

        public function loginGoogle(){
            require_once dirname(dirname(__FILE__))."\\vendor\\autoload.php";


            $ClientID = "345472513802-j30ll5vksng2glo4qdnrj5g5fh5uo05c.apps.googleusercontent.com";
            $ClientSecret = "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";
            $RedirectURL = "http://temptalk.com/login/googleservice";

            $client = new Google_Client();
            $client->setClientId($ClientID);
            $client->setClientSecret($ClientSecret);
            $client->setRedirectUri($RedirectURL);
            $client->addScope("profile");
            $client->addScope("email");

            
            
            if(isset($_GET["code"])){
                $token = $client->fetchAccessTokenWithAuthCode($_GET["code"]);
                $client->setAccessToken($token);
                $auth = new Google\Service\Oauth2($client);
                $info = $auth->userinfo->get();
                var_dump($info);
            }else{
                header("location: ".$client->createAuthUrl());
            }
        }
    }