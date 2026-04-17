{{-- Country Tab Content --}}
<div class="row">
    <div class="col-12">
        <div class="card">
           <div class="card-header d-flex align-items-center">
                <h5 class="mb-0">Size</h5>
                <div class="ml-auto">
                    <a href="#" class="btn-create size-date-modal">
                        <i class="fa fa-plus"></i> Add Size
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table id="SizeTable" class="table dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th>Sr No.</th>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Size Modal --}}
<div class="modal fade" id="SizeModal" tabindex="-1" role="dialog" aria-labelledby="SizeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="SizeModalLabel">Size</h5>
                <button type="button" class="close waves-effect waves-light" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('size.store') }}" method="POST" id="sizeForm">
                    @csrf
                    <input type="hidden" name="size_id" id="size_id">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="size_name" value="" required>
                                <span class="text-danger error size-name-error"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" data-dismiss="modal">Close</button>
                <button type="button" class="btn-submit" id="saveSize">Save</button>
            </div>
        </div>
    </div>
</div>