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

        public function set_2fa_code($number){
            $num = strval($number);
            $user = $this->model->getuserinfofromsession();
            $con = $this->model->createConnection();
            $query = mysqli_query($con,"INSERT INTO `2fa_codes`(`u_id`, `code_text`) VALUES (".$user["u_id"].",'$num')");
            if($query){
                return true;
            }else{
                return false;
            }
        }

        public function is_2fa_code_valid($number,$email){
            $user = $this->model->getuserinfofromsession();
            $con = $this->model->createConnection();
            $query = mysqli_query($con,"SELECT * FROM `2fa_codes` WHERE `code_text`='$number' AND `u_id`=".$user["u_id"]." ORDER BY `code_date` DESC ");
            if(mysqli_fetch_row($query)>0){
                $q1 = mysqli_query($con,"DELETE FROM `2fa_codes` WHERE `u_id`=".$user["u_id"]);
                $q2 = mysqli_query($con,"INSERT INTO `2fa`(`u_id`, `a_type`, `a_info`) VALUES (".$user["u_id"].",'email','$email')");
                if($q2){
                    $q3 = mysqli_query($con, "UPDATE `users` SET `u_2fa`=1 WHERE `u_id`=".$user["u_id"]);
                    if($q3){
                        if($user["u_firsttime"]==1){
                            setcookie("submit2fa","done",time()+(60*60*24*7),'/');
                        }
                        return "Success";
                    }else{
                        return "Error-Editing-Account";
                    }
                }else{
                    return "Error-Saving";
                }
            }else{
                return "Error-Matching";
            }
        }
        public function finishit($bio, $birthdate){
            $user = $this->model->getuserinfofromsession();
            $con = $this->model->createConnection();
            $month = $this->model->get_month_number_from_text($birthdate["month"]);
            $final_birthdate = $birthdate["year"]."/".$month."/".$birthdate["day"];
            $query = mysqli_query($con,"UPDATE `users` SET `u_bio`='$bio',`u_firsttime`=0,`u_birthdate`='$final_birthdate' WHERE `u_id`=".$user["u_id"]); 
            if($query){
                setcookie("choosenpfp","done",time()-(60*60*24*7),'/');
                setcookie("submit2fa","done",time()-(60*60*24*7),'/');
                header("location: /");
                exit;
            }else{
                header('location: /account/setup?error=Err 018: Had problem with changing your information. Please contact the administrator.');
                exit;
            }
        }
    }