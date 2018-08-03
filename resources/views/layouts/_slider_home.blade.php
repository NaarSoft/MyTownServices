<div class="slider-area">
    @include('layouts._menu')

    <div class="container container-position">
	
	
	
	
        {!! Form::open( array('id' => 'form', 'method' => 'post', 'class' => 'small', 'action' => 'PublicController@saveBasicInfo')) !!}
            <!--<div id="div_appointment">
<div class="row">
					<div class="col-md-6 text-right" style="">
						<h2 style="color:#fff; font-weight: 300;
    letter-spacing:1px;">Do you know</h2><br/><h4 style="color:#fff;font-weight: 300;
    letter-spacing: 1px;">Your ACEs ?</h4><br/>
						<p style=""> <a href="#" class="btn btn-warning" role="button">Know More</a> </p>
						</div>
			<div class="col-md-6 vl">
						<h2 style="color:#fff;font-weight: 300;letter-spacing:1px;">What Services </h2><br/><h4 style="color:#fff;font-weight: 300; letter-spacing: 1px;">Can I Receive ? </h4><br/>
						<p style=""> <a href="#" class="btn btn-warning" role="button">Know More</a> </p>
</div>
						 </div>
</div>-->
		
		
		
		<div class="row">
		<div id="div_appointment2">
		
		
		<p style="color:#fff;font-weight:lighter;letter-spacing:1px;font-size:48px;">Do You know</p><br/>
	    <p style="color:#fff;font-weight:lighter;letter-spacing: 1px; font-size:32px;margin:-35px -2px 11px 106px;">Your <b style="font-weight:bold;font-size: 32px;color: #fff;">ACEs ?</b></p><br/>
	     <p style=""> <a href="{{ URL::to('trauma') }}" class="btn btn-warning1" role="button" style="margin: -10px 0px 0px 146px;">Know More</a> </p>
		
		
		
		</div>
		<div id="div_appointment1" class="vl">
			
			
			<p style="color:#fff;font-weight:lighter;letter-spacing:1px;font-size:48px;">What Services </p><br/>
			<p style="color:#fff;font-weight:lighter; letter-spacing: 1px;font-size:32px;margin: -38px -10px 6px 7px;">Can I Receive ? </p><br/>
		   <p style=""> <a href="{{ URL::to('service') }}" class="btn btn-warning1" role="button">Know More</a> </p>
			
</div>
</div>
	{!! Form::close() !!}
    </div>

    <div class="home-page-sliders">
        <?php $item = rand(1,3);?>
        <div class="single-slider-item-{{$item}}"></div>
    </div>
</div>
<!--custom css -->
<style>
div#div_appointment {
    top: 150px !important;
    right: 324px !important;
}


.vl {
    border-left: 1px solid #fff;
    height: 217px;
    position: absolute;
    left: 50%;
    margin-left: 0px;
    top: 0;
	padding-left: 35px;
}

@media only screen and (max-width: 600px) {
   #div_appointment2 {
     display:none;
}

   #div_appointment3 {
     display:none;
}


.vl {
   display:none;
}

}


a.btn.btn-warning1 {
    background: #ff5702;
    color: #fff;
	display: inline-block;
    font-size: 14px;
    /*font-weight: 700;*/
	letter-spacing: 1px;
    font-family: sans-serif;
	padding: 8px 8px 10px 14px;
    margin-left: 11px;
    width: 124px;
}

#div_appointment1 {
    position: absolute;
    z-index: 999;
    right:15px;
    top:138px;
   width:500px;
    /*font-family: "Lato",Helvetica,Arial,sans-serif;*/
}
#div_appointment2 {
    position: absolute;
    z-index: 999;
    right:395px;
    top:138px;
   width:500px;
    /*font-family: "Lato",Helvetica,Arial,sans-serif;*/
}

</style>
