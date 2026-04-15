{{-- City Tab Content --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="mb-0">Cities</h5>
                <div class="ml-auto">
                    <a href="#" class="btn btn-success btn-sm waves-effect waves-light city-date-modal">
                        <i class="fa fa-plus"></i> Add City
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table id="CityTable" class="table dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>Country</th>
                            <th>State</th>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th>Sr No.</th>
                            <th>Country</th>
                            <th>State</th>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- City Modal --}}
<div class="modal fade" id="CityModal" tabindex="-1" role="dialog" aria-labelledby="CityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="CityModalLabel">City</h5>
                <button type="button" class="close waves-effect waves-light" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('city.store') }}" method="POST" id="cityForm">
                    @csrf
                    <input type="hidden" name="city_id" id="city_id_hidden">

                    <div class="row">
                        {{-- Country --}}
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Country <span class="text-danger">*</span></label>
                                <select name="country_id" id="city_country_id" class="form-control" required>
                                    <option value="">Select Country</option>
                                    @foreach(\App\Models\Country::orderBy('name')->get() as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error city-country-error"></span>
                            </div>
                        </div>

                        {{-- State — populated via AJAX on country change --}}
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>State <span class="text-danger">*</span></label>
                                <select name="state_id" id="city_state_id" class="form-control" required>
                                    <option value="">Select State</option>
                                </select>
                                <span class="text-danger error city-state-error"></span>
                            </div>
                        </div>

                        {{-- City Name --}}
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="city_name" required>
                                <span class="text-danger error city-name-error"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary waves-effect waves-light" id="saveCity">Save</button>
            </div>
        </div>
    </div>
</div>