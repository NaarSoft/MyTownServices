<section>
    <div id="respondent" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="col-sm-8 col-xs-12">
                        <h4 class="modal-title" id="myModalLabel">Respondent Information</h4>
                    </div>
                    <div class="col-sm-4 col-xs-12">
                        <button type="button" class="close" data-dismiss="modal" style="border: 1px solid #999; border-radius: 50%; padding: 2px;">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                </div>
                <div class="modal-body">
                    <div id="div_questionnaire">
                        @include('questionnaire._questionnaire_view')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>