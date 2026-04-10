@extends('admin.layouts.app')
@section('title', 'Activity Detail')

@section('content')

{{-- Page Header --}}
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">
          <i class="fas fa-search mr-2 text-teal"></i>Activity Detail
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
    <div class="card card-outline card-teal shadow-sm mb-3">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-info-circle mr-2"></i>Log Summary
        </h3>
        <div class="card-tools">
          <a href="{{ route('activity-logs.index') }}" class="btn btn-default btn-sm">
            <i class="fas fa-arrow-left mr-1"></i>Back
          </a>
        </div>
      </div>
      <div class="card-body">

        @php
          $actionBadge = match($log->action) {
            'login'   => 'badge-success',
            'logout'  => 'badge-secondary',
            'created' => 'badge-primary',
            'updated' => 'badge-info',
            'deleted' => 'badge-danger',
            default   => 'badge-light',
          };
          $icons = ['login'=>'sign-in-alt','logout'=>'sign-out-alt','created'=>'plus','updated'=>'pen','deleted'=>'trash'];
        @endphp

        <div class="row">

          <div class="col-md-4 col-sm-6 mb-3">
            <div class="info-box shadow-none bg-light mb-0">
              <span class="info-box-icon bg-teal"><i class="fas fa-bolt"></i></span>
              <div class="info-box-content">
                <span class="info-box-text font-weight-bold">Action</span>
                <span class="info-box-number">
                  <span class="badge {{ $actionBadge }} badge-lg">
                    <i class="fas fa-{{ $icons[$log->action] ?? 'circle' }} mr-1"></i>
                    {{ $log->action_label }}
                  </span>
                </span>
              </div>
            </div>
          </div>

          <div class="col-md-4 col-sm-6 mb-3">
            <div class="info-box shadow-none bg-light mb-0">
              <span class="info-box-icon bg-primary"><i class="fas fa-user"></i></span>
              <div class="info-box-content">
                <span class="info-box-text font-weight-bold">Performed By</span>
                <span class="info-box-number" style="font-size:1rem;">{{ $log->user_name ?? '—' }}</span>
              </div>
            </div>
          </div>

          <div class="col-md-4 col-sm-6 mb-3">
            <div class="info-box shadow-none bg-light mb-0">
              <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
              <div class="info-box-content">
                <span class="info-box-text font-weight-bold">Date &amp; Time</span>
                <span class="info-box-number" style="font-size:1rem;">{{ $log->created_at->format('d M Y, h:i A') }}</span>
                <span class="progress-description text-muted">{{ $log->created_at->diffForHumans() }}</span>
              </div>
            </div>
          </div>

          @if($log->model_type)
            <div class="col-md-4 col-sm-6 mb-3">
              <div class="info-box shadow-none bg-light mb-0">
                <span class="info-box-icon bg-secondary"><i class="fas fa-database"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text font-weight-bold">Affected Model</span>
                  <span class="info-box-number" style="font-size:1rem;">
                    <span class="badge badge-secondary">{{ $log->model_name }}</span> #{{ $log->model_id }}
                  </span>
                  @if($log->model_label)
                    <span class="progress-description text-muted">{{ $log->model_label }}</span>
                  @endif
                </div>
              </div>
            </div>
          @endif

          <div class="col-md-8 col-sm-12 mb-3">
            <div class="info-box shadow-none bg-light mb-0">
              <span class="info-box-icon bg-info"><i class="fas fa-align-left"></i></span>
              <div class="info-box-content">
                <span class="info-box-text font-weight-bold">Description</span>
                <span class="info-box-number" style="font-size:.9rem;font-weight:400;color:#333;">
                  {{ $log->description ?? '—' }}
                </span>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    {{-- ── Data Changes Card (old vs new) ── --}}
    @if($log->old_values || $log->new_values)
      <div class="card card-outline card-teal shadow-sm">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-exchange-alt mr-2"></i>Data Changes
          </h3>
        </div>
        <div class="card-body">
          <div class="row">

            {{-- Before --}}
            @if($log->old_values)
              <div class="col-md-6 mb-3">
                <div class="card card-danger card-outline shadow-none mb-0">
                  <div class="card-header p-2">
                    <h3 class="card-title" style="font-size:.82rem;">
                      <i class="fas fa-minus-circle mr-1 text-danger"></i>Before
                    </h3>
                  </div>
                  <div class="card-body p-0">
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
              <div class="col-md-6 mb-3">
                <div class="card card-success card-outline shadow-none mb-0">
                  <div class="card-header p-2">
                    <h3 class="card-title" style="font-size:.82rem;">
                      <i class="fas fa-plus-circle mr-1 text-success"></i>After
                    </h3>
                  </div>
                  <div class="card-body p-0">
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