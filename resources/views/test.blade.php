@extends('layouts.app')
@section('slider')
    @parent
    @include('layouts._slider', array('page_header' => 'Home / Trauma', 'slider_class' => 'slider-area-4'))
@stop

@section('content')
<div class="row">






<div class='circle-container'>
	<a href='#' class='center'>Test1<img src="{{ asset('public/assets/agency/1.png')}}"> </a>
	<a href='#' class='deg0'>Test2<img src="{{ asset('public/assets/agency/2.png')}}"> </a>
	<a href='#' class='deg45'>Test3<img src="{{ asset('public/assets/agency/3.png')}}"> </a>
    <a href='#' class='deg135'>Test4<img src="{{ asset('public/assets/agency/4.png')}}"> </a>
	<a href='#' class='deg180'>Test5<img src="{{ asset('public/assets/agency/5.png')}}"> </a>
	<a href='#' class='deg225'>Test6<img src="{{ asset('public/assets/agency/6.png')}}"> </a>
	<a href='#' class='deg315'>Test7<img src="{{ asset('public/assets/agency/7.png')}}"> </a>
	<a href='#' class='deg350'>Test8<img src="{{ asset('public/assets/agency/8.png')}}"> </a>

</div>
</div>
  
@endsection
<!-- custom css-->
<style>


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
	width: 4em; height: 4em;
	margin: -2em; /* 2em = 4em/2 */ /* half the width */
}
.circle-container img { display: block; width: 100%; }
.deg0 { transform: translate(12em); } /* 12em = half the width of the wrapper */
.deg45 { transform: rotate(45deg) translate(12em) rotate(-45deg); }
.deg135 { transform: rotate(135deg) translate(12em) rotate(-135deg); }
.deg180 { transform: translate(-12em); }
.deg225 { transform: rotate(225deg) translate(12em) rotate(-225deg); }
.deg315 { transform: rotate(315deg) translate(12em) rotate(-315deg); }
.deg350 { transform: rotate(350deg) translate(12em) rotate(-350deg); }

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
}


/* this is for showing the circle on which the images are placed */
.circle-container:after {
	position: absolute;
	top: 2.8em; left: 2.8em;
	width: 24em; height: 24em;
	border: dashed 1px deeppink;
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
}
.circle-container:hover a:after { opacity: 1; }






</style>