@extends('admin.layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="fas fa-tachometer-alt mr-2 text-teal"></i>Dashboard</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item active"><i class="far fa-calendar-alt mr-1"></i>{{ date('l, d M Y') }}</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="row">
        <div class="col-lg-3 col-md-6">
          <div class="small-box">
            <div class="inner">
              <h3>7</h3>
              <p>9</p>
            </div>
            <div class="icon"><i class="90"></i></div>
          </div>
        </div>
    </div>
  </div>
</section>
@endsection
