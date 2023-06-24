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

        public function checkLocationCookie(){
            if(!isset($_COOKIE["timezone"])){
                $realIP = file_get_contents("http://ip4only.me/api/");
                $ip = explode(",",$realIP)[1];
                $query = file_get_contents("https://ipapi.co/$ip/timezone/");
                setcookie("timezone",$query,time()+(60*60*24*7));
            }
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

        public function getuserbyid($id){
            $con = $this->createConnection();
            $query = mysqli_query($con,"SELECT * FROM `users` WHERE `u_id`=$id");
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
        public function EncryptMethod1($message){
            $key = '328bb30cf34b6a777116cffb7cefe147'; // Previously generated safely, ie: openssl_random_pseudo_bytes 
 
            $ivlen = openssl_cipher_iv_length($cipher="AES-256-CBC"); 
            $iv = openssl_random_pseudo_bytes($ivlen); 
            $ciphertext_raw = openssl_encrypt($message, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv); 
            $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true); 
            
            // Encrypted string 
            return base64_encode($iv.$hmac.$ciphertext_raw);
        }
        public function EncryptMethod2($message){
            $key            = '967f218558fa6bead3de79f7173ead0fe064d0ad9c8c272280f4f2c49d6bbd17';
            $cipher         = "aes-256-gcm";
            if (!in_array($cipher, openssl_get_cipher_methods())) {
                return false;
            }
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
            $tag = null;
            $ciphertext = openssl_encrypt(
                gzcompress($message),
                $cipher,
                base64_decode($key),
                $options=0,
                $iv,
                $tag,
            );
            return json_encode(
                array(
                    "ciphertext" => base64_encode($ciphertext),
                    "cipher" => $cipher,
                    "iv" => base64_encode($iv),
                    "tag" => base64_encode($tag)
                )
            );
        }
        public function DecryptMethod2($message){
            $key            = '967f218558fa6bead3de79f7173ead0fe064d0ad9c8c272280f4f2c49d6bbd17';
            try {
                $json = json_decode($message, true, 2,  JSON_THROW_ON_ERROR);
            } catch (Exception $e) {
                return $e->getMessage();
            }
            return gzuncompress(
                openssl_decrypt(
                    base64_decode($json['ciphertext']),
                    $json['cipher'],
                    base64_decode($key),
                    $options=0,
                    base64_decode($json['iv']),
                    base64_decode($json['tag'])
                )
            );
        }
        public function DecryptMethod1($message){
            $key = '328bb30cf34b6a777116cffb7cefe147'; // Previously generated safely, ie: openssl_random_pseudo_bytes 
            
            $c = base64_decode($message); 
            $ivlen = openssl_cipher_iv_length($cipher="AES-256-CBC"); 
            $iv = substr($c, 0, $ivlen); 
            $hmac = substr($c, $ivlen, $sha2len=32); 
            $ciphertext_raw = substr($c, $ivlen+$sha2len); 
            $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv); 
            $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true); 
             
            if(hash_equals($hmac, $calcmac)){ //PHP 5.6+ Timing attack safe string comparison 
              return $original_plaintext; 
            }else{ 
              return ''; 
            }
        }
        public function DecryptingMessages($message){
            $de = $this->DecryptMethod1($message);
            $de2 = $this->DecryptMethod2($de);
            return $de2;
        }
        public function EncryptingMessages($message){
            $en = $this->EncryptMethod2($message);
            $en2 = $this->EncryptMethod1($en);
            return $en2;
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

        public function this_servers_public_ip() {
            $realIP = file_get_contents("http://ip4only.me/api/");
            $ip = explode(",",$realIP)[1];
            $query = file_get_contents("https://ipapi.co/$ip/timezone/");
            return $query;
        }


        public function printMessages($info_array){
            error_reporting(E_ERROR | E_PARSE);
            $text = "";
            $color_scheme = isset($_COOKIE["color_scheme"]) ? $_COOKIE["color_scheme"] : false;
            if ($color_scheme === false) $color_scheme = 'light';
                    if ($info_array["status"]=="Success") {
                        if (array_key_exists("pointer",$info_array)) {
                            $text .= <<<EOF
                                        <div class="message-spliter message-spliter-$color_scheme">
                                            <table cell-spacing="0">
                                                <tr>
                                                    <th><span>>></span></th>
                                                    <th><span>Unreaded Messages</span></th>
                                                    <th><span><<</span></th>
                                                </tr>
                                            </table>
                                        </div>
                                        EOF;
                        }
                        for ($i = 0; $i < count($info_array["result"]); $i+=1) {
                            $element = $info_array["result"][$i];
                            $dateTime = new DateTime($element["creationdate"]);
                            $dateTime->setTimeZone(new DateTimeZone($_COOKIE["timezone"]));
                            $date = date_parse($dateTime->format('Y-m-d H:i:s'));
                            $text .= "<div class=\"message ".$element["flag"]."\">";
                            if($element["role"]=="normal")
                                $text .=        '<span class="normal">';
                            else if($element["role"]=="king")
                                $text .=        '<span class="king">';
                            else if($element["role"]=="queen")
                                $text .=        '<span class="queen">';
                            $text .=             "<img class=\"prof-pic\" src=\"".$element["user_pfp"]."\"/>".
                                                "</span>".
                                                "<div>".
                                                    "<p>".$element["text"]."</p>".
                                                    "<div class=\"message-details\">".
                                                        "<span class=\"message-status\">".$element["status"]."</span>".
                                                        "<span class=\"message-time\">".sprintf("%02d", $date["hour"]).":".sprintf("%02d", $date["minute"])."</span>".
                                                    "</div>".
                                                "</div>".
                                            "</div>";
                        }
                    }
            echo $text;
        }
    }