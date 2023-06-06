<?php

    /**
    * The register page controller
    */
    class RegisterController
    {
        private $model;

        function __construct($model)
        {
            $this->model = $model;
        }

        public function ProcessRegister($POST){
            $email = htmlspecialchars($POST["email"]);
            $user = htmlspecialchars($POST["username"]);
            $displayedname = htmlspecialchars($POST["displayedname"]);
            $pass = md5(htmlspecialchars($POST["password"]));
            $con = $this->model->createConnection();
            if($con->connect_errno){
                mysqli_close($con);
                header("location: /register?error=Err 003: we have problems with database.");
                exit;
            }else{
                $q1 = mysqli_query($con,"SELECT * FROM `users` WHERE `u_email`='".$email."'");
                if(mysqli_num_rows($q1)>0){
                    mysqli_close($con);
                    header("location: /register?email=exists");
                    exit;
                }
                $q2 = mysqli_query($con,"SELECT * FROM `users` WHERE `u_username`='".$user."'");
                if(mysqli_num_rows($q2)>0){
                    mysqli_close($con);
                    header("location: /register?user=exists");
                    exit;
                }
                $query = mysqli_query($con,"INSERT INTO `users`(`u_email`, `u_username`, `u_password`, `u_displayedname`) VALUES ('".$email."','".$user."','".$pass."','".$displayedname."');");
                if($query){
                    $expire = time()+60*60*24*7;
                    $data = array("code"=>$_POST["passcode"],"expire"=>$expire);
                    setcookie("islogined",1,$expire,'/');
                    setcookie("loginbate",json_encode($data),$expire,'/');
                    $qid = mysqli_query($con,"SELECT * FROM `users` WHERE `u_email`='".$email."' AND `u_username`='".$user."' AND `u_displayedname`='".$displayedname."' AND `u_firsttime`=1;"); 
                    if (mysqli_num_rows($qid)>0) {
                        $row = mysqli_fetch_row($qid);
                        $qsession = mysqli_query($con, "INSERT INTO `sessions`(`u_id`, `session_code`, `device_location`, `device_os`) VALUES ('".$row[0]."','"."SESSION_".$POST["passcode"]."','".$this->model->getCountry()."','".$this->model->getOS()."');");
                        if($qsession){
                            mysqli_close($con);
                            header("location: /");
                            exit;
                        }else{
                            mysqli_close($con);
                            header("location: /register?error=Err 005: we were not able to define this device as account main identity.");
                            exit;
                        }
                    }else{
                        mysqli_close($con);
                        header("location: /register?error=Err 004: we were not able to create your account.");
                        exit;
                    }                   
                    
                }else{
                    mysqli_close($con);
                    header("location: /register?error=Err 006: we couldn't make your account. some technical issues.");
                    exit;
                }
            }
            
        }

    }