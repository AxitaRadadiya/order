{{-- Country Tab Content --}}
<div class="row" >
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="mb-0">Countries</h5>
                <div class="ml-auto">
                    <a href="#" class="btn-create country-date-modal">
                        <i class="fa fa-plus"></i> Add Country
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table id="CountryTable" class="table dt-responsive nowrap w-100">
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

{{-- Country Modal --}}
<div class="modal fade" id="CountryModal" tabindex="-1" role="dialog" aria-labelledby="CountryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="CountryModalLabel">Country</h5>
                <button type="button" class="close waves-effect waves-light" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('country.store') }}" method="POST" id="countryForm">
                    @csrf
                    <input type="hidden" name="country_id" id="country_id">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="country_name" value="" required>
                                <span class="text-danger error country-name-error"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" data-dismiss="modal">Close</button>
                <button type="button" class="btn-submit" id="saveCountry">Save</button>
            </div>
        </div>
    </div>
</div>