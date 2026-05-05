@extends('admin.layouts.app')
@section('title', 'Customers')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="mr-2 text-teal"></i>Customers</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Customers</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">

    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle mr-1"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
      </div>
    @endif

    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Customer List</h3>
        <div class="card-tools d-flex align-items-center">
          <a href="{{ route('customers.create') }}" class="btn-create">
            <i class="fas fa-plus mr-1"></i>Add Customer
          </a>
        </div>
      </div>
      <div class="card-body pt-2 pb-3 border-bottom">
        <div class="row">
          <div class="col-md-2">
            <div class="form-group mb-0">
              <label class="font weight bold">Country</label>
              <select id="filter_country" class="form-control form-control-sm">
                <option value="">-- All Countries --</option>
                @foreach($countries as $c)
                  <option value="{{ $c->name }}">{{ $c->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group mb-0">
              <label class="font weight bold">State</label>
              <select id="filter_state" class="form-control form-control-sm">
                <option value="">-- All States --</option>
                @foreach($states as $s)
                  <option value="{{ $s->name }}">{{ $s->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group mb-0">
              <label class="font weight bold">City</label>
              <select id="filter_city" class="form-control form-control-sm">
                <option value="">-- All Cities --</option>
                @foreach($cities as $ct)
                  <option value="{{ $ct->name }}">{{ $ct->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group mb-0">
              <label class="font weight bold">Status</label>
              <select id="filter_status" class="form-control form-control-sm">
                <option value="">-- Any --</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="col-md-3 d-flex align-items-end">
            <div class="form-group mb-0">
              <button id="filter_apply" class="btn-submit mr-1"><i class="fas fa-search mr-1"></i> Filter</button>
              <button id="filter_reset" class="btn-cancel"><i class="fas fa-sync-alt mr-1"></i> Reset</button>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="customerTable" class="table mb-0" style="width:100%">
            <thead class="thead-light">
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Company</th>
                <th>Status</th>
                <th width="140">Action</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</section>
@endsection
