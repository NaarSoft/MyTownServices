<style>
    .info-label{
        padding: 5px 0 5px 0;
        color: #000;
    }

    .info-val {
        color: #666;
        padding: 5px;
    }
</style>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="content">
            @if(isset($response) && !is_null($response))
                <div class="col-sm-12">
                    <div class="col-sm-3 info-label">
                        Name
                    </div>
                    <div class="col-sm-9 info-val">
                        {{ $response->name }}
                    </div>
                </div>
                <div class="col-sm-12 ">
                    <div class="col-sm-3 info-label">
                        Email address
                    </div>
                    <div class="col-sm-9 info-val">
                        {{ $response->email_address }}
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="col-sm-3 info-label">
                        Phone number
                    </div>
                    <div class="col-sm-9 info-val">
                        {{ $response->cell_phone }}
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="col-sm-3 info-label">
                        Gender
                    </div>
                    <div class="col-sm-6 info-val">
                        {{ $response->gender }}
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="col-sm-3 info-label">
                        Age
                    </div>
                    <div class="col-sm-6 info-val">
                        {{ $response->age }}
                    </div>
                </div>
            @endif
            <div>&nbsp;</div>
            @if(isset($respondent_basic_info) && $respondent_basic_info)
                <div class="col-sm-12">
                    <h4> Basic Information </h4>
                </div>
                <div class="col-sm-12" style="border-top: 1px solid #E5E5E5;">&nbsp;</div>
                <div class="col-sm-12">
                    @foreach($respondent_basic_info as $row)
                        <div class="col-sm-12 info-label">
                            <span class="info-label"> {{ $row->text }} </span>
                            <span class="info-val">
                            @if($row->answer)
                                Yes
                            @else
                                No
                            @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
            <div>&nbsp;</div>
            @if(isset($requested_services) && $requested_services)
                <div class="col-sm-12">
                    <h4> Services Requested </h4>
                </div>
                <div class="col-sm-12" style="border-top: 1px solid #E5E5E5;">&nbsp;</div>
                <div class="col-sm-12">
                    @foreach($requested_services as $row)
                        <div class="col-sm-12 info-label">
                            {{ $row->text }}
                        </div>
                    @endforeach
                </div>
            @endif
            <div>&nbsp;</div>
            <div>&nbsp;</div>
            <div>&nbsp;</div>
            <div>&nbsp;</div>
        </div>
    </div>
</div>