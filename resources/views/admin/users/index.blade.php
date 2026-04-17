@extends('admin.layouts.app')
@section('title', 'Users')
@section('style')
<link rel="stylesheet" href="{{ asset('admin/dist/css/custom/user-role/users-index.css') }}">
@endsection

@section('content')

{{-- HERO --}}
<div class="page-hero">
  <div class="orb"></div>
  <div class="container-fluid" style="position:relative;z-index:2;">
    <div class="section-tabs">
      <a href="{{ route('users.index') }}" class="section-tab active">
        <i class="fas fa-users"></i>
        <span>User</span>
      </a>
      <a href="{{ route('roles.index') }}" class="section-tab">
        <i class="fas fa-user-tag"></i>
        <span>Role</span>
      </a>
    </div>
  </div>
</div>

{{-- CARD --}}
<div class="pull-card">
  <div class="container-fluid" style="padding:0;">

    @if(session('success'))
    <div class="alert-success-custom mt-3">
      <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <div class="main-card">
      <div class="main-card-head">
        <!-- <div class="main-card-title">
          <i class="fas fa-list"></i> All Users
          <span class="count-badge">{{ $users->total() }}</span>
        </div> -->
        <a href="{{ route('users.create') }}" class="btn-create">
          <i class="fas fa-plus"></i> New User
        </a>
      </div>
      <div class="main-card-body">
        <div class="table-responsive">
          <table id="userTable" class="table table-hover w-100">
            <thead>
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
<div style="height:2rem;"></div>

@endsection
