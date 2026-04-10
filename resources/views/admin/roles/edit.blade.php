@extends('admin.layouts.app')
@section('title', 'Edit Role')
@section('style')
<link rel="stylesheet" href="{{ asset('admin/dist/css/custom/user-role/panel-theme.css') }}">
@endsection
@section('content')

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

<div class="pull-card">
  <div class="container-fluid" style="padding:0;">
    <form action="{{ route('roles.update', $role->id) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="main-card mb-4">
        <div class="main-card-head">
          <div class="main-card-title">
            <i class="fas fa-tag"></i> Role Info
            <span class="count-badge">{{ $role->name }}</span>
          </div>
          <a href="{{ route('roles.index') }}" class="btn-theme-outline">
            <i class="fas fa-arrow-left"></i> Back
          </a>
        </div>
        <div class="main-card-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group mb-0">
                <label for="name" class="font-weight-bold">
                  Role Name <span class="text-danger">*</span>
                </label>
                <input id="name" name="name" type="text"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $role->name) }}" required>
                @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="main-card">
        <div class="main-card-head">
          <div class="main-card-title">
            <i class="fas fa-key"></i> Permissions
            <span class="count-badge">{{ $permissions->flatten()->count() }} total</span>
            <span class="permission-chip" id="assignedBadge">{{ count($assignedIds) }} assigned</span>
          </div>
          <div>
            <button type="button" class="btn-theme mr-1" onclick="selectAll(true)">
              <i class="fas fa-check-double"></i> Select All
            </button>
            <button type="button" class="btn-theme-outline" onclick="selectAll(false)">
              <i class="fas fa-times"></i> Clear All
            </button>
          </div>
        </div>
        <div class="main-card-body">
          @if($permissions->isNotEmpty())
            <div class="row">
              @foreach($permissions as $group => $perms)
                @php $groupAssigned = $perms->whereIn('id', $assignedIds)->count(); @endphp
                <div class="col-md-4 col-sm-6 mb-4">
                  <div class="permission-card">
                    <div class="permission-card-head">
                      <h6 class="mb-0 d-flex align-items-center justify-content-between w-100">
                        <span>
                          <i class="fas fa-layer-group mr-1" style="color:#008d8d;"></i>
                          {{ $group }}
                          <span class="permission-chip ml-1">{{ $perms->count() }}</span>
                          @if($groupAssigned > 0)
                            <span class="permission-chip ml-1">{{ $groupAssigned }} assigned</span>
                          @endif
                        </span>
                        <button type="button" class="btn-theme-outline group-toggle-btn"
                                onclick="toggleGroup(this)">
                          {{ $groupAssigned === $perms->count() ? 'None' : 'All' }}
                        </button>
                      </h6>
                    </div>
                    <div class="permission-card-body">
                      @foreach($perms as $perm)
                        <div class="icheck-primary mb-2">
                          <input class="perm-chk" type="checkbox"
                                 name="permissions[]" value="{{ $perm->id }}"
                                 id="perm_{{ $perm->id }}"
                                 {{ in_array($perm->id, old('permissions', $assignedIds)) ? 'checked' : '' }}>
                          <label for="perm_{{ $perm->id }}" style="font-size:.85rem;">
                            {{ $perm->name }}
                          </label>
                        </div>
                      @endforeach
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center text-muted py-4">
              <i class="fas fa-key fa-2x mb-2 d-block"></i>No permissions found.
            </div>
          @endif
        </div>
        <div class="main-card-body pt-0">
          <button type="submit" class="btn-theme">
            <i class="fas fa-save mr-1"></i> Save Changes
          </button>
          <a href="{{ route('roles.index') }}" class="btn-theme-outline ml-2">
            <i class="fas fa-times mr-1"></i> Cancel
          </a>
        </div>
      </div>
    </form>
  </div>
</div>

@endsection
