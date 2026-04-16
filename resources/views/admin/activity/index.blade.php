@extends('admin.layouts.app')
@section('title', 'Activity Logs')

@section('content')

{{-- Page Header --}}
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">
          <i class="mr-2 text-teal"></i>Activity Logs
        </h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Activity Logs</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">

    {{-- ── Filter Card ── --}}
    <div class="card card-outline card-teal shadow-sm mb-3">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter mr-2"></i>Filter</h3>
        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse">
            <i class="fas fa-minus"></i>
          </button>
        </div>
      </div>
      <div class="card-body">
        <form method="GET" action="{{ route('activity-logs.index') }}">
          <div class="row align-items-end">

            <div class="col-12 col-md-4 col-lg-3 mb-3">
              <label class="col-form-label font-weight-bold" style="font-size:.82rem;">
                <i class="fas fa-search mr-1 text-muted"></i>Search
              </label>
              <input type="text" name="search" class="form-control form-control-sm"
                     placeholder="User name or description…"
                     value="{{ request('search') }}">
            </div>

            <div class="col-12 col-md-3 col-lg-2 mb-3">
              <label class="col-form-label font-weight-bold" style="font-size:.82rem;">
                <i class="fas fa-bolt mr-1 text-muted"></i>Action
              </label>
              <select name="action" class="form-control form-control-sm">
                <option value="">All Actions</option>
                @foreach($actions as $a)
                  <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>
                    {{ ucfirst($a) }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-12 col-md-3 col-lg-2 mb-3">
              <label class="col-form-label font-weight-bold" style="font-size:.82rem;">
                <i class="fas fa-calendar mr-1 text-muted"></i>From Date
              </label>
              <input type="date" name="date_from" class="form-control form-control-sm"
                     value="{{ request('date_from') }}">
            </div>

            <div class="col-12 col-md-3 col-lg-2 mb-3">
              <label class="col-form-label font-weight-bold" style="font-size:.82rem;">
                <i class="fas fa-calendar mr-1 text-muted"></i>To Date
              </label>
              <input type="date" name="date_to" class="form-control form-control-sm"
                     value="{{ request('date_to') }}">
            </div>

            <div class="col-12 col-md-12 col-lg-3 mb-3 d-flex" style="gap:.5rem;">
              <button type="submit" class="btn-submit">
                <i class="fas fa-search mr-1"></i>Filter
              </button>
              <a href="{{ route('activity-logs.index') }}" class="btn-cancel">
                <i class="fas fa-times mr-1"></i>Clear
              </a>
            </div>

          </div>
        </form>

        {{-- Active filter badges --}}
        @if(request('search') || request('action') || request('date_from') || request('date_to'))
          <div class="mt-1">
            @if(request('search'))
              <span class="badge badge-teal mr-1">
                <i class="fas fa-search mr-1"></i>"{{ request('search') }}"
              </span>
            @endif
            @if(request('action'))
              <span class="badge badge-info mr-1">
                <i class="fas fa-bolt mr-1"></i>{{ ucfirst(request('action')) }}
              </span>
            @endif
            @if(request('date_from'))
              <span class="badge badge-warning mr-1">
                From: {{ \Carbon\Carbon::parse(request('date_from'))->format('d M Y') }}
              </span>
            @endif
            @if(request('date_to'))
              <span class="badge badge-warning mr-1">
                To: {{ \Carbon\Carbon::parse(request('date_to'))->format('d M Y') }}
              </span>
            @endif
            <a href="{{ route('activity-logs.index') }}" class="badge badge-danger">
              <i class="fas fa-times-circle mr-1"></i>Clear all
            </a>
          </div>
        @endif

      </div>
    </div>

    {{-- ── Table Card ── --}}
    <div class="card card-outline card-teal shadow-sm">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-list-alt mr-2"></i>All Activity
          <span class="badge badge-teal ml-1">{{ $logs->total() }}</span>
        </h3>
        <div class="card-tools">
          @if($logs->total())
            <span class="text-muted" style="font-size:.8rem;">
              Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }}
            </span>
          @endif
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover table-sm table-bordered mb-0">
            <thead class="thead">
              <tr>
                <th width="40">#</th>
                <th>User</th>
                <th>Action</th>
                <th>Description</th>
                <th>Model</th>
                <th>Date &amp; Time</th>
                <th width="70"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($logs as $i => $log)
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
                <tr>
                  <td class="text-muted">{{ $logs->firstItem() + $i }}</td>

                  <td>
                    <span class="font-weight-bold">{{ $log->user_name ?? '—' }}</span>
                  </td>

                  <td>
                    <span class="badge {{ $actionBadge }}">
                      <i class="fas fa-{{ $icons[$log->action] ?? 'circle' }} mr-1"></i>
                      {{ $log->action_label }}
                    </span>
                  </td>

                  <td class="text-muted" style="max-width:260px;font-size:.83rem;">
                    {{ Str::limit($log->description, 70) }}
                  </td>

                  <td>
                    @if($log->model_type)
                      <span class="badge badge-secondary">{{ $log->model_name }}</span>
                      @if($log->model_label)
                        <div class="text-muted" style="font-size:.72rem;">{{ Str::limit($log->model_label, 28) }}</div>
                      @endif
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </td>

                  <td class="text-nowrap">
                    <div class="font-weight-bold" style="font-size:.83rem;">{{ $log->created_at->format('d M Y') }}</div>
                    <div class="text-muted" style="font-size:.75rem;">{{ $log->created_at->format('h:i A') }}</div>
                  </td>

                  <td>
                    <a href="{{ route('activity-logs.show', $log->id) }}"
                       class="btn btn-xs btn-outline-teal">
                      <i class="fas fa-eye mr-1"></i>View
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">
                    <i class="fas fa-history fa-2x mb-2 d-block"></i>
                    No activity logs found.
                    @if(request()->hasAny(['search','action','date_from','date_to']))
                      <div class="mt-1" style="font-size:.8rem;">Try clearing the filters above.</div>
                    @endif
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if($logs->hasPages())
          <div class="card-footer clearfix">
            {{ $logs->links('pagination::bootstrap-4') }}
          </div>
        @endif

      </div>
    </div>

  </div>
</section>

@endsection