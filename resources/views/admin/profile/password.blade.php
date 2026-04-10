@extends('admin.layouts.app')
@section('title', 'Change Password')

@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-12 text-center">
        <h1 class="m-0"><i class="fas fa-key mr-2 text-teal"></i>Change Password</h1>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card card-outline card-teal">
          <div class="card-body">
            <form method="POST" action="{{ route('admin.profile.updatePassword') }}">
              @csrf

              <div class="form-group">
                <label class="font-weight-bold">Current Password</label>
                <input type="password" name="current_password" class="form-control" required>
                @error('current_password') <small class="text-danger">{{ $message }}</small> @enderror
              </div>

              <div class="form-group">
                <label class="font-weight-bold">New Password</label>
                <input type="password" name="password" class="form-control" required>
                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
              </div>

              <div class="form-group">
                <label class="font-weight-bold">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
              </div>

              <button class="btn btn-teal" type="submit">Update Password</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
