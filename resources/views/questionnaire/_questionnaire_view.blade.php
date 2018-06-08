<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="content">
            @if(isset($response) && !is_null($response))
                @if(!is_null($response->cancellation_reason))
                    <div class="col-md-12">
                        <h3 class="heading-cancel-reason">Appointment Cancelled</h3>
                    </div>
                    <div class="col-md-12 item form-group ">
                        {{ Form::label('cancellation_reason', 'Reason', array('class' => 'control-label col-md-3 col-sm-3 col-xs-12', 'for'=> 'cancellation_reason' )) }}
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            {{ Form::label('cancellation_reason', $response->cancellation_reason, array('class' => 'light-font', 'for'=> 'cancellation_reason' )) }}
                        </div>
                    </div>
                @endif
                <div class="col-md-12">
                    <h3 class="heading">Basic Info</h3>
                </div>
                <div class="col-md-12 item form-group ">
                    {{ Form::label('name', 'Name', array('class' => 'control-label col-md-3 col-sm-3 col-xs-12', 'for'=> 'name' )) }}
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        {{ Form::label('name', $response->name, array('class' => 'light-font', 'for'=> 'name' )) }}
                    </div>
                </div>
                <div class="col-md-12 item form-group ">
                    {{ Form::label('email_address', 'Email address', array('class' => 'control-label col-md-3 col-sm-3 col-xs-12', 'for'=> 'email_address' )) }}
                    <div class="col-md-3 col-sm-3 col-xs-3">
                        {{ Form::label('email_address', $response->email_address, array('class' => 'light-font', 'for'=> 'email_address' )) }}
                    </div>
                </div>
                <div class="col-md-12 item form-group ">
                    {{ Form::label('cell_phone', 'Phone number', array('class' => 'control-label col-md-3 col-sm-3 col-xs-12', 'for'=> 'cell_phone' )) }}
                    <div class="col-md-3 col-sm-3 col-xs-3">
                        {{ Form::label('cell_phone', $response->cell_phone, array('class' => 'light-font', 'for'=> 'cell_phone' )) }}
                    </div>
                </div>
                {{--<div class="item form-group">--}}
                    {{--<div class="col-md-3 col-sm-3 col-xs-12">--}}
                        {{--{{ Form::label('mode_of_contact', 'Best way to contact', array('class' => 'control-label col-md-12 col-sm-12 col-xs-12', 'for'=> 'mode_of_contact' )) }}--}}
                    {{--</div>--}}
                    {{--<div class="col-md-3 col-sm-3 col-xs-3">--}}
                        {{--{{ Form::label('mode_of_contact', $response->mode_of_contact, array('class' => 'light-font', 'for'=> 'mode_of_contact' )) }}--}}
                    {{--</div>--}}
                {{--</div>--}}
                <div class="col-md-12 item form-group">
                    <div class="col-md-3 col-sm-3 col-xs-12  no-padding">
                        {{ Form::label('gender', 'Gender', array('class' => 'control-label col-md-12 col-sm-12 col-xs-12', 'for'=> 'gender' )) }}
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        {{ Form::label('gender', $response->gender, array('class' => 'light-font', 'for'=> 'gender' )) }}
                    </div>
                </div>
                <div class="col-md-12 item form-group">
                    <div class="col-md-3 col-sm-3 col-xs-12  no-padding">
                        {{ Form::label('age', 'Age', array('class' => 'control-label col-md-12 col-sm-12 col-xs-12', 'for'=> 'age' )) }}
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        {{ Form::label('age', $response->age, array('class' => 'light-font', 'for'=> 'age' )) }}
                    </div>
                </div>
                @if($response->age == "0-13" OR $response->age == "14-17")
                    <div class="col-md-12 item form-group">
                        {{ Form::label('parent_name', 'Parent/Guardian Name', array('class' => 'control-label col-md-3 col-sm-3 col-xs-12', 'for'=> 'parent_name' )) }}
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            {{ Form::label('', $response->parent_name, array('class' => 'light-font', 'for'=> 'parent_name' )) }}
                        </div>
                    </div>
                    <div class="col-md-12 item form-group">
                        {{ Form::label('parent_contact_info', 'Parent/Guardian Contact Info', array('class' => 'control-label col-md-3 col-sm-3 col-xs-12', 'for'=> 'parent_contact_info' )) }}
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            {{ Form::label('', $response->parent_contact_info, array('class' => 'light-font', 'for'=> 'parent_contact_info' )) }}
                        </div>
                    </div>
                @endif
                @foreach($questions as $question)
                    @if(is_null($question->service_id))
                        <div class="col-md-12 item form-group">
                            <div class="col-md-3 col-sm-3 col-xs-12  no-padding">
                                {{ Form::label('rb_option', $question->text, array('class' => 'control-label col-md-12 col-sm-12 col-xs-12', 'for'=> 'rb_option' )) }}
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                {{ Form::label('rb_answer', $question->answer == 1 ? 'Yes' : 'No', array('class' => 'light-font', 'for'=> 'rb_answer' )) }}
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
            @if(isset($services) && !is_null($services))
                @foreach($services as $service)
                    @if($service->id < 9)
                        <div class="col-md-12">
                            <h3 class="heading">{{ $service->name }}</h3>
                        </div>
                    @endif
                    @foreach($questions as $question)
                        @if($question->service_id == $service->id && !is_null($question->answer))
                            <div class="col-md-12 item form-group">
                                <div class="col-md-3 col-sm-3 col-xs-12  no-padding">
                                    {{ Form::label('rb_option', $question->text, array('class' => 'control-label col-md-12 col-sm-12 col-xs-12', 'for'=> 'rb_option' )) }}
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    {{ Form::label('rb_answer', $question->answer == 1 ? 'Yes' : 'No', array('class' => 'light-font', 'for'=> 'rb_answer' )) }}
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endforeach
            @endif
        </div>
    </div>
</div>