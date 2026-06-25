@extends('admin.layouts.app')

@section('title', 'Order Report')

@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Order Report</h1>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Order Articles Report</h3>
        </div>
        <form method="GET" action="{{ route('reports.orders') }}">
    <div class="row mb-3">

    <div class="col-md-2">
        <label>Customer Name</label>
        <input type="text"
               name="customer_name"
               class="form-control"
               value="{{ request('customer_name') }}">
    </div>

    <div class="col-md-2">
        <label>Article Number</label>
        <input type="text"
               name="article_number"
               class="form-control"
               value="{{ request('article_number') }}">
    </div>

    <div class="col-md-2">
        <label>Status</label>
        <select name="status" class="form-control">
            <option value="">All Status</option>
            <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
            <option value="confirmed" {{ request('status')=='confirmed' ? 'selected' : '' }}>Confirmed</option>
            <option value="shipped" {{ request('status')=='shipped' ? 'selected' : '' }}>Shipped</option>
            <option value="partial_dispatch" {{ request('status')=='partial_dispatch' ? 'selected' : '' }}>Partial Dispatch</option>
            <option value="cancelled" {{ request('status')=='cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
    </div>

    <div class="col-md-2">
        <label>From Date</label>
        <input type="date"
               name="from_date"
               class="form-control"
               value="{{ request('from_date') }}">
    </div>

    <div class="col-md-2">
        <label>To Date</label>
        <input type="date"
               name="to_date"
               class="form-control"
               value="{{ request('to_date') }}">
    </div>

    <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-create mr-2">
            Search
        </button>

        <a href="{{ route('reports.orders') }}" class="btn btn-secondary">
            Reset
        </a>
    </div>

</div>
</form>

    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order No</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Article No</th>
                        <th>Item Name</th>
                        <th>Color</th>
                        <th>Size</th>
                        <th>Qty</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($reportData as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>

                            <td>
                                {{ $item->order_number ?? ('ORD-' . str_pad($item->order_master_id, 5, '0', STR_PAD_LEFT)) }}
                            </td>

                            <td>{{ $item->customer_name ?? '-' }}</td>

                            <td>
                                {{ !empty($item->order_date) ? date('d-m-Y', strtotime($item->order_date)) : '-' }}
                            </td>

                            <td>{{ $item->article_number ?? '-' }}</td>

                            <td>{{ $item->item_name ?? '-' }}</td>

                            <td>{{ $item->color ?? '-' }}</td>

                            <td>{{ $item->size ?? '-' }}</td>

                            <td>{{ $item->quantity ?? 0 }}</td>

                            <td>
                                <span class="badge badge-info">
                                    {{ ucfirst($item->status ?? 'pending') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">
                                No Records Found
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>
</div>

</div>

@endsection
