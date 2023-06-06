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
                    <script><?php require_once 'js/Login.js'; ?></script>
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
            error_reporting(E_ERROR | E_PARSE);
            if (isset($_POST["username"])&&isset($_POST["password"])&&isset($_POST["passcode"])) {
                $user = $_POST["username"];
                $pass = $_POST["password"];
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
    }