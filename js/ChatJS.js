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
document.querySelector(".chat-panel .header-bar img.user-details").click(function() {
    if (!user_details_menu_shown) {
        //transform: rotate(90deg);
        document.querySelector(".chat-panel .header-bar img.user-details").css("transform","rotate(90deg)");
        document.querySelector(".chat-panel .header-bar img.user-details").css("margin-top","5px");
        document.querySelector(".chat-panel .header-bar div.user-menu ul").css("opacity","1");
    }else{
        document.querySelector(".chat-panel .header-bar img.user-details").css("transform","none");
        document.querySelector(".chat-panel .header-bar img.user-details").css("margin-top","0px");
        document.querySelector(".chat-panel .header-bar div.user-menu ul").css("opacity","0");
    }  
    user_details_menu_shown = !user_details_menu_shown;
});
function ShowProfilePanel(){
    document.querySelector(".chat-panel .header-bar div.user-menu").css("margin-right","450px");
    document.querySelector(".chat-panel .header-bar div.user-menu ul").css("margin-right","450px");
    document.querySelector(".chat-panel .user-profile-details").css("display","block");
    document.querySelector(".chat-panel .user-profile-details .user-details-header-bar").css("padding","10px 20px");
    user_details_menu_shown = !user_details_menu_shown;
    var width = parseInt(document.querySelector("body").css("width").substring(0,document.querySelector("body").css("width").length-2));
    if (width<900) {
        if (user_details_menu_shown) {
            document.querySelector(".chat-panel .header-bar").css("display","none");
            document.querySelector(".chat-panel .user-profile-details").css("min-width",document.querySelector("body").css("width"));
            document.querySelector(".chat-panel .user-profile-details .user-details-header-bar").css("border-top-left-radius","36px");
            document.querySelector(".chat-panel .user-profile-details").css("border-top-left-radius","36px");
        }
    }else{
        var msgwidth = (width-(2*parseInt(document.querySelector(".chat-panel").css("padding").substring(0,document.querySelector(".chat-panel").css("width").length-2)))-(parseInt(document.querySelector(".chat-panel .user-profile-details").css("width").substring(0,document.querySelector(".user-profile-details").css("width").length-2)))).toString()+"px";
        document.querySelector(".chat-panel .message-section div.message-box").css("max-width",msgwidth);
    }
}
function HideProfilePanel(){
    var width = parseInt(document.querySelector("body").css("width").substring(0,document.querySelector("body").css("width").length-2));
    if (width<900){
        if (user_details_menu_shown) {
            document.querySelector(".chat-panel .header-bar").css("display","block");
            document.querySelector(".chat-panel .user-profile-details").css("min-width","450px");
            document.querySelector(".chat-panel .user-profile-details .user-details-header-bar").css("border-top-left-radius","0px");
            document.querySelector(".chat-panel .user-profile-details").css("border-top-left-radius","0px");
        }
    }
    document.querySelector(".chat-panel .header-bar div.user-menu").css("margin-right","0px");
    document.querySelector(".chat-panel .header-bar div.user-menu ul").css("margin-right","0px");
    document.querySelector(".chat-panel .user-profile-details").css("display","none");
    document.querySelector(".chat-panel .user-profile-details .user-details-header-bar").css("padding","0px");
    user_details_menu_shown = !user_details_menu_shown;
    document.querySelector(".chat-panel .message-section div.message-box").css("max-width","1400px");
    
}
if (!ContainsLetters(document.querySelector(".chat-panel .user-profile-details span.user-biography").text())) {
    document.querySelector(".chat-panel .user-profile-details span.user-biography").text("Don't have anything to say!!");
}
function WindowResize() { 
    var width = parseInt(document.querySelector("body").css("width").substring(0,document.querySelector("body").css("width").length-2));
    if (width<900) {
        if (user_details_menu_shown) {
            document.querySelector(".chat-panel .header-bar").css("display","none");
            document.querySelector(".chat-panel .user-profile-details").css("min-width",document.querySelector("body").css("width"));
            document.querySelector(".chat-panel .user-profile-details .user-details-header-bar").css("border-top-left-radius","36px");
            document.querySelector(".chat-panel .user-profile-details").css("border-top-left-radius","36px");
        }
    }else{
        if (user_details_menu_shown){
            document.querySelector(".chat-panel .header-bar").css("display","block");
            document.querySelector(".chat-panel .user-profile-details").css("min-width","450px");
            document.querySelector(".chat-panel .user-profile-details .user-details-header-bar").css("border-top-left-radius","0px");
            document.querySelector(".chat-panel .user-profile-details").css("border-top-left-radius","0px");
            var msgwidth = (width-(2*parseInt(document.querySelector(".chat-panel").css("padding").substring(0,document.querySelector(".chat-panel").css("width").length-2)))-(parseInt(document.querySelector(".chat-panel .user-profile-details").css("width").substring(0,document.querySelector(".user-profile-details").css("width").length-2)))).toString()+"px";
            document.querySelector(".chat-panel .message-section div.message-box").css("max-width",msgwidth);
        }else{
            document.querySelector(".chat-panel .message-section div.message-box").css("max-width","1400px");
        }
    }
}
document.querySelector(".chat-panel .message-section div.message-box textarea").keyup(function (e) { 
    var code = document.querySelector(".chat-panel .message-section div.message-box textarea").val();
    if (!e.shiftKey && e.keyCode == 13) {
        e.preventDefault();
        SendingMessage();
    }
    // Check if is persian or arabics
    if(code.charCodeAt(0) >= 0x0600 && code.charCodeAt(0) <= 0x06FF){
        document.querySelector(".chat-panel .message-section div.message-box textarea").css("text-align","right");
    }else{
        document.querySelector(".chat-panel .message-section div.message-box textarea").css("text-align","left");
    }
});
function SendingMessage() {
    if(ContainsLetters(document.querySelector(".chat-panel .message-section div.message-box textarea").val())){
        document.querySelector.post("/chat/sendmessage", {"senderid":parseInt(sid),"reciverid":parseInt(rid),"msg":(document.querySelector(".chat-panel .message-section div.message-box textarea").val()),"secret":CryptoJS.MD5(sid.toString()+rid.toString()).toString(),"mode":mode,"type":"text"},
            function (data, textStatus, jqXHR) {
                document.querySelector(".chat-panel .message-section div.message-box textarea").val("");
            }
        );
    }
}
document.querySelector(".chat-panel .message-section div.message-box button.message-submit").click(function(){SendingMessage();});
var datas = [{"mode":mode,"cid":cid,"startpoint":0,"limit":20,"isstartpoint":0}];
var htmltext = "";
function loadMessages(){
    for (let i = 0; i < datas.length; i++) {
        const element = datas[i];
        document.querySelector.get("/chat/getmessages", element,
            function (data, textStatus, jqXHR) {
                try {
                    var message = JSON.parse(data);
                    if (message["status"]=="Success") {
                        if (message.hasOwnProperty("pointer")) {
                            var text = `<div class="message-spliter message-spliter-<?=document.querySelectorcolor_scheme?>">
                                            <table cell-spacing="0">
                                                <tr>
                                                    <th><span>>></span></th>
                                                    <th><span>Unreaded Messages</span></th>
                                                    <th><span><<</span></th>
                                                </tr>
                                            </table>
                                        </div>`;
                            htmltext+=text;
                        }
                        for (let i = 0; i < message["result"].length; i++) {
                            const element = message["result"][i];
                            var text = `<div class="message `+element["flag"]+`">`
                            if(element["role"]=="normal")
                                text +=        `<span class="normal">`;
                            else if(element["role"]=="king")
                                text +=        `<span class="king">`;
                            else if(element["role"]=="queen")
                                text +=        `<span class="queen">`;
                            text +=               `<img class="prof-pic" src="`+element["user_pfp"]+`"/>
                                                </span>
                                                <div>
                                                    <p>`+element["text"]+`</p>
                                                    <div class="message-details">
                                                        <span class="message-status">`+element["status"]+`</span>
                                                        <span class="message-time">`+element["creationdate"]+`</span>
                                                    </div>
                                                </div>
                                            </div>`;
                            htmltext+=text;
                        }
                    }
                } catch (error) {}
            }
        );
    }
    document.querySelector(".chat-panel .message-section .message-body").html(htmltext);
    htmltext = "";
}
loadMessages();
setInterval(function () { loadMessages(); },1000);