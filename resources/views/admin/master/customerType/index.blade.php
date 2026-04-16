{{-- Customer Type Tab Content --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="mb-0">Customer Types</h5>
                <div class="ml-auto">
                    <a href="#" class="btn-create customer-type-modal">
                        <i class="fa fa-plus"></i> Add Customer Type
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table id="CustomerTypeTable" class="table dt-responsive nowrap w-100">
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

{{-- Customer Type Modal --}}
<div class="modal fade" id="CustomerTypeModal" tabindex="-1" role="dialog" aria-labelledby="CustomerTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="CustomerTypeModalLabel">Customer Type</h5>
                <button type="button" class="close waves-effect waves-light" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('customer-type.store') }}" method="POST" id="customerTypeForm">
                    @csrf
                    <input type="hidden" name="customer_type_id" id="customer_type_id">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="customer_type_name" value="" required>
                                <span class="text-danger error customer-type-name-error"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" data-dismiss="modal">Close</button>
                <button type="button" class="btn-submit" id="saveCustomerType">Save</button>
            </div>
        </div>
    </div>
</div>