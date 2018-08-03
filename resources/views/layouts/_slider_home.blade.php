<div class="slider-area">
    @include('layouts._menu')

    <div class="container container-position">
	
	
	
	
        {!! Form::open( array('id' => 'form', 'method' => 'post', 'class' => 'small', 'action' => 'PublicController@saveBasicInfo')) !!}
            <div id="div_appointment">

                
                  
                        <div class="row">
						
						<div class="col-md-6">
						<h1 style="color:#fff;">Do you know</h1><br/><h3 style="color:#fff;text-align:center;">Your ACEs ?</h3><br/>
						<p style="text-align:center"> <a href="#" class="btn btn-warning" role="button">Know More</a> </p>
						</div>
						
						
						<div class="col-md-6">
						<h1 style="color:#fff;">What Services</h1><br/><h3 style="color:#fff;text-align:center;">Can I Receive ?</h3><br/>
						<p style="text-align:center"> <a href="#" class="btn btn-warning" role="button">Know More</a> </p>
						
						</div>
						
                            
                            
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