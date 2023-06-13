var is_checkbox_checked = false;
var color_prev = "#fff";
$(".register-panel form .submit").attr('disabled', 'disabled');
function ContainsWords(string) {  
    var words = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789~`_-+={}[]|\":\\/.";
    var wordsarray = words.split('');
    for (let i = 0; i < string.length; i++) {
        for (let j = 0; j < words.length; j++) {
            if(string[i] == words[j])
                return true;
        }
    }
    return false;
}

function ContainsLetters(string) {  
    var words = "abcdefghijklmnopqrstuvwxyz";
    var wordsarray = words.split('');
    for (let i = 0; i < string.length; i++) {
        for (let j = 0; j < words.length; j++) {
            if(string[i] == words[j])
                return 1;
        }
    }
    return 0;
}

function ContainsCapitalLetters(string) {  
    var words = "QWERTYUIOPASDFGHJKLZXCVBNM";
    var wordsarray = words.split('');
    for (let i = 0; i < string.length; i++) {
        for (let j = 0; j < words.length; j++) {
            if(string[i] == words[j])
                return 1;
        }
    }
    return 0;
}

function ContainsNumbers(string) {  
    var words = "0123456789";
    var wordsarray = words.split('');
    for (let i = 0; i < string.length; i++) {
        for (let j = 0; j < words.length; j++) {
            if(string[i] == words[j])
                return 1;
        }
    }
    return 0;
}

function ContainsSymbols(string) {  
    var words = "~`_-!@#$%^&*()+=[{]};:'\"\\|,./<>?";
    var wordsarray = words.split('');
    for (let i = 0; i < string.length; i++) {
        for (let j = 0; j < words.length; j++) {
            if(string[i] == words[j])
                return 1;
        }
    }
    return 0;
}

function resetErrors() {
    $("#registerForm #username-label").css("color","#000");
    $("#registerForm #username").css("border-color","#34495E");
    $("#registerForm #username").css("border-color","#34495E");

    $("#registerForm #password-label").css("color","#000");
    $("#registerForm #password").css("border-color","#34495E");
    $("#registerForm #password").css("border-color","#34495E");

    $("#registerForm #email-label").css("color","#000");
    $("#registerForm #email").css("border-color","#34495E");
    $("#registerForm #email").css("border-color","#34495E");
}

function passwordCheck(){
    var text = $("#registerForm #password").val();
    var safty = ContainsCapitalLetters(text)+ContainsNumbers(text)+ContainsSymbols(text);
    if (text.length<6) {
       color_prev = "#DC2626"; 
    }else if(text.length<13){
        color_prev = "#CA8A04";
    }else if(text.length>=13){
        color_prev = "#047857";
    }
    switch (safty) {
        case 0:
            $("#registerForm #password").css("border-color","#fff !important");
            break;
        case 1:
            $("#registerForm #password").css("border-color", color_prev+" !important");
            $("#registerForm #password").css("border-style", "dotted !important");
            break;
        case 2:
            $("#registerForm #password").css("border-color", color_prev+" !important");
            $("#registerForm #password").css("border-style", "dashed !important");
            break;
        case 2:
            $("#registerForm #password").css("border-color", color_prev+" !important");
            $("#registerForm #password").css("border-style", "solid !important");
            break;
        default:
            break;
    }
}

function Submit(){
    $(".register-panel form input[type=button].submit").attr("disabled","disabled");
    var form = document.getElementById("registerForm");
    var user = document.getElementById("username").value;
    var pass = document.getElementById("password").value;
    var displayedname = document.getElementById("displayedname").value;
    var email = document.getElementById("email").value;
    if (ContainsWords(user)&&ContainsWords(pass)&&ContainsWords(email)) {
        if (!ContainsWords(displayedname)) {
            document.getElementById("displayedname").value = user;
        }
        var hidden = "<input type=\"hidden\" name=\"passcode\" value=\""+
            CryptoJS.MD5(user+"|"+pass+"|"+document.getElementById("displayedname").value+"|"+email).toString()
            +"\"/>"
        $("#registerForm").append(hidden);
        form.submit();
    }else{
        resetErrors();
        if(!ContainsWords(email)){
            $("#registerForm .error p").text("You haven't entered your email.");
            $("#registerForm #email-label").css("color","#B71C1C");
            $("#registerForm #email").css("border-color","#B71C1C");
            $("#registerForm #email").css("border-color","#B71C1C");
            $("#registerForm .error").css("display","block");
        }else if(!ContainsWords(user)){
            $("#registerForm .error p").text("You haven't entered your username.");
            $("#registerForm #username-label").css("color","#B71C1C");
            $("#registerForm #username").css("border-color","#B71C1C");
            $("#registerForm #username").css("border-color","#B71C1C");
            $("#registerForm .error").css("display","block");
        }else if(!ContainsWords(pass)){
            $("#registerForm .error p").text("You haven't entered your password.");
            $("#registerForm #password-label").css("color","#B71C1C");
            $("#registerForm #password").css("border-color","#B71C1C");
            $("#registerForm #password").css("border-color","#B71C1C");
            $("#registerForm .error").css("display","block");
        }
        $(".register-panel form input[type=button].submit").removeAttr("disabled");
    }
}

function onUsernameChanged(){
    $("#registerForm #displayedname").val($("#registerForm #username").val());
}
$("#registerForm #username").keyup(onUsernameChanged());
$("#registerForm .eye-icon").click(function(){
    var arg = $("#registerForm .eye-icon").attr("id");
    if(arg=="opened"){
        $("#registerForm #password").attr("type","text");
        $("#registerForm .eye-icon img").attr("src", "/img/eye-closed.png");
        $("#registerForm .eye-icon").attr("id", "closed");
    }else{
        $("#registerForm #password").attr("type","password");
        $("#registerForm .eye-icon img").attr("src", "/img/eye.png");
        $("#registerForm .eye-icon").attr("id", "opened");
    }
});

$(".register-panel form .agreement img").click(function (e) { 
    if (is_checkbox_checked) {
        $(".register-panel form .agreement img").attr("src","/img/Checkbox.png");
        $(".register-panel form .submit").attr('disabled', 'disabled');
    }else{
        $(".register-panel form .agreement img").attr("src","/img/Checkbox-Checked.png");
        $(".register-panel form .submit").removeAttr("disabled");
    }
    is_checkbox_checked = !is_checkbox_checked;
});
