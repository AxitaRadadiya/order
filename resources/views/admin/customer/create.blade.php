@extends('admin.layouts.app')
@section('title', 'Create Customer')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Create Customer</h1>
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
      <div class="card card-outline card-primary" style="padding:10px;">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-user mr-1"></i>Basic Information</h3>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label>Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror"
                       name="name" value="{{ old('name') }}" placeholder="Full name" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Company Name</label>
                <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                       name="company_name" value="{{ old('company_name') }}" placeholder="Company name">
                @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Customer Type</label>
                <select name="role_id" class="form-control">
                  <option value="">-- Default (retailer) --</option>
                  @foreach($roles as $r)
                    <option value="{{ $r->id }}" @selected(old('role_id') == $r->id)>{{ $r->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                       name="email" value="{{ old('email') }}" placeholder="Email address" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>

            {{-- Mobile: type=tel + inputmode=numeric forces numeric keyboard on mobile --}}
            <div class="col-md-3">
              <div class="form-group">
                <label>Mobile Number</label>
                <input type="tel" inputmode="numeric" pattern="[0-9]{10}"
                       class="form-control phone-only @error('mobile') is-invalid @enderror"
                       name="mobile" value="{{ old('mobile') }}"
                       placeholder="10-digit mobile number" maxlength="10">
                @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Password <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input type="password" class="form-control @error('password') is-invalid @enderror"
                         id="password" name="password" placeholder="Password" required>
                  <div class="input-group-append">
                    <button class="btn btn-outline-secondary toggle-password" type="button"
                            data-target="password"><i class="fas fa-eye"></i></button>
                  </div>
                  @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Confirm Password <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                         id="password_confirmation" name="password_confirmation"
                         placeholder="Confirm Password" required>
                  <div class="input-group-append">
                    <button class="btn btn-outline-secondary toggle-password" type="button"
                            data-target="password_confirmation"><i class="fas fa-eye"></i></button>
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
                  @foreach(['due_on_receipt'=>'Due on Receipt','net_15'=>'Net 15','net_30'=>'Net 30','net_45'=>'Net 45','net_60'=>'Net 60'] as $val=>$lbl)
                    <option value="{{ $val }}" @selected(old('payment_terms') == $val)>{{ $lbl }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            {{-- Place of Supply — state list from DB --}}
            <div class="col-md-3">
              <div class="form-group">
                <label>Place of Supply</label>
                <select class="form-control" name="place_of_supply">
                  <option value="">-- Select State --</option>
                  @foreach($states->pluck('name')->unique()->sort()->values() as $stateName)
                    <option value="{{ $stateName }}" @selected(old('place_of_supply') == $stateName)>
                      {{ $stateName }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Billing + Shipping Address --}}
      <div class="row">

        {{-- BILLING --}}
        <div class="col-lg-6">
          <div class="card card-outline card-primary" style="padding:10px;">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-map-marker-alt mr-1"></i>Billing Address</h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group"><label>Attention</label>
                    <input type="text" class="form-control bf" id="b_att"
                           name="billing_attention" value="{{ old('billing_attention') }}"
                           placeholder="Attention">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group"><label>Street</label>
                    <input type="text" class="form-control bf" id="b_str"
                           name="billing_street" value="{{ old('billing_street') }}"
                           placeholder="Street">
                  </div>
                </div>

                {{-- Country first so data-id is available when JS runs --}}
                <div class="col-md-6">
                  <div class="form-group"><label>Country</label>
                    <select class="form-control bf" id="b_ctr" name="billing_country">
                      <option value="">-- Select Country --</option>
                      @foreach($countries as $country)
                        <option value="{{ $country->name }}"
                                data-id="{{ $country->id }}"
                                @selected(old('billing_country', 'India') == $country->name)>
                          {{ $country->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

              <div class="col-md-6">
                  <div class="form-group"><label>State</label>
                    <select class="form-control bf" id="b_st" name="billing_state">
                      <option value="">-- Select State --</option>
                      @foreach($states as $state)
                        <option value="{{ $state->name }}"
                                data-id="{{ $state->id }}"
                                @selected(old('billing_state', 'Maharashtra') == $state->name)>
                          {{ $state->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group"><label>City</label>
                    <select class="form-control bf" id="b_ctr" name="billing_city">
                      <option value="">-- Select City --</option>
                      @foreach($cities as $city)
                        <option value="{{ $city->name }}"
                                data-id="{{ $city->id }}"
                                @selected(old('billing_city', 'India') == $city->name)>
                          {{ $city->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group"><label>PIN Code</label>
                    <input type="tel" inputmode="numeric" class="form-control bf pin-only" id="b_pin"
                           name="billing_pin_code" value="{{ old('billing_pin_code') }}"
                           placeholder="PIN Code" maxlength="6">
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-group"><label>GST Number</label>
                    <input type="text" class="form-control bf upper" id="b_gst"
                           name="billing_gst_number" value="{{ old('billing_gst_number') }}"
                           placeholder="GST Number">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- SHIPPING --}}
        <div class="col-lg-6">
          <div class="card card-outline card-primary" style="padding:10px;">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-shipping-fast mr-1"></i>Shipping Address</h3>
              <div class="card-tools d-flex align-items-center">
                <div class="custom-control custom-switch mr-3">
                  <input type="checkbox" class="custom-control-input" id="same_as"
                         name="same_as" value="1" {{ old('same_as') ? 'checked' : '' }}>
                  <label class="custom-control-label font-weight-normal" for="same_as">
                    Same as Billing
                  </label>
                </div>
              </div>
            </div>
            <div class="card-body" id="shippingFields">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group"><label>Attention</label>
                    <input type="text" class="form-control" id="s_att"
                           name="shipping_attention" value="{{ old('shipping_attention') }}"
                           placeholder="Attention">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group"><label>Street</label>
                    <input type="text" class="form-control" id="s_str"
                           name="shipping_street" value="{{ old('shipping_street') }}"
                           placeholder="Street">
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group"><label>Country</label>
                    <select class="form-control" id="s_ctr" name="shipping_country">
                      <option value="">-- Select Country --</option>
                      @foreach($countries as $country)
                        <option value="{{ $country->name }}"
                                data-id="{{ $country->id }}"
                                @selected(old('shipping_country', 'India') == $country->name)>
                          {{ $country->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

              <div class="col-md-6">
                  <div class="form-group"><label>State</label>
                    <select class="form-control bf" id="b_st" name="shipping_state">
                      <option value="">-- Select State --</option>
                      @foreach($states as $state)
                        <option value="{{ $state->name }}"
                                data-id="{{ $state->id }}"
                                @selected(old('shipping_state', 'Maharashtra') == $state->name)>
                          {{ $state->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group"><label>City</label>
                    <select class="form-control bf" id="b_ctr" name="shipping_city">
                      <option value="">-- Select City --</option>
                      @foreach($cities as $city)
                        <option value="{{ $city->name }}"
                                data-id="{{ $city->id }}"
                                @selected(old('shipping_city', 'India') == $city->name)>
                          {{ $city->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group"><label>PIN Code</label>
                    <input type="tel" inputmode="numeric" class="form-control pin-only" id="s_pin"
                           name="shipping_pin_code" value="{{ old('shipping_pin_code') }}"
                           placeholder="PIN Code" maxlength="6">
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-group"><label>GST Number</label>
                    <input type="text" class="form-control upper" id="s_gst"
                           name="shipping_gst_number" value="{{ old('shipping_gst_number') }}"
                           placeholder="GST Number">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>{{-- /row --}}

      <div class="mt-2 mb-2 mr-3 text-right">
        <a href="{{ route('customers.index') }}" class="btn-cancel mr-2">
          <i class="fas fa-times mr-1"></i>Cancel
        </a>
        <button type="submit" class="btn-submit">
          <i class="fas fa-save mr-1"></i>Save Customer
        </button>
      </div>
    </form>
  </div>
</section>
@endsection

@push('scripts')
<script>
$(function () {

  /* ── Password toggle ───────────────────────────────────────────────── */
  $('.toggle-password').on('click', function () {
    var $inp = $('#' + $(this).data('target'));
    $inp.attr('type', $inp.attr('type') === 'password' ? 'text' : 'password');
    $(this).find('i').toggleClass('fa-eye fa-eye-slash');
  });

  /* ── Data injected from PHP ────────────────────────────────────────── */
  @php
    $statesArr = $states->map(fn($s) => ['id' => $s->id, 'country_id' => $s->country_id, 'name' => $s->name])->values()->toArray();
    $citiesArr = $cities->map(fn($c) => ['id' => $c->id, 'state_id'   => $c->state_id,   'name' => $c->name])->values()->toArray();
  @endphp
  var STATES = @json($statesArr);
  var CITIES = @json($citiesArr);

  /* ── Helpers ───────────────────────────────────────────────────────── */
  function populateStates($sel, countryId, selectedName) {
    $sel.empty().append('<option value="">-- Select State --</option>');
    STATES.forEach(function (s) {
      if (String(s.country_id) === String(countryId)) {
        $sel.append(
          $('<option>', { value: s.name, 'data-id': s.id, 'data-country-id': s.country_id, text: s.name })
            .prop('selected', s.name === selectedName)
        );
      }
    });
  }

  function populateCities($sel, stateId, selectedName) {
    $sel.empty().append('<option value="">-- Select City --</option>');
    CITIES.forEach(function (c) {
      if (String(c.state_id) === String(stateId)) {
        $sel.append(
          $('<option>', { value: c.name, 'data-id': c.id, text: c.name })
            .prop('selected', c.name === selectedName)
        );
      }
    });
  }

  function getStateId($stateSelect) {
    return $stateSelect.find(':selected').data('id') || null;
  }

  /* ── Init both billing and shipping on page load ───────────────────── */
  ['b', 's'].forEach(function (p) {
    var $ctr = $('#' + p + '_ctr');
    var $st  = $('#' + p + '_st');
    var $cty = $('#' + p + '_cty');

    var savedState = $st.data('selected')  || '';
    var savedCity  = $cty.data('selected') || '';

    // Get country id from the pre-selected country option
    var cid = $ctr.find(':selected').data('id') || null;

    if (cid) {
      populateStates($st, cid, savedState);

      // After states are populated, get the state id for the saved state
      var sid = getStateId($st);
      if (sid) populateCities($cty, sid, savedCity);
    }

    /* Country change → repopulate states, clear cities */
    $ctr.on('change', function () {
      var newCid = $(this).find(':selected').data('id');
      populateStates($st, newCid, '');
      $cty.empty().append('<option value="">-- Select City --</option>');
    });

    /* State change → repopulate cities */
    $st.on('change', function () {
      var newSid = $(this).find(':selected').data('id');
      populateCities($cty, newSid, '');
    });
  });

  /* ── "Same as Billing" ─────────────────────────────────────────────── */
  function syncShipping() {
    if (!$('#same_as').is(':checked')) {
      $('#shippingFields input, #shippingFields select')
        .prop('readonly', false).prop('disabled', false).removeClass('bg-light');
      return;
    }

    // Copy simple text/PIN fields
    $('#s_att').val($('#b_att').val());
    $('#s_str').val($('#b_str').val());
    $('#s_pin').val($('#b_pin').val());
    $('#s_gst').val($('#b_gst').val());

    // Copy country and re-trigger cascade
    var bCtrVal = $('#b_ctr').val();
    var bCtrId  = $('#b_ctr').find(':selected').data('id');
    $('#s_ctr').val(bCtrVal);

    var bStName = $('#b_st').val();
    populateStates($('#s_st'), bCtrId, bStName);

    var bStId  = getStateId($('#b_st'));
    var bCtyName = $('#b_cty').val();
    if (bStId) populateCities($('#s_cty'), bStId, bCtyName);

    $('#shippingFields input, #shippingFields select')
      .prop('readonly', true).addClass('bg-light');
  }

  $('#same_as').on('change', syncShipping);
  // Re-sync when any billing field changes
  $(document).on('input change', '.bf', function () {
    if ($('#same_as').is(':checked')) syncShipping();
  });

  // Run on load if same_as is pre-checked (old() on validation fail)
  if ($('#same_as').is(':checked')) syncShipping();

  /* ── Input masks ───────────────────────────────────────────────────── */
  // Uppercase GST
  $(document).on('input', '.upper',    function () { $(this).val($(this).val().toUpperCase()); });
  // Numeric PIN only
  $(document).on('input', '.pin-only', function () { $(this).val($(this).val().replace(/\D/g, '')); });
  // Numeric phone, max 10 digits
  $(document).on('input', '.phone-only', function () {
    $(this).val($(this).val().replace(/\D/g, '').slice(0, 10));
  });
  // Block non-numeric keypress on phone
  $(document).on('keypress', '.phone-only', function (e) {
    if (!/[0-9]/.test(String.fromCharCode(e.which))) e.preventDefault();
  });

});
</script>
@endpush