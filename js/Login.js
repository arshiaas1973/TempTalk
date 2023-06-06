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
function Submit(){
    var form = document.getElementById("loginForm");
    var user = document.getElementById("username").value;
    var pass = document.getElementById("password").value;
    if (ContainsWords(user)&&ContainsWords(pass)) {
        var hidden = "<input type=\"hidden\" name=\"passcode\" value=\""+
            CryptoJS.MD5(user+"|"+pass).toString()
            +"\"/>"
        $("#loginForm").append(hidden);
        form.submit();
    }else{
        if(!ContainsWords(user)){
            $("#loginForm .error p").text("You haven't entered your username.");
            $("#loginForm #username-label").css("color","#B71C1C");
            $("#loginForm #username").css("border-color","#B71C1C");
            $("#loginForm #username").css("border-color","#B71C1C");
            $("#loginForm .error").css("display","block");
            if(ContainsWords(pass)){
                $("#loginForm #password-label").css("color","#34495E");
                $("#loginForm #password").css("border-color","#34495E");
                $("#loginForm #password").css("border-color","#34495E");
            }
        }else{
            $("#loginForm .error p").text("You haven't entered your password.");
            $("#loginForm #password-label").css("color","#B71C1C");
            $("#loginForm #password").css("border-color","#B71C1C");
            $("#loginForm #password").css("border-color","#B71C1C");
            $("#loginForm .error").css("display","block");
            if(ContainsWords(user)){
                $("#loginForm #username-label").css("color","#34495E");
                $("#loginForm #username").css("border-color","#34495E");
                $("#loginForm #username").css("border-color","#34495E");
            }
        }
    }
}

$("#loginForm .eye-icon").click(function(){
    var arg = $("#loginForm .eye-icon").attr("id");
    if(arg=="opened"){
        $("#loginForm #password").attr("type","text");
        $("#loginForm .eye-icon img").attr("src", "/img/eye-closed.png");
        $("#loginForm .eye-icon").attr("id", "closed");
    }else{
        $("#loginForm #password").attr("type","password");
        $("#loginForm .eye-icon img").attr("src", "/img/eye.png");
        $("#loginForm .eye-icon").attr("id", "opened");
    }
});

function Interval(){
    $(".login-panel .login-loading").css("display", "block");
    $(".login-panel .login-loading").css("opacity", "1");
    setInterval(() => {
        $(".login-panel .login-loading").css("tranform", "rotate3d(0, 1, 0, 360deg);");
    }, 1000);
}