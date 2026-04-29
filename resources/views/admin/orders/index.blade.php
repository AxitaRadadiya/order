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
 
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
      {{ session('success') }}
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
  @endif
 
  <div class="card card-default">
    <div class="card-header">
      <h3 class="card-title">All Orders</h3>
      <div class="card-tools">
        <a href="{{ route('orders.create') }}" class="btn btn-sm btn-primary">
          <i class="fas fa-plus"></i> New Order
        </a>
      </div>
    </div>
    <div class="card-body">
      <table id="orderTable" class="table table-bordered table-hover table-sm w-100">
        <thead>
          <tr>
            <th>#</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Expected Date</th>
            <th>Grand Total</th>
            <th>Status</th>
            <th style="width:110px">Actions</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
 
</div>
@endsection