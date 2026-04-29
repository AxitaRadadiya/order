
@extends('admin.layouts.app')
@section('title', 'Create Role')
@section('style')
<link rel="stylesheet" href="{{ asset('admin/dist/css/custom/user-role/panel-theme.css') }}">
<link rel="stylesheet" href="{{ asset('admin/dist/css/custom/user-role/roles-create.css') }}">
@endsection
@section('content')

<div class="pull-card">
  <div class="container-fluid" style="padding:0;">

    <form action="{{ route('roles.store') }}" method="POST">
      @csrf

      <div class="main-card mb-4">
        <div class="main-card-head" style="justify-content: space-between;">
          <div class="main-card-title"><i class="fas fa-tag"></i> Role Info</div>
          <a href="{{ route('roles.index') }}" class="btn-cancel mb-1">
            <i class="fas fa-arrow-left mr-1"></i> Back
          </a>
        </div>
        <div class="main-card-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="name" class="font-weight-bold">
                  Role Name <span class="text-danger">*</span>
                </label>
                <input id="name" name="name" type="text"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}"
                       placeholder="e.g. Manager, Editor, Viewer" required>
                @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="main-card">
        <div class="main-card-head" style="justify-content: space-between;">
          <div class="main-card-title">
            <i class="fas fa-key"></i> Permissions
            <span class="count-badge">{{ $permissions->flatten()->count() }} total</span>
          </div>
          <div>
            <button type="button" class="btn-submit mr-1 mb-1" onclick="selectAll(true)">
              <i class="fas fa-check-double mr-1"></i> Select All
            </button>
            <button type="button" class="btn-cancel mb-1" onclick="selectAll(false)">
              <i class="fas fa-times mr-1"></i> Clear All
            </button>
          </div>
        </div>
        <div class="main-card-body">
          @if($permissions->isNotEmpty())
            <div class="row">
              @foreach($permissions as $group => $perms)
                <div class="col-md-4 col-sm-6 mb-4">
                  <div class="permission-card">
                    <div class="permission-card-head">
                      <h6 class="mb-0 d-flex align-items-center justify-content-between w-100">
                        <span>
                          <i class="fas fa-layer-group mr-1" style="color:#008d8d;"></i>
                          {{ $group }}
                          <span class="permission-chip ml-1">{{ $perms->count() }}</span>
                        </span>
                        <button type="button" class="btn-outline-primary group-toggle-btn"
                                onclick="toggleGroup(this)">All</button>
                      </h6>
                    </div>
                    <div class="permission-card-body">
                      @foreach($perms as $perm)
                        <div class="icheck-primary mb-1">
                          <input class="perm-chk" type="checkbox"
                                 name="permissions[]" value="{{ $perm->id }}"
                                 id="perm_{{ $perm->id }}"
                                 {{ in_array($perm->id, old('permissions', [])) ? 'checked' : '' }}>
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
              <i class="fas fa-key fa-2x mb-2 d-block"></i>
              No permissions found. Create permissions first.
            </div>
          @endif
        </div>
        <div class="mt-2 d-flex justify-content-end">
          <button type="submit" class="btn-submit">
            <i class="fas fa-shield-alt mr-1"></i> Create Role
          </button>
          <a href="{{ route('roles.index') }}" class="btn-cancel ml-2">
            <i class="fas fa-times mr-1"></i> Cancel
          </a>
        </div>
      </div>

    </form>
  </div>
</div>

@endsection
