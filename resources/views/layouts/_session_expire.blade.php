<script type="text/javascript">
    var timeout = logged_in_user > 0 ? {{ \Config::get('app.admin_session_timeout') }} : {{\Config::get('app.wizard_session_timeout') }};
    var session_alert_time = {{ \Config::get('app.session_expires_alert_time') }};
</script>
<script src="{{ asset('public/assets/js/sessionExpireAlert.js')}}"></script>

<div id="SessionExpireAlert" class="modal" style="z-index:10000;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Alert</h4>
            </div>
            <div class="modal-body">
                <p>
                    In order to protect your privacy, your current session will be logged off in [SECONDS] seconds. Click continue if you would like to continue working.
                </p>
                <div id="dvsessioncountdown">
                    <p>
                        Time remaining : @timeRemaining seconds
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_sessionContinue" class="btn btn-primary">Continue</button>
            </div>
        </div>
    </div>
</div>