@extends('admin.layouts.app')
@section('title', 'Customer Details')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Customer Details</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
          <li class="breadcrumb-item active">Details</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">

    @php $addr = $customer->address; $bank = $customer->bankDetail; @endphp

    <div class="mb-3 mr-3 d-flex justify-content-end">
      <a href="{{ route('customers.index') }}" class="btn-cancel mr-1"><i class="fas fa-arrow-left mr-1"></i>Back</a>
      <a href="{{ route('customers.edit', $customer) }}" class="btn-submit"><i class="fas fa-edit mr-1"></i>Edit</a>
    </div>

    <div class="row">
      {{-- Basic Info full width --}}
      <div class="col-12">
        <div class="card card-outline card-primary mb-4">
          <div class="card-header"><h3 class="card-title"><i class="fas fa-user mr-1"></i>Basic Information</h3></div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-4 mb-2"><strong class="text-muted">Name:</strong> {{ $customer->name }}</div>
              <div class="col-md-4 mb-2"><strong class="text-muted">Company Name:</strong> {{ $customer->company_name ?? '-' }}</div>
              <div class="col-md-4 mb-2"><strong class="text-muted">Email:</strong> <a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a></div>
              <div class="col-md-4 mb-2"><strong class="text-muted">Mobile Number:</strong> {{ $customer->mobile ?? '-' }}</div>
              <div class="col-md-4 mb-2"><strong class="text-muted">Payment Terms:</strong> {{ $customer->payment_terms ? str_replace('_',' ', ucfirst($customer->payment_terms)) : '-' }}</div>
              <div class="col-md-4 mb-2"><strong class="text-muted">Place of Supply:</strong> {{ $customer->place_of_supply ?? '-' }}</div>
            </div>
          </div>
        </div>
      </div>
      {{-- Billing & Shipping Address side by side --}}
      <div class="col-md-6">
        <div class="card card-outline card-primary h-100">
          <div class="card-header"><h3 class="card-title"><i class="fas fa-map-marker-alt mr-1"></i>Billing Address</h3></div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6 mb-2"><strong class="text-muted">Attention:</strong> {{ $addr->billing_attention ?? '-' }}</div>
              <div class="col-md-6 mb-2"><strong class="text-muted">Street:</strong> {{ $addr->billing_street ?? '-' }}</div>
              <div class="col-md-6 mb-2"><strong class="text-muted">City:</strong> {{ $addr->billing_city ?? '-' }}</div>
              <div class="col-md-6 mb-2"><strong class="text-muted">State:</strong> {{ $addr->billing_state ?? '-' }}</div>
              <div class="col-md-6 mb-2"><strong class="text-muted">PIN Code:</strong> {{ $addr->billing_pin_code ?? '-' }}</div>
              <div class="col-md-6 mb-2"><strong class="text-muted">Country:</strong> {{ $addr->billing_country ?? '-' }}</div>
              <div class="col-md-12 mb-2"><strong class="text-muted">GST Number:</strong> {{ $addr->billing_gst_number ?? '-' }}</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card card-outline card-primary h-100">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-shipping-fast mr-1"></i>Shipping Address
              @if($addr && $addr->same_as)
                <span class="badge badge-info ml-2">Same as Billing</span>
              @endif
            </h3>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6 mb-2"><strong class="text-muted">Attention:</strong> {{ $addr->shipping_attention ?? '-' }}</div>
              <div class="col-md-6 mb-2"><strong class="text-muted">Street:</strong> {{ $addr->shipping_street ?? '-' }}</div>
              <div class="col-md-6 mb-2"><strong class="text-muted">City:</strong> {{ $addr->shipping_city ?? '-' }}</div>
              <div class="col-md-6 mb-2"><strong class="text-muted">State:</strong> {{ $addr->shipping_state ?? '-' }}</div>
              <div class="col-md-6 mb-2"><strong class="text-muted">PIN Code:</strong> {{ $addr->shipping_pin_code ?? '-' }}</div>
              <div class="col-md-6 mb-2"><strong class="text-muted">Country:</strong> {{ $addr->shipping_country ?? '-' }}</div>
              <div class="col-md-12 mb-2"><strong class="text-muted">GST Number:</strong> {{ $addr->shipping_gst_number ?? '-' }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title"><i class="fas fa-exclamation-triangle mr-1"></i>Confirm Delete</h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body text-center">
        <p>Delete <strong id="delName"></strong>?</p>
        <small class="text-muted">This action cannot be undone.</small>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
        <form id="deleteForm" method="POST">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-danger btn-sm">Yes, Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    $('.btn-delete').on('click', function () {
        $('#delName').text($(this).data('name'));
        $('#deleteForm').attr('action', '/customers/' + $(this).data('id'));
        $('#deleteModal').modal('show');
    });
});
</script>
@endpush