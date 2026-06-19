@extends('admin.layouts.app')

@section('title', 'Cart')

@section('content')
<style>
.cart-container {
padding: 20px 0;
}
.cart-table {
width: 100%;
border-collapse: collapse;
}
.cart-table th {
background: #f8f9fa;
padding: 12px 15px;
text-align: left;
font-weight: 600;
color: #555;
border-bottom: 2px solid #e8e8e8;
}
.cart-table td {
padding: 12px 15px;
border-bottom: 1px solid #e8e8e8;
vertical-align: middle;
}
.cart-table tr:hover {
background: #fafafa;
}
.product-cell {
display: flex;
align-items: center;
gap: 12px;
}
.product-cell img {
width: 50px;
height: 50px;
object-fit: cover;
border-radius: 6px;
border: 1px solid #e8e8e8;
}
.product-cell .product-name {
font-weight: 500;
color: #333;
}
.color-badge {
display: inline-block;
padding: 3px 10px;
border-radius: 12px;
font-size: 12px;
background: #f0f0f0;
color: #555;
}
.color-badge .color-dot {
display: inline-block;
width: 10px;
height: 10px;
border-radius: 50%;
border: 1px solid #ddd;
margin-right: 4px;
vertical-align: middle;
}
.size-badge {
display: inline-block;
padding: 3px 10px;
border-radius: 4px;
font-size: 12px;
background: #e8e8e8;
color: #555;
font-weight: 500;
}
.qty-control {
display: flex;
align-items: center;
gap: 8px;
}
.qty-control button {
width: 30px;
height: 30px;
border-radius: 50%;
border: 1px solid #ddd;
background: #fff;
cursor: pointer;
font-size: 16px;
display: flex;
align-items: center;
justify-content: center;
transition: all 0.2s;
}
.qty-control button:hover:not(:disabled) {
background: #7F53AC;
color: #fff;
border-color: #7F53AC;
}
.qty-control button:disabled {
opacity: 0.4;
cursor: not-allowed;
}
.qty-control button:disabled:hover {
background: #fff;
color: #333;
border-color: #ddd;
}
.qty-control input {
width: 50px;
text-align: center;
border: 1px solid #ddd;
border-radius: 4px;
padding: 4px;
font-size: 14px;
font-weight: 600;
}
.stock-info {
font-size: 11px;
color: #888;
display: block;
}
.stock-info.in-stock {
color: #28a745;
}
.stock-info.low-stock {
color: #ffc107;
}
.stock-info.out-of-stock {
color: #dc3545;
}
.cart-summary {
background: #f8f9fa;
border-radius: 8px;
padding: 20px;
margin-top: 20px;
}
.cart-summary .summary-row {
display: flex;
justify-content: space-between;
padding: 8px 0;
border-bottom: 1px solid #e8e8e8;
}
.cart-summary .summary-row:last-child {
border-bottom: none;
}
.cart-summary .summary-label {
font-weight: 500;
color: #555;
}
.cart-summary .summary-value {
font-weight: 600;
color: #333;
}
.cart-summary .grand-total {
font-size: 18px;
color: #7F53AC;
}
.cart-actions {
display: flex;
gap: 12px;
margin-top: 20px;
justify-content: flex-end;
}
.btn-continue {
padding: 10px 24px;
border: 2px solid #ddd;
border-radius: 6px;
background: #fff;
color: #555;
font-weight: 600;
cursor: pointer;
transition: all 0.2s;
text-decoration: none;
display: inline-block;
}
.btn-continue:hover {
border-color: #7F53AC;
color: #7F53AC;
}
.btn-checkout {
padding: 10px 24px;
border: none;
border-radius: 6px;
background: #7F53AC;
color: #fff;
font-weight: 600;
cursor: pointer;
transition: background 0.2s;
text-decoration: none;
display: inline-block;
}
.btn-checkout:hover {
background: #6a44a0;
}
.btn-checkout:disabled {
background: #ccc;
cursor: not-allowed;
}
.remove-item {
color: #dc3545;
cursor: pointer;
font-size: 16px;
transition: color 0.2s;
background: none;
border: none;
}
.remove-item:hover {
color: #c82333;
}
.empty-cart {
text-align: center;
padding: 60px 20px;
}
.empty-cart .empty-icon {
font-size: 60px;
color: #ddd;
margin-bottom: 20px;
}
.empty-cart h4 {
color: #555;
margin-bottom: 10px;
}
.empty-cart p {
color: #888;
}
</style>

<div class="content-header">
<div class="container-fluid">
<h1 class="m-0">Shopping Cart</h1>
</div>
</div>

<section class="content">
<div class="container-fluid cart-container">
<div class="row">
<div class="col-12">
@if($items->isEmpty())
<div class="empty-cart">
<div class="empty-icon">
<i class="fas fa-shopping-cart"></i>
</div>
<h4>Your cart is empty</h4>
<p>Browse our catalog and add items you like</p>
<a href="{{ route('catalog') }}" class="btn-checkout">
<i class="fas fa-arrow-left"></i> Continue Shopping
</a>
</div>
@else
<div class="card">
<div class="card-body p-0">
<div class="table-responsive">
<table class="cart-table">
<thead>
<tr>
<th>Product</th>
<th>Color</th>
<th>Color Code</th>
<th>Size</th>

<th>Price</th>
<th>Quantity</th>
<th>Total</th>
<th style="width:50px;">Action</th>
</tr>
</thead>
<tbody>
@foreach($items as $it)
<tr data-entry="{{ $it['entry_id'] }}">
<td>
<div class="product-cell">
@if($it['image'])
<img src="{{ $it['image'] }}" alt="{{ $it['name'] }}">
@else
<div style="width:50px;height:50px;background:#f0f0f0;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#ccc;">
<i class="fas fa-image"></i>
</div>
@endif
<span class="product-name">{{ $it['name'] }}</span>
</div>
</td>
<td>
@php
$colorCode = $it['color_code'] ?? null;
$colorName = $it['color_name'] ?? null;
@endphp
@if($colorCode)
<span class="color-badge">
<span class="color-dot" style="background:{{ $colorCode }};"></span>
{{ $colorName ?? $colorCode }}
</span>
@else
<span class="color-badge">-</span>
@endif
</td>
<td>
{{ $it['color_code'] ?? '-' }}
</td>
<td>
<span class="size-badge">{{ $it['size_name'] ?? '-' }}</span>
</td>

<td>
<strong>₹{{ number_format($it['price'], 2) }}</strong>
</td>
<td>
<div class="qty-control">
<button class="btn-decrease" data-entry="{{ $it['entry_id'] }}" {{ $it['qty'] <= 1 ? 'disabled' : '' }}>−</button>
<input type="text" class="qty-input" value="{{ $it['qty'] }}" data-entry="{{ $it['entry_id'] }}" data-max-stock="{{ $it['max_stock'] ?? 0 }}">
<button class="btn-increase" data-entry="{{ $it['entry_id'] }}" {{ (isset($it['max_stock']) && $it['qty'] >= $it['max_stock']) ? 'disabled' : '' }}>+</button>
@if(isset($it['max_stock']))
<span class="stock-info {{ $it['max_stock'] <= 0 ? 'out-of-stock' : ($it['max_stock'] <= 5 ? 'low-stock' : 'in-stock') }}">
Stock: {{ $it['max_stock'] }}
</span>
@endif
</div>
</td>
<td>
<strong>₹{{ number_format($it['price'] * $it['qty'], 2) }}</strong>
</td>
<td>
<button class="remove-item" data-entry="{{ $it['entry_id'] }}" title="Remove item">
<i class="fas fa-trash"></i>
</button>
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>

{{-- Cart Summary --}}
<div class="card-footer">
<div class="row">
<div class="col-md-6">
<div class="cart-actions">
<a href="{{ route('catalog') }}" class="btn-continue">
<i class="fas fa-arrow-left"></i> Continue Shopping
</a>
</div>
</div>
<div class="col-md-6">
<div class="cart-summary">
<div class="summary-row">
<span class="summary-label">Subtotal</span>
<span class="summary-value">₹{{ number_format($subtotal, 2) }}</span>
</div>
<div class="summary-row">
<span class="summary-label">Total Items</span>
<span class="summary-value">{{ $items->sum('qty') }}</span>
</div>
<div class="summary-row" style="border-bottom: 2px solid #7F53AC; padding-bottom: 12px; margin-bottom: 8px;">
<span class="summary-label" style="font-size: 16px; color: #7F53AC;">Grand Total</span>
<span class="summary-value grand-total">₹{{ number_format($subtotal, 2) }}</span>
</div>
<div class="summary-row" style="border-bottom: none; padding-top: 12px;">
<span></span>
<a href="{{ route('orders.create', ['from_cart' => 1]) }}" class="btn-checkout">
<i class="fas fa-shopping-bag"></i> Proceed to Checkout
</a>
</div>
</div>
</div>
</div>
</div>
</div>
@endif
</div>
</div>
</div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
// Increase quantity
document.querySelectorAll('.btn-increase').forEach(function(btn) {
btn.addEventListener('click', function() {
const entryId = this.dataset.entry;
const input = document.querySelector('.qty-input[data-entry="' + entryId + '"]');
const maxStock = parseInt(input.dataset.maxStock) || 0;
const currentQty = parseInt(input.value) || 0;

if (maxStock > 0 && currentQty >= maxStock) {
alert('Cannot exceed available stock: ' + maxStock);
return;
}

updateQty(entryId, currentQty + 1);
});
});

// Decrease quantity
document.querySelectorAll('.btn-decrease').forEach(function(btn) {
btn.addEventListener('click', function() {
const entryId = this.dataset.entry;
const input = document.querySelector('.qty-input[data-entry="' + entryId + '"]');
const currentQty = parseInt(input.value) || 0;

if (currentQty <= 1) {
return;
}

updateQty(entryId, currentQty - 1);
});
});

// Manual quantity input change
document.querySelectorAll('.qty-input').forEach(function(input) {
input.addEventListener('change', function() {
const entryId = this.dataset.entry;
const maxStock = parseInt(this.dataset.maxStock) || 0;
let qty = parseInt(this.value) || 0;

if (qty < 1) {
qty = 1;
this.value = 1;
}

if (maxStock > 0 && qty > maxStock) {
alert('Cannot exceed available stock: ' + maxStock);
this.value = maxStock;
qty = maxStock;
}

updateQty(entryId, qty);
});
});

// Remove item
document.querySelectorAll('.remove-item').forEach(function(btn) {
btn.addEventListener('click', function() {
const entryId = this.dataset.entry;

if (confirm('Are you sure you want to remove this item from cart?')) {
removeItem(entryId);
}
});
});
});

function updateQty(itemId, qty) {
const url = '{{ route("cart.update", ["cart" => "__ID__"]) }}'.replace('__ID__', itemId);

fetch(url, {
method: 'PUT',
headers: {
'Content-Type': 'application/json',
'X-CSRF-TOKEN': '{{ csrf_token() }}'
},
body: JSON.stringify({ qty: qty })
})
.then(response => {
if (!response.ok) {
return response.json().then(data => {
throw new Error(data.message || 'Failed to update cart');
});
}
return response.json();
})
.then(data => {
if (data.success) {
location.reload();
} else {
alert(data.message || 'Failed to update cart');
}
})
.catch(error => {
alert(error.message || 'Failed to communicate with server');
});
}

function removeItem(itemId) {
const url = '{{ route("cart.destroy", ["cart" => "__ID__"]) }}'.replace('__ID__', itemId);

fetch(url, {
method: 'DELETE',
headers: {
'X-CSRF-TOKEN': '{{ csrf_token() }}'
}
})
.then(response => {
if (!response.ok) {
return response.json().then(data => {
throw new Error(data.message || 'Failed to remove item');
});
}
return response.json();
})
.then(data => {
if (data.success) {
location.reload();
} else {
alert('Failed to remove item');
}
})
.catch(error => {
alert(error.message || 'Failed to communicate with server');
});
}
</script>

@endsection
