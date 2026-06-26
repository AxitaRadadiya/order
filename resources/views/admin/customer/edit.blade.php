@extends('admin.layouts.app')
@section('title', 'Edit Customer')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Edit Customer</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
          <li class="breadcrumb-item active">Edit</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">

    @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
      </div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
      </div>
    @endif

    @php $addr = $customer->address; $bank = $customer->bankDetail; @endphp

    <form id="customerForm" action="{{ route('customers.update', $customer) }}" method="POST" enctype="multipart/form-data">
      @csrf @method('PUT')

      @if(!empty($isDistributorPanel) && $isDistributorPanel)
        {{-- When distributor edits customer, fix role to retailer and distributor_id to current distributor --}}
        @php
          $retailerRole = $roles->first();
        @endphp
        <input type="hidden" name="role_id" value="{{ $retailerRole?->id }}">
        <input type="hidden" name="distributor_id" value="{{ $currentDistributorId }}">
      @endif

      {{-- 1. Basic Info --}}
      <div class="card card-outline card-primary" style="padding:10px;">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-user mr-1"></i>Basic Information</h3>
        </div>
        <div class="card-body" style="padding:10px;">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label>First Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                       name="first_name" value="{{ old('first_name', $customer->first_name) }}"
                       placeholder="First name" required>
                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Last Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                       name="last_name" value="{{ old('last_name', $customer->last_name) }}"
                       placeholder="Last name" required>
                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Company Name</label>
                <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                       name="company_name" value="{{ old('company_name', $customer->company_name) }}"
                       placeholder="Company name">
                @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Customer Type <span class="text-danger">*</span></label>
                @if(!empty($isDistributorPanel) && $isDistributorPanel)
                  <input type="text" class="form-control" value="Retailer" disabled>
                @else
                  <select id="role_id" name="role_id" class="form-control">
                    <option value="">-- Default (retailer) --</option>
                    @foreach($roles as $r)
                      <option value="{{ $r->id }}" data-name="{{ $r->name }}"
                              @selected(old('role_id', $customer->role_id) == $r->id)>
                        {{ $r->name }}
                      </option>
                    @endforeach
                  </select>
                @endif
              </div>
            </div>
            <div class="col-md-3" id="distributor_field" style="display:none;">
              <div class="form-group">
                <label>Distributor <span class="text-danger">*</span></label>
                @if(!empty($isDistributorPanel) && $isDistributorPanel)
                  <input type="text" class="form-control" value="{{ optional($distributors->first())['company_name'] ?: optional($distributors->first())['name'] }}" disabled>
                @else
                  <select id="distributor_id" name="distributor_id" class="form-control">
                    <option value="">-- Select distributor --</option>
                    @foreach($distributors ?? [] as $d)
                      <option value="{{ $d->id }}" @selected(old('distributor_id', $customer->distributor_id ?? '') == $d->id)>{{ $d->company_name ?: $d->name }}</option>
                    @endforeach
                  </select>
                @endif
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Email <span class="text-danger">*</span></label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                       name="email" value="{{ old('email', $customer->email) }}"
                       placeholder="Email address" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>

            {{-- Mobile: type=tel + inputmode=numeric --}}
            <div class="col-md-3">
              <div class="form-group">
                <label>Mobile Number <span class="text-danger">*</span></label>
                <input id="mobile" type="tel" inputmode="numeric" pattern="[0-9]{10}"
                       class="form-control phone-only @error('mobile') is-invalid @enderror"
                       name="mobile" value="{{ old('mobile', $customer->mobile) }}"
                       placeholder="10-digit mobile number" maxlength="10" required>
                @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>GST Number</label>
                <input type="text" class="form-control" name="gst_number" value="{{ old('gst_number', $customer->gst_number) }}" placeholder="GST Number">
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>PAN Number</label>
                <input type="text" class="form-control" name="pan_number" value="{{ old('pan_number', $customer->pan_number) }}" placeholder="PAN Number">
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Profile Image <span class="text-danger">*</span></label>
                <input type="file" name="profile_image" class="form-control">
                @if(isset($customer) && $customer->profile_image)
                  <div class="image-wrapper mt-2">
                    <img src="{{ asset('storage/' . $customer->profile_image) }}" width="80" class="mt-2 rounded">
                    <button type="button"
                        class="btn-danger btn-sm image-remove-btn"
                        onclick="removeImage('profile_image')">&times;
                    </button>
                    <input type="hidden" id="profile_image_remove" name="profile_image_remove" value="0">
                  </div>
                @endif
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Shop Image</label>
                <input type="file" name="shop_image" class="form-control">
                @if(isset($customer) && $customer->shop_image)
                  <div class="image-wrapper mt-2">
                    <img src="{{ asset('storage/' . $customer->shop_image) }}" width="80" class="mt-2 rounded">
                    <button type="button"
                        class="btn-danger btn-sm image-remove-btn"
                        onclick="removeImage('shop_image')">&times;
                    </button>
                    <input type="hidden" name="shop_image_remove" id="shop_image_remove" value="0">
                  </div>
                @endif
              </div>
            </div>            

            <div class="col-md-3">
              <div class="form-group">
                <label>PAN Card Image</label>
                <input type="file" name="pan_card_image" class="form-control">
                @if(isset($customer) && $customer->pan_card_image)
                  <div class="image-wrapper mt-2">
                    <img src="{{ asset('storage/' . $customer->pan_card_image) }}" width="80" class="mt-2 rounded">
                    <button type="button"
                        class="btn-danger btn-sm image-remove-btn"
                        onclick="removeImage('pan_card_image')">&times;
                    </button>
                    <input type="hidden" name="pan_card_image_remove" id="pan_card_image_remove" value="0">
                  </div>
                @endif
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>GST Certificate Image</label>
                <input type="file" name="gst_certificate_image" class="form-control">
                @if(isset($customer) && $customer->gst_certificate_image)
                  <div class="image-wrapper mt-2">
                    <img src="{{ asset('storage/' . $customer->gst_certificate_image) }}" width="80" class="mt-2 rounded">
                    <button type="button"
                        class="btn-danger btn-sm image-remove-btn"
                        onclick="removeImage('gst_certificate_image')">&times;
                    </button>
                    <input type="hidden" name="gst_certificate_image_remove" id="gst_certificate_image_remove" value="0">
                  </div>
                @endif
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Google Location Link</label>
                <input type="url" class="form-control @error('google_location_link') is-invalid @enderror"
                       name="google_location_link" value="{{ old('google_location_link', $customer->google_location_link) }}">
                @error('google_location_link')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Status <span class="text-danger">*</span></label>
                <select class="form-control" name="status" required>
                  <option value="1" {{ old('status', $customer->status ?? '') == '1' ? 'selected' : '' }}>Active</option>
                  <option value="0" {{ old('status', $customer->status ?? '') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Payment Terms</label>
                <select class="form-control select2" name="payment_terms">
                  <option value="">-- Select --</option>
                  @foreach(['due_on_receipt'=>'Due on Receipt','net_15'=>'Net 15','net_30'=>'Net 30','net_45'=>'Net 45','net_60'=>'Net 60'] as $val=>$lbl)
                    <option value="{{ $val }}"
                            @selected(old('payment_terms', $customer->payment_terms) == $val)>
                      {{ $lbl }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>

            {{-- Place of Supply — restored from create form --}}
            <div class="col-md-3">
              <div class="form-group">
                <label>Place of Supply</label>
                <select class="form-control select2" name="place_of_supply">
                  <option value="">-- Select State --</option>
                  @foreach($states->pluck('name')->unique()->sort()->values() as $stateName)
                    <option value="{{ $stateName }}"
                            @selected(old('place_of_supply', $customer->place_of_supply) == $stateName)>
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
          <div class="card card-outline card-primary h-100">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-map-marker-alt mr-1"></i>Billing Address</h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group"><label>Attention</label>
                    <input type="text" class="form-control bf" id="b_att"
                           name="billing_attention"
                           value="{{ old('billing_attention', $addr->billing_attention ?? '') }}"
                           placeholder="Attention">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group"><label>Street</label>
                    <input type="text" class="form-control bf" id="b_str"
                           name="billing_street"
                           value="{{ old('billing_street', $addr->billing_street ?? '') }}"
                           placeholder="Street">
                  </div>
                </div>

                {{-- Country first --}}
                <div class="col-md-6">
                  <div class="form-group"><label>Country</label>
                    <select class="form-control bf" id="b_ctr" name="billing_country">
                      <option value="">-- Select Country --</option>
                      @foreach($countries as $country)
                        <option value="{{ $country->name }}"
                                data-id="{{ $country->id }}"
                                @selected(old('billing_country', $addr->billing_country ?? 'India') == $country->name)>
                          {{ $country->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group"><label>State</label>
                    <select class="form-control bf" id="b_st" name="billing_state" data-selected="{{ old('billing_state', $addr->billing_state ?? '') }}">
                      <option value="">-- Select State --</option>
                      @foreach($states as $state)
                        <option value="{{ $state->name }}"
                                data-id="{{ $state->id }}"
                                @selected(old('billing_state', $addr->billing_state ?? 'Maharashtra') == $state->name)>
                          {{ $state->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group"><label>City</label>
                    <select class="form-control bf" id="b_cty" name="billing_city" data-selected="{{ old('billing_city', $addr->billing_city ?? '') }}">
                      <option value="">-- Select City --</option>
                      @foreach($cities as $city)
                        <option value="{{ $city->name }}"
                                data-id="{{ $city->id }}"
                                @selected(old('billing_city', $addr->billing_city ?? 'Mumbai') == $city->name)>
                          {{ $city->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>


                <div class="col-md-6">
                  <div class="form-group"><label>PIN Code</label>
                    <input type="tel" inputmode="numeric" class="form-control bf pin-only" id="b_pin"
                           name="billing_pin_code"
                           value="{{ old('billing_pin_code', $addr->billing_pin_code ?? '') }}"
                           placeholder="PIN Code" maxlength="6">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- SHIPPING --}}
        <div class="col-lg-6">
          <div class="card card-outline card-primary h-100">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-shipping-fast mr-1"></i>Shipping Address</h3>
              <div class="card-tools d-flex align-items-center">
                <div class="custom-control custom-switch mr-3">
                  <input type="checkbox" class="custom-control-input" id="same_as"
                         name="same_as" value="1"
                         {{ old('same_as', $addr->same_as ?? false) ? 'checked' : '' }}>
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
                           name="shipping_attention"
                           value="{{ old('shipping_attention', $addr->shipping_attention ?? '') }}"
                           placeholder="Attention">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group"><label>Street</label>
                    <input type="text" class="form-control" id="s_str"
                           name="shipping_street"
                           value="{{ old('shipping_street', $addr->shipping_street ?? '') }}"
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
                                @selected(old('shipping_country', $addr->shipping_country ?? 'India') == $country->name)>
                          {{ $country->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

               <div class="col-md-6">
                  <div class="form-group"><label>State</label>
                    <select class="form-control" id="s_st" name="shipping_state" data-selected="{{ old('shipping_state', $addr->shipping_state ?? '') }}">
                      <option value="">-- Select State --</option>
                      @foreach($states as $state)
                        <option value="{{ $state->name }}"
                                data-id="{{ $state->id }}"
                                @selected(old('shipping_state', $addr->shipping_state ?? '') == $state->name)>
                          {{ $state->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group"><label>City</label>
                    <select class="form-control" id="s_cty" name="shipping_city" data-selected="{{ old('shipping_city', $addr->shipping_city ?? '') }}">
                      <option value="">-- Select City --</option>
                      @foreach($cities as $city)
                        <option value="{{ $city->name }}"
                                data-id="{{ $city->id }}"
                                @selected(old('shipping_city', $addr->shipping_city ?? '') == $city->name)>
                          {{ $city->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group"><label>PIN Code</label>
                    <input type="tel" inputmode="numeric" class="form-control pin-only" id="s_pin"
                           name="shipping_pin_code"
                           value="{{ old('shipping_pin_code', $addr->shipping_pin_code ?? '') }}"
                           placeholder="PIN Code" maxlength="6">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>{{-- /row --}}

      <div class="mt-3 mb-3 mr-3 text-right">
        <a href="{{ route('customers.index') }}" class="btn-cancel mr-2">
          <i class="fas fa-times mr-1"></i>Cancel
        </a>
        <button type="submit" class="btn-submit">
          <i class="fas fa-save mr-1"></i>Update Customer
        </button>
      </div>
    </form>
  </div>
</section>
@endsection

@section('pageScript')
<script>
$(function () {

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
    var cid = $ctr.find(':selected').data('id') || null;

    if (cid) {
      populateStates($st, cid, savedState);
      var sid = getStateId($st);
      if (sid) populateCities($cty, sid, savedCity);
    }

    $ctr.on('change', function () {
      var newCid = $(this).find(':selected').data('id');
      populateStates($st, newCid, '');
      $cty.empty().append('<option value="">-- Select City --</option>');
    });

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

    $('#s_att').val($('#b_att').val());
    $('#s_str').val($('#b_str').val());
    $('#s_pin').val($('#b_pin').val());
    $('#s_gst').val($('#b_gst').val());

    var bCtrVal  = $('#b_ctr').val();
    var bCtrId   = $('#b_ctr').find(':selected').data('id');
    var bStName  = $('#b_st').val();
    var bCtyName = $('#b_cty').val();
    var bStId    = getStateId($('#b_st'));

    $('#s_ctr').val(bCtrVal);
    populateStates($('#s_st'), bCtrId, bStName);
    if (bStId) populateCities($('#s_cty'), bStId, bCtyName);

    $('#shippingFields input, #shippingFields select')
      .prop('readonly', true).addClass('bg-light');
  }

  $('#same_as').on('change', syncShipping);
  $(document).on('input change', '.bf', function () {
    if ($('#same_as').is(':checked')) syncShipping();
  });

  // Lock on load if same_as was saved
  if ($('#same_as').is(':checked')) syncShipping();

  /* ── Input masks ───────────────────────────────────────────────────── */
  $(document).on('input', '.upper',    function () { $(this).val($(this).val().toUpperCase()); });
  $(document).on('input', '.pin-only', function () { $(this).val($(this).val().replace(/\D/g, '')); });
  $(document).on('input', '.phone-only', function () {
    $(this).val($(this).val().replace(/\D/g, '').slice(0, 10));
  });
  $(document).on('keypress', '.phone-only', function (e) {
    if (!/[0-9]/.test(String.fromCharCode(e.which))) e.preventDefault();
  });

  /* ── Client-side validation (required + format) ───────────────── */
  var mobileInput = document.getElementById('mobile');
  var emailInput  = document.getElementById('email');
  var firstNameInput = document.querySelector('input[name="first_name"]');
  var lastNameInput  = document.querySelector('input[name="last_name"]');

  var statusSelect = document.querySelector('select[name="status"]');

  var mobilePattern = /^\d{10}$/;
  var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  var pinPattern = /^\d{1,6}$/;
  var namePattern = /^[A-Za-z][A-Za-z\s.'-]*$/;
  var gstPattern = /^[0-9A-Za-z]{15}$/;
  var panPattern = /^[A-Z]{5}[0-9]{4}[A-Z]$/;
  var googleUrlPattern = /^(https?:\/\/)?([\w-]+\.)+[\w-]{2,}(\/.*)?$/i;

  function showError(msg, el) {
    toastr.error(msg, 'Validation Error');
    if (el && typeof el.focus === 'function') el.focus();
  }

  function validateName(input, label) {
    if (!input) return true;
    var value = String(input.value || '').trim();
    if (!value) {
      showError(label + ' is required.', input);
      return false;
    }
    if (value.length < 2) {
      showError(label + ' must be at least 2 characters.', input);
      return false;
    }
    if (!namePattern.test(value)) {
      showError(label + ' contains invalid characters.', input);
      return false;
    }
    return true;
  }

  function validateMobile() {
    if (!mobileInput) return true;
    var value = mobileInput.value.trim();

    if (!value) {
      showError('Mobile number is required.', mobileInput);
      return false;
    }

    if (!mobilePattern.test(value)) {
      showError('Mobile number must be exactly 10 digits.', mobileInput);
      return false;
    }
    return true;
  }

  function validateEmail() {
    if (!emailInput) return true;
    var value = emailInput.value.trim();

    if (!value) {
      showError('Email is required.', emailInput);
      return false;
    }

    if (!emailPattern.test(value)) {
      showError('Enter a valid email address.', emailInput);
      return false;
    }
    return true;
  }

  function validateOptionalPattern(input, pattern, label) {
    if (!input) return true;
    var raw = String(input.value || '').trim();
    if (!raw) return true;
    if (!pattern.test(raw)) {
      showError(label + ' format is invalid.', input);
      return false;
    }
    return true;
  }

  function validateCustomerTypeRequired() {
    // role_id (customer type) is required when NOT in distributor panel
    // In distributor panel it is fixed via hidden input.
    var roleSelect = document.querySelector('select[name="role_id"]');
    var roleFixed = document.querySelector('input[name="role_id"]');

    if (roleFixed && roleFixed.value) return true;
    if (!roleSelect) return true;

    var val = String(roleSelect.value || '');
    if (!val) {
      showError('Customer Type is required.', roleSelect);
      return false;
    }
    return true;
  }

  function validateStatus() {
    if (!statusSelect) return true;
    var val = String(statusSelect.value || '');
    if (val === '') {
      showError('Status is required.', statusSelect);
      return false;
    }
    return true;
  }

  function validatePinOnSubmit(prefix) {
    var input = document.getElementById(prefix + '_pin');
    if (!input) return true;
    var value = String(input.value || '').trim();
    if (!value) return true;

    var maxLen = input.getAttribute('maxlength') || '6';

    if (!pinPattern.test(value) || value.length !== Number(maxLen)) {
      showError('PIN Code must be exactly ' + maxLen + ' digits.', input);
      return false;
    }
    return true;
  }

  function validateGoogleLocationLink() {
    var input = document.querySelector('input[name="google_location_link"]');
    if (!input) return true;
    var value = String(input.value || '').trim();
    if (!value) return true;
    if (!googleUrlPattern.test(value)) {
      showError('Google Location Link must be a valid URL.', input);
      return false;
    }
    return true;
  }

  function validateShippingFieldsIfNeeded() {
    var sameAs = document.getElementById('same_as');
    if (sameAs && sameAs.checked) return true;
    return validatePinOnSubmit('s');
  }

  // Validate on submit
  $('#customerForm').on('submit', function (e) {
    if (
      !validateName(firstNameInput, 'First Name') ||
      !validateName(lastNameInput, 'Last Name') ||
      !validateEmail() ||
      !validateMobile() ||
      !validateCustomerTypeRequired() ||
      !validateStatus() ||
      !validateOptionalPattern(document.querySelector('input[name="pan_number"]'), panPattern, 'PAN Number') ||
      !validateOptionalPattern(document.querySelector('input[name="gst_number"]'), gstPattern, 'GST Number') ||
      !validateGoogleLocationLink() ||
      !validatePinOnSubmit('b') ||
      !validateShippingFieldsIfNeeded()
    ) {
      e.preventDefault();
      return false;
    }
  });
});

function removeImage(type) {
  if (confirm('Are you sure you want to remove this image?')) {
    // call ajax or set hidden input
    document.getElementById(type + '_remove').value = 1;

    if (type === 'profile_image') {
        document.querySelector('input[name="profile_image"]')
            .setAttribute('required', true);
    }

      // Hide image wrapper
    document.getElementById(type + '_remove')
        .closest('.image-wrapper')
        .style.display = 'none';
  }
}
</script>
@endsection