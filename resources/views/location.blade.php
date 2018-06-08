@extends('layouts.app')
@section('slider')
    @parent
    @include('layouts._slider', array('page_header' => 'Home / locations', 'slider_class' => 'slider-area-5'))
@stop

@section('content')
<div class="row">
<p>My Town Services offers residents of Montcalm Country with an arrayservicesat each of convenient locations.</p>
   <div class="col-md-6"><i class="fa fa-map-marker" style="font-size:88px;color:#2f91db"></i><p style="text-align:center; margin-top:-78px;"><b>Carson City:</b> Coming Soon</p></div>
   <div class="col-md-6"><i class="fa fa-map-marker" style="font-size:88px;color:#2f91db;"></i><p style="text-align:center; margin-top:-78px;">Greenville: Located at 114 S.Greenville West Drive,<br/>inside West Michigan Works.Kiosks and <br/>appointments availble Monday- Friday, 8am-5pm.</p></div></div>
   <br/><br/><br/><br/><br/>
   
   
   
   <div class="row">
    <div class="col-md-6"><i class="fa fa-map-marker" style="font-size:88px;color:#2f91db"></i><p style="text-align:center;margin-top:-78px;margin-left: 30px;">Howard City: Located at 220 Edgerton Street Edmore.<br/>Kiosks,DHHS Eligibility workers and appointments available<br/> Monday-Wednessday, 9am-4pm.</p></div>
   <div class="col-md-6"><i class="fa fa-map-marker" style="font-size:88px;color:#2f91db"></i><p style="text-align:center;margin-top:-78px;">Stanton:Located at 611 N. State Street, Stanton,<br/>inside Montcalm Care Network, Appointments<br/>availble Monday-Friday,8am-5pm</p></div></div>
   <br/><br/>
   <div class="row">
<p>Not sure which agencies or services are right for you? Completeour online questionnaire to find out which services you may be eligible to receive and easily schedule one convenient appointment time to meet with multiple agencies.Connecting to services in Montcalm Country has never been easier!</p><br/><br/>
<p style=""><a href="#" class="btn btn-warning" role="button">Click here to get started</a></p>
</div>
@endsection
<!-- custom css-->
<style>
i.fa.fa-map-marker.lo {
    width: 89px;
    border: 1px solid;
    padding-left: 20px;
    border-radius: 53px;
    border-width: 2px;
}

.slider-area {
    /*background: yellow;*/
background: url("../images/contact_us.jpg");
}

</style>