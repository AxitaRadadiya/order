@extends('admin.layouts.app')
@section('title', 'User Details')
@section('style')
<link rel="stylesheet" href="{{ asset('admin/dist/css/custom/user-role/panel-theme.css') }}">
@endsection
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">User Details</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
          <li class="breadcrumb-item active">show</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="pull-card">
  <div class="container-fluid" style="padding:0;">
    <div class="row">
      <div class="col-lg-3 col-md-4 mb-4">
        <div class="profile-panel text-center shadow-sm p-4 bg-white rounded">
          <img src="{{ $user->profile_image_url }}"
               alt="{{ $user->name }}"
               class="profile-avatar mb-3">
          <h4 class="font-weight-bold mb-1">{{ $user->name }}</h4>
          <p class="text-muted mb-2">{{ $user->email }}</p>

          <div class="d-flex justify-content-center mb-3">
            <span class="badge badge-{{ $user->status ? 'success' : 'secondary' }} mr-2">{{ $user->status ? 'Active' : 'Inactive' }}</span>
            <span class="badge badge-{{ $user->is_active ? 'info' : 'warning' }}">{{ $user->is_active ? 'Enabled' : 'Disabled' }}</span>
          </div>
          <div class="d-flex">
            <a href="{{ route('users.edit', $user->id) }}" class="btn-submit mr-2 w-50">
              <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('users.index') }}" class="btn-cancel w-50">
              <i class="fas fa-arrow-left"></i> Back
            </a>
          </div>
        </div>
      </div>

      <div class="col-lg-9 col-md-8">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Profile Details</h5>
            <div class="row mt-3">
              <div class="col-md-6">
                <dl class="row">
                  <dt class="col-sm-4">Name</dt>
                  <dd class="col-sm-8">{{ $user->name }}</dd>

                  <dt class="col-sm-4">Email</dt>
                  <dd class="col-sm-8">{{ $user->email }}</dd>

                  <dt class="col-sm-4">Mobile</dt>
                  <dd class="col-sm-8">{{ $user->mobile ?? '-' }}</dd>

                  <dt class="col-sm-4">Role</dt>
                  <dd class="col-sm-8">{{ $user->role->name ?? '-' }}</dd>
                </dl>
              </div>

              <div class="col-md-6">
                <dl class="row">
                  <dt class="col-sm-4">Status</dt>
                  <dd class="col-sm-8">{{ $user->status ? 'Active' : 'Inactive' }}</dd>

                  <dt class="col-sm-4">Account</dt>
                  <dd class="col-sm-8">{{ $user->is_active ? 'Enabled' : 'Disabled' }}</dd>

                  <dt class="col-sm-4">Created</dt>
                  <dd class="col-sm-8">{{ optional($user->created_at)->format('d M Y, H:i') ?? '-' }}</dd>

                  <dt class="col-sm-4">Last Updated</dt>
                  <dd class="col-sm-8">{{ optional($user->updated_at)->diffForHumans() ?? '-' }}</dd>
                </dl>
              </div>
            </div>

            <hr>

            <h6>Notes</h6>
            <p class="text-muted">{{ $user->note ?: 'No notes available.' }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
