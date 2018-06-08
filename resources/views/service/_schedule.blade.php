<section>
    <div id="div_service">
        <div id="div_response" class="alert alert-warning" style="display: none"></div>
        <h4>Below are the services you may be eligible to receive. Please confirm if you would like to meet with someone
            from these agencies. (Check all that apply)</h4>

        <div class="row">
            @if(isset($responseId))
                @foreach($agencies as $agency)
                    <?php $css = $agency->available_slots == 0 ? 'text-red' : ''; ?>
                    <div id="chk_service" class="checkbox i-checks">
                        <label class="">
                            {{ Form::checkbox('service_id', $agency->service_id, '', ['id'=>$agency->service_id, 'class' => 'flat','required'])}}
                            &nbsp;
                            {{$agency->name}}
                            <span class="text-red">{{ $agency->available_slots == 0 ? ' (No appointment slots are available)' : '' }}</span>
                        </label>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="ln_solid"></div>
        <div class="form-group">
            <div class="col-md-6 col-md-offset-5">
                {!! Form::open(['url' => '/service/goToPrevious']) !!}
                {!! Form::hidden('responseId', isset($responseId) ? $responseId : 0, array('id'=> 'responseId')) !!}
                <button type="submit" id="btnPrevious" name="btnPrevious" class="btn btn-success">Previous
                </button>
                <button type="button" id="btnBookAppointment" name="btnBookAppointment" data-toggle="modal"
                        class="btn btn-success">Show Available Appointments
                </button>
                <button type="button" id="btnCancelAppointment" name="btnCancelAppointment" data-toggle="modal"
                        class="btn btn-success">Cancel
                </button>
                {!! Form::close() !!}
            </div>
        </div>
        <br/><br/><br/>

        <div id='service_calendar'></div>
    </div>
    <script type="text/javascript" src="<?php echo e(asset('public/assets/js/module/appointment.js')); ?>"></script>
</section>