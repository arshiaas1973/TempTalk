var user_details_menu_shown = false;
function ContainsLetters(string) {  
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
$(".chat-panel .header-bar img.user-details").click(function() {
    if (!user_details_menu_shown) {
        //transform: rotate(90deg);
        $(".chat-panel .header-bar img.user-details").css("transform","rotate(90deg)");
        $(".chat-panel .header-bar img.user-details").css("margin-top","5px");
        $(".chat-panel .header-bar div.user-menu ul").css("opacity","1");
    }else{
        $(".chat-panel .header-bar img.user-details").css("transform","none");
        $(".chat-panel .header-bar img.user-details").css("margin-top","0px");
        $(".chat-panel .header-bar div.user-menu ul").css("opacity","0");
    }  
    user_details_menu_shown = !user_details_menu_shown;
});
function ShowProfilePanel(){
    $(".chat-panel .header-bar div.user-menu").css("margin-right","450px");
    $(".chat-panel .header-bar div.user-menu ul").css("margin-right","450px");
    $(".chat-panel .user-profile-details").css("display","block");
    $(".chat-panel .user-profile-details .user-details-header-bar").css("padding","10px 20px");
    user_details_menu_shown = !user_details_menu_shown;
    var width = parseInt($("body").css("width").substring(0,$("body").css("width").length-2));
    if (width<900) {
        if (user_details_menu_shown) {
            $(".chat-panel .header-bar").css("display","none");
            $(".chat-panel .user-profile-details").css("min-width",$("body").css("width"));
            $(".chat-panel .user-profile-details .user-details-header-bar").css("border-top-left-radius","36px");
            $(".chat-panel .user-profile-details").css("border-top-left-radius","36px");
        }
    }else{
        var msgwidth = (width-(2*parseInt($(".chat-panel").css("padding").substring(0,$(".chat-panel").css("width").length-2)))-(parseInt($(".chat-panel .user-profile-details").css("width").substring(0,$(".user-profile-details").css("width").length-2)))).toString()+"px";
        $(".chat-panel .message-section div.message-box").css("max-width",msgwidth);
    }
}
function HideProfilePanel(){
    var width = parseInt($("body").css("width").substring(0,$("body").css("width").length-2));
    if (width<900){
        if (user_details_menu_shown) {
            $(".chat-panel .header-bar").css("display","block");
            $(".chat-panel .user-profile-details").css("min-width","450px");
            $(".chat-panel .user-profile-details .user-details-header-bar").css("border-top-left-radius","0px");
            $(".chat-panel .user-profile-details").css("border-top-left-radius","0px");
        }
    }
    $(".chat-panel .header-bar div.user-menu").css("margin-right","0px");
    $(".chat-panel .header-bar div.user-menu ul").css("margin-right","0px");
    $(".chat-panel .user-profile-details").css("display","none");
    $(".chat-panel .user-profile-details .user-details-header-bar").css("padding","0px");
    user_details_menu_shown = !user_details_menu_shown;
    $(".chat-panel .message-section div.message-box").css("max-width","1400px");
    
}
if (!ContainsLetters($(".chat-panel .user-profile-details span.user-biography").text())) {
    $(".chat-panel .user-profile-details span.user-biography").text("Don't have anything to say!!");
}
function WindowResize() { 
    var width = parseInt($("body").css("width").substring(0,$("body").css("width").length-2));
    if (width<900) {
        if (user_details_menu_shown) {
            $(".chat-panel .header-bar").css("display","none");
            $(".chat-panel .user-profile-details").css("min-width",$("body").css("width"));
            $(".chat-panel .user-profile-details .user-details-header-bar").css("border-top-left-radius","36px");
            $(".chat-panel .user-profile-details").css("border-top-left-radius","36px");
        }
    }else{
        if (user_details_menu_shown){
            $(".chat-panel .header-bar").css("display","block");
            $(".chat-panel .user-profile-details").css("min-width","450px");
            $(".chat-panel .user-profile-details .user-details-header-bar").css("border-top-left-radius","0px");
            $(".chat-panel .user-profile-details").css("border-top-left-radius","0px");
            var msgwidth = (width-(2*parseInt($(".chat-panel").css("padding").substring(0,$(".chat-panel").css("width").length-2)))-(parseInt($(".chat-panel .user-profile-details").css("width").substring(0,$(".user-profile-details").css("width").length-2)))).toString()+"px";
            $(".chat-panel .message-section div.message-box").css("max-width",msgwidth);
        }else{
            $(".chat-panel .message-section div.message-box").css("max-width","1400px");
        }
    }
}
$(".chat-panel .message-section div.message-box textarea").keyup(function (e) { 
    var code = $(".chat-panel .message-section div.message-box textarea").val();
    if (!e.shiftKey && e.keyCode == 13) {
        e.preventDefault();
        SendingMessage();
    }
    // Check if is persian or arabics
    if(code.charCodeAt(0) >= 0x0600 && code.charCodeAt(0) <= 0x06FF){
        $(".chat-panel .message-section div.message-box textarea").css("text-align","right");
    }else{
        $(".chat-panel .message-section div.message-box textarea").css("text-align","left");
    }
});
function SendingMessage() {
    if(ContainsLetters($(".chat-panel .message-section div.message-box textarea").val())){
        $.post("/chat/sendmessage", {"senderid":parseInt(sid),"reciverid":parseInt(rid),"msg":($(".chat-panel .message-section div.message-box textarea").val()),"secret":CryptoJS.MD5(sid.toString()+rid.toString()).toString(),"mode":mode,"type":"text"},
            function (data, textStatus, jqXHR) {
                $(".chat-panel .message-section div.message-box textarea").val("");
            }
        );
    }
}
$(".chat-panel .message-section div.message-box button.message-submit").click(function(){SendingMessage();});
var datas = [{"mode":mode,"cid":cid,"startpoint":0,"limit":20,"isstartpoint":0}];
function loadMessages(){
    for (let i = 0; i < datas.length; i++) {
        const element = datas[i];
        $.get("/chat/getmessages", element,
            function (data, textStatus, jqXHR) {
                $(".chat-panel .message-section .message-body").html(data);
            }
        );
    }
}
loadMessages();