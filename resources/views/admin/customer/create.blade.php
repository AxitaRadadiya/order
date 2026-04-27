@extends('admin.layouts.app')
@section('title', 'Create Customer')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="mr-2 text-teal"></i>Create Customer</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
          <li class="breadcrumb-item active">Create</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">

    @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0 mt-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
      </div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
      </div>
    @endif

    <form action="{{ route('customers.store') }}" method="POST">
      @csrf

      {{-- 1. Basic Info --}}
      <div class="card " style="padding:10px;">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-user mr-1"></i>Basic Information</h3>
          <!-- <div class="card-tools"><button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button></div> -->
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label>Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Full name" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Company Name</label>
                <input type="text" class="form-control @error('company_name') is-invalid @enderror" name="company_name" value="{{ old('company_name') }}" placeholder="Company name">
                @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Role</label>
                <select name="role_id" class="form-control">
                  <option value="">-- Default (retailer) --</option>
                  @foreach($roles as $r)
                    <option value="{{ $r->id }}" {{ old('role_id') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Email address" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Mobile Number</label>
                <input type="text" class="form-control" name="phone" value="{{ old('phone') }}" placeholder="Mobile number">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Password <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required>
                  <div class="input-group-append">
                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password"><i class="fas fa-eye"></i></button>
                  </div>
                  @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Confirm Password <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required>
                  <div class="input-group-append">
                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password_confirmation"><i class="fas fa-eye"></i></button>
                  </div>
                  @error('password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Payment Terms</label>
                <select class="form-control" name="payment_terms">
                  <option value="">-- Select --</option>
                  @foreach(['due_on_receipt'=>'Due on Receipt','net_15'=>'Net 15','net_30'=>'Net 30','net_45'=>'Net 45','net_60'=>'Net 60'] as $val=>$label)
                    <option value="{{ $val }}" {{ old('payment_terms')==$val?'selected':'' }}>{{ $label }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                  <label>Place of Supply</label>
                  <select class="form-control" name="place_of_supply">
                    <option value="">-- Select State --</option>
                    @foreach($states->pluck('name')->unique()->sort()->values() as $stateName)
                      <option value="{{ $stateName }}" {{ old('place_of_supply') == $stateName ? 'selected' : '' }}>
                        {{ $stateName }}
                      </option>
                    @endforeach
                  </select>
                </div>
            </div>
          </div>
        </div>
      </div>

      {{-- 3 & 4. Billing + Shipping Address (Side by Side) --}}
      <div class="row">
        <div class="col-lg-6">
          <div class="card" style="padding:10px;">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-map-marker-alt mr-1"></i>Billing Address</h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group"><label>Attention</label>
                    <input type="text" class="form-control bf" id="b_att" name="billing_attention" value="{{ old('billing_attention') }}" placeholder="Attention"></div>
                </div>
                <div class="col-md-6">
                  <div class="form-group"><label>Street</label>
                    <input type="text" class="form-control bf" id="b_str" name="billing_street" value="{{ old('billing_street') }}" placeholder="Street"></div>
                </div>
                <div class="col-md-6">
                  <div class="form-group"><label>City</label>
                    <select class="form-control" id="b_cty" name="billing_city" data-selected="{{ old('billing_city') }}">
                      <option value="">-- Select City --</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group"><label>State</label>
                    <select class="form-control bf" id="b_st" name="billing_state" data-selected="{{ old('billing_state') }}">
                      <option value="">-- Select State --</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group"><label>PIN Code</label>
                    <input type="text" class="form-control bf pin-only" id="b_pin" name="billing_pin_code" value="{{ old('billing_pin_code') }}" placeholder="PIN Code" maxlength="6"></div>
                </div>
                <div class="col-md-6">
                  <div class="form-group"><label>Country</label>
                    <select class="form-control bf" id="b_ctr" name="billing_country">
                      <option value="">-- Select Country --</option>
                      @foreach($countries as $country)
                        <option value="{{ $country->name }}" data-id="{{ $country->id }}" {{ old('billing_country','India')==$country->name? 'selected' : '' }}>{{ $country->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group"><label>GST Number</label>
                    <input type="text" class="form-control bf upper" id="b_gst" name="billing_gst_number" value="{{ old('billing_gst_number') }}" placeholder="GST Number"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="card" style="padding:10px;">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-shipping-fast mr-1"></i>Shipping Address</h3>
              <div class="card-tools d-flex align-items-center">
                <div class="custom-control custom-switch mr-3">
                  <input type="checkbox" class="custom-control-input" id="same_as" name="same_as" value="1" {{ old('same_as')?'checked':'' }}>
                  <label class="custom-control-label font-weight-normal" for="same_as">Same as Billing</label>
                </div>
              </div>
            </div>
            <div class="card-body" id="shippingFields">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group"><label>Attention</label>
                    <input type="text" class="form-control" id="s_att" name="shipping_attention" value="{{ old('shipping_attention') }}" placeholder="Attention"></div>
                </div>
                <div class="col-md-6">
                  <div class="form-group"><label>Street</label>
                    <input type="text" class="form-control" id="s_str" name="shipping_street" value="{{ old('shipping_street') }}" placeholder="Street"></div>
                </div>
                <div class="col-md-6">
                  <div class="form-group"><label>City</label>
                    <select class="form-control" id="s_cty" name="shipping_city" data-selected="{{ old('shipping_city') }}">
                      <option value="">-- Select City --</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group"><label>State</label>
                    <select class="form-control" id="s_st" name="shipping_state" data-selected="{{ old('shipping_state') }}">
                      <option value="">-- Select State --</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group"><label>PIN Code</label>
                    <input type="text" class="form-control pin-only" id="s_pin" name="shipping_pin_code" value="{{ old('shipping_pin_code') }}" placeholder="PIN Code" maxlength="6"></div>
                </div>
                <div class="col-md-6">
                  <div class="form-group"><label>Country</label>
                    <select class="form-control" id="s_ctr" name="shipping_country">
                      <option value="">-- Select Country --</option>
                      @foreach($countries as $country)
                        <option value="{{ $country->name }}" data-id="{{ $country->id }}" {{ old('shipping_country','India')==$country->name? 'selected' : '' }}>{{ $country->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group"><label>GST Number</label>
                    <input type="text" class="form-control upper" id="s_gst" name="shipping_gst_number" value="{{ old('shipping_gst_number') }}" placeholder="GST Number"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="mt-2 mb-2 text-right">
        <a href="{{ route('customers.index') }}" class="btn-cancel mr-2"><i class="fas fa-times mr-1"></i>Cancel</a>
        <button type="submit" class="btn-submit"><i class="fas fa-save mr-1"></i>Save Customer</button>
      </div>
    </form>
  </div>
</section>
@endsection

@push('scripts')
<script>
$(function () {
  $('.toggle-password').on('click', function () {
    var $inp = $('#' + $(this).data('target'));
    $inp.attr('type', $inp.attr('type') === 'password' ? 'text' : 'password');
    $(this).find('i').toggleClass('fa-eye fa-eye-slash');
  });

  // Data lists from server
  @php
    $statesArr = $states->map(function($s){ return ['id'=>$s->id, 'country_id'=>$s->country_id, 'name'=>$s->name]; })->toArray();
    $citiesArr = $cities->map(function($c){ return ['id'=>$c->id, 'state_id'=>$c->state_id, 'country_id'=>$c->country_id, 'name'=>$c->name]; })->toArray();
  @endphp
  var states = @json($statesArr);
  var cities = @json($citiesArr);

  function populateStates($select, countryId) {
    var selected = $select.data('selected') || '';
    $select.empty().append('<option value="">-- Select State --</option>');
    states.forEach(function(s){ if(s.country_id == countryId){ var sel = (s.name==selected)?' selected':''; $select.append('<option value="'+s.name+'" data-id="'+s.id+'" data-country-id="'+s.country_id+'"'+sel+'>'+s.name+'</option>'); } });
  }
  function populateCities($select, stateId) {
    var selected = $select.data('selected') || '';
    $select.empty().append('<option value="">-- Select City --</option>');
    cities.forEach(function(c){ if(c.state_id == stateId){ var sel = (c.name==selected)?' selected':''; $select.append('<option value="'+c.name+'" data-id="'+c.id+'" data-state-id="'+c.state_id+'" data-country-id="'+c.country_id+'"'+sel+'>'+c.name+'</option>'); } });
  }

  // Initialize billing and shipping selects
  ['b','s'].forEach(function(prefix){
    var $country = $('#'+prefix+'_ctr');
    var $state = $('#'+prefix+'_st');
    var $city = $('#'+prefix+'_cty');
    var cid = $country.find(':selected').data('id') || null;
    if(cid) populateStates($state, cid);
    var preState = $state.data('selected') || '';
    if(preState) {
      // find id for preState
      var obj = states.find(function(s){ return s.name==preState && (cid==null || s.country_id==cid); });
      if(obj) populateCities($city, obj.id);
    }

    $country.on('change', function(){
      var newCid = $(this).find(':selected').data('id');
      populateStates($state, newCid);
      $city.empty().append('<option value="">-- Select City --</option>');
    });
    $state.on('change', function(){
      var newSid = $(this).find(':selected').data('id');
      populateCities($city, newSid);
    });
  });

  // Keep sync behaviour for shipping
  $('#same_as').on('change', syncShipping);
  $('.bf').on('input change', function () { if ($('#same_as').is(':checked')) syncShipping(); });

  function syncShipping() {
    if (!$('#same_as').is(':checked')) {
      $('#shippingFields input, #shippingFields select').prop('readonly', false).removeClass('bg-light');
      return;
    }
    var map = {s_att:'b_att', s_str:'b_str', s_cty:'b_cty', s_st:'b_st', s_pin:'b_pin', s_ctr:'b_ctr', s_gst:'b_gst'};
    $.each(map, function(s, b) { $('#'+s).val($('#'+b).val()).trigger('change'); });
    $('#shippingFields input, #shippingFields select').prop('readonly', true).addClass('bg-light');
  }

  $(document).on('input', '.upper', function () { $(this).val($(this).val().toUpperCase()); });
  $(document).on('input', '.pin-only', function () { $(this).val($(this).val().replace(/\D/g,'')); });
});
</script>
@endpush