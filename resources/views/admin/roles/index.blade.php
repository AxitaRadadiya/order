@extends('admin.layouts.app')
@section('title', 'Roles')

@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Role Management</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Roles</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xl-12">

    {{-- Tabs (Same as Master Style) --}}
    <ul class="nav nav-tabs mb-3" style="border-bottom: none;">
        <li class="nav-item">
            <a href="{{ route('users.index') }}" class="nav-link">
                <span class="font-weight-bold">User</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('roles.index') }}" class="nav-link active">
                <span class="font-weight-bold">Role</span>
            </a>
        </li>
    </ul>

    {{-- Card --}}
    <div class="card" style="box-shadow: none;">
        <div class="card-body">

            {{-- Success Message --}}
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Header --}}
          <div class="card-header d-flex align-items-center">
                <h5 class="mb-0">All Roles</h5>
                <div class="ml-auto">
                    <a href="{{ route('roles.create') }}" class="btn-create">
          <i class="fas fa-plus"></i> New Role
        </a>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table id="roleTable" class="table table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Role Name</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>
    </div>

  </div>
</div>

@endsection