@extends('admin.layouts.app')
@section('title', 'Orders')

@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Orders</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Orders</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="card card-default">
    <div class="card-header">
      <h3 class="card-title">All Orders</h3>
      <div class="card-tools d-flex align-items-center">
        @if(auth()->user() && auth()->user()->hasRole('super-admin'))
        <a href="{{ route('orders.create') }}" class="btn-create">
          <i class="fas fa-plus"></i> New Order
        </a>
        @endif
      </div>
    </div>
    <div class="card-body">
      <div class="row mb-3">
        <div class="col-md-3">
          <div class="form-group">
            <label for="filter_customer_name">Customer name / email</label>
            <input type="text" id="filter_customer_name" class="form-control" placeholder="Search customer">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="filter_status">Order status</label>
            <select id="filter_status" class="form-control">
              <option value="">All statuses</option>
              <option value="pending">Pending</option>
              <option value="confirmed">Confirmed</option>
              <option value="shipped">Shipped</option>
              <option value="partial_dispatch">Partial Dispatch</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group">
            <label for="filter_date_from">Date from</label>
            <input type="date" id="filter_date_from" class="form-control">
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group">
            <label for="filter_date_to">Date to</label>
            <input type="date" id="filter_date_to" class="form-control">
          </div>
        </div>
       <div class="col-md-2 d-flex align-items-end">
    <div class="row w-100">
        <div class="col-6 pr-1">
            <button type="button"
                    id="filter_apply"
                    class="btn btn-create">
                Apply
            </button>
        </div>

        <div class="col-6 pl-1">
            <button type="button"
                    id="filter_reset"
                    class="btn btn-secondary">
                Reset
            </button>
        </div>
    </div>
</div>
      </div>

      <table id="orderTable" class="table table-bordered table-hover table-sm w-100">
        <thead>
          <tr>
            <th>#</th>
            <th>Order No.</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Approval Status</th>
            <th>Grand Total</th>
            <th>Status</th>
            <th style="width:110px">Actions</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>

    </div>
  </div>

</div>
@endsection