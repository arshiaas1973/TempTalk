<?php

    /**
    * The home page view
    */
    class TestView
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
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>TempMail - Please verify your 2FA email</title>
                <style>
                    @import url("https://fonts.cdnfonts.com/css/rubik-marker-hatch");
                    @import url("https://fonts.cdnfonts.com/css/boogaloo");
                    @import url("https://fonts.cdnfonts.com/css/share-techmono-2");
                    body{
                        width: 100%;
                        height: 100%;
                        padding: 0px;
                        margin: 0px;
                    }
                    .body-light{ background-color: #ECF0F1; }
                    .body-dark{ background-color:#34495E; }
                    ul{ list-style: none; }
                    a{ text-decoration: none; }
                    h1{ font-family: "Rubik Marker Hatch", sans-serif; font-weight: normal;}
                    *:focus{ outline: none;}
                    .email-section{
                        max-width: 600px;
                        margin-top: 60px;
                        padding: 20px;
                        border-radius: 40px;
                    }
                    .email-section-light{ background-color: #E5E5E5;}
                    .email-section-dark{ background-color: #334155;}
                    .heading-light{color: #000;}
                    .heading-dark{color: aliceblue;}
                    .email-section span.titles{
                        display: flex;
                        font-family: "Boogaloo", sans-serif;
                        font-size: 25px; 
                        justify-content: center;
                        margin-left: 20px;
                    }
                    .email-section span.titles-light{color: #232323;}
                    .email-section span.titles-dark{color: #dedede;}
                    .email-section div.code-section{
                        padding: 10px;
                        border-radius: 10px;
                        margin-top: 25px;
                        font-size: 30px;
                        font-family: "Share-TechMono", sans-serif;
                        width: -webkit-fit-content;
                        width: -moz-fit-content;
                        width: fit-content;
                    }
                    .email-section div.code-section-light{background:#E1F5FE; color:#121212;}
                    .email-section div.code-section-dark{background:#607D8B; color:#dedede;}
                </style>
            </head>
            <body class="body">
                <center class="main-box">
                    <div class="email-section">
                        <h1 class="email-heading">Verify your 2FA Email</h1>
                        <span class="titles">This code is your 2FA Email Verification.</span>
                        <span class="titles">Don't show this code to anybody else. Just put this code the textbox that site provides.</span>
                        <div class="code-section">#Code_Here#</span>
                    </div>
                </center>
                <script src="https://cdn.jsdelivr.net/npm/js-cookie/dist/js.cookie.min.js"></script>
                <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
                <script>
                    var $color_scheme = Cookies.get("color_scheme");
                    function get_color_scheme() {
                    return (window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches) ? "dark" : "light";
                    }
                    function update_color_scheme() {
                        Cookies.set("color_scheme", get_color_scheme());
                        
                    }
                    if ((typeof $color_scheme === "undefined") || (get_color_scheme() != $color_scheme))
                    update_color_scheme();
                    if (window.matchMedia)
                    window.matchMedia("(prefers-color-scheme: dark)").addListener( update_color_scheme );
                    $("body").addClass("body-"+get_color_scheme()); 
                    $("div.email-section").addClass("email-section-"+get_color_scheme());
                    $("h1.email-heading").addClass("heading-"+get_color_scheme());
                    $("span.titles").addClass("titles-"+get_color_scheme());
                    $("div.code-section").addClass("code-section-"+get_color_scheme());
                </script>
            </body>
            </html>
            <?php
        }

        public function error(){
            ?>
            <br />
<font size='1'><table class='xdebug-error xe-warning' dir='ltr' border='1' cellspacing='0' cellpadding='1'>
<tr><th align='left' bgcolor='#f57900' colspan="5"><span style='background-color: #cc0000; color: #fce94f; font-size: x-large;'>( ! )</span> Warning: mail(): Failed to connect to mailserver at &amp;quot;localhost&amp;quot; port 25, verify your &amp;quot;SMTP&amp;quot; and &amp;quot;smtp_port&amp;quot; setting in php.ini or use ini_set() in C:\wamp64\www\TempTalk\Models\Config.php on line <i>18</i></th></tr>
<tr><th align='left' bgcolor='#e9b96e' colspan='5'>Call Stack</th></tr>
<tr><th align='center' bgcolor='#eeeeec'>#</th><th align='left' bgcolor='#eeeeec'>Time</th><th align='left' bgcolor='#eeeeec'>Memory</th><th align='left' bgcolor='#eeeeec'>Function</th><th align='left' bgcolor='#eeeeec'>Location</th></tr>
<tr><td bgcolor='#eeeeec' align='center'>1</td><td bgcolor='#eeeeec' align='center'>0.0407</td><td bgcolor='#eeeeec' align='right'>366952</td><td bgcolor='#eeeeec'>{main}(  )</td><td title='C:\wamp64\www\TempTalk\index.php' bgcolor='#eeeeec'>...\index.php<b>:</b>0</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>2</td><td bgcolor='#eeeeec' align='center'>0.0430</td><td bgcolor='#eeeeec' align='right'>368624</td><td bgcolor='#eeeeec'>AccountView->verifytwofa( <span>[]</span> )</td><td title='C:\wamp64\www\TempTalk\index.php' bgcolor='#eeeeec'>...\index.php<b>:</b>62</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>3</td><td bgcolor='#eeeeec' align='center'>0.0661</td><td bgcolor='#eeeeec' align='right'>372168</td><td bgcolor='#eeeeec'>send_email( <span>$to = </span><span>&#39;arshiaas1973@gmail.com&#39;</span>, <span>$subject = </span><span>&#39;Please verify your 2FA email&#39;</span>, <span>$body = </span><span>&#39;&lt;!DOCTYPE html&gt; &lt;html lang=&quot;en&quot;&gt; &lt;head&gt; &lt;meta charset=&quot;UTF-8&quot;&gt; &lt;meta http-equiv=&quot;X-UA-Compatible&quot; content=&quot;IE=edge&quot;&gt; &lt;meta name=&quot;viewport&quot; content=&quot;width=device-width, initial-scale=1.0&quot;&gt; &lt;title&gt;TempMail - Please verify your 2FA email&lt;/title&gt; &lt;style&gt; @import url(&quot;https://fonts.cdnfonts.com/css/rubik-marker-hatch&quot;); @import url(&quot;https://fonts.cdnfonts.com/css/boogaloo&quot;); @import url(&quot;https://fonts.cdnfonts.com/css/share-techmono-2&quot;); body{ width: 100%; height: 100%; padding: 0px; margin: 0px; } .body-light{ &#39;...</span>, <span>$isHTML = </span>??? )</td><td title='C:\wamp64\www\TempTalk\Views\AccountView.php' bgcolor='#eeeeec'>...\AccountView.php<b>:</b>408</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>4</td><td bgcolor='#eeeeec' align='center'>0.0662</td><td bgcolor='#eeeeec' align='right'>372296</td><td bgcolor='#eeeeec'><a href='http://www.php.net/function.mail' target='_new'>mail</a>( <span>$to = </span><span>&#39;arshiaas1973@gmail.com&#39;</span>, <span>$subject = </span><span>&#39;Please verify your 2FA email&#39;</span>, <span>$message = </span><span>&#39;&lt;!DOCTYPE html&gt; &lt;html lang=&quot;en&quot;&gt; &lt;head&gt; &lt;meta charset=&quot;UTF-8&quot;&gt; &lt;meta http-equiv=&quot;X-UA-Compatible&quot; content=&quot;IE=edge&quot;&gt; &lt;meta name=&quot;viewport&quot; content=&quot;width=device-width, initial-scale=1.0&quot;&gt; &lt;title&gt;TempMail - Please verify your 2FA email&lt;/title&gt; &lt;style&gt; @import url(&quot;https://fonts.cdnfonts.com/css/rubik-marker-hatch&quot;); @import url(&quot;https://fonts.cdnfonts.com/css/boogaloo&quot;); @import url(&quot;https://fonts.cdnfonts.com/css/share-techmono-2&quot;); body{ width: 100%; height: 100%; padding: 0px; margin: 0px; } .body-light{ &#39;...</span>, <span>$additional_headers = </span><span>&#39;From: tempmaildev2023@gmail.com\r\nMIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8&#39;</span> )</td><td title='C:\wamp64\www\TempTalk\Models\Config.php' bgcolor='#eeeeec'>...\Config.php<b>:</b>18</td></tr>
</table></font>
            <?php
        }

    }