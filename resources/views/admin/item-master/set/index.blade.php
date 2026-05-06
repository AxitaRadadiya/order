<div class="row">
    <div class="col-12">
        <div class="card">
           <div class="card-header d-flex align-items-center mb-3">
                <h5 class="mb-0">Set</h5>
                <div class="ml-auto">
                    <a href="#" class="btn-create set-date-modal">
                        <i class="fa fa-plus"></i> Add Set
                    </a>
                </div>
            </div>
            <div class="card-body" style="padding: 0px !important;">
                <table id="SetTable" class="table dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>Name</th>
                            <th>Sizes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="SetModal" tabindex="-1" role="dialog" aria-labelledby="SetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="SetModalLabel">Set</h5>
                <button type="button" class="close waves-effect waves-light" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="setForm">
                    @csrf
                    <input type="hidden" name="set_id" id="set_id">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="set_name" value="" required>
                                <span class="text-danger error set-name-error"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Sizes <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="size_ids[]" id="set_size_ids" multiple required style="width: 100%;">
                                    @foreach($sizes as $size)
                                        <option value="{{ $size->id }}">{{ $size->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error set-size-error"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" data-dismiss="modal">Close</button>
                <button type="button" class="btn-submit" id="saveSet">Save</button>
            </div>
        </div>
    </div>
</div>
