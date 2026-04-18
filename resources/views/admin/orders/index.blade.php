@extends('admin.layouts.app')
@section('title', 'Orders')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="mr-2 text-teal"></i>Orders</h1>
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

<section class="content">
  <div class="container-fluid">

    <!-- @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle mr-1"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
      </div>
    @endif -->

    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">Order List</h3>
        <div class="card-tools d-flex align-items-center">
          <a href="{{ route('orders.create') }}" class="btn-create">
            <i class="fas fa-plus mr-1"></i>Add Order
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="orderTable" class="table table-hover table-bordered mb-0" style="width:100%">
            <thead class="thead-light">
              <tr>
                <th>#</th>
                <th>Order ID</th>
                <th>Client</th>
                <th>Date</th>
                <th>Total</th>
                <th width="140">Action</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</section>
@endsection
