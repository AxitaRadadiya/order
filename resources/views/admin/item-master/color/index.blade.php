{{-- Country Tab Content --}}
<div class="row">
    <div class="col-12">
        <div class="card">
           <div class="card-header d-flex align-items-center mb-3">
                <h5 class="mb-0">Color</h5>
                <div class="ml-auto">
                    <a href="#" class="btn-create color-date-modal">
                        <i class="fa fa-plus"></i> Add Color
                    </a>
                </div>
            </div>
            <div class="card-body" style="padding: 0px !important;">
                <table id="ColorTable" class="table dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Color Modal --}}
<div class="modal fade" id="ColorModal" tabindex="-1" role="dialog" aria-labelledby="ColorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ColorModalLabel">Color</h5>
                <button type="button" class="close waves-effect waves-light" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('color.store') }}" method="POST" id="colorForm">
                    @csrf
                    <input type="hidden" name="color_id" id="color_id">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="color_name" value="" required>
                                <span class="text-danger error color-name-error"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" data-dismiss="modal">Close</button>
                <button type="button" class="btn-submit" id="saveColor">Save</button>
            </div>
        </div>
    </div>
</div>