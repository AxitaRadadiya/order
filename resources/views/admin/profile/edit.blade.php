@extends('admin.layouts.app')
@section('title', 'Profile')

@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-12 text-center">
        <h1 class="m-0"><i class="mr-2 text-teal"></i>Profile</h1>
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
            <form method="POST" action="{{ route('admin.profile.update') ?? route('profile.update') }}" enctype="multipart/form-data">
              @csrf
              @method('PATCH')

              <div class="form-group">
                <label class="font-weight-bold">Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
              </div>

              <div class="form-group">
                <label class="font-weight-bold">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" readonly>
                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
              </div>
              <div class="form-group">
                <label class="font-weight-bold">Mobile</label>
                <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $user->mobile) }}">
                @error('mobile') <small class="text-danger">{{ $message }}</small> @enderror
              </div>

              <div class="form-group">
                <label class="font-weight-bold">Profile Image</label>
                <input type="file" name="profile_image" class="form-control-file">
                @if($user->profile_image_url)
                  <div class="mt-2"><img src="{{ $user->profile_image_url }}" style="width:60px;height:60px;object-fit:cover;border-radius:6px;"></div>
                @endif
                @error('profile_image') <small class="text-danger">{{ $message }}</small> @enderror
              </div>

              <button class="btn-submit" type="submit">Save Profile</button>
            </form>
          </div>
        </div>
      </div>
  </div>
</section>

@endsection
