@extends('layouts.app')
@section('slider')
    @parent
    @include('layouts._slider', array('page_header' => 'Home / Services', 'slider_class' => 'slider-area-6'))
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
	
        <p class="service-content text-center">My Town Services is a one stop shop for residents of Montcalm County looking for services or assistance with everything from basic needs to mental health and is here to help children, adults, seniors and veterans to connect with services in the Howard City area. Click on the logos below to find out more about participating agencies.</p>
		
		</div>
<br/>
<br/>
<br/>	 
<br/>
<br/>	 
<div class="row">

<div class="col-md-4">

<div class="row" style="margin-left:10px;">
<div class="col-md-10">
<a href="http://mytownservices.org/mts_dev/agency/2"><p style="/*border-bottom:3px solid #60b5b3;*/ color:#60b5b3;font-weight:bold;padding-left: 175px;">EightCAP, Inc.</p></a>
<p style="border-top:1px solid #60b5b3;margin-top: -10px;padding-left: 66px;">Getting assistance with food or</p>
<p style="margin-top:-10px;padding-left: 15px;">clothing finding or paying for child care</p>
</div>
<div class="col-md-2">
<img src="{{ asset('public/assets/agency/line2.PNG')}}" alt="" style="width:71px !important; height:71px !important;margin-left:-30px !important;">
</div>
</div><br/>


<div class="row">
<div class="col-md-10">
<a href="http://mytownservices.org/mts_dev/agency/3"><p style="/*border-bottom:3px solid #2592c8;*/ color:#2592c8;font-weight:bold;padding-left: 14px;">Montcalm County Commission on Aging</p></a>
<p style="border-top:1px solid #2592c8;margin-top: -10px;padding-left: 135px;">Accessing Senior service</p>
<p style="margin-top:-10px;padding-left: 183px;">(age 60 or older)</p>
</div>
<div class="col-md-2">
<img src="{{ asset('public/assets/agency/line5.PNG')}}" alt=""style="width:71px !important; height:71px !important;margin-left:-30px !important;" >
</div>
</div><br/>

<div class="row">
<div class="col-md-10">
<a href="http://mytownservices.org/mts_dev/agency/4"><p style="/*border-bottom:3px solid #ce40ce;*/ color:#ce40ce;font-weight:bold;padding-left: 120px;">Montcalm Care Network</p></a>
<p style="border-top:1px solid #ce40ce;margin-top: -10px;padding-left: 24px;">Accessg mental health or autism services.</p>
<p style="margin-top:-10px;">Accessing substance use or recovery services</p>
</div>
<div class="col-md-2">
<img src="{{ asset('public/assets/agency/lion6.PNG')}}" alt="" style="width:71px !important; height:71px !important;margin-left:-30px !important;" >
</div>
</div><br/>

<div class="row">
<div class="col-md-10">
<a href="http://mytownservices.org/mts_dev/agency/5"><p style="/*border-bottom:3px solid #72001e;*/ color:#72001e;font-weight:bold;">Department of Health & Human Services</p></a>
<p style="border-top:1px solid #72001e;margin-top: -10px;padding-left: 78px;">Getting unemployment benefits </p>
<p style="margin-top:-10px;padding-left: 69px;">paying for rent.heat or electricity.</p>
</div>
<div class="col-md-2">
<img src="{{ asset('public/assets/agency/lion7.PNG')}}" alt=""  style="width:71px !important; height:71px !important;margin-left:-30px !important;">
</div>
</div><br/>

</div>





<div class="col-md-4">



<div class='circle-container'>
	<a href='#' class='center'> <img src="{{ asset('public/assets/images/mytown-logo.png')}}" alt=""> </a>
	<a href='#' class='deg0'><img src="{{ asset('public/assets/agency/3.png')}}" alt=""> </a>
	<a href='#' class='deg45'><img src="{{ asset('public/assets/agency/1.png')}}" alt=""> </a>
	<a href='#' class='deg135'><img src="{{ asset('public/assets/agency/8.png')}}" alt=""> </a>
	<a href='#' class='deg136'><img src="{{ asset('public/assets/agency/5.png')}}" alt=""> </a>
	<a href='#' class='deg180'><img src="{{ asset('public/assets/agency/4.png')}}" alt=""> </a>
	<a href='#' class='deg225'><img src="{{ asset('public/assets/agency/3.png')}}" alt=""> </a>
	<a href='#' class='deg226'><img src="{{ asset('public/assets/agency/2.png')}}" alt=""> </a>
	<a href='#' class='deg315'><img src="{{ asset('public/assets/agency/6.png')}}" alt=""> </a>
</div>


</div>
<div class="col-md-4">
<!--left services -->





<div class="row">

<div class="col-md-2">
<img src="{{ asset('public/assets/agency/lion4.PNG')}}" alt="" style="width:71px !important; height:71px !important;margin-left:9px !important;">
</div>
<div class="col-md-10" style="margin-left: -16px;">
<a href="http://mytownservices.org/mts_dev/agency/6"><p style="border-bottom:1px solid #ee5e67;font-weight:bold; color:#ee5e67;font-weight:bold;">Mid-Michigan District Health Department</p></a>
<p style="margin-top: -10px;">Getting or paying for Health Insurance</p>
<p style="margin-top: -10px;">Finding a primary care physician.</p>
</div>
</div><br/>


<div class="row">

<div class="col-md-2">
<img src="{{ asset('public/assets/agency/lion4.PNG')}}" alt="" style="width:71px !important; height:71px !important;margin-left: 9px !important;">
</div>
<div class="col-md-10" style="margin-left: -16px;">
<a href="http://mytownservices.org/mts_dev/agency/7"><p style="border-bottom:1px solid #ff6601; color:#ff6601;font-weight:bold;">Veteran Services</p></a>
<p style="margin-top: -10px;">Accessing Veteran Services</p>
</div>
</div><br/>

<div class="row">

<div class="col-md-2">
<img src="{{ asset('public/assets/agency/lion8.PNG')}}" alt="" style="width:71px !important; height:71px !important;margin-left:9px !important;margin-left: 9px !important;" >
</div>
<div class="col-md-10" style="margin-left: -16px;">
<a href="http://mytownservices.org/mts_dev/agency/1"><p style="border-bottom:1px solid #fab421;color:#fab421;font-weight:bold;">West Michigan Works</p></a>
<p style="margin-top: -10px;">Getting unemployment benefits</p>
<p style="margin-top: -10px;">Finding a job or getting job training</p>
</div>
</div><br/>

<div class="row">

<div class="col-md-2">
<img src="{{ asset('public/assets/agency/lion9.PNG')}}" alt="" style="width:71px !important; height:71px !important;margin-left:9px !important; " >
</div>
<div class="col-md-10" style="margin-left: -16px;">
<a href="http://mytownservices.org/mts_dev/agency/8"><p style="border-bottom:1px solid #93a519; color:#93a519;font-weight:bold;">Great Start Collaborative</p></a>
<p style="margin-top: -10px;">Accessing Early Education services</p>
<p style="margin-top: -10px;">(age 3-5)</p>
</div>
</div><br/>
















<!--End left services -->
</div>





</div>


</div>
 



<div class="col-sm-12 text-center" style="margin-top:12px">
  <a href="{{ URL::to('index') }}" class="btn btn-warning" role="button" style="background: #ff5702;
    border-color: #ff5702;color:#fff;margin-left:-2px;">Schedule an Appointment</a>
 </div>

 
 </div>
   
@endsection
 
<!-- custom css-->
<style>
a.center {
    width: 140px !important;
    height: 200px !important;
    margin: -76px 0px 0px -70px !important;
}

  a.center img {
    border-style: none;
}

/**
 * Position icons into circle (SO)
 * http://stackoverflow.com/q/12813573/1397351 
 */
.circle-container {
	position: relative;
	width: 24em;
	height: 24em;
	padding: 2.8em; /*= 2em * 1.4 (2em = half the width of an img, 1.4 = sqrt(2))*/
	border: dashed 1px;
	border-radius: 50%;
	margin: 1.75em auto 0;
}
.circle-container a {
	display: block;
	overflow: hidden;
	position: absolute;
	top: 50%; left: 50%;
	width: 7em; height: 7em;
	margin: -4em; /* 2em = 4em/2 */ /* half the width */
	margin-left: -63px;
}
.circle-container img { display: block; width: 100%;/*border: 1px dotted #000000;*/
    border-radius: 58px; }
.deg0 { transform: rotate(-16deg) translate(13em) rotate(17deg); } /* 12em = half the width of the wrapper */
.deg45 { transform: rotate(15deg) translate(13em) rotate(-15deg); }
.deg135 { transform: rotate(46deg) translate(13em) rotate(-46deg); }
.deg136 { transform: rotate(128deg) translate(12em) rotate(-129deg); }
.deg180 { transform: rotate(162deg) translate(12em) rotate(199deg); }
.deg225 { transform: rotate(196deg) translate(12em) rotate(-200deg); }
.deg226 { transform: rotate(230deg) translate(12em) rotate(-230deg); }
.deg315 { transform: rotate(313deg) translate(12em) rotate(-315deg); }

/* this is just for showing the angle on hover */
.circle-container a:not(.center):before {
	position: absolute;
	width: 4em;
	color: white;
	opacity: 0;
	background: rgba(0,0,0,.5);
	font: 1.25em/3.45 Courier, monospace;
	letter-spacing: 2px;
	text-decoration: none;
	text-indent: -2em;
	text-shadow: 0 0 .1em deeppink;
	transition: .7s; /* only works in Firefox */
	content: attr(class)'°';
	display:none;
}
.circle-container a:hover:before { opacity: 1; }

/* this is for showing the circle on which the images are placed */
.circle-container:after {
	position: absolute;
	top: 2.8em; left: 2.8em;
	width: 24em; height: 24em;
	/*border: dashed 1px deeppink;*/
	border-radius: 50%;
	opacity: .3;
	pointer-events: none;
	content: '';
}
.circle-container:hover:after { opacity: 1; }
.circle-container a:not(.center):after {
	position: absolute;
	top: 50%; left: 50%;
	width: 4px; height: 4px;
	border-radius: 50%;
	box-shadow: 0 0 .5em .5em white;
	margin: -2px;
	background: deeppink;
	opacity: .3;
	content: '';
	display:none;
}
.circle-container:hover a:after { opacity: 1; }
.circle-container a:hover:after { opacity: .3; }
</style>