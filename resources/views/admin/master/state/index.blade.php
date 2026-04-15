{{-- State Tab Content --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="mb-0">State</h5>
                <div class="ml-auto">
                    <a href="#" class="btn btn-success btn-sm waves-effect waves-light state-date-modal">
                        <i class="fa fa-plus"></i> Add State
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table id="StateTable" class="table dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>Country</th>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th>Sr No.</th>
                            <th>Country</th>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- State Modal --}}
<div class="modal fade" id="StateModal" tabindex="-1" role="dialog" aria-labelledby="StateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="StateModalLabel">State</h5>
                <button type="button" class="close waves-effect waves-light" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('state.store') }}" method="POST" id="stateForm">
                    @csrf
                    <input type="hidden" name="state_id" id="state_id_hidden">

                    <div class="row">
                        {{-- Country --}}
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Country <span class="text-danger">*</span></label>
                                <select name="country_id" id="state_country_id" class="form-control" required>
                                    <option value="">Select Country</option>
                                    @foreach(\App\Models\Country::orderBy('name')->get() as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error state-country-error"></span>
                            </div>
                        </div>

                        {{-- State Name --}}
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="state_name" required>
                                <span class="text-danger error state-name-error"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary waves-effect waves-light" id="saveState">Save</button>
            </div>
        </div>
    </div>
</div>