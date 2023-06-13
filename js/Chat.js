var user_details_menu_shown = false;
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
}
function HideProfilePanel(){
    $(".chat-panel .header-bar div.user-menu").css("margin-right","0px");
    $(".chat-panel .header-bar div.user-menu ul").css("margin-right","0px");
    $(".chat-panel .user-profile-details").css("display","none");
    $(".chat-panel .user-profile-details .user-details-header-bar").css("padding","0px");
}