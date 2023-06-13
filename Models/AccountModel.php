<?php

    /**
    * The account page model
    */
    class AccountModel
    {
        private $host="127.0.0.1", 
               $user="temptalksite", 
               $pass="temptalksite@2023", 
               $db="temptalk", 
               $port=3307,
               $ImageUploadLocation="/public/img/";

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

        public function getImageUploadLocation(){
            return $this->ImageUploadLocation;
        }

        public function image_resize($file, $extension, $w, $h, $crop=FALSE) {
            list($width, $height) = getimagesize($file);
            $r = $width / $height;
            if ($crop) {
                if ($width > $height) {
                    $width = ceil($width-($width*abs($r-$w/$h)));
                } else {
                    $height = ceil($height-($height*abs($r-$w/$h)));
                }
                $newwidth = $w;
                $newheight = $h;
            } else {
                if ($w/$h > $r) {
                    $newwidth = $h*$r;
                    $newheight = $h;
                } else {
                    $newheight = $w/$r;
                    $newwidth = $w;
                }
            }

            if($extension=="jpeg"||$extension=="jpg")
                $src  = imagecreatefromjpeg($file);
            else if($extension=="png")
                $src  = imagecreatefrompng($file);
            else if($extension=="gif")
                $src  = imagecreatefromgif($file);
            else if($extension=="webp")
                $src  = imagecreatefromwebp($file);
            else if($extension=="bmp")
                $src  = imagecreatefrombmp($file);
            else if($extension=="avif")
                $src  = imagecreatefromavif($file);

            $dst = imagecreatetruecolor($newwidth, $newheight);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            return $dst;
        }

        public function get_month_number_from_text($month){
            switch (strtolower($month)) {
                case 'january':
                    return "01";
                    
                case 'february':
                    return "02";
                
                case 'march':
                    return "03";

                case 'april':
                    return "04";

                case 'may':
                    return "05";

                case 'june':
                    return "06";

                case 'july':
                    return "07";
                
                case 'august':
                    return "08";

                case 'september':
                    return "09";
                
                case 'october':
                    return "10";
                
                case 'november':
                    return "11";

                case 'december':
                    return "12";
                    
                default:
                    return "13";
            }
        }
    }