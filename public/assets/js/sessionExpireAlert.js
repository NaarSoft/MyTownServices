/// 1. Include jQuery
/// 2. Include jQuery UI (for dialog)

///// Declare global variables

// set log out time as in web config
var logoutTimeInConfig, // in minutes

// set count down timer in minutes, will be shown in seconds during coundown
countdowntime, // in minutes

//refresh time in milliseconds open the disclaimer dialog with continue
refreshTime,

//logout timer in milliseconds, this is the countdown time in milliseconds
logoutTime,

// to show it during count down running
countdownseconds,

//our last activity timer
lastActivityTimer = null,

//our logout timer to click continue
logoutAlertTimer = null,
countdowninterval = null,

// Keep url
url = '',
flagSetCookieTime = true;

$(document).ready(function () {
    //// set times form config, as set in hidden fields of model partial view, and hence set refreshTime,logoutTime and countdownseconds
    SetTimeOutFromConfig();

    // pass the function you want to call at 'window.onload'
    sessionLoadEvent(windowonloadfunction);
});

//// function will check if there is already a function hooked to the window.onload event and will run that before your function.
function sessionLoadEvent(func) {
    // assign any pre-defined functions on 'window.onload' to a variable
    var oldOnLoad = window.onload;

    // if there is not any function hooked to it
    if (typeof window.onload != 'function') {
        // you can hook your function with it
        window.onload = func
    } else {     // someone already hooked a function
        window.onload = function () {
            // call the function hooked already
            oldOnLoad();
            // call your onload function
            func();
        }
    }
}

function windowonloadfunction() {

    //  monitor document events for key up & mouse click - to know inactivity
    $(document).keyup(restartSession).mouseup(restartSession).mousemove(restartSession);

    // set cookie timer as of now time
    setCookieTimer(true);

    // set count down seconds as text in dialog to be open
    SetSecondInDialogView();

    //start the last activity timer
    lastActivityTimer = setTimeout(logoutAlert, refreshTime);

    // handle click event of continue button
    $('#btn_sessionContinue').click(function (e) {
        CloseDialogById('SessionExpireAlert');
        //  clearTimeout(logoutAlertTimer);
        clearInterval(countdowninterval);
        resetCountDownSeconds();
    });
}


//restart session timer.
function restartSession(e) {
    //clear the timer if set
    if (lastActivityTimer != null)
        clearTimeout(lastActivityTimer);

    // set cookie timer as of now time
    if (flagSetCookieTime) {
        setCookieTimer();
    }

    //// get cookie timer and check diff
    var cookieTime = getCookieTimer();
    if (cookieTime != '') {
        //        var diff = (new Date().getTime()) - new Date(cookieTime).getTime();
        //        var diffInMinutes = diff / 60000;
        //        refreshTime = refreshTime - (diffInMinutes * 60000);
        // alert('refreshTime' + refreshTime / 60000);
    }
    else {
        /// if cookie timer is ''. logout
        forceLogout();
    }

    //reset the timer for refresh minutes
    lastActivityTimer = setTimeout(logoutAlert, refreshTime);

    flagSetCookieTime = true;
}

//function fired when no activity has been  received for few mins
function logoutAlert() {

    //clear the timer if set
    if (logoutAlertTimer != null)
        clearTimeout(logoutAlertTimer);

    //// get cookie timer and check diff
    var cookieTime = getCookieTimer();

    if (cookieTime != '') {

        var diff = (new Date().getTime()) - new Date(cookieTime).getTime();
        var diffInSeconds = diff / 1000;

        if (diffInSeconds >= logoutTimeInConfig - countdowntime) {
            // set cookie time to avoid dialog to get opened at some other tab
            setCookieTimer();

            //notify user of logout and ask if want to continue
            ///// IMP : NOTE : Cannot remove try catch from open dialog by id
            try {
                $("#SessionExpireAlert").modal('show');
            } catch (e) { }

            /// start cound down
            countdown('dvsessioncountdown');
        }
        else {
            /// if diff in minutes is less then log out set value; restart refresh session
            flagSetCookieTime = false;
            // deleteLogoutAlertProcessingCookie(); // delete already processing cookie
            restartSession();
        }
    }
    else {
        /// if cookie timer is ''. logout
        forceLogout();
    }
}

//function fired when no activity has been recieved or donot want to continue
function forceLogout() {

    if (this.opener != null) {
        /// If parent window is also opened
        deleteCookieTimer();

        this.opener.clearTimeout(lastActivityTimer);
        url = this.opener.location;

        // logout and close this tab;
        //  logout user
        $.getJSON(redirect_url_after_force_logout, function (returnurl) {
            if (returnurl == '') {
                returnurl = url
            }
            window.location.href = returnurl;
        });
        this.close();
        this.opener.window.CloseDialogById('SessionExpireAlert');
        this.opener.window.focus();
        this.opener.window.forceLogout();
    }
    else {

        url = window.location.href;
        deleteCookieTimer();

        // needToConfirm is defined in common.js for showing confirmation message on page refresh/tab close.
        needToConfirm = false;
        window.location.href = redirect_url_after_force_logout;
    }
}

function getCookieTimer() {
    return getCookie('lastActivityTimer');
}

function getLogoutAlertProcessingCookie() {
    return getCookie('LogoutAlertProcessing');
}

function setCookieTimer(isWindowLoading) {
    var cookieTime = getCookieTimer();
    if (cookieTime != '' || isWindowLoading) {
        setCookie("lastActivityTimer", new Date());
    }
    else {
        forceLogout();
    }
}

function setLogoutAlertProcessingCookie() {
    //   setCookie("LogoutAlertProcessing", true);
}

function deleteCookieTimer() {
    deleteCookie('lastActivityTimer');
}

function deleteLogoutAlertProcessingCookie() {
    //   deleteCookie('LogoutAlertProcessing');
}

//// set count down seconds as set in the dialog view to be open
function SetTimeOutFromConfig() {
    logoutTimeInConfig = timeout;
    countdowntime = session_alert_time;

    //refresh time in milliseconds open the disclaimer dialog with continue
    refreshTime = 1000 * (logoutTimeInConfig - countdowntime);
    // Note : (60,000(to convert to millisecond) * logout time(in web config e.g 5) - 1 min (for prior alert of 60 seconds)  i.e 4 mins in milliseconds
    //logout timer in milliseconds, this is the countdown time in MS
    logoutTime = 1000 * countdowntime;
    // to show it during count down running
    countdownseconds = countdowntime;
}

//// set count down seconds as set in the dialog view to be open
function SetSecondInDialogView() {
    resetCountDownSeconds();
    var htmltext = $('#SessionExpireAlert').html();
    htmltext = htmltext.replace('[SECONDS]', countdownseconds);
    $('#SessionExpireAlert').html(htmltext);
}

/// reset the count down seconds time remaining
function resetCountDownSeconds() {
    countdownseconds = (logoutTime / 1000);
    var el = document.getElementById('dvsessioncountdown');
    el.innerHTML = "Time remaining : " + countdownseconds + ' seconds';
}

var minutes = 0;
function countdown() {
    countdowninterval = setInterval(function () {
        var el = document.getElementById('dvsessioncountdown');
        if (countdownseconds == 0) {
            //if (minutes == 0) {
            el.innerHTML = "countdown's over!";
            clearInterval(countdowninterval);

            CloseDialogById('SessionExpireAlert');
            resetCountDownSeconds();
            forceLogout();
            return;
        }
        countdownseconds--;
        var second_text = countdownseconds > 1 ? 'seconds' : 'second';
        el.innerHTML = "Time remaining : " + countdownseconds + ' ' + second_text;
    }, 1000);
}

function CloseDialogById(id) {
    $("#" + id).modal('hide');
}

function setCookie(cname, cvalue) {
    $.cookie(cname, cvalue, { path: '/' });
}

function getCookie(cname) {
    if ($.cookie(cname) != undefined) {
        return $.cookie(cname);
    }
    return "";
}

function deleteCookie(cname) {
    $.cookie(cname, null, { path: '/' });
    $.removeCookie(cname, { path: '/' });
}