@extends('admin.layouts.app')
@section('title', 'Edit User')
@section('style')
<link rel="stylesheet" href="{{ asset('admin/dist/css/custom/user-role/panel-theme.css') }}">
@endsection
@section('content')

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

<div class="pull-card">
  <div class="container-fluid" style="padding:0;">
    <div class="main-card">
      <div class="main-card-head" style="justify-content: space-between;">
        <div class="main-card-title">
          <i class="fas fa-pen"></i>Edit User
          <span class="count-badge">{{ $user->name }}</span>
        </div>
        <a href="{{ route('users.index') }}" class="btn-cancel mb-1">
          <i class="fas fa-arrow-left mr-1"></i> Back
        </a>
      </div>
      <div class="main-card-body">

        @if($errors->any())
          <div class="alert-danger-custom">
            <i class="fas fa-ban mt-1"></i>
            <div>
              <div class="font-weight-bold mb-1">Please fix the following errors.</div>
              <ul class="mb-0 pl-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
          </div>
        @endif

        <form action="{{ route('users.update', $user->id) }}" method="POST" autocomplete="off">
          @csrf
          @method('PUT')

          <div class="form-label-title">
            <i class="fas fa-id-card mr-1"></i> Basic Information
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="font-weight-bold">Name <span class="text-danger">*</span></label>
                <input id="name" name="name" type="text"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $user->name) }}" placeholder="e.g. John Doe" required>
                @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="font-weight-bold">Email Address <span class="text-danger">*</span></label>
                <input id="email" name="email" type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $user->email) }}" placeholder="user@example.com" required>
                @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="font-weight-bold">Role <span class="text-danger">*</span></label>
                <select id="role_id" name="role_id"
                        class="form-control @error('role_id') is-invalid @enderror" required>
                  <option value="">Select Role</option>
                  @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                      {{ $role->name }}
                    </option>
                  @endforeach
                </select>
                @error('role_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="font-weight-bold">Status <span class="text-danger">*</span></label>
                <select id="status" name="status"
                        class="form-control @error('status') is-invalid @enderror" required>
                  <option value="1" {{ old('status', $user->status) == 1 ? 'selected' : '' }}>Active</option>
                  <option value="0" {{ old('status', $user->status) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="font-weight-bold">Password</label>
                <div class="input-group">
                  <input id="password" name="password" type="password"
                         class="form-control @error('password') is-invalid @enderror"
                         placeholder="Leave blank to keep current password">
                  <div class="input-group-append">
                    <button type="button" class="btn btn-outline-secondary"
                            onclick="togglePw('password','eye1')">
                      <i id="eye1" class="fas fa-eye"></i>
                    </button>
                  </div>
                  @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
              </div>
            </div>
          </div>

      </div>
      <div class="main-card-body pt-0">
        <button type="submit" class="btn-submit">
          <i class="fas fa-save mr-1"></i> Save Changes
        </button>
        <a href="{{ route('users.index') }}" class="btn-cancel ml-2">
          <i class="fas fa-times mr-1"></i> Cancel
        </a>
      </div>

        </form>
    </div>
  </div>
</div>

@endsection
