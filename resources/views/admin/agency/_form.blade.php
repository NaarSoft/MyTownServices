<section class="agency_content">
    <div class="item form-group required">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name
        </label>

        <div class="col-md-6 col-sm-6 col-xs-12">
            {!! Form::text('name', null, array('id'=>'name', 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Name' )) !!}
            @if ($errors->has('name'))<p class="validation-error">{!!$errors->first('name')!!}</p>@endif
        </div>
    </div>
    <div class="item form-group required">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="address">Address
        </label>

        <div class="col-md-6 col-sm-6 col-xs-12">
            {!! Form::textarea('address', null, array('id'=>'address', 'class' => 'form-control col-md-7 col-xs-12', 'size' => '30x3', 'placeholder' => 'Address' )) !!}
            @if ($errors->has('address'))<p class="validation-error">{!!$errors->first('address')!!}</p>@endif
        </div>
    </div>
    <div class="item form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="contact_info">Contact Information
        </label>

        <div class="col-md-6 col-sm-6 col-xs-12">
            {!! Form::text('contact_info', null, array('id'=>'contact_info', 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Contact Information' )) !!}
            @if ($errors->has('contact_info'))<p class="validation-error">{!!$errors->first('contact_info')!!}</p>@endif
        </div>
    </div>
    <div class="item form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="website">Website
        </label>

        <div class="col-md-6 col-sm-6 col-xs-12">
            {!! Form::text('website', null, array('id'=>'website', 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Website' )) !!}
            @if ($errors->has('website'))<p class="validation-error">{!!$errors->first('website')!!}</p>@endif
        </div>
    </div>
    {{--<div class="item form-group required">--}}
        {{--<label class="control-label col-md-3 col-sm-3 col-xs-12" for="htmlcontent">Content--}}
        {{--</label>--}}

        {{--<div class="col-md-6 col-sm-6 col-xs-12">--}}
            {{--{!! Form::textarea('htmlcontent', null, array('id'=>'htmlcontent', 'class' => 'form-control col-md-7 col-xs-12', 'size' => '30x3', 'placeholder' => 'Content' )) !!}--}}
            {{--@if ($errors->has('htmlcontent'))<p class="validation-error">{!!$errors->first('htmlcontent')!!}</p>@endif--}}
        {{--</div>--}}
    {{--</div>--}}
    <div class="item form-group required">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="service_name">Service
        </label>

        <div class="col-md-6 col-sm-6 col-xs-12">
            {!! Form::text('service_name', $agency->agency_service->name, array('id'=>'service_name', 'class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Service Name' )) !!}
            {!! Form::hidden('service_id', null, array('id'=>'service_id', 'class' => 'form-control col-md-7 col-xs-12' )) !!}
            {{--{!! Form::select('service_id', [0 => "Select"] + $services, null, ['class' => 'form-control m-bot15']) !!}--}}
            @if ($errors->has('service_name'))<p class="validation-error">{!!$errors->first('service_name')!!}</p>@endif
        </div>
    </div>
    <div class="item form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="locations">Locations
        </label>

        <div class="col-md-6 col-sm-6 col-xs-12">
            @foreach ($locations as $locationId => $locationName)
                <label class="checkbox-inline">
                    {{ Form::checkbox('agency_locations[]', $locationId, (in_array($locationId, $agency_locations)) ? 'checked' : '',['class' => 'flat'])}}
                    {{ $locationName }}
                </label>
            @endforeach
        </div>
    </div>
    <div class="item form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="logo">Upload Logo
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="input-group">
                <input type="text" class="form-control" disabled placeholder="Upload Logo"/>
                <span class="input-group-btn file-container">
                    {!! Form::file('image', array('id' => 'image', 'class' => 'input-group-btn file' )) !!}
                    <button class="browse btn btn-primary" type="button" style="margin:-6px 0px 0px 0px;"><i
                                class="glyphicon glyphicon-search"></i> Browse
                    </button>
                </span>
            </div>
            @if ($errors->has('image'))<p class="validation-error">{!!$errors->first('image')!!}</p>@endif
        </div>
    </div>
    <div class="item form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="logo"></label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="docs-preview clearfix">
                <div class="img-preview preview-md" style="width: 200px; height: 100px; padding-left:10px;">
                    <img id="img-upload" src="{{asset('public/assets/agency/'. $agency->image_path )}}"
                            style="display: block; width: 100px; height: 100px; min-width: 0px !important; min-height: 0px !important; max-width: none !important; max-height: none !important; image-orientation: 0deg !important; margin-left: -8.93193px; margin-top: -8.65415px; transform: none;">
                </div>
            </div>
        </div>
    </div>
    <div class="ln_solid"></div>
    <div class="form-group">
        <div class="col-md-6 col-md-offset-3">
            <button type="button" class="btn btn-primary"
                    onclick="document.location='{{ URL::to('admin/agency/index') }}'">Cancel
            </button>
            {!! Form::submit('Submit', array('class'=>'btn btn-success')) !!}
        </div>
    </div>
</section>
<script type="text/javascript" src="{{ asset('public/assets/js/module/agency.js') }}"></script>