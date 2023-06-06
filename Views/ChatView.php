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
                    <link rel="stylesheet" type="text/css" href="/css/Chat.css"/>
                    <link rel="icon" href="/img/favicon.png" type="image/x-icon"/>
                    <title>TempTalk - Chat</title>
                    
                </head>
                <body class="body-<?=$color_scheme?>">
                    <script src="https://cdn.jsdelivr.net/npm/js-cookie/dist/js.cookie.min.js"></script>
                    <script><?php require_once 'js/Cookie.js'; ?></script>
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
        }

    }