<?php

    /**
    * The register page view
    */
    class RegisterView
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
                ?>
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link rel="stylesheet" type="text/css" href="/css/Register.css"/>
                    <link rel="icon" href="/img/favicon.png" type="image/x-icon"/>
                    <title>TempTalk - Register</title>
                    
                </head>
                <body class="body-<?=$color_scheme?>">
                    <center>
                        <div class="navbar navbar-<?=$color_scheme?>">
                            <center>
                                <a href="/"><img class="navbar-logo" alt="TempTalk" src="/img/logo-<?=$color_scheme?>.png"/></a>
                            </center>
                        </div>
                        <div class="register-panel register-panel-<?=$color_scheme?>">
                            <span class="header">Register</span><br/>
                            <form method="POST" action="/register/process" id="registerForm">
                                <div class="error" <?php if(isset($_GET["error"])||isset($_GET["email"])||isset($_GET["user"])){echo "style=\"display:block;\"";} ?>>
                                    <p><?php if(isset($_GET["error"])){echo htmlspecialchars($_GET["error"]);}else if(isset($_GET["email"])){if($_GET["email"]=="exists")echo "An Account with this email already exists.";}else if(isset($_GET["user"])){if($_GET["user"]=="exists")echo "An Account with this username already exists.";} ?></p>
                                </div>
                                <span class="titles" id="email-label">Email *</span><br/>
                                <input type="email" id="email" class="<?=$color_scheme?>-input" name="email"/><br/>
                                <span class="titles" id="username-label">Username *</span><br/>
                                <input type="text" id="username" class="<?=$color_scheme?>-input" name="username" maxlength="100"/><br/>
                                <span class="titles" id="displayedname-label">Displayed Name</span><br/>
                                <input type="text" id="displayedname" class="<?=$color_scheme?>-input" name="displayedname" maxlength="100"/><br/>
                                <span class="titles" id="password-label">Password *</span><br/>
                                <input type="password" id="password" onkeydown="passwordCheck();" class="<?=$color_scheme?>-input" name="password"/>
                                <button type="button" class="eye-icon" id="opened"><img src="/img/eye.png" width="22" height="22"/></button><br/>
                                <div class="agreement">
                                    <img class="<?=$color_scheme?>-input" src="/img/Checkbox.png"/><span class="titles">     I agree with <a href="/terms" target="_blank">the terms of service</a> and <a href="/policy" target="_blank">the website policy</a></span>
                                </div>
                                <input type="button" name="RegisterButton" class="submit" value="Register" onclick="Submit();" disabled/>
                                
                            </form><br/>
                            <a href="/login" class="login-button">Login to Existing Account</a>
                            <img src="/img/favicon.png" style="display:none;" />                            
                        </div>
                    </center>
                    <script src="https://cdn.jsdelivr.net/npm/js-cookie/dist/js.cookie.min.js"></script>
                    <script><?php require_once 'js/Cookie.js'; ?></script>
                    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/core.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/md5.js"></script>
                    <script><?php require_once 'js/Register.js';  require_once 'Models/Dev.php'; if(!DEVELOPEMENT_STATUS){ require_once 'js/ProductionMode.js'; } ?></script>
                    <script>
                        $("#registerForm #password").keydown(function(event){
                            if (event.keyCode == 13) {
                                $("#registerForm input.submit").click();
                            }
                        });
                        $("#registerForm #username").keydown(onUsernameChanged());
                    </script>
                </body>
                </html>
            <?php
        }

        public function process(){
            if($this->model->check_if_logged_in()){
                header("location: /");
                exit;
            }
            if (isset($_POST["email"])&&isset($_POST["username"])&&isset($_POST["displayedname"])
                &&isset($_POST["password"])&&isset($_POST["passcode"])) {
                $email = $_POST["email"];
                $user = $_POST["username"];
                $displayedname = $_POST["displayedname"];
                $pass = $_POST["password"];
                $passcode = $_POST["passcode"];
                if(md5($user."|".$pass."|".$displayedname."|".$email)!=$passcode){
                    header("location: /register?error=Err 002: passcode was not valid.");
                    exit;
                }
                $this->controller->ProcessRegister($_POST);
            }else{
                header("location: /register");
                exit;
            }
        }
    }