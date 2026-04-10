@extends('admin.layouts.app')
@section('title', 'Customers')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="fas fa-users mr-2 text-teal"></i>Customers</h1>
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

    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">Customer List</h3>
        <div class="card-tools d-flex align-items-center">
          <a href="{{ route('customers.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus mr-1"></i>Add Customer
          </a>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table id="customerTable" class="table table-hover table-bordered mb-0" style="width:100%">
            <thead class="thead-light">
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
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
