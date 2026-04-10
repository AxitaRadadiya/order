@extends('admin.layouts.app')
@section('title', 'My Profile')

@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="fas fa-user-circle mr-2 text-teal"></i>My Profile</h1>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="card card-outline card-teal">
      <div class="card-body">
        @if(session('status') === 'profile-updated')
          <div class="alert alert-success">Profile updated successfully.</div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}">
          @csrf
          @method('PATCH')

          <div class="form-group">
            <label class="font-weight-bold">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="form-group">
            <label class="font-weight-bold">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="d-flex" style="gap:.5rem;">
            <button type="submit" class="btn btn-teal">Save</button>
            <button type="button" class="btn btn-outline-danger" data-toggle="modal" data-target="#deleteAccountModal">Delete Account</button>
          </div>
        </form>

        {{-- Change Password Card --}}
        <form method="POST" action="{{ route('profile.password') }}" class="mt-3">
          @csrf
          <div class="card card-outline card-teal">
            <div class="card-header"><h5 class="card-title">Change Password</h5></div>
            <div class="card-body">
              @if(session('password-updated'))
                <div class="alert alert-success">Password updated successfully.</div>
              @endif

              <div class="form-group">
                <label class="font-weight-bold">Current Password</label>
                <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                @error('current_password') <small class="text-danger">{{ $message }}</small> @enderror
              </div>

              <div class="form-group">
                <label class="font-weight-bold">New Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
              </div>

              <div class="form-group">
                <label class="font-weight-bold">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
              </div>
            </div>
            <div class="card-footer">
              <button type="submit" class="btn btn-danger">Update Password</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- Delete modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Delete account</h5></div>
      <div class="modal-body">
        <p>Are you sure? This action is irreversible.</p>
        <form method="POST" action="{{ route('profile.destroy') }}" id="deleteAccountForm">
          @csrf
          @method('DELETE')
          <div class="form-group">
            <label>Confirm with password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="submit" form="deleteAccountForm" class="btn btn-danger">Delete</button>
      </div>
    </div>
  </div>
</div>

@endsection
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
