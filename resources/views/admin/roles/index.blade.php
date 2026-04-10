@extends('admin.layouts.app')
@section('title', 'Roles')
@section('style')
<link rel="stylesheet" href="{{ asset('admin/dist/css/custom/user-role/roles-index.css') }}">
@endsection

@section('content')

{{-- HERO --}}
<div class="page-hero">
  <div class="orb"></div>
  <div class="container-fluid" style="position:relative;z-index:2;">
    <div class="section-tabs">
      <a href="{{ route('users.index') }}" class="section-tab">
        <i class="fas fa-users"></i>
        <span>User</span>
      </a>
      <a href="{{ route('roles.index') }}" class="section-tab active">
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
        <div class="main-card-title">
          <i class="fas fa-list"></i> All Roles
          <span class="count-badge">{{ $roles->count() }}</span>
        </div>
        <a href="{{ route('roles.create') }}" class="btn-create">
          <i class="fas fa-plus"></i> New Role
        </a>
      </div>
      <div class="main-card-body">
        <div class="table-responsive">
        <table id="roleTable" class="table table-hover w-100">
          <thead>
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
<div style="height:2rem;"></div>
@endsection
