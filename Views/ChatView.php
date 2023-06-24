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
                $this->model->checkLocationCookie();
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
                <body class="body-<?=$color_scheme?>" onresize="WindowResize();">
                    <div class="chat-panel chat-panel-<?=$color_scheme?>">
                        <div class="header-bar header-bar-<?=$color_scheme?>">
                            <img class="back-button" src="/img/back-<?=$color_scheme?>.png" 
                                onclick="window.location.href='<?php if($mode=='c') echo '/chat/create'; else echo '/chat/join';?>'"/>
                            <img class="user-pfp user-pfp-<?=$color_scheme?>" src="<?php if($user["u_id"]!=$usr["u_id"]) echo htmlspecialchars($usr["u_profilepic"]); else echo '/img/savedimages.webp'; ?>" onclick="ShowProfilePanel();"/>
                            <span class="user-displayedname" onclick="ShowProfilePanel();"><?php if($user["u_id"]!=$usr["u_id"]) echo htmlspecialchars($usr["u_displayedname"]); else echo 'Saved Messages'; ?></span>
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
                        <div class="message-section">
                            <div class="message-body">
                            </div>
                            <div class="message-box">
                                <textarea></textarea>
                                <button class="message-submit message-submit-<?=$color_scheme?>"><img src="/img/message-blob.png" width="40" height="40"/></button>
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
                                    <span class="user-displayedname user-displayedname-<?=$color_scheme?>"><?php if($user["u_id"]!=$usr["u_id"]) echo $usr["u_displayedname"]; else echo 'Saved Messages'; ?></span>
                                    <span class="user-username user-username-<?=$color_scheme?>">@<?php echo $usr['u_username']; ?></span>
                                </div>
                                <span class="user-titles">Joined since <span class="user-date">
                                    <?php
                                    $d = date_parse($usr["u_creationdate"]);
                                    $y = $d["year"]; $m = sprintf("%02d", $d["month"]); $day = $d["day"];
                                    echo "$day/$m/$y";
                                    ?>
                                </span></span><br/>
                                <span class="user-titles">Biography</span><br/>
                                <span class="user-biography"><?php echo $usr["u_bio"]; ?></span>
                            </center>
                        </div>
                    </div>
                    <script src="https://cdn.jsdelivr.net/npm/js-cookie/dist/js.cookie.min.js"></script>
                    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/core.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/md5.js"></script>
                    <script><?php require_once 'js/Cookie.js'; ?>var sid = <?php echo $user["u_id"];?>;var rid = <?php echo $usr["u_id"];?>;var cid = <?php echo $this->model->getcidformuids($user["u_id"],$usr["u_id"]); ?>;var mode = "<?php echo $mode;?>";<?php require_once 'js/Chat.js';  require_once 'Models/Dev.php'; if(!DEVELOPEMENT_STATUS){ require_once 'js/ProductionMode.js'; } ?></script>
                    <script><?php require_once 'js/ChatContextMenu.js'; ?></script>
                </body>
                </html>
            <?php
        }

        public function sendmessage(){
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
            if(isset($_POST["senderid"])&&isset($_POST["reciverid"])&&isset($_POST["msg"])
                &&isset($_POST["mode"])&&isset($_POST["type"])
                &&isset($_POST["secret"])&&$_POST["secret"]==md5(strval($_POST["senderid"]).strval($_POST["reciverid"]))){
                $msg = htmlspecialchars($_POST["msg"]);
                $sid = htmlspecialchars($_POST["senderid"]);
                $rid = htmlspecialchars($_POST["reciverid"]);  
                $mode = htmlspecialchars($_POST["mode"]);
                $msgtype = htmlspecialchars($_POST["type"]);
                if($this->model->userexistsbyid($sid)&&$this->model->userexistsbyid($rid)){
                    $con = $this->model->createConnection();
                    if($this->model->checkifconversationexists($sid,$rid)){
                        $q1 = mysqli_query($con,"SELECT * FROM `conversations` WHERE `u_starter`=$sid AND `u_destination`=$rid");
                        if(mysqli_num_rows($q1)>0){
                            $conversation = mysqli_fetch_assoc($q1);
                            $msgcon = $this->model->createMessagesConnection();
                            if($msgcon!=null){
                                $sql = 'INSERT INTO messages(c_id,u_id,msg_text,msg_type) VALUES(:cid,:uid,:msgtext,:msgtype)';
                                $q2 = $msgcon->prepare($sql);
                                $q2->bindValue(':cid',$conversation["c_id"]);
                                $q2->bindValue(':uid',$sid);
                                $q2->bindValue(':msgtext',$this->model->EncryptingMessages($msg));
                                $q2->bindValue(':msgtype',$msgtype);
                                if($q2->execute()){
                                    mysqli_query($con,"UPDATE `user_status_in_conversations` `u_isupdated`=1 WHERE `u_id`=$rid AND `c_id`=".$conversation["c_id"].";");
                                    return "Success";
                                }else{
                                    $q3 = mysqli_query($con,"SELECT * FROM `users` WHERE `u_id`=$rid");
                                    if(mysqli_num_rows($q3)>0){
                                        $usr = mysqli_fetch_assoc($q3);
                                        header("location: /chat?u=".$usr["u_username"]."&m=$mode");
                                        exit;
                                    }else{
                                        header("location: /");
                                        exit;
                                    }
                                }
                            }else{
                                $q2 = mysqli_query($con,"SELECT * FROM `users` WHERE `u_id`=$rid");
                                if(mysqli_num_rows($q2)>0){
                                    $usr = mysqli_fetch_assoc($q2);
                                    header("location: /chat?u=".$usr["u_username"]."&m=$mode");
                                    exit;
                                }else{
                                    header("location: /");
                                    exit;
                                }
                            }
                        }else{
                            $q2 = mysqli_query($con,"SELECT * FROM `users` WHERE `u_id`=$rid");
                            if(mysqli_num_rows($q2)>0){
                                $usr = mysqli_fetch_assoc($q2);
                                header("location: /chat?u=".$usr["u_username"]."&m=$mode");
                                exit;
                            }else{
                                header("location: /");
                                exit;
                            }
                        }
                    }else{
                        $q1 = mysqli_query($con,"INSERT INTO `conversations`(`u_starter`, `u_destination`) VALUES ($sid,$rid)");
                        if($q1){
                            $q2 = mysqli_query($con,"SELECT * FROM `conversations` WHERE `u_starter`=$sid AND `u_destination`=$rid");
                            if(mysqli_num_rows($q2)>0){
                                $conversation = mysqli_fetch_assoc($q2);
                                $q3 = mysqli_query($con,"INSERT INTO `user_status_in_conversations`(`c_id`, `u_id`) VALUES (".$conversation["c_id"].",$sid)");
                                if($q3){
                                    $q4 = mysqli_query($con,"INSERT INTO `user_status_in_conversations`(`c_id`, `u_id`) VALUES (".$conversation["c_id"].",$rid)");
                                    if ($q4) {
                                        $msgcon = $this->model->createMessagesConnection();
                                        if($msgcon!=null){
                                            $sql = 'INSERT INTO messages(c_id,u_id,msg_text,msg_type) VALUES(:cid,:uid,:msgtext,:msgtype)';
                                            $q5 = $msgcon->prepare($sql);
                                            $q5->bindValue(':cid',$conversation["c_id"]);
                                            $q5->bindValue(':uid',$sid);
                                            $q5->bindValue(':msgtext',$this->model->EncryptingMessages($msg));
                                            $q5->bindValue(':msgtype',$msgtype);
                                            if($q5->execute()){
                                                return "Success";
                                            }else{
                                                $q6 = mysqli_query($con,"SELECT * FROM `users` WHERE `u_id`=$rid");
                                                if(mysqli_num_rows($q6)>0){
                                                    $usr = mysqli_fetch_assoc($q6);
                                                    header("location: /chat?u=".$usr["u_username"]."&m=$mode");
                                                    exit;
                                                }else{
                                                    header("location: /");
                                                    exit;
                                                }
                                            }
                                        }else{
                                            $q5 = mysqli_query($con,"SELECT * FROM `users` WHERE `u_id`=$rid");
                                            if(mysqli_num_rows($q5)>0){
                                                $usr = mysqli_fetch_assoc($q5);
                                                header("location: /chat?u=".$usr["u_username"]."&m=$mode");
                                                exit;
                                            }else{
                                                header("location: /");
                                                exit;
                                            }
                                        }
                                    }else{
                                        $q5 = mysqli_query($con,"SELECT * FROM `users` WHERE `u_id`=$rid");
                                        if(mysqli_num_rows($q5)>0){
                                            $usr = mysqli_fetch_assoc($q5);
                                            header("location: /chat?u=".$usr["u_username"]."&m=$mode");
                                            exit;
                                        }else{
                                            header("location: /");
                                            exit;
                                        }
                                    }
                                }else{
                                    $q4 = mysqli_query($con,"SELECT * FROM `users` WHERE `u_id`=$rid");
                                    if(mysqli_num_rows($q4)>0){
                                        $usr = mysqli_fetch_assoc($q4);
                                        header("location: /chat?u=".$usr["u_username"]."&m=$mode");
                                        exit;
                                    }else{
                                        header("location: /");
                                        exit;
                                    }
                                }
                            }else{
                                $q3 = mysqli_query($con,"SELECT * FROM `users` WHERE `u_id`=$rid");
                                if(mysqli_num_rows($q3)>0){
                                    $usr = mysqli_fetch_assoc($q3);
                                    header("location: /chat?u=".$usr["u_username"]."&m=$mode");
                                    exit;
                                }else{
                                    header("location: /");
                                    exit;
                                }
                            }
                        }else{
                            $q2 = mysqli_query($con,"SELECT * FROM `users` WHERE `u_id`=$rid");
                            if(mysqli_num_rows($q2)>0){
                                $usr = mysqli_fetch_assoc($q2);
                                header("location: /chat?u=".$usr["u_username"]."&m=$mode");
                                exit;
                            }else{
                                header("location: /");
                                exit;
                            }
                        }
                    }
                }else{
                    header("location: /");
                    exit;
                }
            }else{
                header("location: /");
                exit;
            }
        }

        public function getmessages(){
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
            if(isset($_GET["mode"])&&($_GET["mode"]=="c"||$_GET["mode"]=="j")&&isset($_GET["cid"])
            &&isset($_GET["startpoint"])&&isset($_GET["limit"])&&isset($_GET["isstartpoint"])){
                $con = $this->model->createConnection();
                $msgcon = $this->model->createMessagesConnection();
                $uid = $this->model->getuserinfofromsession()['u_id'];
                $cid = htmlspecialchars($_GET["cid"]);
                $limit = htmlspecialchars($_GET["limit"]);
                $mode = htmlspecialchars($_GET["mode"]);
                $q1 = mysqli_query($con,"SELECT * FROM `conversations` WHERE (`u_starter`=$uid OR `u_destination`=$uid) AND `c_id`=$cid;");
                if(mysqli_num_rows($q1)>0){
                    if(intval($_GET["isstartpoint"])==1){
                        if($msgcon!=null){
                            $start = $_GET["startpoint"];
                            if(!isset($_GET["position"]))
                                $sql = "SELECT * FROM messages WHERE msg_creationtime>='$start' AND c_id=$cid ORDER BY msg_creationtime ASC LIMIT $limit;";
                            else if($_GET["position"]=="top")
                                $sql = "SELECT * FROM messages WHERE msg_creationtime<='$start' AND c_id=$cid ORDER BY msg_creationtime ASC LIMIT $limit;";
                            else
                                $sql = "SELECT * FROM messages WHERE msg_creationtime>='$start' AND c_id=$cid ORDER BY msg_creationtime ASC LIMIT $limit;";
                            $arr = array();
                            $q2 = $msgcon->query($sql);
                            while ($res = $q2->fetch(PDO::FETCH_ASSOC)) {
                                $uu = $this->model->getuserbyid($res["u_id"]);
                                array_push($arr,array("msg_id"=>$res["msg_id"],"flag"=>($res["u_id"]==$uid)?"sender":"reciever","role"=>$uu["u_role"],"user_pfp"=>$uu["u_profilepic"],"text"=>$res["msg_text"],"type"=>$res["msg_type"],"status"=>($res["msg_seen_by_starter"]==1&&$res["msg_seen_by_destination"]==1)?"Seen":"Delivered","creationdate"=>$res["msg_creationtime"]));
                            }
                            $this->model->printMessages(array("status"=>"Success","result"=>$arr));
                        }else{
                            $q2 = mysqli_query($con,"SELECT * FROM `users` WHERE `u_id`=$uid");
                            if(mysqli_num_rows($q2)>0){
                                $usr = mysqli_fetch_assoc($q2);
                                header("location: /chat?u=".$usr["u_username"]."&m=$mode");
                                exit;
                            }else{
                                header("location: /");
                                exit;
                            }
                        }
                    }else{
                        if($msgcon!=null){
                            $sql = "SELECT * FROM messages WHERE c_id=$cid AND msg_seen_by_starter=1 AND msg_seen_by_destination=1 ORDER BY msg_creationtime DESC LIMIT $limit;";
                            $q2 = $msgcon->query($sql);
                            if ($q2->fetchColumn() > 0) {
                                $lastid = intval($q2->fetch(PDO::FETCH_ASSOC)["msg_creationtime"]);
                                $arr = array();
                                $sql2 = "SELECT * FROM messages WHERE msg_creationtime>'$lastid' AND c_id=$cid ORDER BY msg_creationtime ASC LIMIT $limit;";
                                $q3 = $msgcon->query($sql2);
                                if ($q3->fetchColumn() > 0){
                                    while ($res = $q3->fetch(PDO::FETCH_ASSOC)) {
                                        $uu = $this->model->getuserbyid($res["u_id"]);
                                        array_push($arr,array("msg_id"=>$res["msg_id"],"flag"=>($res["u_id"]==$uid)?"sender":"reciever","role"=>$uu["u_role"],"user_pfp"=>$uu["u_profilepic"],"text"=>$this->model->DecryptingMessages($res["msg_text"]),"type"=>$res["msg_type"],"status"=>($res["msg_seen_by_starter"]==1&&$res["msg_seen_by_destination"]==1)?"Seen":"Delivered","creationdate"=>$res["msg_creationtime"]));
                                    }
                                    if(count($arr)>0)
                                        $this->model->printMessages(array("status"=>"Success","result"=>$arr,"pointer"=>1));
                                    else
                                        $this->model->printMessages(array("status"=>"Success","result"=>$arr));
                                }else{
                                    $sql = "SELECT * FROM messages WHERE msg_creationtime<'$lastid' AND c_id=$cid ORDER BY msg_creationtime ASC LIMIT $limit;";
                                    $arr = array();
                                    $q4 = $msgcon->query($sql);
                                    while ($res = $q4->fetch(PDO::FETCH_ASSOC)) {
                                        $uu = $this->model->getuserbyid($res["u_id"]);
                                        array_push($arr,array("msg_id"=>$res["msg_id"],"flag"=>($res["u_id"]==$uid)?"sender":"reciever","role"=>$uu["u_role"],"user_pfp"=>$uu["u_profilepic"],"text"=>$this->model->DecryptingMessages($res["msg_text"]),"type"=>$res["msg_type"],"status"=>($res["msg_seen_by_starter"]==1&&$res["msg_seen_by_destination"]==1)?"Seen":"Delivered","creationdate"=>$res["msg_creationtime"]));
                                    }
                                    if(count($arr)>0)
                                        $this->model->printMessages(array("status"=>"Success","result"=>$arr,"pointer"=>1));
                                    else
                                        $this->model->printMessages(array("status"=>"Success","result"=>$arr));
                                }
                            }else{
                                $start = 0;
                                $sql = "SELECT * FROM messages WHERE c_id=$cid ORDER BY msg_creationtime ASC LIMIT $limit;";
                                $arr = array();
                                $q3 = $msgcon->query($sql);
                                while ($res = $q3->fetch(PDO::FETCH_ASSOC)) {
                                    $uu = $this->model->getuserbyid($res["u_id"]);
                                    array_push($arr,array("msg_id"=>$res["msg_id"],"flag"=>($res["u_id"]==$uid)?"sender":"reciever","role"=>$uu["u_role"],"user_pfp"=>$uu["u_profilepic"],"text"=>$this->model->DecryptingMessages($res["msg_text"]),"type"=>$res["msg_type"],"status"=>($res["msg_seen_by_starter"]==1&&$res["msg_seen_by_destination"]==1)?"Seen":"Delivered","creationdate"=>$res["msg_creationtime"]));
                                }
                                if(count($arr)>0)
                                    $this->model->printMessages(array("status"=>"Success","result"=>$arr,"pointer"=>1));
                                else
                                    $this->model->printMessages(array("status"=>"Success","result"=>$arr));
                            }
                                
                        }else{
                            $q5 = mysqli_query($con,"SELECT * FROM `users` WHERE `u_id`=$uid");
                            if(mysqli_num_rows($q5)>0){
                                $usr = mysqli_fetch_assoc($q5);
                                header("location: /chat?u=".$usr["u_username"]."&m=$mode");
                                exit;
                            }else{
                                header("location: /");
                                exit;
                            }
                        }
                    }
                }else{
                    
                    echo json_encode(array("status"=>"Access-Denied"));
                }
            }else{
                header("location: /");
                exit;
            }
        }

        public function checkformessageupdate(){
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
            if(isset($_GET["uid"])&&isset($_GET["cid"])){
                $con = $this->model->createConnection();
                $uid = htmlspecialchars($_GET["uid"]); $cid = htmlspecialchars($_GET["cid"]);
                $q1 = mysqli_query($con,"SELECT * FROM `conversations` WHERE (`u_starter`=$uid OR `u_destination`=$uid) AND `c_id`=$cid;");
                if(mysqli_num_rows($q1)>0){
                    $q2 = mysqli_query($con,"SELECT * FROM `user_status_in_conversations` WHERE `c_id`=sid AND `u_id`=uid;")
                }else{
                    header("location: /");
                    exit;
                }
            }
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
                $search = htmlspecialchars($_POST["search"]);
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
                $search = htmlspecialchars($_POST["search"]);
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