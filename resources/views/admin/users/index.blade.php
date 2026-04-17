@extends('admin.layouts.app')
@section('title', 'Users')

@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">User Management</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Users</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xl-12">

    {{-- Tabs (Same as Role Page) --}}
    <ul class="nav nav-tabs mb-3" style="border-bottom: none;">
        <li class="nav-item">
            <a href="{{ route('users.index') }}" class="nav-link active">
                <span class="font-weight-bold">User</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('roles.index') }}" class="nav-link">
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
                <h5 class="mb-0">All Users</h5>
                <div class="ml-auto">
                    <a href="{{ route('users.create') }}" class="btn-create">
                        + New User
                    </a>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table id="userTable" class="table table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
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