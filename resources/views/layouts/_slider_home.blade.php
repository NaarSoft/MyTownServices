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
    top: 196px !important;
    right: 9px !important;
}
</style>