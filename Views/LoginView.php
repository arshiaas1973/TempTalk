<?php

    /**
    * The login page view
    */
    class LoginView
    {

        private $model;

        private $controller;

        function __construct($controller, $model)
        {
            $this->controller = $controller;

            $this->model = $model;
            
        }

        public function index()
        {
                if($this->model->check_if_logged_in()){
                    header("location: /");
                    exit;
                }
                $color_scheme = isset($_COOKIE["color_scheme"]) ? $_COOKIE["color_scheme"] : false;
                if ($color_scheme === false) $color_scheme = 'light';
                if ($color_scheme != 'light' && $color_scheme != 'light'){
                    setcookie("color_scheme","light",time()+(60*60*24*7),"/");
                    $color_scheme = "light";
                }
                ?>
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link rel="stylesheet" type="text/css" href="/css/Login.css"/>
                    <link rel="icon" href="/img/favicon.png" type="image/x-icon"/>
                    <title>TempTalk - Login</title>
                    
                </head>
                <body class="body-<?=$color_scheme?>">
                    <center>
                        <div class="navbar navbar-<?=$color_scheme?>">
                            <center>
                                <a href="/"><img class="navbar-logo" alt="TempTalk" src="/img/logo-<?=$color_scheme?>.png"/></a>
                            </center>
                        </div>
                        <div class="login-panel login-panel-<?=$color_scheme?>">
                            <span class="header">Login</span><br/>
                            <form method="POST" action="/login/process" id="loginForm">
                                <div class="error" <?php if(isset($_GET["error"])){echo "style=\"display:block;\"";} ?>>
                                    <p><?php if(isset($_GET["error"])){echo htmlspecialchars($_GET["error"]);}?></p>
                                </div>
                                <span class="titles" id="username-label">Username or Email</span><br/>
                                <input type="text" id="username" class="<?=$color_scheme?>-input" name="username" maxlength="30"/><br/>
                                <span class="titles" id="password-label">Password</span><br/>
                                <input type="password" id="password" class="<?=$color_scheme?>-input" name="password"/>
                                <button type="button" class="eye-icon" id="opened"><img src="/img/eye.png" width="22" height="22"/></button><br/>
                                <input type="button" class="submit" value="Login" onclick="Interval();Submit();"/>
                            </form><br/>
                            <a href="/register" class="register-button">Create new Account</a>
                            <img class="login-loading" src="/img/favicon.png" style="display:none;" />                            
                        </div>
                    </center>
                    <script src="https://cdn.jsdelivr.net/npm/js-cookie/dist/js.cookie.min.js"></script>
                    <script><?php require_once 'js/Cookie.js'; ?></script>
                    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/core.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/md5.js"></script>
                    <script><?php require_once 'js/Login.js';  require_once 'Models/Dev.php'; if(!DEVELOPEMENT_STATUS){ require_once 'js/ProductionMode.js'; } ?></script>
                    <script>
                        $("#loginForm #password").keydown(function(event){
                            if (event.keyCode == 13) {
                                $("#loginForm input.submit").click();
                            }
                        });
                    </script>
                </body>
                </html>
            <?php
        }

        // public function google(){
        //     $this->controller->loginGoogle();
        // }
        public function googleservice(){
            $this->controller->loginGoogle();
        }

        public function process(){
            if($this->model->check_if_logged_in()){
                header("location: /");
                exit;
            }
            error_reporting(E_ERROR | E_PARSE);
            if (isset($_POST["username"])&&isset($_POST["password"])&&isset($_POST["passcode"])) {
                $user = htmlspecialchars($_POST["username"]);
                $pass = htmlspecialchars($_POST["password"]);
                $passcode = $_POST["passcode"];
                if(md5($user."|".$pass)!=$passcode){
                    header("location: /login?error=Err 002: passcode was not valid.");
                    exit;
                }
                $this->controller->ProcessLogin($_POST);
            }else{
                header("location: /login");
                exit;
            }
        }

        public function twofaverification(){
            if($this->model->check_if_logged_in()){
                header("location: /");
                exit;
            }
            if(!isset($_COOKIE["is2fa"])||!isset($_COOKIE["2faid"])||$_COOKIE["is2fa"]!=1){
                header("location: /");
                exit;
            }
            $color_scheme = isset($_COOKIE["color_scheme"]) ? $_COOKIE["color_scheme"] : false;
            if ($color_scheme === false) $color_scheme = 'light';
            if ($color_scheme != 'light' && $color_scheme != 'light'){
                setcookie("color_scheme","light",time()+(60*60*24*7),"/");
                $color_scheme = "light";
            }
            $res = $this->model->getuserfirst2fa();
            if ($res==null){
                header("location: /login/finished2fa");
                exit;
            }
            ?>
            <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link rel="stylesheet" type="text/css" href="/css/LoginTwoFA.css"/>
                    <link rel="icon" href="/img/favicon.png" type="image/x-icon"/>
                    <title>TempTalk - Login</title>
                </head>
                <body class="body-<?=$color_scheme?>">
                    <center>
                        <div class="navbar navbar-<?=$color_scheme?>">
                            <center>
                                <a href="/"><img class="navbar-logo" alt="TempTalk" src="/img/logo-<?=$color_scheme?>.png"/></a>
                            </center>
                        </div>
                        <div class="twofa-section twofa-section-<?=$color_scheme?>">
                            <form id="TwofaForm" action="" method="POST">
                                <div class="error">
                                    <p></p>
                                </div>
                                <h1 class="heading-<?=$color_scheme?>">Adding Two-Factor</h1>
                                <?php if(strtolower($res["a_type"])=="email"){ ?>
                                <span class="titles titles-<?=$color_scheme?>">Email Address: </span>
                                <input type="email" id="email" required/>
                                <input type="button" class="button" id="EmailValidate" value="Validate"/><br/>
                                <div class="code-prompt">
                                    <span class="titles titles-<?=$color_scheme?>">Code: </span>
                                    <input type="text" id="code" required maxlength="7"/>
                                    <input type="button" class="button" id="SubmitCode" value="Submit" disabled/>
                                </div>
                                <?php } ?>       
                            </form>
                        </div>
                    </center>
                    <script src="https://cdn.jsdelivr.net/npm/js-cookie/dist/js.cookie.min.js"></script>
                    <script><?php require_once 'js/Cookie.js'; ?></script>
                    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
                    <script>var email="<?php $revealcount=4; echo substr($res["a_info"],0,$revealcount); ?>";var reveal_length=<?php echo $revealcount; ?>;<?php require_once 'js/LoginTwoFA.js';  require_once 'Models/Dev.php'; if(!DEVELOPEMENT_STATUS){ require_once 'js/ProductionMode.js'; } ?></script>
                </body>
                </html>
            <?php
        }

        public function verifyemail(){
            if($this->model->check_if_logged_in()){
                header("location: /");
                exit;
            }
            if(isset($_POST["email"])&&$_POST["email"]!=""&&
            isset($_COOKIE["is2fa"])&&isset($_COOKIE["2faid"])&&$_COOKIE["is2fa"]==1){
                $con = $this->model->createConnection();
                $q = mysqli_query($con,"SELECT * FROM `2fa_temp` WHERE `u_id`=".$_COOKIE["2faid"]." ORDER BY `a_id` DESC");
                if(mysqli_num_rows($q)>0){
                    $tmp = mysqli_fetch_assoc($q);
                    if($tmp["a_info"]==$_POST["email"]){
                        require_once dirname(dirname(__FILE__))."\\vendor\\autoload.php";
                        require_once dirname(dirname(__FILE__))."/Models/Config.php";
                        $mail = new PHPMailer\PHPMailer\PHPMailer(false);
        
                        try{
                            $mail->Host             =  MAIL_HOST;
                            $mail->isSMTP();
                            $mail->SMTPAuth         =  true;
                            $mail->Username         =  MAIL_USER;
                            $mail->Password         =  MAIL_PASS;
                            $mail->SMTPSecure       =  PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                            $mail->Port             =  MAIL_PORT;
                            $mail->setFrom(MAIL_EMAIL);
                            $mail->addAddress($_POST["email"]);
                            $random_num = rand(1000000,9999999);
                            $res = $this->controller->set_2fa_code($random_num);
                            if(!$res){
                                echo "Error-Saving";
                            }else{
                                // $mail->isHTML(true);
                                $mail->Subject  = "Please verify your 2FA email";
                                $mail->Body     = '<!DOCTYPE html> <html lang="en"> <head> <meta charset="UTF-8"> <meta http-equiv="X-UA-Compatible" content="IE=edge"> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>TempMail - Please verify your 2FA email</title> <style> @import url("https://fonts.cdnfonts.com/css/rubik-marker-hatch"); @import url("https://fonts.cdnfonts.com/css/boogaloo"); @import url("https://fonts.cdnfonts.com/css/share-techmono-2"); body{ width: 100%; height: 100%; padding: 0px; margin: 0px; } .body-light{ background-color: #ECF0F1; } .body-dark{ background-color:#34495E; } ul{ list-style: none; } a{ text-decoration: none; } h1{ font-family: "Rubik Marker Hatch", sans-serif; font-weight: normal;} *:focus{ outline: none;} .email-section{ max-width: 600px; margin-top: 60px; padding: 20px; border-radius: 40px; } .email-section-light{ background-color: #E5E5E5;} .email-section-dark{ background-color: #334155;} .heading-light{color: #000;} .heading-dark{color: aliceblue;} .email-section span.titles{ display: flex; font-family: "Boogaloo", sans-serif; font-size: 25px; justify-content: center; margin-left: 20px; } .email-section span.titles-light{color: #232323;} .email-section span.titles-dark{color: #dedede;} .email-section div.code-section{ padding: 10px; border-radius: 10px; margin-top: 25px; font-size: 30px; font-family: "Share-TechMono", sans-serif; width: -webkit-fit-content; width: -moz-fit-content; width: fit-content; } .email-section div.code-section-light{background:#E1F5FE; color:#121212;} .email-section div.code-section-dark{background:#607D8B; color:#dedede;} </style> </head> <body class="body">  <center class="main-box"> <div class="email-section"> <h1 class="email-heading">Verify your 2FA Email</h1> <span class="titles">This code is your 2FA Email Verification.</span> <span class="titles">Don\'t show this code to anybody else. Just put this code the textbox that site provides.</span> <div class="code-section">'.
                                    $random_num.'</span> </div> </center> <script src="https://cdn.jsdelivr.net/npm/js-cookie/dist/js.cookie.min.js"></script> <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script> <script> var $color_scheme = Cookies.get("color_scheme"); function get_color_scheme() { return (window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches) ? "dark" : "light"; } function update_color_scheme() { Cookies.set("color_scheme", get_color_scheme()); }  if ((typeof $color_scheme === "undefined") || (get_color_scheme() != $color_scheme)) update_color_scheme();  if (window.matchMedia) window.matchMedia("(prefers-color-scheme: dark)").addListener( update_color_scheme ); $("body").addClass("body-"+get_color_scheme()); $("div.email-section").addClass("email-section-"+get_color_scheme()); $("h1.email-heading").addClass("heading-"+get_color_scheme()); $("span.titles").addClass("titles-"+get_color_scheme()); $("div.code-section").addClass("code-section-"+get_color_scheme()); </script> </body> </html>';                    
                                $mail->AltBody  = "This code is your 2FA Email Verification. \nDon't show this code to anybody else. Just put this code the textbox that site provides. \nCode is: ".$random_num;
                                $mail->IsHTML(true);
                                $mail->send();
                                echo "Success";
                            }
                        }catch(Exception $e){
                            echo "Error-Emailing";
                        }
                    }else{
                        echo "Error-Matching";
                    }
                }else{
                    header("location: /login/finished2fa");
                    exit;
                }
            }else{
                header("location: /");
                exit;
            }
        }
        public function checktwofa(){
            if($this->model->check_if_logged_in()){
                header("location: /");
                exit;
            }
            if(isset($_POST["code"])&&isset($_POST["type"])&&isset($_POST["email"])&&
            isset($_COOKIE["is2fa"])&&isset($_COOKIE["2faid"])&&$_COOKIE["is2fa"]==1){
                if($_POST["type"]=="email"){
                    echo $this->controller->is_2fa_code_valid(htmlspecialchars($_POST["code"]),htmlspecialchars($_POST["email"]));
                }
            }else{
                header("location: /");
                exit;
            }
        }
        public function finished2fa(){
            if($this->model->check_if_logged_in()){
                header("location: /");
                exit;
            }
            if (isset($_COOKIE["is2fa"])&&isset($_COOKIE["2faid"])&&$_COOKIE["is2fa"]==1) {
                $con = $this->model->createConnection();
                $res = $this->model->getuserfirst2fa();
                if($res==null){
                    $expire = time()+(60*60*24*7);
                    $usr = $this->model->getuserinfofromid($_COOKIE["2faid"]);
                    $passcode = md5($usr["u_username"]."|".$usr["u_password"]);
                    $data = array("code"=>$passcode,"expire"=>$expire);
                    setcookie("islogined",1,$expire,'/');
                    setcookie("loginbate",json_encode($data),$expire,'/');
                    $uid = $usr["u_id"];
                    $qsession = mysqli_query($con,"SELECT * FROM `sessions` WHERE `u_id`=".$uid);
                    if(mysqli_num_rows($qsession)>0){
                        $newpasscode = md5($usr["u_username"]."|".$usr["u_password"]."|".$this->model->getCountry()."|".$this->model->getOS());
                        $data = array("code"=>$newpasscode,"expire"=>$expire);
                        setcookie("loginbate",json_encode($data),$expire,'/');
                        $qsession = mysqli_query($con, "INSERT INTO `sessions`(`u_id`, `session_code`, `device_location`, `device_os`) VALUES ('".$uid."','"."SESSION_".$newpasscode."','".$this->model->getCountry()."','".$this->model->getOS()."');");
                        if($qsession){
                            setcookie("islogined",1,time()+(60*60*24*7),'/');
                            setcookie("loginbate",json_encode($data),time()+(60*60*24*7),'/');
                            header("location: /");
                            exit;
                        }else{
                            header("location: /login?error=Err 005: we were not able to define this device as account main identity.");
                            exit;
                        }
                    }else{
                        $qsession = mysqli_query($con, "INSERT INTO `sessions`(`u_id`, `session_code`, `device_location`, `device_os`) VALUES ('".$uid."','"."SESSION_".$passcode."','".$this->model->getCountry()."','".$this->model->getOS()."');");
                        if($qsession){
                            setcookie("islogined",1,time()+(60*60*24*7),'/');
                            setcookie("loginbate",json_encode($data),time()+(60*60*24*7),'/');
                            header("location: /");
                            exit;
                        }else{
                            header("location: /login?error=Err 005: we were not able to define this device as account main identity.");
                            exit;
                        }
                    }
                }else{
                    header("location: /login/twofaverification");
                    exit;
                }
            }else{
                header("location: /");
                exit;
            }
        }
    }