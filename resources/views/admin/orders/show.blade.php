@extends('admin.layouts.app')
@section('title','Order #'.$order->id)

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Order Details</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Orders</a></li>
          <li class="breadcrumb-item active">show</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="mb-3 mr-3 d-flex justify-content-end">
      <a href="{{ route('orders.index') }}" class="btn-cancel mr-1"><i class="fas fa-arrow-left mr-1"></i>Back</a>
      <a href="{{ route('orders.edit', $order) }}" class="btn-submit"><i class="fas fa-edit mr-1"></i>Edit</a>
    </div>
    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-invoice mr-1"></i>Order Information</h3>
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-3 mb-3"><strong>Order ID:</strong> {{ $order->id }}</div>
          <div class="col-md-3 mb-3"><strong>Customer:</strong> {{ $order->customer?->name }}</div>
          <div class="col-md-3 mb-3"><strong>Date:</strong> {{ $order->date?->format('d-m-Y') }}</div>
          <div class="col-md-3 mb-3"><strong>Expected Delivery Date:</strong> {{ $order->expected_date?->format('d-m-Y') }}</div>
          <div class="col-md-3 mb-3"><strong>E-way Bill Number:</strong> {{ $order->eway_bill_number }}</div>
          <div class="col-md-3 mb-3"><strong>Transport Number:</strong> {{ $order->transport_number }}</div>
          <div class="col-md-3 mb-3"><strong>LR Number:</strong> {{ $order->lr_number }}</div>
          <div class="col-md-3 mb-3"><strong>Status:</strong> {{ $order->status }}</div>
        </div>
        <h5>Addresses</h5>
        <div class="row mb-3">
          <div class="col-md-6 mb-3"><strong>Billing Address:</strong><br>{{ $order->billing_address }}</div>
          <div class="col-md-6 mb-3"><strong>Shipping Address:</strong><br>{{ $order->shipping_address }}</div>
        </div>
        <h5>Items</h5>
        <table class="table table-sm table-bordered"><thead><tr><th width="100">Article No.</th><th width="100">Item</th><th width="140">Color</th><th width="140">Size</th><th>Desc</th><th width="80">Qty</th><th width="100">MRP</th><th width="100">Total</th></tr></thead><tbody>
          @foreach($order->items as $it)
            <tr><td>{{ $it->article_number }}</td><td>{{ $it->item_name }}</td><td>{{ $it->color }}</td><td>{{ $it->size }}</td><td>{{ $it->description }}</td><td>{{ $it->quantity }}</td><td>{{ number_format($it->rate,2) }}</td><td>{{ number_format($it->total,2) }}</td></tr>
          @endforeach
        </tbody></table>
        <div class="row mb-2 mt-5">
          <div class="col-md-6 mb-3"><strong>Terms and Conditions:</strong> {{ $order->terms }}</div>
          <div class="col-md-6 mb-3"><strong>Notes:</strong> {{ $order->notes }}</div>
        </div>
        <h5 class="text-right mt-3"><strong style="color: #7F53AC;">Grand Total: </strong>{{ number_format($order->grand_total,2) }}</h5>
      </div>
    </div>
  </div>
</section>
@endsection
