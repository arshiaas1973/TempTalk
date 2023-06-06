<?php

    /**
    * The login page model
    */
    class LoginModel
    {
        private $host="127.0.0.1", 
               $user="temptalksite", 
               $pass="temptalksite@2023", 
               $db="temptalk", 
               $port=3307;
        public $ClientID, $ClientSecret, $RedirectURL;
        function __construct()
        {
            $ClientID = "345472513802-j30ll5vksng2glo4qdnrj5g5fh5uo05c.apps.googleusercontent.com";
            $ClientSecret = "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";
            $RedirectURL = "http://temptalk.com/login/googleservice";
        }

        public function check_if_logged_in(){
            if (isset($_COOKIE["islogined"])&&$_COOKIE["islogined"]==1){
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

        public function getCountry()
        {
            $ip = $_REQUEST['REMOTE_ADDR']; // the IP address to query
            $query = @unserialize(file_get_contents('http://ip-api.com/php/'.$ip));
            return $query["country"];
        }
        public function getOS() { 

            $user_agent = $_SERVER['HTTP_USER_AGENT'];
        
            $os_platform  = "Unknown OS Platform";
        
            $os_array     = array(
                                  '/windows nt 10/i'      =>  'Windows 10',
                                  '/windows nt 6.3/i'     =>  'Windows 8.1',
                                  '/windows nt 6.2/i'     =>  'Windows 8',
                                  '/windows nt 6.1/i'     =>  'Windows 7',
                                  '/windows nt 6.0/i'     =>  'Windows Vista',
                                  '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                                  '/windows nt 5.1/i'     =>  'Windows XP',
                                  '/windows xp/i'         =>  'Windows XP',
                                  '/windows nt 5.0/i'     =>  'Windows 2000',
                                  '/windows me/i'         =>  'Windows ME',
                                  '/win98/i'              =>  'Windows 98',
                                  '/win95/i'              =>  'Windows 95',
                                  '/win16/i'              =>  'Windows 3.11',
                                  '/macintosh|mac os x/i' =>  'Mac OS X',
                                  '/mac_powerpc/i'        =>  'Mac OS 9',
                                  '/linux/i'              =>  'Linux',
                                  '/ubuntu/i'             =>  'Ubuntu',
                                  '/iphone/i'             =>  'iPhone',
                                  '/ipod/i'               =>  'iPod',
                                  '/ipad/i'               =>  'iPad',
                                  '/android/i'            =>  'Android',
                                  '/blackberry/i'         =>  'BlackBerry',
                                  '/webos/i'              =>  'Mobile'
                            );
        
            foreach ($os_array as $regex => $value)
                if (preg_match($regex, $user_agent))
                    $os_platform = $value;
        
            return $os_platform;
        }
    }