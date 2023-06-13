<?php

    /**
    * The account page view
    */
    class AccountView
    {

        private $model;

        private $controller;

        function __construct($controller, $model)
        {
            $this->controller = $controller;

            $this->model = $model;
            
        }

        // public function index()
        // {
            
        // }

        public function setup(){
            
            error_reporting(E_ERROR | E_PARSE);
            if(!$this->controller->isLoggedin()){
                header("location: /login");
                exit;
            }else{
                $user=$this->model->getuserinfofromsession();
                if($user["u_firsttime"]!=1){
                    header("location: /");
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
                <link rel="stylesheet" href="/css/AccountSetup.css"/>
                <link rel="icon" href="/img/favicon.png" type="image/x-icon"/>
                <title>TempTalk - Account Setup</title>
            </head>
            <body class="body-<?=$color_scheme?>">
                <center class="main-box">
                    <div class="navbar navbar-<?=$color_scheme?>">
                        <center>
                            <a href="/"><img class="navbar-logo" alt="TempTalk" src="/img/logo-<?=$color_scheme?>.png"/></a>
                            <span>First time <span class="<?php if($color_scheme=="light")echo "blue"; else echo "red"; ?>">Account Setup</span></span>
                        </center>
                    </div>
                    <?php 
                    if(!isset($_COOKIE["choosenpfp"])&&$_COOKIE["choosenpfp"]!="done"
                        &&((isset($_GET["step"])&&$_GET["step"]=="0")||!isset($_GET["step"]))){
                    ?>
                    <div class="big-panel big-panel-<?=$color_scheme?>">
                        <h1 class="big-panel-heading-<?=$color_scheme?>">Choose Profile Picture</h1>
                        <div class="error" <?php if(isset($_GET["error"])){echo "style=\"display:block;\"";} ?>>
                            <p><?php if(isset($_GET["error"])){echo htmlspecialchars($_GET["error"]);}?></p>
                        </div>
                        <img class="prof-pic" src="/img/profile.png"/>
                        <form id="pfpform" action="/account/pfpupload" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="AccountSetup" value="AccountSetupSession"/>
                            <input type="file" value="/img/profile.png" name="profile-image" accept=".jpeg,.jpg,.png,.gif,.webp,.bmp,.avif"/>
                            <input type="button" class="edit-pic" value="Crop profile pic" onclick="EditPic();"/>
                            <input type="button" class="submit-pic" value="Submit profile pic" onclick="SubmitPic();"/>
                        </form>
                    </div>
                    
                </center>
                <center>
                    <div class="edit-dialog edit-dialog-<?=$color_scheme?>">
                        <div class="close-button">
                            <input type="button" onclick="CloseEditPic();" value="X"/>
                        </div>
                        <div>
                            <img class="prof-pic" src="/img/profile.png" />
                        </div>
                        <div class="overlay-owner">
                            <div class="overlay">
                                <div class="sizable"></div>
                            </div>
                        </div>
                        <input type="button" class="submit-pic" onclick="CropAction();" value="Crop Image"/>
                        
                    </div>
                    <div class="error-dialog error-dialog-<?=$color_scheme?>">
                        <div class="close-button">
                            <input type="button" onclick="CloseError();" value="X"/>
                        </div>
                        We can't crop images larger than 700x700.
                    </div>
                    <?php
                        }else if(isset($_COOKIE["choosenpfp"])&&$_COOKIE["choosenpfp"]=="done"
                        &&!isset($_COOKIE["submit2fa"])&&$_COOKIE["submit2fa"]!="done"
                        &&((isset($_GET["step"])&&$_GET["step"]=="1")||!isset($_GET["step"]))){
                            ?>
                    <div class="twofa-main twofa-main-<?=$color_scheme?>">
                        <h1 class="big-panel-heading-<?=$color_scheme?>" onclick="window.open('https://www.microsoft.com/en-ww/security/business/security-101/what-is-two-factor-authentication-2fa', '_blank');">Two-Factor Authentication</h1>
                        <span class="header-tooltip header-tooltip-<?=$color_scheme?>">Click to see what does it mean.</span>
                        <form id="twofa-form" action="/account/twofa" method="post">
                            <div class="options" id="dontwant">
                                <img class="<?=$color_scheme?>-checkbox" src="/img/Checkbox-White.png"/><span class="checkbox-text checkbox-text-<?=$color_scheme?>">I don't need Two-Factor Authentication</span>
                            </div>
                            <div class="options" id="dowant">
                                <img class="<?=$color_scheme?>-checkbox" src="/img/Checkbox-Checked-White.png"/><span class="checkbox-text checkbox-text-<?=$color_scheme?>">I need Two-Factor Authentication</span>
                            </div>
                            <div class="choices">
                                <span class="text-<?=$color_scheme?>">2FA Option: </span>
                                <select id="selector">
                                    <option>Email Address</option>
                                    <option>SMS</option>
                                    <option>Google Authentication app</option>
                                </select>
                            </div>
                            <input type="hidden" name="authtype" id="authtype" value="Email Address"/>
                            <input type="hidden" name="choice" id="choice" value="dowant"/>
                            <input type="hidden" name="redirectionURL" value="/account/setup"/>
                            <input type="button" class="submit-2fa" value="Submit" onclick="SubmitTwoFA();"/>
                        </form>
                    </div>
                            <?php
                        }else if(isset($_COOKIE["choosenpfp"])&&$_COOKIE["choosenpfp"]=="done"
                        &&isset($_COOKIE["submit2fa"])&&$_COOKIE["submit2fa"]=="done"
                        &&!isset($_COOKIE["finaltouch"])&&$_COOKIE["finaltouch"]!="done"
                        &&((isset($_GET["step"])&&$_GET["step"]=="2")||!isset($_GET["step"]))){
                    ?>
                        <div class="last-main last-main-<?=$color_scheme?>">
                            <h1 class="big-panel-heading-<?=$color_scheme?>">Fill last informations</h1>
                            <form id="last-form" action="/account/completeinfo" method="post">
                                <div class="choices">
                                    <span class="text-<?=$color_scheme?>">Birthdate: </span>
                                    <select class="selector" id="year-selector">
                                        <?php 
                                            for ($i=date("Y"); $i>(date("Y")-150); $i--) { 
                                                echo "<option>$i</option>";
                                            }
                                        ?>
                                    </select>
                                    <select class="selector" id="month-selector">
                                        <option>January</option>
                                        <option>February</option>
                                        <option>March</option>
                                        <option>April</option>
                                        <option>May</option>
                                        <option>June</option>
                                        <option>July</option>
                                        <option>August</option>
                                        <option>September</option>
                                        <option>October</option>
                                        <option>November</option>
                                        <option>December</option>
                                    </select>
                                    <select class="selector" id="day-selector">
                                        <?php 
                                            for ($i=1; $i<31; $i++) { 
                                                echo "<option>".sprintf("%02d", $i)."</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="options">
                                    <span class="titles titles-<?=$color_scheme?>">Bio: </span>
                                    <textarea name="bio"></textarea>
                                </div>
                                <input type="hidden" name="birthyear" id="birthyear" value="<?php echo date("Y"); ?>"/>
                                <input type="hidden" name="birthmonth" id="birthmonth" value="January"/>
                                <input type="hidden" name="birthday" id="birthday" value="01"/>
                                <input type="button" class="button" id="submit-info" value="Finish it!!" onclick="SubmitInfos();"/>
                            </form>
                        </div>
                    <?php
                        }
                    ?>
                </center>
                <script src="https://cdn.jsdelivr.net/npm/js-cookie/dist/js.cookie.min.js"></script>
                <script><?php require_once 'js/Cookie.js'; ?></script>
                <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
                <script><?php require_once 'js/AccountSetup.js'; require_once 'Models/Dev.php'; if(!DEVELOPEMENT_STATUS){ require_once 'js/ProductionMode.js'; } ?></script>
            </body>
            </html>
            <?php
        }
        public function completeinfo(){
            if(!$this->controller->isLoggedin()){
                header("location: /login");
                exit;
            }else{
                $user=$this->model->getuserinfofromsession();
                if($user["u_firsttime"]!=1){
                    header("location: /");
                    exit;
                }
            }
            if(isset($_COOKIE["choosenpfp"])&&$_COOKIE["choosenpfp"]=="done"
            &&isset($_COOKIE["submit2fa"])&&$_COOKIE["submit2fa"]=="done"
            &&!isset($_COOKIE["finaltouch"])&&$_COOKIE["finaltouch"]!="done"){
                if(isset($_POST["bio"])&&isset($_POST["birthyear"])&&isset($_POST["birthmonth"])&&isset($_POST["birthday"])){
                    $bio = $_POST["bio"];
                    $year = $_POST["birthyear"];
                    $month = $_POST["birthmonth"];
                    $day = $_POST["birthday"];
                    $this->controller->finishit($bio,['year'=>$year,'month'=>$month,'day'=>$day]);
                }else{
                    header("location: /account/setup");
                    exit;
                }
            }else{
                header("location: /account/setup");
                exit;
            }
        }
        public function pfpupload(){
            // var_dump($_POST);
            error_reporting(E_ERROR | E_PARSE);
            if(!isset($_COOKIE["choosenpfp"])&&$_COOKIE["choosenpfp"]!="done"){
                if (isset($_FILES["profile-image"])&&isset($_POST["AccountSetup"])
                    &&$_POST["AccountSetup"]=="AccountSetupSession") {
                    
                    $fileName = htmlspecialchars($_FILES['profile-image']['name']);
                    $fileSize = $_FILES['profile-image']['size'];
                    $fileTmpName  = $_FILES['profile-image']['tmp_name'];
                    // $fileType = $_FILES['the_file']['type'];
                    $fileExtension = strtolower(end(explode('.',$fileName)));
                    $currentDirectory = dirname( dirname(__FILE__) );
                    $uploadDirectory = $this->model->getImageUploadLocation();
                    $file_name = "upload_image_".md5(basename($fileName));
                    if(basename($fileName)==""){
                        $uploadDirectory = "/img/";
                        $file_name = "profile";
                        $uploadPath = $currentDirectory."/img/profile.png";
                    }else
                        $uploadPath = $currentDirectory . $uploadDirectory . $file_name .".".$fileExtension; 
                    
                    if ($fileSize > 10000000) {
                        header("location: /account/setup?error=Err 008: File is bigger than maximum size (10MB)");
                        exit;
                    }

                    // var_dump($_POST);

                    $Upload = move_uploaded_file($fileTmpName, $uploadPath);
                    if ($Upload) {
                        if(isset($_POST["cropwidth"])&&isset($_POST["cropheight"])){
                            
                            if($fileExtension=="jpeg"||$fileExtension=="jpg")
                                $img = imagecreatefromjpeg($uploadPath);
                            else if($fileExtension=="png")
                                $img = imagecreatefrompng($uploadPath);
                            else if($fileExtension=="gif")
                                $img = imagecreatefromgif($uploadPath);
                            else if($fileExtension=="webp")
                                $img = imagecreatefromwebp($uploadPath);
                            else if($fileExtension=="bmp")
                                $img = imagecreatefrombmp($uploadPath);
                            else if($fileExtension=="avif")
                                $img = imagecreatefromavif($uploadPath);
                            else
                                $img = imagecreatefrompng($uploadPath);


                            $crop_x = (imagesx($img)-$_POST["cropwidth"])/2;
                            $crop_y = 0;
                            $image2 = imagecrop($img,['x'=>$crop_x,'y'=>$crop_y,"width"=>intval($_POST["cropwidth"]),"height"=>intval($_POST["cropheight"])]);
                            if($image2 !== FALSE) {
                                if($fileExtension=="gif"){
                                    $fileExtension = "gif";
                                    imagegif($image2, ($currentDirectory . $uploadDirectory . $file_name."_croped" .".".$fileExtension));
                                }else{    
                                    $fileExtension = "png";
                                    imagepng($image2, ($currentDirectory . $uploadDirectory . $file_name."_croped" .".".$fileExtension));
                                }    
                            }

                            $imageInfo = getimagesize(($currentDirectory . $uploadDirectory . $file_name."_croped" .".".$fileExtension));

                            $result['oldSize'] = filesize(($currentDirectory . $uploadDirectory . $file_name."_croped" .".".$fileExtension)) / 1024 . 'KB';
                            $result['mime'] = $imageInfo['mime'];


                            if ($imageInfo['mime'] == 'image/gif') {

                                $imageLayer = imagecreatefromgif(($currentDirectory . $uploadDirectory . $file_name."_croped" .".".$fileExtension));
                            } elseif ($imageInfo['mime'] == 'image/jpeg') {

                                $imageLayer = imagecreatefromjpeg(($currentDirectory . $uploadDirectory . $file_name."_croped" .".".$fileExtension));
                            } elseif ($imageInfo['mime'] == 'image/png') {

                                $imageLayer = imagecreatefrompng(($currentDirectory . $uploadDirectory . $file_name."_croped" .".".$fileExtension));
                            }else if($fileExtension=="webp")
                                $imageLayer = imagecreatefromwebp(($currentDirectory . $uploadDirectory . $file_name."_croped" .".".$fileExtension));
                            else if($fileExtension=="bmp")
                                $imageLayer = imagecreatefrombmp(($currentDirectory . $uploadDirectory . $file_name."_croped" .".".$fileExtension));
                            else if($fileExtension=="avif")
                                $imageLayer = imagecreatefromavif(($currentDirectory . $uploadDirectory . $file_name."_croped" .".".$fileExtension));


                            if ($imageInfo['mime'] == 'image/gif') {
                                $compressedImage = imagegif($imageLayer, ($currentDirectory . $uploadDirectory . $file_name."_croped_compressed" .".".$fileExtension));
                            } elseif ($imageInfo['mime'] == 'image/jpeg') {
                                $compressedImage = imagejpeg($imageLayer, ($currentDirectory . $uploadDirectory . $file_name."_croped_compressed" .".".$fileExtension), 20);
                            } elseif ($imageInfo['mime'] == 'image/png') {
                                $compressedImage = imagepng($imageLayer, ($currentDirectory . $uploadDirectory . $file_name."_croped_compressed" .".".$fileExtension), 2);
                            }else if($fileExtension=="webp")
                                $compressedImage = imagewebp($imageLayer, ($currentDirectory . $uploadDirectory . $file_name."_croped_compressed" .".".$fileExtension), 20);
                            else if($fileExtension=="bmp")
                                $compressedImage = imagebmp($imageLayer, ($currentDirectory . $uploadDirectory . $file_name."_croped_compressed" .".".$fileExtension), true);
                            else if($fileExtension=="avif")
                                $compressedImage = imageavif($imageLayer, ($currentDirectory . $uploadDirectory . $file_name."_croped_compressed" .".".$fileExtension), 20);
                            else
                                $compressedImage = imagepng($imageLayer, ($currentDirectory . $uploadDirectory . $file_name."_croped_compressed" .".".$fileExtension), 2);
                            
                            
                            
                            $finalimage = $this->model->image_resize(($currentDirectory . $uploadDirectory . $file_name."_croped_compressed" .".".$fileExtension),$fileExtension,400,400);
                            
                            if ($imageInfo['mime'] == 'image/gif') {
                                $finalimagefile = imagegif($finalimage, ($currentDirectory . $uploadDirectory . $file_name."_croped_compressed_resized" .".".$fileExtension));
                            } elseif ($imageInfo['mime'] == 'image/jpeg') {
                                $finalimagefile = imagejpeg($finalimage, ($currentDirectory . $uploadDirectory . $file_name."_croped_compressed_resized" .".".$fileExtension), 20);
                            } elseif ($imageInfo['mime'] == 'image/png') {
                                $finalimagefile = imagepng($finalimage, ($currentDirectory . $uploadDirectory . $file_name."_croped_compressed_resized" .".".$fileExtension), 2);
                            }else if($fileExtension=="webp")
                                $finalimagefile = imagewebp($finalimage, ($currentDirectory . $uploadDirectory . $file_name."_croped_compressed_resized" .".".$fileExtension), 20);
                            else if($fileExtension=="bmp")
                                $finalimagefile = imagebmp($finalimage, ($currentDirectory . $uploadDirectory . $file_name."_croped_compressed_resized" .".".$fileExtension), true);
                            else if($fileExtension=="avif")
                                $finalimagefile = imageavif($finalimage, ($currentDirectory . $uploadDirectory . $file_name."_croped_compressed_resized" .".".$fileExtension), 20);
                            else
                                $finalimagefile = imagepng($finalimage, ($currentDirectory . $uploadDirectory . $file_name."_croped_compressed_resized" .".".$fileExtension), 20);

                            if ($finalimagefile) {
                                $this->controller->setProfile(($uploadDirectory . $file_name."_croped_compressed_resized".".".$fileExtension));
                            } else {
                                header("location: /account/setup?error=Err 011: Couldn't Compress your profile image. Use proper file extentions like png,jpg,bmp,webp,gif.");
                                exit;
                            }
                        }else{
                            $imageInfo = getimagesize($uploadPath);

                            $result['oldSize'] = filesize($uploadPath) / 1024 . 'KB';
                            $result['mime'] = $imageInfo['mime'];


                            if ($imageInfo['mime'] == 'image/gif') {

                                $imageLayer = imagecreatefromgif($uploadPath);
                            } elseif ($imageInfo['mime'] == 'image/jpeg') {

                                $imageLayer = imagecreatefromjpeg($uploadPath);
                            } elseif ($imageInfo['mime'] == 'image/png') {

                                $imageLayer = imagecreatefrompng($uploadPath);
                            }else if($fileExtension=="webp")
                                $imageLayer = imagecreatefromwebp($uploadPath);
                            else if($fileExtension=="bmp")
                                $imageLayer = imagecreatefrombmp($uploadPath);
                            else if($fileExtension=="avif")
                                $imageLayer = imagecreatefromavif($uploadPath);


                                if ($imageInfo['mime'] == 'image/gif') {
                                    $compressedImage = imagegif($imageLayer, ($currentDirectory . $uploadDirectory . $file_name."_compressed" .".".$fileExtension));
                                } elseif ($imageInfo['mime'] == 'image/jpeg') {
                                    $compressedImage = imagejpeg($imageLayer, ($currentDirectory . $uploadDirectory . $file_name."_compressed" .".".$fileExtension), 20);
                                } elseif ($imageInfo['mime'] == 'image/png') {
                                    $compressedImage = imagepng($imageLayer, ($currentDirectory . $uploadDirectory . $file_name."_compressed" .".".$fileExtension), 2);
                                }else if($fileExtension=="webp")
                                    $compressedImage = imagewebp($imageLayer, ($currentDirectory . $uploadDirectory . $file_name."_compressed" .".".$fileExtension), 20);
                                else if($fileExtension=="bmp")
                                    $compressedImage = imagebmp($imageLayer, ($currentDirectory . $uploadDirectory . $file_name."_compressed" .".".$fileExtension), true);
                                else if($fileExtension=="avif")
                                    $compressedImage = imageavif($imageLayer, ($currentDirectory . $uploadDirectory . $file_name."_compressed" .".".$fileExtension), 20);
                                else
                                    $compressedImage = imagepng($imageLayer, ($currentDirectory . $uploadDirectory . $file_name."_compressed" .".".$fileExtension), 20);
                                
                                
                                
                                $finalimage = $this->model->image_resize(($currentDirectory . $uploadDirectory . $file_name."_compressed" .".".$fileExtension),$fileExtension,400,400);
                                
                                if ($imageInfo['mime'] == 'image/gif') {
                                    $finalimagefile = imagegif($finalimage, ($currentDirectory . $uploadDirectory . $file_name."_compressed_resized" .".".$fileExtension));
                                } elseif ($imageInfo['mime'] == 'image/jpeg') {
                                    $finalimagefile = imagejpeg($finalimage, ($currentDirectory . $uploadDirectory . $file_name."_compressed_resized" .".".$fileExtension), 20);
                                } elseif ($imageInfo['mime'] == 'image/png') {
                                    $finalimagefile = imagepng($finalimage, ($currentDirectory . $uploadDirectory . $file_name."_compressed_resized" .".".$fileExtension), 2);
                                }else if($fileExtension=="webp")
                                    $finalimagefile = imagewebp($finalimage, ($currentDirectory . $uploadDirectory . $file_name."_compressed_resized" .".".$fileExtension), 20);
                                else if($fileExtension=="bmp")
                                    $finalimagefile = imagebmp($finalimage, ($currentDirectory . $uploadDirectory . $file_name."_compressed_resized" .".".$fileExtension), 20);
                                else if($fileExtension=="avif")
                                    $finalimagefile = imageavif($finalimage, ($currentDirectory . $uploadDirectory . $file_name."_compressed_resized" .".".$fileExtension), true);
                                else
                                    $finalimagefile = imagepng($finalimage, ($currentDirectory . $uploadDirectory . $file_name."_compressed_resized" .".".$fileExtension), 20);
    
                            if ($finalimagefile) {
                                $this->controller->setProfile(($uploadDirectory . $file_name."_compressed_resized" .".".$fileExtension));
                            } else {
                                header("location: /account/setup?error=Err 011: Couldn't Compress your profile image. Use proper file extentions like png,jpg,bmp,webp,gif.");
                                exit;
                            }
                        }
                    } else {
                        header("location: /account/setup?error=Err 009: An error occurred in accessing files. Please contact the administrator.");
                        exit;
                    }
                }else{
                    header("location: /account/setup?nofile");
                    exit;
                }
            }else{
                header("location: /account/setup");
                exit;
            }
        }

        public function twofa(){
            error_reporting(E_ERROR | E_PARSE);
            if(!$this->controller->isLoggedin()){
                header("location: /login");
                exit;
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
                <link rel="stylesheet" href="/css/AccountTwoFA.css"/>
                <link rel="icon" href="/img/favicon.png" type="image/x-icon"/>
                <title>TempTalk - 2FA Setup</title>
            </head>
            <body class="body-<?=$color_scheme?>">
                <center class="main-box">
                    <div class="navbar navbar-<?=$color_scheme?>">
                        <center>
                            <a href="/"><img class="navbar-logo" alt="TempTalk" src="/img/logo-<?=$color_scheme?>.png"/></a>
                        </center>
                    </div>
                    <div class="twofa-section twofa-section-<?=$color_scheme?>">
                        <div class="error">
                            <p></p>
                        </div>
                        <h1 class="heading-<?=$color_scheme?>">Adding Two-Factor</h1>
                        <?php if((isset($_POST["authtype"])&&$_POST["authtype"]=="Email Address")||
                                    (isset($_GET["authtype"])&&$_GET["authtype"]=="Email Address")){ ?>
                        <span class="titles titles-<?=$color_scheme?>">Email Address: </span>
                        <input type="email" id="email" required/>
                        <input type="button" class="button" id="EmailValidate" value="Validate" onclick="EmailValidate();"/><br/>
                        <div class="code-prompt">
                            <span class="titles titles-<?=$color_scheme?>">Code: </span>
                            <input type="text" id="code" required maxlength="7"/>
                            <input type="button" class="button" id="SubmitCode" value="Submit" onclick="SubmitCodeEmail();" disabled/>
                        </div>
                        <?php } ?>
                    </div>
                </center>
                <script src="https://cdn.jsdelivr.net/npm/js-cookie/dist/js.cookie.min.js"></script>
                <script><?php require_once 'js/Cookie.js'; ?></script>
                <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
                <?php
                if(isset($_POST["redirectionURL"])){
                ?>
                <script>var url = "<?php echo $_POST["redirectionURL"]; ?>";</script>
                <?php
                }else{
                ?>
                <script>var url = "/";</script>
                <?php
                }
                ?>
                <script><?php require_once 'js/AccountTwoFA.js';  require_once 'Models/Dev.php'; if(!DEVELOPEMENT_STATUS){ require_once 'js/ProductionMode.js'; } ?></script>
            </body>
            </html>
            <?php
        }
        
        public function verifytwofa(){
            if(isset($_POST["email"])){
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
                header("location: /");
                exit;
            }
        }

        public function twofaset(){
            error_reporting(E_ERROR | E_PARSE);
            if(!$this->controller->isLoggedin()){
                header("location: /login");
                exit;
            }else{
                $user=$this->model->getuserinfofromsession();
                if($user["u_firsttime"]!=1){
                    if(isset($_POST["set"])&&$_POST["set"]!="none"){
                        if($_POST["set"]=="email"){

                        }
                    }else{
                        header("location: /");
                        exit;
                    }
                }else{

                    if(isset($_COOKIE["choosenpfp"])&&$_COOKIE["choosenpfp"]=="done"
                          &&!isset($_COOKIE["submit2fa"])&&$_COOKIE["submit2fa"]!="done"){
                            if(isset($_GET["set"])&&$_GET["set"]=="none"){
                                setcookie("submit2fa","done",time()+(60*60*24*7),'/');
                                header("location: /account/setup?step=2");
                                exit;
                            }else{
                                if(isset($_POST["set"])&&isset($_POST["email"])&&isset($_POST["code"])){
                                    if($_POST["set"]!="none"){
                                        echo $this->controller->is_2fa_code_valid(htmlspecialchars($_POST["code"]),$_POST["email"]);
                                    }
                                }else{
                                    header("location: /account/setup");
                                    exit;
                                }
                            }
                    }
                }
            }
        }




    }

    