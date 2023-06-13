<?php
    define("MAIL_HOST","smtp.gmail.com");
    define("MAIL_PORT",587);
    define("MAIL_EMAIL","temptalkdev@gmail.com");
    define("MAIL_USER","temptalkdev@gmail.com");
    define("MAIL_PASS","ruwvookerypbgbqw");

    function send_email($to, $subject, $body, $isHTML=true)
    {
        if($isHTML){
            $headers = "From: ".MAIL_USER."\r\n".
                "MIME-Version: 1.0\r\n".
                "Content-type: text/html; charset=utf-8";
        }else{
            $headers = "From: ".MAIL_USER."\r\n".
                "MIME-Version: 1.0\r\n".
                "Content-type: text/plain; charset=utf-8";
        }
        if(mail($to, $subject, $body, $headers))
            return true;
        else
            return false;
    }