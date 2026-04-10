@extends('admin.layouts.app')
@section('title', 'Create Customer')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="fas fa-user-plus mr-2 text-teal"></i>Create Customer</h1>
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
        <i class="fas fa-exclamation-circle mr-1"></i><strong>Please fix the following errors:</strong>
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
      <div class="card card-outline card-primary">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-user mr-1"></i>Basic Information</h3>
          <div class="card-tools"><button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button></div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Full name" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Company Name</label>
                <input type="text" class="form-control @error('company_name') is-invalid @enderror" name="company_name" value="{{ old('company_name') }}" placeholder="Company name">
                @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Email address" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" class="form-control" name="phone" value="{{ old('phone') }}" placeholder="Phone number">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Website</label>
                <input type="url" class="form-control" name="website" value="{{ old('website') }}" placeholder="https://example.com">
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
                <label>Payment Terms</label>
                <select class="form-control" name="payment_terms">
                  <option value="">-- Select --</option>
                  @foreach(['due_on_receipt'=>'Due on Receipt','net_15'=>'Net 15','net_30'=>'Net 30','net_45'=>'Net 45','net_60'=>'Net 60'] as $val=>$label)
                    <option value="{{ $val }}" {{ old('payment_terms')==$val?'selected':'' }}>{{ $label }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- 2. Tax & Financial --}}
      <div class="card card-outline card-success">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-rupee-sign mr-1"></i>Tax & Financial Details</h3>
          <div class="card-tools"><button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button></div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label>GST Treatment</label>
                <select class="form-control" name="gst_treatment">
                  <option value="">-- Select --</option>
                  @foreach(['registered_business_regular'=>'Registered – Regular','registered_business_composition'=>'Registered – Composition','unregistered_business'=>'Unregistered Business','consumer'=>'Consumer','overseas'=>'Overseas','sez'=>'Special Economic Zone'] as $val=>$label)
                    <option value="{{ $val }}" {{ old('gst_treatment')==$val?'selected':'' }}>{{ $label }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>GST Number</label>
                <input type="text" class="form-control upper" name="gst_number" value="{{ old('gst_number') }}" placeholder="22AAAAA0000A1Z5" maxlength="15">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>PAN Number</label>
                <input type="text" class="form-control upper" name="pan_number" value="{{ old('pan_number') }}" placeholder="ABCDE1234F" maxlength="10">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Place of Supply</label>
                <select class="form-control" name="place_of_supply">
                  <option value="">-- Select State --</option>
                  @foreach(['Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram','Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal','Andaman and Nicobar Islands','Chandigarh','Dadra and Nagar Haveli and Daman and Diu','Delhi','Jammu and Kashmir','Ladakh','Lakshadweep','Puducherry'] as $state)
                    <option value="{{ $state }}" {{ old('place_of_supply')==$state?'selected':'' }}>{{ $state }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Discount (%)</label>
                <input type="number" class="form-control" name="discount" value="{{ old('discount',0) }}" min="0" max="100" step="0.01">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Credit Limit (₹)</label>
                <input type="number" class="form-control" name="credit_limit" value="{{ old('credit_limit',0) }}" min="0" step="0.01">
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- 3. Billing Address --}}
      <div class="card card-outline card-warning">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-map-marker-alt mr-1"></i>Billing Address</h3>
          <div class="card-tools"><button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button></div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group"><label>Attention</label>
                <input type="text" class="form-control bf" id="b_att" name="billing_attention" value="{{ old('billing_attention') }}" placeholder="Attention"></div>
            </div>
            <div class="col-md-4">
              <div class="form-group"><label>Street</label>
                <input type="text" class="form-control bf" id="b_str" name="billing_street" value="{{ old('billing_street') }}" placeholder="Street"></div>
            </div>
            <div class="col-md-4">
              <div class="form-group"><label>City</label>
                <input type="text" class="form-control bf" id="b_cty" name="billing_city" value="{{ old('billing_city') }}" placeholder="City"></div>
            </div>
            <div class="col-md-3">
              <div class="form-group"><label>State</label>
                <input type="text" class="form-control bf" id="b_st" name="billing_state" value="{{ old('billing_state') }}" placeholder="State"></div>
            </div>
            <div class="col-md-3">
              <div class="form-group"><label>PIN Code</label>
                <input type="text" class="form-control bf pin-only" id="b_pin" name="billing_pin_code" value="{{ old('billing_pin_code') }}" placeholder="PIN Code" maxlength="6"></div>
            </div>
            <div class="col-md-3">
              <div class="form-group"><label>Country</label>
                <input type="text" class="form-control bf" id="b_ctr" name="billing_country" value="{{ old('billing_country','India') }}" placeholder="Country"></div>
            </div>
            <div class="col-md-3">
              <div class="form-group"><label>GST Number</label>
                <input type="text" class="form-control bf upper" id="b_gst" name="billing_gst_number" value="{{ old('billing_gst_number') }}" placeholder="GST Number"></div>
            </div>
          </div>
        </div>
      </div>

      {{-- 4. Shipping Address --}}
      <div class="card card-outline card-info">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-shipping-fast mr-1"></i>Shipping Address</h3>
          <div class="card-tools d-flex align-items-center">
            <div class="custom-control custom-switch mr-3">
              <input type="checkbox" class="custom-control-input" id="same_as" name="same_as" value="1" {{ old('same_as')?'checked':'' }}>
              <label class="custom-control-label font-weight-normal" for="same_as">Same as Billing</label>
            </div>
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
          </div>
        </div>
        <div class="card-body" id="shippingFields">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group"><label>Attention</label>
                <input type="text" class="form-control" id="s_att" name="shipping_attention" value="{{ old('shipping_attention') }}" placeholder="Attention"></div>
            </div>
            <div class="col-md-4">
              <div class="form-group"><label>Street</label>
                <input type="text" class="form-control" id="s_str" name="shipping_street" value="{{ old('shipping_street') }}" placeholder="Street"></div>
            </div>
            <div class="col-md-4">
              <div class="form-group"><label>City</label>
                <input type="text" class="form-control" id="s_cty" name="shipping_city" value="{{ old('shipping_city') }}" placeholder="City"></div>
            </div>
            <div class="col-md-3">
              <div class="form-group"><label>State</label>
                <input type="text" class="form-control" id="s_st" name="shipping_state" value="{{ old('shipping_state') }}" placeholder="State"></div>
            </div>
            <div class="col-md-3">
              <div class="form-group"><label>PIN Code</label>
                <input type="text" class="form-control pin-only" id="s_pin" name="shipping_pin_code" value="{{ old('shipping_pin_code') }}" placeholder="PIN Code" maxlength="6"></div>
            </div>
            <div class="col-md-3">
              <div class="form-group"><label>Country</label>
                <input type="text" class="form-control" id="s_ctr" name="shipping_country" value="{{ old('shipping_country','India') }}" placeholder="Country"></div>
            </div>
            <div class="col-md-3">
              <div class="form-group"><label>GST Number</label>
                <input type="text" class="form-control upper" id="s_gst" name="shipping_gst_number" value="{{ old('shipping_gst_number') }}" placeholder="GST Number"></div>
            </div>
          </div>
        </div>
      </div>

      {{-- 5. Bank Details --}}
      <div class="card card-outline card-secondary">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-university mr-1"></i>Bank Details</h3>
          <div class="card-tools"><button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button></div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group"><label>Bank Name</label>
                <input type="text" class="form-control" name="bank_name" value="{{ old('bank_name') }}" placeholder="Bank name"></div>
            </div>
            <div class="col-md-3">
              <div class="form-group"><label>Account Number</label>
                <input type="text" class="form-control" name="account_no" value="{{ old('account_no') }}" placeholder="Account number"></div>
            </div>
            <div class="col-md-3">
              <div class="form-group"><label>IFSC Code</label>
                <input type="text" class="form-control upper" name="ifsc_code" value="{{ old('ifsc_code') }}" placeholder="SBIN0000001" maxlength="11"></div>
            </div>
            <div class="col-md-3">
              <div class="form-group"><label>Branch Name</label>
                <input type="text" class="form-control" name="branch_name" value="{{ old('branch_name') }}" placeholder="Branch name"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-body text-right">
          <a href="{{ route('customers.index') }}" class="btn btn-secondary mr-2"><i class="fas fa-times mr-1"></i>Cancel</a>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Save Customer</button>
        </div>
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

    $('#same_as').on('change', syncShipping);
    $('.bf').on('input', function () { if ($('#same_as').is(':checked')) syncShipping(); });

    function syncShipping() {
        if (!$('#same_as').is(':checked')) {
            $('#shippingFields input').prop('readonly', false).removeClass('bg-light');
            return;
        }
        var map = {s_att:'b_att', s_str:'b_str', s_cty:'b_cty', s_st:'b_st', s_pin:'b_pin', s_ctr:'b_ctr', s_gst:'b_gst'};
        $.each(map, function(s, b) { $('#'+s).val($('#'+b).val()); });
        $('#shippingFields input').prop('readonly', true).addClass('bg-light');
    }

    $(document).on('input', '.upper', function () { $(this).val($(this).val().toUpperCase()); });
    $(document).on('input', '.pin-only', function () { $(this).val($(this).val().replace(/\D/g,'')); });
});
</script>
@endpush