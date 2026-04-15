@extends('admin.layouts.app')
@section('title','Order #'.$order->id)

@section('content')
<div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col-sm-6"><h1 class="m-0">Order #{{ $order->id }}</h1></div><div class="col-sm-6 text-right"><a href="{{ route('orders.index') }}" class="btn btn-secondary">Back</a></div></div></div></div>
<section class="content"><div class="container-fluid">
  <div class="card"><div class="card-body">
    <p><strong>Client:</strong> {{ $order->customer?->name }}</p>
    <p><strong>Date:</strong> {{ $order->date?->format('Y-m-d') }}</p>
    <p><strong>Billing Address:</strong><br>{{ $order->billing_address }}</p>
    <p><strong>Shipping Address:</strong><br>{{ $order->shipping_address }}</p>
    <h5>Items</h5>
    <table class="table table-sm table-bordered"><thead><tr><th>Item</th><th>Desc</th><th>Qty</th><th>Rate</th><th>Total</th></tr></thead><tbody>
      @foreach($order->items as $it)
        <tr><td>{{ $it->item_name }}</td><td>{{ $it->description }}</td><td>{{ $it->quantity }}</td><td>{{ number_format($it->rate,2) }}</td><td>{{ number_format($it->total,2) }}</td></tr>
      @endforeach
    </tbody></table>
    <p class="text-right"><strong>Grand Total: </strong>{{ number_format($order->grand_total,2) }}</p>
  </div></div>
</div></section>
@endsection
