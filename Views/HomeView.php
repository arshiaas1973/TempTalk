<?php

    /**
    * The home page view
    */
    class HomeView
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
                error_reporting(E_ERROR | E_PARSE);
                $color_scheme = isset($_COOKIE["color_scheme"]) ? $_COOKIE["color_scheme"] : false;
                if ($color_scheme === false) $color_scheme = 'light';
                ?>
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link rel="stylesheet" type="text/css" href="/css/Home.css"/>
                    <link rel="icon" href="/img/favicon.png" type="image/x-icon"/>
                    <title>TempTalk</title>
                    
                </head>
                <body class="body-<?=$color_scheme?>">
                    
                    <script>
                        function close_window() {
                            window.open('','_self').close();
                        }
                    </script>
                    <center>
                        <div class="navbar navbar-<?=$color_scheme?>">
                            <center>
                                <img class="navbar-logo" alt="TempTalk" src="/img/logo-<?=$color_scheme?>.png"/>
                            </center>
                        </div>
                    </center>
                    <?php 
                        if($this->model->check_if_logged_in()){
                        // $this->controller->checkCookie();
                        $user = $this->model->getuserinfofromsession();
                        if($user != "false"){
                            ?>
                            <div class="profile profile-<?=$color_scheme?>">
                                <center>
                                    <img src="<?php echo $user["u_profilepic"]; ?>" width="64" height="64" onclick="toggleevt();"/>
                                </center>
                                <span id="profile-text" onclick="logout();">Welcome</span>
                            </div>
                            <?php
                        }
                    } ?>
                    
                    <div class="top-left-section">
                        <center><span class="text" onclick="createChat();">Create Chat</span></center>
                    </div>
                    <div class="top-right-section">
                        <center><span class="text" onclick="createGroup();">Create Group</span></center>
                    </div>
                    <div class="bottom-left-section">
                        <center><span class="text" onclick="joinChat();">Join Chat</span>
                    </div>
                    <div class="bottom-right-section">
                        <center><span class="text" onclick="joinGroup();">Join Group</span>
                    </div>
                    <div></div>
                    <script src="https://cdn.jsdelivr.net/npm/js-cookie/dist/js.cookie.min.js"></script>
                    <script><?php require_once 'js/Cookie.js'; ?></script>
                    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
                    <script><?php require_once 'js/Main.js'; ?></script>
                </body>
                </html>
                <?php
        }

        public function logout(){
            $con = $this->model->createConnection();
            $q1 = mysqli_query($con,"DELETE FROM `sessions` WHERE `session_code`='"."SESSION_".json_decode($_COOKIE["loginbate"])->code."'");
            setcookie("islogined",$_COOKIE["islogined"],time()-(60*60*24*7),'/');
            setcookie("loginbate",$_COOKIE["loginbate"],time()-(60*60*24*7),'/');
            unset($_COOKIE["islogined"]);
            unset($_COOKIE["loginbate"]);
            mysqli_close($con);
            header("location: /");
            exit;
        }
    }

