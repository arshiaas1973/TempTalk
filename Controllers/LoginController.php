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
                        $usr = mysqli_fetch_assoc($q1);
                        if($usr["u_2fa"]==0){
                            $expire = time()+(60*60*24*7);
                            $data = array("code"=>$_POST["passcode"],"expire"=>$expire);
                            setcookie("islogined",1,$expire,'/');
                            setcookie("loginbate",json_encode($data),$expire,'/');
                            $uid = $usr["u_id"];
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
                            $q2 = mysqli_query($con,"SELECT * FROM `2fa` WHERE `u_id`=".$usr["u_id"]);
                            if(mysqli_num_rows($q2)>0){
                                while($res=mysqli_fetch_assoc($q2)){
                                    mysqli_query($con,"INSERT INTO `2fa_temp`(`u_id`, `a_type`, `a_info`) VALUES (".$res["u_id"].",'".$res["a_type"]."','".$res["a_info"]."')");
                                }
                                setcookie("is2fa",1,time()+(60*60*24*7),'/');
                                setcookie("2faid",$usr["u_id"],time()+(60*60*24*7),'/');
                                header("location: /login/twofaverification");
                                exit;
                            }else{
                                mysqli_query($con,"UPDATE `users` SET `u_2fa`=0 WHERE `u_id`=".$usr["u_id"]);
                                $expire = time()+(60*60*24*7);
                                $data = array("code"=>$_POST["passcode"],"expire"=>$expire);
                                setcookie("islogined",1,$expire,'/');
                                setcookie("loginbate",json_encode($data),$expire,'/');
                                $uid = $usr["u_id"];
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
                            }
                        }
                    }else{
                        $q2 = mysqli_query($con,"SELECT * FROM `users` WHERE `u_username`='".$user."' AND `u_password`='".$pass."'");
                        if(mysqli_num_rows($q2)>0){
                            $usr = mysqli_fetch_assoc($q2);
                            if($usr["u_2fa"]==0){
                                $expire = time()+(60*60*24*7);
                                $data = array("code"=>$_POST["passcode"],"expire"=>$expire);
                                setcookie("islogined",1,$expire);
                                setcookie("loginbate",json_encode($data),$expire);
                                $uid = $usr["u_id"];
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
                                $q3 = mysqli_query($con,"SELECT * FROM `2fa` WHERE `u_id`=".$usr["u_id"]);
                                if(mysqli_num_rows($q3)>0){
                                    while($res=mysqli_fetch_assoc($q3)){
                                        mysqli_query($con,"INSERT INTO `2fa_temp`(`u_id`, `a_type`, `a_info`) VALUES (".$res["u_id"].",'".$res["a_type"]."','".$res["a_info"]."')");
                                    }
                                    setcookie("is2fa",1,time()+(60*60*24*7),'/');
                                    setcookie("2faid",$usr["u_id"],time()+(60*60*24*7),'/');
                                    header("location: /login/twofaverification");
                                    exit;
                                }else{
                                    mysqli_query($con,"UPDATE `users` SET `u_2fa`=0 WHERE `u_id`=".$usr["u_id"]);
                                    $expire = time()+(60*60*24*7);
                                    $data = array("code"=>$_POST["passcode"],"expire"=>$expire);
                                    setcookie("islogined",1,$expire,'/');
                                    setcookie("loginbate",json_encode($data),$expire,'/');
                                    $uid = $usr["u_id"];
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
                                }
                            }
                        }else{
                            header("location: /login?error=Err 007: you have entered wrong login credentials.");
                            exit;
                        }
                    }
                }
            
        }

        public function set_2fa_code($number){
            $num = strval($number);
            $user = $_COOKIE["2faid"];
            $con = $this->model->createConnection();
            $query = mysqli_query($con,"INSERT INTO `2fa_codes`(`u_id`, `code_text`) VALUES (".$user.",'$num')");
            if($query){
                return true;
            }else{
                return false;
            }
        }

        public function is_2fa_code_valid($number,$email){
            $con = $this->model->createConnection();
            $query = mysqli_query($con,"SELECT * FROM `2fa_codes` WHERE `code_text`='$number' AND `u_id`=".$_COOKIE["2faid"]." ORDER BY `code_date` DESC ");
            if(mysqli_fetch_row($query)>0){
                $q1 = mysqli_query($con,"DELETE FROM `2fa_codes` WHERE `u_id`=".$_COOKIE["2faid"]);
                $q2 = mysqli_query($con, "DELETE FROM `2fa_temp` WHERE `a_type`='email' AND `a_info`='$email' AND `u_id`=".$_COOKIE["2faid"]);
                if($q2)
                    return "Success";
                else
                    return "Error-Deleting";                    
            }else{
                return "Error-Matching";
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