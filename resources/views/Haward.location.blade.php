@extends('layouts.app')
@section('slider')
    @parent
    @include('layouts._slider', array('page_header' => 'Home / locations', 'slider_class' => 'slider-area-5'))
@stop

@section('content')
<div class="row">

<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d11594.411223485162!2d-85.04793621680851!3d43.40623066608636!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8818a18b30a9f357%3A0xc973a03c6b697acb!2sEdmore%2C+MI+48829%2C+USA!5e0!3m2!1sen!2sin!4v1528460952869" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>

</div>

@endsection
<!-- custom css-->
