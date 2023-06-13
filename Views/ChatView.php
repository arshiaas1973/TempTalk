<?php

    /**
    * The chat page view
    */
    class ChatView
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
                $user=null;
                if(!$this->controller->isLoggedin()){
                    header("location: /login");
                    exit;
                }else{
                    $user=$this->model->getuserinfofromsession();
                    if($user["u_firsttime"]==1){
                        header("location: /account/setup");
                        exit;
                    }
                }
                $this->controller->checkCookie();
                $color_scheme = isset($_COOKIE["color_scheme"]) ? $_COOKIE["color_scheme"] : false;
                if ($color_scheme === false) $color_scheme = 'light';
                
                $usr = ""; $usr_id = 0; $mode = "";
                if(!isset($_GET["u"])||!isset($_GET["m"])){
                    header("location: /");
                    exit;
                }else{
                    $usr = $_GET["u"]; $mode = $_GET["m"];
                    if(!$this->model->userexists($usr)){
                        header("location: /");
                        exit;
                    }else{
                        $usr = $this->model->getuserbyusername($usr);
                    }
                }
                ?>
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link rel="stylesheet" type="text/css" href="/css/Chat.css"/>
                    <link rel="icon" href="/img/favicon.png" type="image/x-icon"/>
                    <title>TempTalk - Create Chat</title>
                </head>
                <body class="body-<?=$color_scheme?>">
                    <div class="chat-panel chat-panel-<?=$color_scheme?>">
                        <div class="header-bar header-bar-<?=$color_scheme?>">
                            <img class="back-button" src="/img/back-<?=$color_scheme?>.png" 
                                onclick="window.location.href='<?php if($mode=='c') echo '/chat/create'; else echo '/chat/join';?>'"/>
                            <img class="user-pfp user-pfp-<?=$color_scheme?>" src="<?php if($user["u_id"]!=$usr["u_id"]) echo $usr["u_profilepic"]; else echo '/img/savedimages.webp'; ?>" onclick="ShowProfilePanel();"/>
                            <span class="user-displayedname" onclick="ShowProfilePanel();"><?php if($user["u_id"]!=$usr["u_id"]) echo $usr["u_displayedname"]; else echo 'Saved Messages'; ?></span>
                            <?php
                            if($usr['u_role']=="king"){
                                ?>
                                <img class="user-pfp-badge" src="/img/king-crown.png">
                                <?php
                            }else if($usr['u_role']=="queen"){
                                ?>
                                <img class="user-pfp-badge" src="/img/queen-crown.png">
                                <?php
                            }
                            ?>
                            <div class="user-menu">
                                <img class="user-details" src="/img/menu-icon-<?=$color_scheme?>.png"/>
                                <ul>
                                    <li>Friend User</li><hr/>
                                    <li>Block User</li><hr/>
                                    <li>Report User</li>
                                </ul>
                            </div>
                        </div>
                        <div class="user-profile-details user-profile-details-<?=$color_scheme?>">
                            <div class="user-details-header-bar user-details-header-bar-<?=$color_scheme?>">
                                <img class="hide-button" src="/img/back-dark.png" onclick="HideProfilePanel();"/>
                                <span class="panel-title">Profile details</span>
                            </div>
                            <center>
                                <img class="full-user-pfp" src="<?php if($user["u_id"]!=$usr["u_id"]) echo $usr["u_profilepic"]; else echo '/img/savedimages.webp'; ?>"/>
                                <div class="names">
                                    <span class="user-displayedname"><?php if($user["u_id"]!=$usr["u_id"]) echo $usr["u_displayedname"]; else echo 'Saved Messages'; ?></span>
                                    <span class="user-username">@<?php echo $usr['u_username']; ?></span>
                                </div>
                            </center>
                        </div>
                    </div>
                    <script src="https://cdn.jsdelivr.net/npm/js-cookie/dist/js.cookie.min.js"></script>
                    <script><?php require_once 'js/Cookie.js'; ?></script>
                    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
                    <script><?php require_once 'js/Chat.js';  require_once 'Models/Dev.php'; if(!DEVELOPEMENT_STATUS){ require_once 'js/ProductionMode.js'; } ?></script>
                    <script><?php require_once 'js/ChatContextMenu.js'; ?></script>
                </body>
                </html>
            <?php
        }

        public function create(){
            if(!$this->controller->isLoggedin()){
                header("location: /login");
                exit;
            }else{
                $user=$this->model->getuserinfofromsession();
                if($user["u_firsttime"]==1){
                    header("location: /account/setup");
                    exit;
                }
            }
            $this->controller->checkCookie();
            $color_scheme = isset($_COOKIE["color_scheme"]) ? $_COOKIE["color_scheme"] : false;
            if ($color_scheme === false) $color_scheme = 'light';
                ?>
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link rel="stylesheet" type="text/css" href="/css/CreateChat.css"/>
                    <link rel="icon" href="/img/favicon.png" type="image/x-icon"/>
                    <title>TempTalk - Create Chat</title>
                    
                </head>
                <body class="body-<?=$color_scheme?>">
                    <center>
                        <div class="navbar navbar-<?=$color_scheme?>">
                            <center>
                                <a href="/"><img class="navbar-logo" alt="TempTalk" src="/img/logo-<?=$color_scheme?>.png"/></a>
                                <span>Create <span class="<?php if($color_scheme=="light")echo "blue"; else echo "red"; ?>">Chat</span></span>
                            </center>
                        </div>
                        <div class="chat-panel chat-panel-<?=$color_scheme?>">
                            <input type="text" class="search" name="search-box" id="search-box"/>
                            <img class="search-icon" src="/img/search-white.png"/>
                            <div class="user-panel"></div>
                        </div>
                        
                    </center>
                    <script src="https://cdn.jsdelivr.net/npm/js-cookie/dist/js.cookie.min.js"></script>
                    <script><?php require_once 'js/Cookie.js'; ?></script>
                    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
                    <script><?php require_once 'js/CreateChat.js';  require_once 'Models/Dev.php'; if(!DEVELOPEMENT_STATUS){ require_once 'js/ProductionMode.js'; } ?></script>
                </body>
                </html>
            <?php
        }

        public function join(){
            if(!$this->controller->isLoggedin()){
                header("location: /login");
                exit;
            }else{
                $user=$this->model->getuserinfofromsession();
                if($user["u_firsttime"]==1){
                    header("location: /account/setup");
                    exit;
                }
            }
            $this->controller->checkCookie();
            $color_scheme = isset($_COOKIE["color_scheme"]) ? $_COOKIE["color_scheme"] : false;
            if ($color_scheme === false) $color_scheme = 'light';
                ?>
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link rel="stylesheet" type="text/css" href="/css/JoinChat.css"/>
                    <link rel="icon" href="/img/favicon.png" type="image/x-icon"/>
                    <title>TempTalk - Join Chat</title>
                </head>
                <body class="body-<?=$color_scheme?>">
                    <center>
                        <div class="navbar navbar-<?=$color_scheme?>">
                            <center>
                                <a href="/"><img class="navbar-logo" alt="TempTalk" src="/img/logo-<?=$color_scheme?>.png"/></a>
                                <span>Create <span class="<?php if($color_scheme=="light")echo "blue"; else echo "red"; ?>">Chat</span></span>
                            </center>
                        </div>
                        <div class="chat-panel chat-panel-<?=$color_scheme?>">
                            <input type="text" class="search" name="search-box" id="search-box"/>
                            <img class="search-icon" src="/img/search-white.png"/>
                            <div class="user-panel"></div>
                        </div>
                        
                    </center>
                    <script src="https://cdn.jsdelivr.net/npm/js-cookie/dist/js.cookie.min.js"></script>
                    <script><?php require_once 'js/Cookie.js'; ?></script>
                    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
                    <script><?php require_once 'js/JoinChat.js';  require_once 'Models/Dev.php'; if(!DEVELOPEMENT_STATUS){ require_once 'js/ProductionMode.js'; } ?></script>
                </body>
                </html>
            <?php
        }

        public function fetchusers(){
            if(isset($_POST["search"])){
                $user = $this->model->getuserinfofromsession();
                $con = $this->model->createConnection();
                $search = $_POST["search"];
                $query = mysqli_query($con,"SELECT * FROM `users` WHERE `u_username` LIKE '%$search' OR `u_username` LIKE '$search%' OR `u_displayedname` LIKE '%$search' OR `u_displayedname` LIKE '$search%' ORDER BY `u_creationdate` DESC LIMIT 7");
                if(mysqli_num_rows($query)>0){
                    $arr = array();
                    if ((str_contains("Saved Messages",$search)||str_contains("SavedMessages",$search)
                        ||str_contains("saved messages",$search)||str_contains("savedmessages",$search))&&$search!=""&&$this->model->ContainsLetters($search)) {
                        $q1 = mysqli_query($con,"SELECT * FROM `conversations` WHERE (`u_starter`=".$user["u_id"]." AND `u_destination`=".$user["u_id"].") OR (`u_destination`=".$user["u_id"]." AND `u_starter`=".$user["u_id"].")");
                        if (mysqli_num_rows($q1)<=0) {
                            array_push($arr,array("pfp"=>"/img/savedimages.webp","displayedname"=>"Saved Messages","username"=>$user["u_username"],"role"=>$user["u_role"]));
                        }
                    }
                    while($info = mysqli_fetch_assoc($query)){
                        $q1 = mysqli_query($con,"SELECT * FROM `conversations` WHERE (`u_starter`=".$info["u_id"]." AND `u_destination`=".$user["u_id"].") OR (`u_destination`=".$info["u_id"]." AND `u_starter`=".$user["u_id"].")");
                        if(mysqli_num_rows($q1)<=0){
                            if($info["u_id"]==$user["u_id"]){
                                if(!array_search(array("pfp"=>"/img/savedimages.webp","displayedname"=>"Saved Messages","username"=>$info["u_username"],"role"=>$info["u_role"]),$arr))
                                    array_push($arr,array("pfp"=>"/img/savedimages.webp","displayedname"=>"Saved Messages","username"=>$info["u_username"],"role"=>$info["u_role"]));
                            }else
                                array_push($arr,array("pfp"=>$info["u_profilepic"],"displayedname"=>$info["u_displayedname"],"username"=>$info["u_username"],"role"=>$info["u_role"]));
                        }
                    }
                    echo json_encode($arr);
                }else{
                    $arr = array();
                    if (str_contains("Saved Messages",$search)||str_contains("SavedMessages",$search)
                        ||str_contains("saved messages",$search)||str_contains("savedmessages",$search)) {
                        $q1 = mysqli_query($con,"SELECT * FROM `conversations` WHERE (`u_starter`=".$user["u_id"]." AND `u_destination`=".$user["u_id"].") OR (`u_destination`=".$user["u_id"]." AND `u_starter`=".$user["u_id"].")");
                        if (mysqli_num_rows($q1)<=0) {
                            array_push($arr,array("pfp"=>"/img/savedimages.webp","displayedname"=>"Saved Messages","username"=>$user["u_username"],"role"=>$user["u_role"]));
                        }
                    }
                    echo json_encode($arr);
                }
            }else{
                header("location: /");
                exit;
            }
        }
        public function joinusers(){
            if(isset($_POST["search"])){
                $user = $this->model->getuserinfofromsession();
                $con = $this->model->createConnection();
                $search = $_POST["search"];
                $query = mysqli_query($con,"SELECT * FROM `users` WHERE `u_username` LIKE '%$search%' OR `u_displayedname` LIKE '%$search%' ORDER BY `u_creationdate` DESC LIMIT 7");
                if(mysqli_num_rows($query)>0){
                    $arr = array();
                    if ((str_contains("Saved Messages",$search)||str_contains("SavedMessages",$search)
                        ||str_contains("saved messages",$search)||str_contains("savedmessages",$search))&&$search!=""&&$this->model->ContainsLetters($search)) {
                        $q1 = mysqli_query($con,"SELECT * FROM `conversations` WHERE (`u_starter`=".$user["u_id"]." AND `u_destination`=".$user["u_id"].") OR (`u_destination`=".$user["u_id"]." AND `u_starter`=".$user["u_id"].")");
                        if (mysqli_num_rows($q1)<=0) {
                            array_push($arr,array("pfp"=>"/img/savedimages.webp","displayedname"=>"Saved Messages","username"=>$user["u_username"],"role"=>$user["u_role"]));
                        }
                    }
                    while($info = mysqli_fetch_assoc($query)){
                        $q1 = mysqli_query($con,"SELECT * FROM `conversations` WHERE (`u_starter`=".$info["u_id"]." AND `u_destination`=".$user["u_id"].") OR (`u_destination`=".$info["u_id"]." AND `u_starter`=".$user["u_id"].")");
                        if(mysqli_num_rows($q1)>0){
                            if($info["u_id"]==$user["u_id"])
                                if(!array_search(array("pfp"=>"/img/savedimages.webp","displayedname"=>"Saved Messages","username"=>$info["u_username"],"role"=>$info["u_role"]),$arr))
                                    array_push($arr,array("pfp"=>"/img/savedimages.webp","displayedname"=>"Saved Messages","username"=>$info["u_username"],"role"=>$info["u_role"]));
                            else
                                array_push($arr,array("pfp"=>$info["u_profilepic"],"displayedname"=>$info["u_displayedname"],"username"=>$info["u_username"],"role"=>$info["u_role"]));
                        }
                    }
                    echo json_encode($arr);
                }else{
                    $arr = array();
                    if (str_contains("Saved Messages",$search)||str_contains("SavedMessages",$search)
                        ||str_contains("saved messages",$search)||str_contains("savedmessages",$search)) {
                        $q1 = mysqli_query($con,"SELECT * FROM `conversations` WHERE (`u_starter`=".$user["u_id"]." AND `u_destination`=".$user["u_id"].") OR (`u_destination`=".$user["u_id"]." AND `u_starter`=".$user["u_id"].")");
                        if (mysqli_num_rows($q1)>0) {
                            array_push($arr,array("pfp"=>"/img/savedimages.webp","displayedname"=>"Saved Messages","username"=>$user["u_username"],"role"=>$user["u_role"]));
                        }
                    }
                    echo json_encode($arr);
                }
            }else{
                header("location: /");
                exit;
            }
        }
    }