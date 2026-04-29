@extends('admin.layouts.app')
@section('title', 'Edit Role')

@section('style')
<link rel="stylesheet" href="{{ asset('admin/dist/css/custom/user-role/panel-theme.css') }}">
@endsection

@section('pageScript')
<script>
  function selectAll(state) {
    const form = document.querySelector('form[action="{{ route('roles.update', $role->id) }}"]') || document.querySelector('form');
    if (!form) return;
    const checks = form.querySelectorAll('input.perm-chk:not([disabled])');
    checks.forEach(c => { c.checked = !!state; const td = c.closest('td'); if (td) td.classList.toggle('checked', c.checked); });
    document.querySelectorAll('input.perm-all').forEach(a => a.checked = !!state);
  }

  function toggleRow(checkbox) {
    const row = checkbox.closest('tr');
    if (!row) return;
    const checks = row.querySelectorAll('input.perm-chk:not([disabled])');
    checks.forEach(c => { c.checked = checkbox.checked; const td = c.closest('td'); if (td) td.classList.toggle('checked', c.checked); });
  }

  function toggleGroup(btn) {
    const row = btn ? (btn.closest('tr') || btn.closest('.permission-card')) : null;
    if (!row) return;
    const checks = row.querySelectorAll('input.perm-chk:not([disabled])');
    const anyChecked = Array.from(checks).some(c => c.checked);
    checks.forEach(c => { c.checked = !anyChecked; const td = c.closest('td'); if (td) td.classList.toggle('checked', c.checked); });
    const allBox = row.querySelector('input.perm-all'); if (allBox) allBox.checked = !anyChecked;
  }

  document.addEventListener('change', function(e){
    if (e.target.matches('input.perm-chk')) {
      const td = e.target.closest('td'); if (td) td.classList.toggle('checked', e.target.checked);
      const row = e.target.closest('tr');
      if (row) {
        const checks = row.querySelectorAll('input.perm-chk:not([disabled])');
        const all = checks.length && Array.from(checks).every(c => c.checked);
        const allBox = row.querySelector('input.perm-all'); if (allBox) allBox.checked = !!all;
      }
    }
  });

  document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('table tbody tr').forEach(row => {
      const checks = row.querySelectorAll('input.perm-chk:not([disabled])');
      if (checks.length) {
        const all = Array.from(checks).every(c => c.checked);
        const allBox = row.querySelector('input.perm-all'); if (allBox) allBox.checked = !!all;
      }
    });
  });
</script>
@endsection

@section('content')

<div class="pull-card">
  <div class="container-fluid p-0">

    <form action="{{ route('roles.update', $role->id) }}" method="POST">
      @csrf
      @method('PUT')

      <!-- Role Info -->
      <div class="main-card mb-4">
        <div class="main-card-head d-flex justify-content-between">
          <div class="main-card-title fs-5 fw-bold" style="font-size:20px;">
            <i class="fas fa-tag me-2"></i> Role Info
          </div>
          <a href="{{ route('roles.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left me-1"></i> Back
          </a>
        </div>

        <div class="main-card-body">
          <div class="col-md-4">
            <label class="fw-bold">Role Name <span class="text-danger">*</span></label>
            <input type="text" name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $role->name) }}"
                   placeholder="e.g. Manager" required>
            @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
          </div>
        </div>
      </div>

      <!-- Permissions -->
      <div class="main-card">
        <div class="main-card-head d-flex justify-content-between">
          <div class="main-card-title fs-5 fw-bold" style="font-size:20px;">
            <i class="fas fa-key me-2"></i> Permissions
          </div>

          <div>
            <button type="button" class="btn-submit me-1" onclick="selectAll(true)">Select All</button>
            <button type="button" class="btn-cancel" onclick="selectAll(false)">Clear</button>
          </div>
        </div>

        <div class="main-card-body">
          @if($permissions->isNotEmpty())

            <div class="table-responsive">
              <table class="table table-bordered text-center align-middle">
                <thead class="bg-light">
                  <tr>
                    <th class="text-left">Module</th>
                    <th>View</th>
                    <th>Create</th>
                    <th>Edit</th>
                    <th>Delete</th>
                    <th>All</th>
                  </tr>
                </thead>

                <tbody>
                  @foreach($permissions as $group => $perms)
                    <tr>
                      <td class="text-left font-weight-bold">{{ $group }}</td>
                      @php $actions = ['view','create','edit','delete']; @endphp
                      @foreach($actions as $action)
                        <td>
                          @php
                            $perm = $perms->firstWhere('name', $action . ' ' . strtolower($group));
                            if (! $perm) {
                              $perm = $perms->first(function($p) use($action, $group) {
                                return str_contains(strtolower($p->name), $action) && str_contains(strtolower($p->name), strtolower($group));
                              });
                            }
                          @endphp

                          @if($perm)
                            <input type="checkbox" class="perm-chk" name="permissions[]" value="{{ $perm->id }}" id="perm_{{ $perm->id }}" {{ in_array($perm->id, old('permissions', $assignedIds)) ? 'checked' : '' }}>
                          @else
                            <input type="checkbox" disabled>
                          @endif
                        </td>
                      @endforeach
                      <td>
                        <input type="checkbox" class="perm-all" onchange="toggleRow(this)">
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

          @else
            <div class="text-center text-muted py-4">
              <i class="fas fa-key fa-2x mb-2 d-block"></i>No permissions found.
            </div>
          @endif
        </div>

        <div class="d-flex justify-content-end mt-3">
          <button type="submit" class="btn-submit">Save Changes</button>
          <a href="{{ route('roles.index') }}" class="btn-cancel ms-2">Cancel</a>
        </div>
      </div>

    </form>
  </div>
</div>

@endsection
