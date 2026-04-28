{{-- Group Tab Content --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center mb-3">
                <h5 class="mb-0">sub-Group</h5>
                <div class="ml-auto">
                    <a href="#" class="btn btn-create btn-sm waves-effect waves-light sub-group-date-modal">
                        <i class="fa fa-plus"></i> Add sub-Group
                    </a>
                </div>
            </div>
            <div class="card-body" style="padding: 0px !important;">
                <table id="SubGTable" class="table dt-responsive nowrap w-100">
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

{{-- sub-Group Modal --}}
<div class="modal fade" id="SubGroupModal" tabindex="-1" role="dialog" aria-labelledby="SubGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="SubGroupModalLabel">sub-Group</h5>
                <button type="button" class="close waves-effect waves-light" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('sub-group.store') }}" method="POST" id="subGroupForm">
                    @csrf
                    <input type="hidden" name="sub_group" id="sub_group_hidden">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Group <span class="text-danger">*</span></label>
                                <select class="form-control" name="group_id" id="group_id" required>
                                    <option value="">Select Group</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error sub-group-group-error"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="sub_group_name" required>
                                <span class="text-danger error sub-group-name-error"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel waves-effect waves-light" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-submit waves-effect waves-light" id="saveSubGroup">Save</button>
            </div>
        </div>
    </div>
</div>