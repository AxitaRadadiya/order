@extends('admin.layouts.app')
@section('title', 'Activity Detail')

@section('content')

{{-- Page Header --}}
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">
          <i class="mr-2 text-teal"></i>Activity Detail
        </h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('activity-logs.index') }}">Activity Logs</a></li>
          <li class="breadcrumb-item active">Detail</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">

    {{-- ── Log Summary Card ── --}}
    <div class="card ">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-info-circle mr-2"></i>Log Summary
        </h3>
        <div class="card-tools">
          <a href="{{ route('activity-logs.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left mr-1"></i>Back
          </a>
        </div>
      </div>
      <div class="card-body">

        @php
        $actionBadge = match($log->action) {
        'login' => 'badge-success',
        'logout' => 'badge-secondary',
        'created' => 'badge-primary',
        'updated' => 'badge-info',
        'deleted' => 'badge-danger',
        default => 'badge-light',
        };
        $icons = ['login'=>'sign-in-alt','logout'=>'sign-out-alt','created'=>'plus','updated'=>'pen','deleted'=>'trash'];
        @endphp

        <div class="row mb-3">

          <div class="col-md-3 col-sm-6">
            <div class="d-flex align-items-start">
              <div class="mr-3 rounded-circle bg-teal d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                <i class="fas fa-{{ $icons[$log->action] ?? 'circle' }} text-white"></i>
              </div>
              <div>
                <div class="text-muted text-uppercase font-weight-bold small">Action</div>
                <div class="mt-1">
                  <span class="badge {{ $actionBadge }}">{{ $log->action_label }}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-3 col-sm-6">
            <div class="d-flex align-items-start">
              <div class="mr-3 rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                <i class="fas fa-user text-white"></i>
              </div>
              <div>
                <div class="text-muted text-uppercase font-weight-bold small">Performed By</div>
                <div class="mt-1 font-weight-medium">{{ $log->user_name ?? '—' }}</div>
                @if($log->user_role ?? false)
                <div class="text-muted small">{{ $log->user_role }}</div>
                @endif
              </div>
            </div>
          </div>

          <div class="col-md-3 col-sm-6">
            <div class="d-flex align-items-start">
              <div class="mr-3 rounded-circle bg-warning d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                <i class="fas fa-clock text-white"></i>
              </div>
              <div>
                <div class="text-muted text-uppercase font-weight-bold small">Date &amp; Time</div>
                <div class="mt-1 font-weight-medium">{{ $log->created_at->format('d-m-Y, h:i A') }}</div>
                <div class="text-muted small">{{ $log->created_at->diffForHumans() }}</div>
              </div>
            </div>
          </div>

          @if($log->model_type)
          <div class="col-md-3 col-sm-6">
            <div class="d-flex align-items-start">
              <div class="mr-3 rounded-circle bg-secondary d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                <i class="fas fa-database text-white"></i>
              </div>
              <div>
                <div class="text-muted text-uppercase font-weight-bold small">Affected Model</div>
                <div class="mt-1 font-weight-medium"><span class="badge badge-secondary">{{ $log->model_name }}</span> #{{ $log->model_id }}</div>
                @if($log->model_label)
                <div class="text-muted small">{{ $log->model_label }}</div>
                @endif
              </div>
            </div>
          </div>
          @endif
        </div>

        <div class="row mb-3">
          <div class="col-12">
            <div class="border rounded p-3 bg-light">
              <div class="text-muted text-uppercase font-weight-bold small mb-1">Description</div>
              <div class="font-weight-normal">{{ $log->description ?? '—' }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ── Data Changes Card (old vs new) ── --}}
    @if($log->old_values || $log->new_values)
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-exchange-alt mr-2"></i>Data Changes
        </h3>
      </div>
      <div class="card-body">
        <div class="row">

          {{-- Before --}}
          @if($log->old_values)

          <div class="col-md-6 mb-4">
            <div class="card card-danger card-outline shadow-none mb-0">
              <div class="card-header p-2">
                <h3 class="card-title" style="font-size:.82rem;">
                  <i class="fas fa-minus-circle mr-1 text-danger"></i>Before
                </h3>
              </div>
              <div class="card-body">
                <table class="table table-sm table-bordered mb-0">
                  @foreach($log->old_values as $key => $val)
                  <tr>
                    <td class="font-weight-bold text-muted bg-light" width="40%">{{ $key }}</td>
                    <td class="text-danger">{{ is_array($val) ? json_encode($val) : ($val ?? '—') }}</td>
                  </tr>
                  @endforeach
                </table>
              </div>
            </div>
          </div>
          @endif

          {{-- After --}}
          @if($log->new_values)
          <div class="col-md-6 mb-2">
            <div class="card card-success card-outline shadow-none mb-0">
              <div class="card-header p-2">
                <h3 class="card-title" style="font-size:.82rem;">
                  <i class="fas fa-plus-circle mr-1 text-success"></i>After
                </h3>
              </div>
              <div class="card-body">
                <table class="table table-sm table-bordered mb-0">
                  @foreach($log->new_values as $key => $val)
                  <tr>
                    <td class="font-weight-bold text-muted bg-light" width="40%">{{ $key }}</td>
                    <td class="text-success">{{ is_array($val) ? json_encode($val) : ($val ?? '—') }}</td>
                  </tr>
                  @endforeach
                </table>
              </div>
            </div>
          </div>
          @endif

        </div>
      </div>
    </div>
    @endif

  </div>
</section>

@endsection