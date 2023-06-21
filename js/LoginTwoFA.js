$("input[type=email]#email").val(email);
var date = 0;
function ValidateInput() {
    var text = $("#email").val();
    if((text.indexOf("@")>=0)&&(text.indexOf(".")>=0)) {
        if((text.split('@').length>=2)&&(text.split('.').length>=2))
            if((text.split('@')[(text.split('@').length-1)].split('.').length>=2)&&(text.split('.')[(text.split('.').length-2)].split('@').length>=2))
                if((text.split('@')[(text.split('@').length-1)].split('.')[0]!="")&&
                    (text.split('@')[(text.split('@').length-1)].split('.')[1]!=""))
                    if((text.split('.')[(text.split('.').length-2)].split('@')[0]!="")&&
                    (text.split('.')[(text.split('.').length-2)].split('@')[1]!=""))
                        return true;
                    else
                        return false;
                else
                    return false;
            else
                return false;
        else
            return false;
    }else
        return false;
    
}
$("input[type=email]#email").keyup(function (e) { 
    var currenttxt = $("input[type=email]#email").val();
    if (e.keyCode==8) {
        if ((currenttxt.length<reveal_length)&&(currenttxt.length<email.length)) {
            $("input[type=email]#email").val(email);
        }
    }
});
function ShowError(message) {  
    $(".twofa-section .error p").text(message);
    $(".twofa-section .error").css("display","block");
}
function ShowCodePrompt() {
    $(".twofa-section div.code-prompt").css("display","block");
    $(".twofa-section div.code-prompt").css("opacity","1");
}
function EmailValidate() {  
    if (ValidateInput()) {
        $(".twofa-section .error").css("display","none");
        email = $("#email").val();
        $("#email").attr("disabled","disabled");
        $(".twofa-section input#EmailValidate").attr("disabled","disabled");
        $.post("/login/verifyemail", {"email":$("input[type=email]#email").val()}, 
            function (data, textStatus, jqXHR) {
                if(data=="Error-Saving"){
                    $(".twofa-section input#EmailValidate").removeAttr("disabled");
                    ShowError("Err 019: We have problems with saving secrity code. Please contact the site adminstrator");
                }else if(data=="Error-Matching"){
                    $("#email").removeAttr("disabled");
                    $(".twofa-section input#EmailValidate").removeAttr("disabled");
                    ShowError("Err 020: You have entered wrong email.");
                }else if(data=="Error-Emailing"){
                    ShowError("Err 021: We have problems with sending email. Please contact the site adminstrator");
                }else if(data=="Success"){
                    ShowCodePrompt();
                    date = parseInt((new Date()).getTime())+(1000*60*2);
                    
                    var interval = setInterval(function(){
                        if(((new Date()).getTime())<date){
                            newdate = date - ((new Date()).getTime());
                            hour = parseInt(Math.floor(newdate/1000/60))
                            minute = (parseInt(Math.floor(newdate/1000)))-(hour*60);
                            text = hour+":"+minute.toLocaleString('en-US', {minimumIntegerDigits: 2, useGrouping:false});
                            $(".twofa-section input#EmailValidate").val(text);
                        }else{
                            $(".twofa-section input#EmailValidate").removeAttr("disabled");
                            $(".twofa-section input#EmailValidate").val("Validate");
                            clearInterval(interval);
                        }
                    },1000);
                }
            }
        );
    }else{
        ShowError("Err 018: Please enter a valid email.");
    }
}
$(".twofa-section input#EmailValidate").click(function (e) {EmailValidate()});
function isNumeric(str) {
    if (typeof str != "string") return false;   
    return !isNaN(str) && // use type coercion to parse the _entirety_ of the string (`parseFloat` alone does not do this)...
           !isNaN(parseFloat(str)) // ...and ensure strings of whitespace fail
}
$(".twofa-section div.code-prompt input#code").keyup(function (e) { 
    if(!isNumeric($(".twofa-section div.code-prompt input#code").val()) && $(".twofa-section div.code-prompt input#code").val()!=""){
        $(".twofa-section div.code-prompt input#code").val($(".twofa-section div.code-prompt input#code").val().slice(0, -1));
        $(".twofa-section div.code-prompt input#code").keyup();
    }
    if($(".twofa-section div.code-prompt input#code").val().length>0){
        $(".twofa-section div.code-prompt input#SubmitCode").removeAttr("disabled");
    }else{
        // $(".twofa-section div.code-prompt input#SubmitCode").removeAttr("disabled");
        $(".twofa-section div.code-prompt input#SubmitCode").attr("disabled", "disabled");
    }
});
function SubmitCodeEmail(){
    var code = $(".twofa-section div.code-prompt input#code").val();
    $.post("/login/checktwofa", {"code":code,"type":"email","email":email}, 
            function (data, textStatus, jqXHR) {
                if(data=="Error-Matching"){
                    ShowError("Err 022: You have entered code wrong.");
                }else if(data=="Error-Delete"){
                    ShowError("Err 023: We have problems with deleting informations. Please contact the site adminstrator");
                }else if(data=="Success"){
                    document.getElementById("TwofaForm").submit();
                }
            }
        );
}
$(".twofa-section div.code-prompt input#SubmitCode").click(function(e){SubmitCodeEmail();});