var tmppath = "/img/prof-pic";
var iscroping = false;
const img = new Image();
var checked = "dowant";
var img_width = 400, img_height = 400;
$(".edit-dialog div.overlay-owner div.overlay").css("max-width",img_width+"px");
$(".edit-dialog div.overlay-owner div.overlay").css("max-height",img_height+"px");
img.onload = function() {
  if(iscroping){
    $(".big-panel form input#cropwidth").remove();
    $(".big-panel form input#cropheight").remove(); 
    iscroping = false;
  }
  img_width = parseInt(this.width);
  img_height = parseInt(this.height);
  $(".edit-dialog div.overlay-owner div.overlay").css("max-width",img.width+"px");
  $(".edit-dialog div.overlay-owner div.overlay").css("max-height",img.height+"px");
  $(".edit-dialog div.overlay-owner").css("max-width",img.width+"px");
  $(".edit-dialog div.overlay-owner").css("max-height",img.height+"px");
  $(".edit-dialog div.overlay-owner").css("height",img.height+"px");
  $(".edit-dialog div.overlay-owner").css("width",img.width+"px");
  $(".edit-dialog div .prof-pic").css("width",img.width+"px");
  $(".edit-dialog div .prof-pic").css("height",img.height+"px");
  $(".edit-dialog div.overlay-owner").css("min-width",img.width+"px");
  $(".edit-dialog div.overlay-owner").css("min-height",img.height+"px"); 
  $(".edit-dialog div.overlay-owner").css("margin-top","-"+img.height+"px"); 
}
$('input[type=file]').change(function (event) {
    tmppath = URL.createObjectURL(event.target.files[0]);
    $(".big-panel .prof-pic").attr("src",tmppath);
    $(".edit-dialog .prof-pic").attr("src",tmppath);
    img.src = tmppath; 
});
$(".big-panel .prof-pic").click(function(){
    $(".big-panel form input[type=file]").click();
});
function EditPic() {
    if (img.width > 700 || img.height > 700) {
        $(".main-box").css("opacity","0.3");
        $(".error-dialog").css("display","block");
    }else{
        $(".main-box").css("opacity","0.3");
        $(".edit-dialog").css("display","block");
        if(iscroping){
            $(".edit-dialog div.overlay-owner div.overlay").css("width",($(".big-panel form input#cropwidth").val())+"px");
            $(".edit-dialog div.overlay-owner div.overlay").css("width",($(".big-panel form input#cropheight").val())+"px");
        }
    }
}
function CloseEditPic() {
    $(".main-box").css("opacity","1");
    $(".edit-dialog").css("display","none");
}
function CloseError() {
    $(".main-box").css("opacity","1");
    $(".error-dialog").css("display","none");
}
function CropAction() {
    if(iscroping){
        if (parseInt(($(".edit-dialog div.overlay-owner div.overlay").css("width")).split('px')[0])<400&&
            parseInt(($(".edit-dialog div.overlay-owner div.overlay").css("height")).split('px')[0])<400) {
            $(".big-panel form input#cropwidth").val(($(".edit-dialog div.overlay-owner div.overlay").css("width")).split('px')[0]);
            $(".big-panel form input#cropheight").val(($(".edit-dialog div.overlay-owner div.overlay").css("height")).split('px')[0]);
        }else{
            $(".big-panel form input#cropwidth").remove();
            $(".big-panel form input#cropheight").remove();
            iscroping = false;
        }
    }else{
        if (parseInt(($(".edit-dialog div.overlay-owner div.overlay").css("width")).split('px')[0])<400&&
            parseInt(($(".edit-dialog div.overlay-owner div.overlay").css("height")).split('px')[0])<400) {
            $(".big-panel form").append('<input type="hidden" name="cropwidth" id="cropwidth" value="'+
                    ($(".edit-dialog div.overlay-owner div.overlay").css("width")).split('px')[0]+'" />');
            $(".big-panel form").append('<input type="hidden" name="cropheight" id="cropheight" value="'+
                    ($(".edit-dialog div.overlay-owner div.overlay").css("height")).split('px')[0]+'" />');
            iscroping = true;
        }
    }
    CloseEditPic();
}
function SubmitPic(){
    var form = document.getElementById("pfpform");
    form.submit();
}

$(".twofa-main form div.options#dontwant img").click(function (e) { 
    if (checked != "dontwant") {
        
    }
});

$(".twofa-main form div.options#dowant img").click(function (e) { 
    if (checked != "dowant") {
        if ($(".twofa-main form div.options#dowant img").attr("src")=="/img/Checkbox-White.png") {
            $(".twofa-main form div.options#dowant img").attr("src","/img/Checkbox-Checked-White.png");
            $(".twofa-main form div.options#dontwant img").attr("src","/img/Checkbox-White.png");
        }else{
            $(".twofa-main form div.options#dowant img").attr("src","/img/Checkbox-Checked.png");
            $(".twofa-main form div.options#dontwant img").attr("src","/img/Checkbox.png");
        }
        if($('.twofa-main form input[type=hidden]#choice').length){
            $('.twofa-main form input[type=hidden]#choice').val("dowant");
        }else{
            $('.twofa-main form').append("<input type=\"hidden\" name=\"choice\" id=\"choice\" value=\"dowant\"/>");
        }
        checked = "dowant";
        $(".twofa-main form div.choices").css("display","block");
    }
});

$(".twofa-main form div.options#dontwant img").click(function (e) { 
    if (checked != "dontwant") {
        if ($(".twofa-main form div.options#dontwant img").attr("src")=="/img/Checkbox-White.png") {
            $(".twofa-main form div.options#dontwant img").attr("src","/img/Checkbox-Checked-White.png");
            $(".twofa-main form div.options#dowant img").attr("src","/img/Checkbox-White.png");
        }else{
            $(".twofa-main form div.options#dontwant img").attr("src","/img/Checkbox-Checked.png");
            $(".twofa-main form div.options#dowant img").attr("src","/img/Checkbox.png");
        }
        if($('.twofa-main form input[type=hidden]#choice').length){
            $('.twofa-main form input[type=hidden]#choice').val("dontwant");
        }else{
            $('.twofa-main form').append("<input type=\"hidden\" name=\"choice\" id=\"choice\" value=\"dontwant\"/>");
        }
        checked = "dontwant";
        $(".twofa-main form div.choices").css("display","none");
    }
});

$(".twofa-main form div.choices select").change(function (e) { 
    if($('.twofa-main form input[type=hidden]#authtype').length){
        $('.twofa-main form input[type=hidden]#authtype').val($(".twofa-main form div.choices select").val());
    }else{
        $('.twofa-main form').append("<input type=\"hidden\" name=\"authtype\" id=\"authtype\" value=\""+$(".twofa-main form div.choices select").val()+"\"/>");
    }
});

function SubmitTwoFA() {
    if(checked == "dontwant"){
        var r = confirm("Are you sure you don't want Two-Factor Authentication?");
        if(r == true){
            var res = confirm("Do you know what does Two-Factor Authentication?");
            if(res == true){
                window.location.href = "/account/twofaset?set=none";
            }else{
                window.open("https://www.techtarget.com/searchsecurity/definition/two-factor-authentication", "_blank");
            }
        }
    }else{
        var form = document.getElementById("twofa-form");
        form.submit();
    }
}