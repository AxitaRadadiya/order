@extends('admin.layouts.app')
@section('title', 'User Details')
@section('style')
<link rel="stylesheet" href="{{ asset('admin/dist/css/custom/user-role/panel-theme.css') }}">
@endsection
@section('content')

<div class="page-hero">
  <div class="orb"></div>
  <div class="container-fluid" style="position:relative;z-index:2;">
    <div class="section-tabs">
      <a href="{{ route('users.index') }}" class="section-tab active">
        <i class="fas fa-users"></i>
        <span>User</span>
      </a>
      <a href="{{ route('roles.index') }}" class="section-tab">
        <i class="fas fa-user-tag"></i>
        <span>Role</span>
      </a>
    </div>
    <div class="subsection-tabs">
      <a href="{{ route('users.index') }}" class="subsection-tab">
        <i class="fas fa-list"></i>
        <span>List</span>
      </a>
      <a href="{{ route('users.create') }}" class="subsection-tab">
        <i class="fas fa-plus"></i>
        <span>Create</span>
      </a>
      <a href="{{ route('users.edit', $user->id) }}" class="subsection-tab">
        <i class="fas fa-pen"></i>
        <span>Edit</span>
      </a>
      <a href="{{ route('users.show', $user->id) }}" class="subsection-tab active">
        <i class="fas fa-eye"></i>
        <span>View</span>
      </a>
    </div>
    <div class="hero-title">
      <h1>User Details</h1>
      <p>View profile, CRM details, balances, expenses, and transfers for {{ $user->name }}.</p>
    </div>
  </div>
</div>

<div class="pull-card">
  <div class="container-fluid" style="padding:0;">
    <div class="row">
      <div class="col-lg-3 col-md-4 mb-4">
        <div class="profile-panel text-center">
          <img src="{{ $user->profile_image_url }}"
               alt="{{ $user->name }}"
               class="profile-avatar mb-3">
          <h5 class="font-weight-bold mb-1">{{ $user->name }}</h5>
          <p class="text-muted mb-2">{{ $user->email }}</p>
          <span class="status-pill {{ $user->status ? 'active' : 'inactive' }} mb-3">
            {{ $user->status ? 'Active' : 'Inactive' }}
          </span>

          <div class="info-surface text-left mt-3 mb-3">
            <div class="info-label">Role</div>
            <div class="info-value">{{ $user->role->name ?? '-' }}</div>
          </div>
          <div class="info-surface text-left mb-3">
            <div class="info-label">Mobile</div>
            <div class="info-value">{{ $user->mobile ?? '-' }}</div>
          </div>
          <div class="info-surface text-left mb-3">
            <div class="info-label">Direct Balance</div>
            <div class="info-value text-success">Rs {{ number_format((float) ($user->amount ?? 0), 2) }}</div>
          </div>
          <div class="info-surface text-left mb-3">
            <div class="info-label">User Type</div>
            <div class="info-value">{{ ucfirst($user->user_type ?? 'staff') }}</div>
          </div>

          <a href="{{ route('users.edit', $user->id) }}" class="btn-theme w-100 justify-content-center">
            <i class="fas fa-edit"></i> Edit User
          </a>
        </div>
      </div>

      <div class="col-lg-9 col-md-8">
        <div class="row mb-4">
          <div class="col-sm-6 col-xl-3 mb-3">
            <div class="stat-card">
              <div class="stat-label">Opening Balance</div>
              <div class="stat-value">Rs {{ number_format($opening, 2) }}</div>
            </div>
          </div>
          <div class="col-sm-6 col-xl-3 mb-3">
            <div class="stat-card">
              <div class="stat-label">Transfer Balance</div>
              <div class="stat-value">Rs {{ number_format($totalTransfers ?? 0, 2) }}</div>
            </div>
          </div>
          <div class="col-sm-6 col-xl-3 mb-3">
            <div class="stat-card">
              <div class="stat-label">Total Debited</div>
              <div class="stat-value">Rs {{ number_format($totalDebited ?? 0, 2) }}</div>
            </div>
          </div>
          <div class="col-sm-6 col-xl-3 mb-3">
            <div class="stat-card">
              <div class="stat-label">Current Balance</div>
              <div class="stat-value">Rs {{ number_format($currentBalance, 2) }}</div>
            </div>
          </div>
        </div>

        <div class="main-card mb-4">
          <div class="main-card-head">
            <div class="main-card-title"><i class="fas fa-address-card"></i> CRM Details</div>
          </div>
          <div class="main-card-body">
            <div class="row">
              <div class="col-md-4 mb-3"><div class="info-surface"><div class="info-label">Company</div><div class="info-value">{{ $user->company_name ?: '-' }}</div></div></div>
              <div class="col-md-4 mb-3"><div class="info-surface"><div class="info-label">Lead Source</div><div class="info-value">{{ $user->leadSource->name ?? '-' }}</div></div></div>
              <div class="col-md-4 mb-3"><div class="info-surface"><div class="info-label">Lead Owner</div><div class="info-value">{{ $user->leadOwner->name ?? '-' }}</div></div></div>
              <div class="col-md-4 mb-3"><div class="info-surface"><div class="info-label">Lead Stage</div><div class="info-value">{{ $user->leadStage->name ?? '-' }}</div></div></div>
              <div class="col-md-4 mb-3"><div class="info-surface"><div class="info-label">Customer Type</div><div class="info-value">{{ $user->customerType->name ?? '-' }}</div></div></div>
              <div class="col-md-4 mb-3"><div class="info-surface"><div class="info-label">Priority</div><div class="info-value">{{ ucfirst($user->priority ?? 'medium') }}</div></div></div>
              <div class="col-md-4 mb-3"><div class="info-surface"><div class="info-label">Next Follow-up</div><div class="info-value">{{ optional($user->next_followup_date)->format('d M Y') ?? '-' }}</div></div></div>
              <div class="col-md-4 mb-3"><div class="info-surface"><div class="info-label">CRM Active</div><div class="info-value">{{ $user->is_active ? 'Yes' : 'No' }}</div></div></div>
              <div class="col-md-4 mb-3"><div class="info-surface"><div class="info-label">Converted From Lead</div><div class="info-value">{{ $user->convertedFromLead->name ?? '-' }}</div></div></div>
              <div class="col-md-4 mb-3"><div class="info-surface"><div class="info-label">City</div><div class="info-value">{{ $user->city ?: '-' }}</div></div></div>
              <div class="col-md-4 mb-3"><div class="info-surface"><div class="info-label">State</div><div class="info-value">{{ $user->state ?: '-' }}</div></div></div>
              <div class="col-md-4 mb-3"><div class="info-surface"><div class="info-label">Pincode</div><div class="info-value">{{ $user->pincode ?: '-' }}</div></div></div>
              <div class="col-md-4 mb-3"><div class="info-surface"><div class="info-label">GSTIN</div><div class="info-value">{{ $user->gstin ?: '-' }}</div></div></div>
              <div class="col-md-8 mb-3"><div class="info-surface"><div class="info-label">Address</div><div class="info-value">{{ $user->address ?: '-' }}</div></div></div>
              <div class="col-md-12"><div class="info-surface"><div class="info-label">Note</div><div class="info-value">{{ $user->note ?: '-' }}</div></div></div>
            </div>
          </div>
        </div>

        <div class="main-card mb-4">
          <div class="main-card-head">
            <div class="main-card-title">
              <i class="fas fa-receipt"></i> Debited (Expenses)
              <span class="count-badge">{{ $expenses->total() }}</span>
            </div>
          </div>
          <div class="main-card-body p-0">
            <div class="table-responsive">
              <table class="table table-sm table-hover table-bordered mb-0 table-theme">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th class="text-right">Amount</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($expenses as $i => $exp)
                    <tr>
                      <td class="text-muted">{{ $expenses->firstItem() + $i }}</td>
                      <td class="text-nowrap">{{ optional($exp->expense_date)->format('d M Y') ?? '-' }}</td>
                      <td class="text-right">Rs {{ number_format((float) $exp->amount, 2) }}</td>
                      <td class="text-muted" style="font-size:.82rem;">{{ \Illuminate\Support\Str::limit($exp->description ?? '-', 60) }}</td>
                      <td>{{ ucfirst($exp->status ?? '-') }}</td>
                      <td>
                        <a href="{{ route('expense.show', $exp->id) }}" class="btn-theme-outline">
                          <i class="fas fa-eye"></i>
                        </a>
                      </td>
                    </tr>
                  @empty
                    <tr><td colspan="6" class="text-center text-muted py-3">No expenses found.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
            @if($expenses->hasPages())
              <div class="main-card-body">{{ $expenses->links('pagination::bootstrap-4') }}</div>
            @endif
          </div>
        </div>

        <div class="main-card mb-4">
          <div class="main-card-head">
            <div class="main-card-title">
              <i class="fas fa-exchange-alt"></i> Transfers
              <span class="count-badge">{{ $transfers->total() }}</span>
            </div>
          </div>
          <div class="main-card-body p-0">
            <div class="table-responsive">
              <table class="table table-sm table-hover table-bordered mb-0 table-theme">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th class="text-right">Amount</th>
                    <th>Created By</th>
                    <th>Note</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($transfers as $i => $t)
                    <tr>
                      <td class="text-muted">{{ $transfers->firstItem() + $i }}</td>
                      <td class="text-nowrap">{{ optional($t->start_date)->format('d M Y') ?? '-' }}</td>
                      <td class="text-right">Rs {{ number_format((float) $t->amount, 2) }}</td>
                      <td>{{ optional($t->creator)->name ?? '-' }}</td>
                      <td class="text-muted" style="font-size:.82rem;">{{ \Illuminate\Support\Str::limit($t->note ?? '-', 60) }}</td>
                    </tr>
                  @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">No transfers found.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
            @if($transfers->hasPages())
              <div class="main-card-body">{{ $transfers->links('pagination::bootstrap-4') }}</div>
            @endif
          </div>
        </div>

        <div class="main-card mb-4">
          <div class="main-card-head">
            <div class="main-card-title">
              <i class="fas fa-history"></i> Balance History
              <span class="count-badge">{{ $balanceHistories->total() }}</span>
            </div>
          </div>
          <div class="main-card-body p-0">
            <div class="table-responsive">
              <table class="table table-sm table-hover table-bordered mb-0 table-theme">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th class="text-right">Change</th>
                    <th class="text-right">Before</th>
                    <th class="text-right">After</th>
                    <th>Note</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($balanceHistories as $i => $b)
                    <tr>
                      <td class="text-muted">{{ $balanceHistories->firstItem() + $i }}</td>
                      <td class="text-nowrap" style="font-size:.82rem;">{{ optional($b->created_at)->format('d M Y H:i') ?? '-' }}</td>
                      <td>{{ ucfirst($b->change_type ?? '-') }}</td>
                      <td class="text-right">Rs {{ number_format((float) $b->change_amount, 2) }}</td>
                      <td class="text-right text-muted">Rs {{ number_format((float) $b->balance_before, 2) }}</td>
                      <td class="text-right text-muted">Rs {{ number_format((float) $b->balance_after, 2) }}</td>
                      <td class="text-muted" style="font-size:.82rem;">{{ \Illuminate\Support\Str::limit($b->note ?? '-', 50) }}</td>
                    </tr>
                  @empty
                    <tr><td colspan="7" class="text-center text-muted py-3">No balance history found.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
            @if($balanceHistories->hasPages())
              <div class="main-card-body">{{ $balanceHistories->links('pagination::bootstrap-4') }}</div>
            @endif
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
