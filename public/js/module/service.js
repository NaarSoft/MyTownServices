var needToConfirm = true;

$(window).bind("beforeunload",function(event) {
    if(needToConfirm){
        return "You have unsaved changes";
    }
});

if(!!window.performance && window.performance.navigation.type === 2)
{
    needToConfirm = false;
    window.location.href= root_URL +'/index';
}