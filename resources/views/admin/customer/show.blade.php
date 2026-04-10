@extends('admin.layouts.app')
@section('title', 'Customer Details')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="fas fa-user mr-2 text-teal"></i>Customer Details</h1>
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

    <div class="mb-3 d-flex justify-content-between">
      <a href="{{ route('customers.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i>Back</a>
      <div>
        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning btn-sm mr-1"><i class="fas fa-edit mr-1"></i>Edit</a>
        <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $customer->id }}" data-name="{{ $customer->name }}">
          <i class="fas fa-trash mr-1"></i>Delete
        </button>
      </div>
    </div>

    <div class="row">
      {{-- Basic Info --}}
      <div class="col-md-6">
        <div class="card card-outline card-primary">
          <div class="card-header"><h3 class="card-title"><i class="fas fa-user mr-1"></i>Basic Information</h3></div>
          <div class="card-body p-0">
            <table class="table table-sm table-borderless mb-0">
              <tr><th width="40%" class="pl-3 text-muted">Name</th><td>{{ $customer->name }}</td></tr>
              <tr class="bg-light"><th class="pl-3 text-muted">Company</th><td>{{ $customer->company_name ?? '-' }}</td></tr>
              <tr><th class="pl-3 text-muted">Email</th><td><a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a></td></tr>
              <tr class="bg-light"><th class="pl-3 text-muted">Phone</th><td>{{ $customer->phone ?? '-' }}</td></tr>
              <tr><th class="pl-3 text-muted">Website</th><td>
                @if($customer->website)<a href="{{ $customer->website }}" target="_blank">{{ $customer->website }}</a>@else-@endif
              </td></tr>
              <tr class="bg-light"><th class="pl-3 text-muted">Payment Terms</th>
                <td>{{ $customer->payment_terms ? str_replace('_',' ', ucfirst($customer->payment_terms)) : '-' }}</td></tr>
            </table>
          </div>
        </div>
      </div>

      {{-- Tax & Financial --}}
      <div class="col-md-6">
        <div class="card card-outline card-success">
          <div class="card-header"><h3 class="card-title"><i class="fas fa-rupee-sign mr-1"></i>Tax & Financial Details</h3></div>
          <div class="card-body p-0">
            <table class="table table-sm table-borderless mb-0">
              <tr><th width="40%" class="pl-3 text-muted">GST Treatment</th>
                <td>{{ $customer->gst_treatment ? str_replace('_',' ', ucfirst($customer->gst_treatment)) : '-' }}</td></tr>
              <tr class="bg-light"><th class="pl-3 text-muted">GST Number</th><td>{{ $customer->gst_number ?? '-' }}</td></tr>
              <tr><th class="pl-3 text-muted">PAN Number</th><td>{{ $customer->pan_number ?? '-' }}</td></tr>
              <tr class="bg-light"><th class="pl-3 text-muted">Place of Supply</th><td>{{ $customer->place_of_supply ?? '-' }}</td></tr>
              <tr><th class="pl-3 text-muted">Discount</th><td>{{ $customer->discount ? $customer->discount.'%' : '-' }}</td></tr>
              <tr class="bg-light"><th class="pl-3 text-muted">Credit Limit</th>
                <td>{{ $customer->credit_limit ? '₹'.number_format($customer->credit_limit,2) : '-' }}</td></tr>
            </table>
          </div>
        </div>
      </div>

      {{-- Billing Address (customer_addresses) --}}
      <div class="col-md-6">
        <div class="card card-outline card-warning">
          <div class="card-header"><h3 class="card-title"><i class="fas fa-map-marker-alt mr-1"></i>Billing Address</h3></div>
          <div class="card-body p-0">
            <table class="table table-sm table-borderless mb-0">
              <tr><th width="40%" class="pl-3 text-muted">Attention</th><td>{{ $addr->billing_attention ?? '-' }}</td></tr>
              <tr class="bg-light"><th class="pl-3 text-muted">Street</th><td>{{ $addr->billing_street ?? '-' }}</td></tr>
              <tr><th class="pl-3 text-muted">City</th><td>{{ $addr->billing_city ?? '-' }}</td></tr>
              <tr class="bg-light"><th class="pl-3 text-muted">State</th><td>{{ $addr->billing_state ?? '-' }}</td></tr>
              <tr><th class="pl-3 text-muted">PIN Code</th><td>{{ $addr->billing_pin_code ?? '-' }}</td></tr>
              <tr class="bg-light"><th class="pl-3 text-muted">Country</th><td>{{ $addr->billing_country ?? '-' }}</td></tr>
              <tr><th class="pl-3 text-muted">GST Number</th><td>{{ $addr->billing_gst_number ?? '-' }}</td></tr>
            </table>
          </div>
        </div>
      </div>

      {{-- Shipping Address (customer_addresses) --}}
      <div class="col-md-6">
        <div class="card card-outline card-info">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-shipping-fast mr-1"></i>Shipping Address
              @if($addr && $addr->same_as)
                <span class="badge badge-info ml-2">Same as Billing</span>
              @endif
            </h3>
          </div>
          <div class="card-body p-0">
            <table class="table table-sm table-borderless mb-0">
              <tr><th width="40%" class="pl-3 text-muted">Attention</th><td>{{ $addr->shipping_attention ?? '-' }}</td></tr>
              <tr class="bg-light"><th class="pl-3 text-muted">Street</th><td>{{ $addr->shipping_street ?? '-' }}</td></tr>
              <tr><th class="pl-3 text-muted">City</th><td>{{ $addr->shipping_city ?? '-' }}</td></tr>
              <tr class="bg-light"><th class="pl-3 text-muted">State</th><td>{{ $addr->shipping_state ?? '-' }}</td></tr>
              <tr><th class="pl-3 text-muted">PIN Code</th><td>{{ $addr->shipping_pin_code ?? '-' }}</td></tr>
              <tr class="bg-light"><th class="pl-3 text-muted">Country</th><td>{{ $addr->shipping_country ?? '-' }}</td></tr>
              <tr><th class="pl-3 text-muted">GST Number</th><td>{{ $addr->shipping_gst_number ?? '-' }}</td></tr>
            </table>
          </div>
        </div>
      </div>

      {{-- Bank Details (customer_bank_details) --}}
      <div class="col-md-6">
        <div class="card card-outline card-secondary">
          <div class="card-header"><h3 class="card-title"><i class="fas fa-university mr-1"></i>Bank Details</h3></div>
          <div class="card-body p-0">
            <table class="table table-sm table-borderless mb-0">
              <tr><th width="40%" class="pl-3 text-muted">Bank Name</th><td>{{ $bank->bank_name ?? '-' }}</td></tr>
              <tr class="bg-light"><th class="pl-3 text-muted">Account No.</th><td>{{ $bank->account_no ?? '-' }}</td></tr>
              <tr><th class="pl-3 text-muted">IFSC Code</th><td>{{ $bank->ifsc_code ?? '-' }}</td></tr>
              <tr class="bg-light"><th class="pl-3 text-muted">Branch Name</th><td>{{ $bank->branch_name ?? '-' }}</td></tr>
            </table>
          </div>
        </div>
      </div>

      {{-- Record Info --}}
      <div class="col-md-6">
        <div class="card card-outline card-dark">
          <div class="card-header"><h3 class="card-title"><i class="fas fa-clock mr-1"></i>Record Info</h3></div>
          <div class="card-body p-0">
            <table class="table table-sm table-borderless mb-0">
              <tr><th width="40%" class="pl-3 text-muted">Customer ID</th><td>#{{ $customer->id }}</td></tr>
              <tr class="bg-light"><th class="pl-3 text-muted">Created At</th><td>{{ $customer->created_at->format('d M Y, h:i A') }}</td></tr>
              <tr><th class="pl-3 text-muted">Updated At</th><td>{{ $customer->updated_at->format('d M Y, h:i A') }}</td></tr>
            </table>
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