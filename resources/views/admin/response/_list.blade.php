<div id="list_view" style="display: none;">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="form-group form-group-sm">
                <label for="agency" class="control-label left-align col-lg-1 col-md-2 col-sm-2 col-xs-12" style="padding-left: 0px !important">
                    Search
                </label>
                <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                    <div class="input-group">
                        {!! Form::text('txtSearch', null, array('id'=>'txtSearch', 'class' => 'form-control')) !!}
                        <div class="input-group-btn margin-left" style="vertical-align: top">
                            <button id="btnSearch" type="button"
                                    class="btn btn-sm btn-success">
                                Search
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 pull-right">
                    <div class="checkbox i-checks">
                        <label class="pull-right">
                            {{ Form::checkbox('chk_show_all', 1, false, ['id'=>'chk_show_all', 'class' => 'flat'])}}
                            Show All
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>

    <table id="responses" class="table table-bordered" width="100%">
        <thead>
        <tr>
            <th class="sorting_desc">Applicant Name</th>
            <th width="70px">Gender</th>
            <th width="150px">Age</th>
            <th width="150px">Services Requested</th>
            <th width="150px">Date & Time of Appointment</th>
            <th width="150px">Form Filled On</th>
            <th width="150px">Status</th>
            @if(Entrust::hasRole('admin'))
                <th width="150px">Actions</th>
            @endrole
        </tr>
        </thead>
    </table>
</div>
@include('service._schedule_modal')
@include('service._cancel_modal')