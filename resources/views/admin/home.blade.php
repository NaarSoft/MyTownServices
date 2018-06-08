@extends('layouts.adminmaster')
@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    @if(Entrust::hasRole('admin'))
                        <h2>Admin: Dashboard</h2>
                    @elseif(Entrust::hasRole('agency'))
                        <h2>Agency: Dashboard</h2>
                    @endif
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
