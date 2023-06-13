function createchat(){
    $(".chat-panel .user-panel").html("");
    $.post("/chat/fetchusers", {"search":$(".chat-panel input.search").val()},
        function (data, textStatus, jqXHR) {
            var json = JSON.parse(data);
            for (let i = 0; i < json.length; i++) {
                const element = json[i];
                var text =   `<div class="user-selection" onclick="window.location.href = '/chat/?u=`+element['username']+`&m=c'">
                                    <img class="user-pfp" src="`+element['pfp']+`">
                                    <div class="names">
                                        <span class="user-displayedname">`+element['displayedname']+`</span>
                                        <span class="user-username">@`+element['username']+`</span>
                                    </div>
                                    `;
                if(element['role']=="king"){
                    text+=`<img class="user-pfp-badge" src="/img/king-crown.png">`;
                }else if(element['role']=="queen"){
                    text+=`<img class="user-pfp-badge" src="/img/queen-crown.png">`;
                }
                text+=`</div>`;
                $(".chat-panel .user-panel").append(text);
            }
        }
    );
}
$(".chat-panel input.search").keyup(function (e) { createchat();});
$(".chat-panel img.search-icon").keyup(function (e) { createchat();});