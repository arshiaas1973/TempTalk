var toggle = false;
function createChat() { window.location.href="/chat/create" }
function joinChat() { window.location.href="/chat/join" }
function createGroup() { window.location.href="/group/create" }
function joinGroup() { window.location.href="/group/join" }
function toggleevt(){
    if (!toggle) {
        $("#profile-text").text("Logout");
        $("#profile-text").addClass("profile-logout-text");
    }else{
        $("#profile-text").text("Welcome");
        $("#profile-text").removeClass("profile-logout-text");
    }
    toggle = !toggle;
}
function logout(){
    if(toggle){
        window.location.href="/home/logout";
    }
}