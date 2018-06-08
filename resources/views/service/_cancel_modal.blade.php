<section>
    <div id="cancel_appointment" class="modal fade bs-example-modal-xs" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xs">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">Please give a reason for Cancelling appointment.</h4>
                </div>
                <div class="modal-body">
                    {!! Form::open(['route' => ['service.cancel'], 'id' => 'form', 'name' => 'form', 'class' => 'form-horizontal form-label-left']) !!}
                        {!! Form::hidden('response_id', isset($responseId) ? $responseId : 0  , array('id'=> 'response_id')) !!}
                        <div class="form-group required">
                            <label for="txtMessage" class="control-label col-md-2 col-sm-2 col-xs-12">Reason</label>
                            <div class="col-md-10 col-sm-10 col-xs-12">
                                {!! Form::textarea('txtReason', null, ['id' => 'txtReason', 'class' => 'form-control','style' => 'min-width: 100%', 'size' => '30x8', 'required', 'data-parsley-required-message' => 'Enter reason.', 'data-parsley-maxlength'=> 500, 'data-parsley-maxlength-message'=> 'Reason may be up to 500 characters in length.']) !!}
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnCancel" class="btn btn-success">OK</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="<?php echo e(asset('public/assets/js/module/cancel_modal.js')); ?>"></script>
</section>