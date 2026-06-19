@extends('admin.layouts.app')
@php
  $hasLockedItem = $hasLockedItem ?? false;
@endphp
@section('title', 'Edit Order')

@section('style')
<style>
  .flash-warning {
    animation: flashWarn 0.6s ease;
  }
  @keyframes flashWarn {
    0% { background-color: transparent; }
    30% { background-color: #ffe0e0; }
    100% { background-color: transparent; }
  }
  #variantSaveBtn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
  .remove-item:disabled,
  .deleteButton:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
  .variant-drawer-size {
    background: #f0f0f0;
    color: #333;
    border: 2px solid #ccc;
    border-radius: 8px;
    padding: 6px 10px;
    margin: 4px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }
  .variant-drawer-size small {
    opacity: 0.75;
    font-size: 11px;
    font-weight: 600;
  }
  .variant-drawer-size.active {
    background: #7F53AC;
    color: #fff;
    border-color: #7F53AC;
    font-weight: 700;
    box-shadow: 0 2px 6px rgba(127, 83, 172, 0.3);
  }
  .variant-drawer-size:hover:not(.active) {
    background: #e0d4f5;
    border-color: #7F53AC;
  }
  .status-badge-locked {
    padding: 5px 12px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
  }
  .status-badge-confirmed {
    background: #17a2b8;
    color: #fff;
  }
  .status-badge-shipped {
    background: #007bff;
    color: #fff;
  }
  .status-badge-delivered {
    background: #28a745;
    color: #fff;
  }
  .status-badge-cancelled {
    background: #dc3545;
    color: #fff;
  }
  .status-badge-pending {
    background: #ffc107;
    color: #212529;
  }
  .status-badge-draft {
    background: #6c757d;
    color: #fff;
  }
</style>
@endsection

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Edit Order</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Orders</a></li>
          <li class="breadcrumb-item active">Edit</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
      @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('orders.update', $order) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="card card-outline card-light">
        <div class="card-body">

          {{-- ── Customer / Dates ────────────────────────────────────────── --}}
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Customer Name <span class="text-danger">*</span></label>
                <select name="user_id" id="customer_id" class="form-control select2" @if($hasLockedItem) disabled @endif>
                  <option value="">-- Select Customer --</option>
                  @foreach($customers as $c)
                  <option value="{{ $c->id }}"
                    data-billing="{{ trim(($c->address->billing_street  ?? '').' '.
                                          ($c->address->billing_city    ?? '').' '.
                                          ($c->address->billing_state   ?? '').' '.
                                          ($c->address->billing_country ?? '')) }}"
                    data-shipping="{{ trim(($c->address->shipping_street  ?? $c->address->billing_street  ?? '').' '.
                                           ($c->address->shipping_city    ?? $c->address->billing_city    ?? '').' '.
                                           ($c->address->shipping_state   ?? $c->address->billing_state   ?? '').' '.
                                           ($c->address->shipping_country ?? $c->address->billing_country ?? '')) }}"
                    {{ old('user_id', $order->user_id) == $c->id ? 'selected' : '' }}>
                    {{ $c->name }}
                  </option>
                  @endforeach
                </select>
                @if($hasLockedItem)
                <small class="text-muted">Customer selection is locked because order has confirmed/shipped/delivered/cancelled items.</small>
                @endif
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" class="form-control" @if($hasLockedItem) readonly @endif
                  value="{{ old('date', $order->date?->format('Y-m-d')) }}">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Expected Date</label>
                <input type="date" name="expected_date" class="form-control" @if($hasLockedItem) readonly @endif
                  value="{{ old('expected_date', $order->expected_date?->format('Y-m-d')) }}">
              </div>
            </div>
          </div>

          {{-- ── Transport ─────────────────────────────────────────────── --}}
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>E-way Bill Number</label>
                <input type="text" name="eway_bill_number" class="form-control" @if($hasLockedItem) readonly @endif
                  value="{{ old('eway_bill_number', $order->eway_bill_number) }}">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Transport Number</label>
                <input type="text" name="transport_number" class="form-control" @if($hasLockedItem) readonly @endif
                  value="{{ old('transport_number', $order->transport_number) }}">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>LR Number</label>
                <input type="text" name="lr_number" class="form-control" @if($hasLockedItem) readonly @endif
                  value="{{ old('lr_number', $order->lr_number) }}">
              </div>
            </div>
          </div>

          {{-- ── Addresses ─────────────────────────────────────────────── --}}
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Billing Address</label>
                <textarea name="billing_address" id="billing_address"
                  class="form-control" rows="2" @if($hasLockedItem) readonly @endif>{{ old('billing_address', $order->billing_address) }}</textarea>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Shipping Address</label>
                <textarea name="shipping_address" id="shipping_address"
                  class="form-control" rows="2" @if($hasLockedItem) readonly @endif>{{ old('shipping_address', $order->shipping_address) }}</textarea>
              </div>
            </div>
          </div>

          {{-- ── Normal Items Table ────────────────────────────────────── --}}
          <div id="normalTable">
            <table class="table table-sm table-bordered" id="itemsTable">
              <thead class="thead-light">
                <tr>
                  <th>Article Number</th>
                  <th>Item</th>
                  <th>Color code</th>
                  <th>Size(s)</th>
                  <th>Description</th>
                  <th>Qty</th>
                  <th>MRP</th>
                  <th>Tax %</th>
                  <th>Total</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @php
                $rowItems = old('items') ? collect(old('items')) : $order->items;
                @endphp

                @foreach($rowItems as $i => $it)
                @php
                $isArr = is_array($it);
                $articleNum = $isArr ? ($it['article_number'] ?? '') : ($it->article_number ?? '');
                $itemId = $isArr ? ($it['item_id'] ?? '') : ($it->item_id ?? '');
                $itemName = $isArr ? ($it['item_name'] ?? '') : ($it->item_name ?? '');
                $color = $isArr ? ($it['color'] ?? '') : ($it->color ?? '');
                $size = $isArr ? ($it['size'] ?? '') : ($it->size ?? '');
                $desc = $isArr ? ($it['description'] ?? '') : ($it->description ?? '');
                $qty = $isArr ? ($it['quantity'] ?? 1) : ($it->quantity ?? 1);
                $rate = $isArr ? ($it['rate'] ?? 0) : ($it->rate ?? 0);
                $taxRate = $isArr ? ($it['tax_rate'] ?? 0) : ($it->tax_rate ?? 0);
                $total = $isArr ? ($it['total'] ?? 0) : ($it->total ?? 0);
                $selectedStatus = $isArr ? ($it['status'] ?? '') : ($it->status ?? '');
                $isLockedStatus = in_array($selectedStatus, ['confirmed', 'shipped', 'delivered', 'cancelled']);
                @endphp
                <tr>
                  <td>
                    @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']))
                    @if($isLockedStatus || $hasLockedItem)
                    <input type="text" class="form-control" value="{{ $articleNum }}" readonly>
                    <input type="hidden" name="items[{{ $i }}][article_number]" value="{{ $articleNum }}">
                    <input type="hidden" name="items[{{ $i }}][item_id]" class="item-id-hidden" value="{{ $itemId }}">
                    <input type="hidden" name="items[{{ $i }}][order_item_id]" class="order-item-id-hidden" value="{{ $isArr ? ($it['id'] ?? '') : ($it->id ?? '') }}">
                    @else
                    <select name="items[{{ $i }}][article_number]" class="form-control article-select">
                      <option value="">--</option>
                      @foreach($items as $itm)
                      <option value="{{ $itm->article_number }}"
                        data-id="{{ $itm->id }}"
                        data-rate="{{ $itm->price }}"
                        data-tax="{{ $itm->tax_percent ?? 0 }}"
                        data-desc="{{ $itm->description ?? '' }}"
                        {{ ($itemId == $itm->id || $articleNum == $itm->article_number) ? 'selected' : '' }}>
                        {{ $itm->article_number }}
                      </option>
                      @endforeach
                    </select>
                    <input type="hidden" name="items[{{ $i }}][item_id]" class="item-id-hidden" value="{{ $itemId }}">
                    <input type="hidden" name="items[{{ $i }}][order_item_id]" class="order-item-id-hidden" value="{{ $isArr ? ($it['id'] ?? '') : ($it->id ?? '') }}">
                    @endif
                    @else
                    <input type="text" class="form-control" value="{{ ($items->firstWhere('id', $itemId)->article_number ?? $articleNum) }}" readonly>
                    <input type="hidden" name="items[{{ $i }}][article_number]" value="{{ ($items->firstWhere('id', $itemId)->article_number ?? $articleNum) }}">
                    <input type="hidden" name="items[{{ $i }}][item_id]" class="item-id-hidden" value="{{ $itemId }}">
                    <input type="hidden" name="items[{{ $i }}][order_item_id]" class="order-item-id-hidden" value="{{ $isArr ? ($it['id'] ?? '') : ($it->id ?? '') }}">
                    @endif
                  </td>
                  <td>
                    <input type="text" name="items[{{ $i }}][item_name]" class="form-control item-name-input" value="{{ $itemName }}" @if($isLockedStatus || $hasLockedItem) readonly @endif>
                  </td>
                  <td>
                    {{-- Color select --}}
                    @php
                    $selectedColors = $color;
                    if (!is_array($selectedColors)) {
                    $selectedColors = $selectedColors === null ? [] : explode(',', $selectedColors);
                    }
                    $selectedColors = array_map('trim', $selectedColors);
                    $rowItem = !empty($itemId) ? $items->firstWhere('id', $itemId) : null;
                    $rowVariantColors = ($rowItem && $rowItem->relationLoaded('variants'))
                      ? $rowItem->variants->map(fn($variant) => $variant->color)->filter()->unique('id')->values()
                      : collect();
                    $rowColors = $rowVariantColors->isNotEmpty()
                      ? $rowVariantColors
                      : ($rowItem ? $rowItem->colors : collect());
                    $selectedColors = collect($selectedColors)
                      ->filter(fn($id) => $rowColors->contains('id', $id))
                      ->values()
                      ->all();
                    $selectedColorNames = [];
                    foreach ($selectedColors as $sc) {
                    $cobj = $rowColors->firstWhere('id', $sc) ?: $colors->firstWhere('id', $sc);
                    if ($cobj) { $selectedColorNames[] = $cobj->color_code; }
                    }
                    @endphp
                    @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']))
                    @if($isLockedStatus || $hasLockedItem)
                    <div class="form-control color-read" readonly>{{ implode(', ', $selectedColorNames) }}</div>
                    @foreach($selectedColors as $sc)
                    <input type="hidden" name="items[{{ $i }}][color][]" value="{{ $sc }}">
                    @endforeach
                    @else
                    <select name="items[{{ $i }}][color][]" class="form-control color-select select2">
                      @foreach($rowColors as $col)
                      <option value="{{ $col->id }}" {{ in_array((string) $col->id, $selectedColors) ? 'selected' : '' }}>{{ $col->color_code }}</option>
                      @endforeach
                    </select>
                    @endif
                    @else
                    <div class="form-control color-read" readonly>{{ implode(', ', $selectedColorNames) }}</div>
                    @foreach($selectedColors as $sc)
                    <input type="hidden" name="items[{{ $i }}][color][]" value="{{ $sc }}">
                    @endforeach
                    @endif
                  </td>
                  <td>
                    {{-- Size multi-select --}}
                    @php
                    $selectedSizes = [];
                    if ($isArr && !empty($it['sizes'])) {
                    $selectedSizes = is_array($it['sizes']) ? $it['sizes'] : explode(',', $it['sizes']);
                    $selectedSizes = array_map('trim', $selectedSizes);
                    } elseif (!empty($size)) {
                    $selectedSizes = array_map('trim', explode(',', $size));
                    }
                    $sizeQuantities = $isArr ? ($it['size_quantities'] ?? []) : ($it->size_quantities ?? []);
                    $fallbackSizeQty = count($selectedSizes) && (float) $qty > 0
                    ? round(((float) $qty) / count($selectedSizes), 2)
                    : 1;
                    @endphp
                    @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']))
                    @if($isLockedStatus || $hasLockedItem)
                    <div class="form-control size-readonly-box" readonly>{{ implode(', ', $selectedSizes) }}</div>
                    @foreach($selectedSizes as $selectedSize)
                    <input type="hidden" name="items[{{ $i }}][sizes][]" value="{{ $selectedSize }}">
                    @endforeach
                    @else
                    <select name="items[{{ $i }}][sizes][]" class="size-select d-none" multiple>
                      @foreach($sizesJson as $sz)
                      <option value="{{ $sz }}" {{ in_array($sz, $selectedSizes) ? 'selected' : '' }}>{{ $sz }}</option>
                      @endforeach
                    </select>
                    <div class="size-chips-wrap">
                      @foreach($sizesJson as $sz)
                      <button type="button" class="size-chip {{ in_array($sz, $selectedSizes) ? 'active' : '' }}" data-size="{{ $sz }}">{{ $sz }}</button>
                      @endforeach
                    </div>
                    <div class="size-qty-wrapper size-qty-panel" style="display:none;">
                      @foreach($selectedSizes as $selectedSize)
                      @php
                      $savedSizeQty = $sizeQuantities[$selectedSize] ?? null;
                      $displaySizeQty = ((float) $savedSizeQty > 0) ? $savedSizeQty : $fallbackSizeQty;
                      @endphp
                      <div class="size-qty-row" data-size="{{ $selectedSize }}">
                        <div class="input-group input-group-sm mb-1">
                          <div class="input-group-prepend"><span class="input-group-text">{{ $selectedSize }}</span></div>
                          <div class="size-stepper">
                            <button type="button" class="stepper-btn minus">−</button>
                            <input type="text" step="1" min="0" name="items[{{ $i }}][size_quantities][{{ $selectedSize }}]" class="size-qty" value="{{ $displaySizeQty }}" readonly>
                            <button type="button" class="stepper-btn plus">+</button>
                          </div>
                        </div>
                      </div>
                      @endforeach
                    </div>
                    @endif
                    @else
                    <div class="form-control size-readonly-box" readonly>{{ implode(', ', $selectedSizes) }}</div>
                    @endif
                  </td>
                  <td><input type="text" name="items[{{ $i }}][description]" class="form-control desc" value="{{ $desc }}" @if($isLockedStatus || $hasLockedItem) readonly @endif></td>
                  <td><input type="number" step="0.01" name="items[{{ $i }}][quantity]" class="form-control qty" value="{{ $qty }}" @if($isLockedStatus || $hasLockedItem) readonly @endif></td>
                  <td><input type="number" step="0.01" name="items[{{ $i }}][rate]" class="form-control rate" value="{{ $rate }}" @if($isLockedStatus || $hasLockedItem) readonly @endif></td>
                  <td><input type="number" step="0.01" name="items[{{ $i }}][tax_rate]" class="form-control tax" value="{{ $taxRate }}" @if($isLockedStatus || $hasLockedItem) readonly @endif></td>
                  <td><input type="number" step="0.01" name="items[{{ $i }}][total]" class="form-control total" value="{{ $total }}" @if($isLockedStatus || $hasLockedItem) readonly @endif></td>
                  <td>
                    @if(auth()->user() && auth()->user()->hasRole('retailer'))
                    <input type="hidden" name="items[{{ $i }}][status]" value="{{ $selectedStatus ?: 'pending' }}">
                    <span class="badge badge-secondary">{{ ucfirst($selectedStatus ?: 'pending') }}</span>
                    @else
                    @if($isLockedStatus || $hasLockedItem)
                    <input type="hidden" name="items[{{ $i }}][status]" value="{{ $selectedStatus }}">
                    <span class="status-badge-locked status-badge-{{ $selectedStatus }}">
                      {{ ucfirst($selectedStatus) }}
                    </span>
                    @else
                    <select name="items[{{ $i }}][status]" class="form-control status-select" data-order-item-id="{{ $isArr ? ($it['id'] ?? '') : ($it->id ?? '') }}" data-old-status="{{ $isArr ? ($it['status'] ?? 'pending') : ($it->status ?? 'pending') }}">
                      @foreach(['pending','draft','confirmed','shipped','delivered','cancelled'] as $st)
                      <option value="{{ $st }}" {{ ($selectedStatus == $st) ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                      @endforeach
                    </select>
                    @endif
                    @endif
                  </td>
                  <td>
                    @if(!$isLockedStatus && !$hasLockedItem && auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']))
                    <button type="button" class="btn btn-sm btn-danger remove-item"><i class="fas fa-trash"></i></button>
                    @else
                    <button type="button" class="btn btn-sm btn-danger remove-item" disabled><i class="fas fa-trash"></i></button>
                    @endif
                  </td>
                </tr>
                @endforeach

                @if($rowItems->isEmpty())
                <tr>
                  <td>
                    @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']))
                    @if($hasLockedItem)
                    <input type="text" class="form-control" value="" readonly>
                    <input type="hidden" name="items[0][article_number]" value="">
                    <input type="hidden" name="items[0][item_id]" class="item-id-hidden" value="">
                    @else
                    <select name="items[0][article_number]" class="form-control article-select">
                      <option value="">--</option>
                      @foreach($items as $itm)
                      <option value="{{ $itm->article_number }}"
                        data-id="{{ $itm->id }}"
                        data-rate="{{ $itm->price }}"
                        data-tax="{{ $itm->tax_percent ?? 0 }}"
                        data-desc="{{ $itm->description ?? '' }}">
                        {{ $itm->article_number }}
                      </option>
                      @endforeach
                    </select>
                    <input type="hidden" name="items[0][item_id]" class="item-id-hidden" value="">
                    @endif
                    @else
                    <input type="text" class="form-control" value="" readonly>
                    <input type="hidden" name="items[0][article_number]" value="">
                    <input type="hidden" name="items[0][item_id]" class="item-id-hidden" value="">
                    @endif
                  </td>
                  <td>
                    <input type="text" name="items[0][item_name]" class="form-control item-name-input" value="" @if($hasLockedItem) readonly @endif>
                  </td>
                  <td>
                    @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']))
                    @if($hasLockedItem)
                    <input type="text" class="form-control color-read" readonly value="">
                    @else
                    <select name="items[0][color][]" class="form-control color-select select2">
                      <option value="">--</option>
                    </select>
                    @endif
                    @else
                    <input type="text" class="form-control color-read" readonly value="">
                    @endif
                  </td>
                  <td>
                    @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']))
                    @if($hasLockedItem)
                    <div class="form-control size-readonly-box" readonly></div>
                    @else
                    <select name="items[0][sizes][]" class="size-select d-none" multiple>
                      @foreach($sizesJson as $sz)
                      <option value="{{ $sz }}">{{ $sz }}</option>
                      @endforeach
                    </select>
                    <div class="size-chips-wrap">
                      @foreach($sizesJson as $sz)
                      <button type="button" class="size-chip" data-size="{{ $sz }}">{{ $sz }}</button>
                      @endforeach
                    </div>
                    <div class="size-qty-wrapper size-qty-panel" style="display:none;"></div>
                    @endif
                    @else
                    <div class="form-control size-readonly-box" readonly></div>
                    @endif
                  </td>
                  <td><input type="text" name="items[0][description]" class="form-control desc" @if($hasLockedItem) readonly @endif></td>
                  <td><input type="number" step="0.01" name="items[0][quantity]" class="form-control qty" value="0" @if($hasLockedItem) readonly @endif></td>
                  <td><input type="number" step="0.01" name="items[0][rate]" class="form-control rate" value="0" @if($hasLockedItem) readonly @endif></td>
                  <td><input type="number" step="0.01" name="items[0][tax_rate]" class="form-control tax" value="0" @if($hasLockedItem) readonly @endif></td>
                  <td><input type="number" step="0.01" name="items[0][total]" class="form-control total" value="0" @if($hasLockedItem) readonly @endif></td>
                  <td>
                    @if(auth()->user() && auth()->user()->hasRole('retailer'))
                    <input type="hidden" name="items[0][status]" value="pending">
                    <span class="badge badge-secondary">Pending</span>
                    @else
                    @if($hasLockedItem)
                    <input type="hidden" name="items[0][status]" value="pending">
                    <span class="badge badge-secondary">Pending</span>
                    @else
                    <select name="items[0][status]" class="form-control status-select">
                      @foreach(['pending','draft','confirmed','shipped','delivered','cancelled'] as $st)
                      <option value="{{ $st }}">{{ ucfirst($st) }}</option>
                      @endforeach
                    </select>
                    @endif
                    @endif
                  </td>
                  @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']))
                  @if(!$hasLockedItem)
                  <td><button type="button" class="btn btn-sm btn-danger remove-item"><i class="fas fa-trash"></i></button></td>
                  @else
                  <td><button type="button" class="btn btn-sm btn-danger remove-item" disabled><i class="fas fa-trash"></i></button></td>
                  @endif
                  @else
                  <td></td>
                  @endif
                </tr>
                @endif
              </tbody>
            </table>
            <div class="text-right mb-3">
              @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']) && !$hasLockedItem)
              <button type="button" id="addItem" class="btn-create">
                <i class="fas fa-plus"></i> Add Row
              </button>
              @endif
            </div>
          </div>

          {{-- ── Size Range Panel (dynamic from DB) ──────────────────── --}}
          <div id="sizeRangePanel" style="display:none">
            <div class="card bg-light mb-3">
              <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <h6 class="m-0">Size Range Entry</h6>
                <a href="{{ route('size.index') }}" target="_blank"
                  class="btn btn-xs btn-outline-secondary">
                  <i class="fas fa-cog"></i> Manage Sizes
                </a>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Product</label>
                      <select id="sr_item" class="form-control">
                        <option value="">-- Select Item --</option>
                        @foreach($items as $itm)
                        <option value="{{ $itm->id }}"
                          data-rate="{{ $itm->price }}"
                          data-tax="{{ $itm->tax_percent ?? 0 }}">
                          {{ $itm->name }}
                        </option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label>Size From</label>
                      <select id="sr_from" class="form-control">
                        @foreach($sizesJson as $sz)
                        <option value="{{ $sz }}">{{ $sz }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label>Size To</label>
                      <select id="sr_to" class="form-control">
                        @foreach($sizesJson as $sz)
                        <option value="{{ $sz }}">{{ $sz }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label>Sets</label>
                      <input type="number" id="sr_sets" class="form-control" value="1" min="1">
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label>MRP (₹/pc)</label>
                      <input type="number" id="sr_rate" class="form-control" value="0" step="0.01">
                    </div>
                  </div>
                </div>

                <div id="sr_chips" class="mb-2"></div>
                <div id="sr_summary" class="alert alert-info py-2 mb-2" style="display:none"></div>

                <div class="row text-center mb-3">
                  <div class="col-4">
                    <small class="text-muted d-block">Pcs / Set</small>
                    <strong id="sr_pcs_set">—</strong>
                  </div>
                  <div class="col-4">
                    <small class="text-muted d-block">Total Pcs</small>
                    <strong id="sr_total_pcs">—</strong>
                  </div>
                  <div class="col-4">
                    <small class="text-muted d-block">Amount</small>
                    <strong id="sr_amount">—</strong>
                  </div>
                </div>

                <button type="button" id="sr_add" class="btn btn-success btn-sm btn-block">
                  <i class="fas fa-plus"></i> Add to Order
                </button>
              </div>
            </div>
          </div>

          {{-- ── Totals ────────────────────────────────────────────────── --}}
          <div class="row">
            <div class="col-md-8"></div>
            <div class="col-md-4">
              <div class="card card-outline card-light">
                <div class="card-body p-2">
                  <div class="d-flex justify-content-between py-1">
                    <strong>Sub Total</strong>
                    <input type="text" name="subtotal" id="subtotal"
                      class="form-control form-control-sm w-50 text-right"
                      readonly value="{{ old('subtotal', $order->subtotal ?? 0) }}">
                  </div>
                  <div class="d-flex justify-content-between py-1">
                    <strong>Mark Down (%)</strong>
                    @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']) && !$hasLockedItem)
                    <input type="number" step="0.01" name="markdown" id="markdown"
                      class="form-control form-control-sm w-50 text-right"
                      value="{{ old('markdown', $order->markdown ?? 0) }}">
                    @else
                    <input type="number" step="0.01" name="markdown" id="markdown"
                      class="form-control form-control-sm w-50 text-right" readonly
                      value="{{ old('markdown', $order->markdown ?? 0) }}">
                    @endif
                  </div>
                  <div class="d-flex justify-content-between py-1">
                    <strong>Discount (%)</strong>
                    @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']) && !$hasLockedItem)
                    <input type="number" step="0.01" min="0" max="100" name="discount" id="discount"
                      class="form-control form-control-sm w-50 text-right"
                      value="{{ old('discount', $order->discount ?? 0) }}">
                    @else
                    <input type="number" step="0.01" min="0" max="100" name="discount" id="discount"
                      class="form-control form-control-sm w-50 text-right" readonly
                      value="{{ old('discount', $order->discount ?? 0) }}">
                    @endif
                  </div>
                  <div class="d-flex justify-content-between py-1">
                    <strong>Adjustment</strong>
                    @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']) && !$hasLockedItem)
                    <input type="number" step="0.01" name="adjustment" id="adjustment"
                      class="form-control form-control-sm w-50 text-right"
                      value="{{ old('adjustment', $order->adjustment ?? 0) }}">
                    @else
                    <input type="number" step="0.01" name="adjustment" id="adjustment"
                      class="form-control form-control-sm w-50 text-right" readonly
                      value="{{ old('adjustment', $order->adjustment ?? 0) }}">
                    @endif
                  </div>
                  <hr class="my-2">
                  <div class="d-flex justify-content-between py-1">
                    <strong>Grand Total</strong>
                    <input type="text" name="grand_total" id="grand_total"
                      class="form-control form-control-sm w-50 text-right font-weight-bold"
                      readonly value="{{ old('grand_total', $order->grand_total ?? 0) }}">
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- ── Terms / Notes / Status ───────────────────────────────── --}}
          <div class="row">
            <div class="form-group col-md-6">
              <label>Terms &amp; Conditions</label>
              <textarea name="terms" class="form-control" rows="2" @if($hasLockedItem) readonly @endif>{{ old('terms', $order->terms) }}</textarea>
            </div>
            <div class="form-group col-md-6">
              <label>Notes</label>
              <textarea name="notes" class="form-control" rows="2" @if($hasLockedItem) readonly @endif>{{ old('notes', $order->notes) }}</textarea>
            </div>
            <div class="form-group col-md-3">
              <label>Status</label>
              @if(auth()->user() && auth()->user()->hasRole('retailer'))
              <input type="hidden" name="status" value="{{ old('status', $order->status) }}">
              <div><span class="badge badge-secondary">{{ ucfirst(old('status', $order->status) ?? 'pending') }}</span></div>
              @else
              @if($hasLockedItem)
              <input type="hidden" name="status" value="{{ old('status', $order->status) }}">
              <div><span class="badge badge-secondary">{{ ucfirst(old('status', $order->status) ?? 'pending') }}</span></div>
              @else
              <select name="status" class="form-control">
                @foreach(['pending' => 'Pending', 'draft' => 'Draft', 'confirmed' => 'Confirmed', 'shipped' => 'Shipped', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled'] as $val => $label)
                <option value="{{ $val }}" {{ old('status', $order->status) == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
              </select>
              @endif
              @endif
            </div>
          </div>

        </div>
      </div>


      <div class="mt-2 mb-2 mr-3 text-right">
        <a href="{{ route('orders.index') }}" class="btn-cancel mr-2"><i class="fas fa-times"></i> Cancel</a>
        @if(!$hasLockedItem)
        <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Update Order</button>
        @else
        <button type="button" class="btn btn-secondary" disabled><i class="fas fa-lock"></i> Order Locked</button>
        @endif
      </div>

    </form>
  </div>
</section>
@if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']) && !$hasLockedItem)
<div class="variant-drawer-backdrop" id="variantDrawerBackdrop" aria-hidden="true">
  <div class="variant-drawer" role="dialog" aria-modal="true" aria-labelledby="variantDrawerTitle">
    <div class="variant-drawer-header">
      <div>
        <div class="variant-drawer-meta" id="variantDrawerItem">-</div>
      </div>
      <button type="button" class="variant-drawer-close" data-variant-close aria-label="Close">&times;</button>
    </div>
    <div class="variant-drawer-body">
      <span class="variant-drawer-label">Choose Sizes</span>
      <div class="variant-drawer-sizes" id="variantDrawerSizes"></div>
      <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="variant-drawer-label mb-0">Selected Size</span>
        <button type="button" class="btn btn-link btn-sm p-0 text-danger" id="variantClearAll">Clear All</button>
      </div>
      <div class="variant-selected-list" id="variantSelectedList"></div>
      <div class="variant-total-row">
        Total Quantity
        <span class="variant-total-badge" id="variantDrawerTotal"><strong>0</strong></span>
      </div>
    </div>
    <div class="variant-drawer-footer">
      <button type="button" class="btn-cancel flex-fill" data-variant-close>Cancel</button>
      <button type="button" class="btn-submit flex-fill" id="variantSaveBtn">Save Variants</button>
    </div>
  </div>
</div>
@endif
@endsection

@section('pageScript')
<style>
  .flash-warning {
    animation: flashWarn 0.6s ease;
  }
  @keyframes flashWarn {
    0%   { background-color: transparent; }
    30%  { background-color: #ffe0e0; }
    100% { background-color: transparent; }
  }
  #variantSaveBtn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
  .remove-item:disabled,
  .deleteButton:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
  .variant-drawer-size {
    background: #f0f0f0;
    color: #333;
    border: 2px solid #ccc;
    border-radius: 8px;
    padding: 6px 10px;
    margin: 4px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }
  .variant-drawer-size small {
    opacity: 0.75;
    font-size: 11px;
    font-weight: 600;
  }
  .variant-drawer-size.active {
    background: #7F53AC;
    color: #fff;
    border-color: #7F53AC;
    font-weight: 700;
    box-shadow: 0 2px 6px rgba(127,83,172,0.3);
  }
  .variant-drawer-size:hover:not(.active) {
    background: #e0d4f5;
    border-color: #7F53AC;
  }
  .status-badge-locked {
    padding: 5px 12px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
  }
  .status-badge-confirmed {
    background: #17a2b8;
    color: #fff;
  }
  .status-badge-shipped {
    background: #007bff;
    color: #fff;
  }
  .status-badge-delivered {
    background: #28a745;
    color: #fff;
  }
  .status-badge-cancelled {
    background: #dc3545;
    color: #fff;
  }
  .status-badge-pending {
    background: #ffc107;
    color: #212529;
  }
  .status-badge-draft {
    background: #6c757d;
    color: #fff;
  }
</style>
<script>
  $(function() {

    // ALL_SIZES comes from DB via controller → $sizesJson (array of label strings)
    var ALL_SIZES = @json($sizesJson);

    var ITEMS = @json($itemsJson);
    var COLORS = @json($colors);
    var IS_RETAILER = @json(optional(auth()->user())->hasRole('retailer') ?? false);
    var IS_SUPER_ADMIN = @json(optional(auth()->user())->hasRole(['super-admin', 'superadmin']) ?? false);
    var IS_DISTRIBUTOR = @json(optional(auth()->user())->hasRole('distributor') ?? false);
    var HAS_LOCKED_ITEM = @json($hasLockedItem ?? false);

    function itemByArticle(val) {
      return ITEMS.find(function(i) {
        return i.article_number == val || i.id == val;
      });
    }

    function rowIndex($row) {
      var name = $row.find('.qty').attr('name') || '';
      var match = name.match(/items\[(\d+)\]/);
      return match ? match[1] : 0;
    }

    function escapeHtml(value) {
      return String(value).replace(/[&<>"']/g, function(ch) {
        return ({
          '&': '&amp;',
          '<': '&lt;',
          '>': '&gt;',
          '"': '&quot;',
          "'": '&#039;'
        })[ch];
      });
    }

    function normalizeSelected(value) {
      if (!value) return [];
      return Array.isArray(value) ? value.map(String) : String(value).split(',').map(function(v) {
        return v.trim();
      });
    }

    function variantStockMap($row) {
      var stockMap = {};
      if (!$row) return stockMap;
      try {
        stockMap = JSON.parse($row.attr('data-stock-map') || '{}');
      } catch (e) {
        stockMap = {};
      }
      return stockMap && typeof stockMap === 'object' ? stockMap : {};
    }

    function variantSizeLabel(size, stockMap) {
      return String(size || '');
    }

    function colorOptions(colors, selected) {
      selected = normalizeSelected(selected);
      colors = (colors && colors.length) ? colors : [];

      return colors.map(function(c) {
        var id = String(c.id);
        var label = c.color_code ? c.color_code : c.name;
        return '<option value="' + escapeHtml(id) + '"' +
          (selected.indexOf(id) !== -1 ? ' selected' : '') +
          '>' + escapeHtml(label) + '</option>';
      }).join('');
    }

    function populateColorSelect($row, colors, sel) {
      var $cs = $row.find('.color-select');
      sel = sel || ($cs.length ? $cs.val() : []) || [];
      var availableIds = [];
      if (colors && colors.length) {
        availableIds = colors.map(function(c) {
          return String(c.id);
        });
      }
      var selArr = normalizeSelected(sel).filter(function(id) {
        return availableIds.indexOf(id) !== -1;
      });
      if ($cs.length) {
        if ($cs.hasClass('select2-hidden-accessible')) $cs.select2('destroy');
        $cs.html(colorOptions(colors, selArr));
        $cs.select2({
          placeholder: 'Colors',
          width: '100%'
        });
      } else {
        // no select present (non-super-admin) — show readonly text and hidden inputs
        var names = selArr.map(function(id) {
          var c = COLORS.find(function(x) {
            return String(x.id) == String(id);
          });
          return c ? (c.color_code ? c.color_code : c.name) : '';
        }).filter(Boolean).join(', ');
        var $rd = $row.find('.color-read');
        if ($rd.length) $rd.val(names);
        var $cell = $row.find('td').has('.color-read');
        if (!$cell.length) $cell = $row.find('td').has('.color-select');
        if (!$cell.length) $cell = $row.find('td').eq(2);
        if ($cell.length) {
          $cell.find('input[type=hidden][name$="[color][]"]').remove();
          selArr.forEach(function(id) {
            $cell.append('<input type="hidden" name="items[' + rowIndex($row) + '][color][]" value="' + escapeHtml(id) + '">');
          });
        }
      }
    }

    // Returns the number of colors selected in a row (minimum 1 so qty is never zeroed)
    function colorCount($row) {
      var $cs = $row.find('.color-select');
      if ($cs.length) {
        var val = $cs.val() || [];
        return Math.max(1, val.length);
      }
      // non-super-admin: count hidden color inputs
      var hidden = $row.find('input[type=hidden][name$="[color][]"]').length;
      return Math.max(1, hidden);
    }

    function rebuildSizePanel($row) {
      var idx = rowIndex($row);
      var $select = $row.find('.size-select');
      var $panel = $row.find('.size-qty-wrapper');
      var selected = $select.val() || [];

      // Sync chip active states
      $row.find('.size-chip').each(function() {
        var s = $(this).data('size');
        $(this).toggleClass('active', selected.indexOf(String(s)) !== -1);
      });

      if (!selected.length) {
        $panel.hide().html('');
        $row.find('.qty').val(0);
        recalc();
        return;
      }

      // Preserve existing qty values
      var oldQtys = {};
      $panel.find('.size-qty').each(function() {
        var sz = $(this).closest('.size-qty-row').data('size');
        oldQtys[sz] = $(this).val();
      });

      var html = selected.map(function(sz) {
        var q = oldQtys[sz] || 0;
        return '<div class="size-qty-row" data-size="' + escapeHtml(sz) + '">' +
          '<div class="input-group input-group-sm mb-1">' +
          '<div class="input-group-prepend"><span class="input-group-text">' + escapeHtml(sz) + '</span></div>' +
          '<div class="size-stepper">' +
          (IS_SUPER_ADMIN && !HAS_LOCKED_ITEM ? '<button type="button" class="stepper-btn minus">−</button>' : '') +
          (IS_SUPER_ADMIN && !HAS_LOCKED_ITEM ? '<input type="text" step="1" min="0" name="items[' + idx + '][size_quantities][' + escapeHtml(sz) + ']" class="size-qty" value="' + escapeHtml(q) + '" readonly>' : '<input type="text" name="items[' + idx + '][size_quantities][' + escapeHtml(sz) + ']" class="size-qty" value="' + escapeHtml(q) + '" readonly>') +
          (IS_SUPER_ADMIN && !HAS_LOCKED_ITEM ? '<button type="button" class="stepper-btn plus">+</button>' : '') +
          '</div></div></div>';
      }).join('');

      if (IS_SUPER_ADMIN) {
        html += '<div class="size-qty-total"><small>Total</small><span class="total-qty-badge">0</span></div>';
      }

      $panel.html(html).show();
      updateTotalQtyBadge($row);
      updateRowQty($row);
      recalc();
    }

    function updateTotalQtyBadge($row) {
      var tot = 0;
      $row.find('.size-qty').each(function() {
        tot += parseFloat($(this).val()) || 0;
      });
      var colors = colorCount($row);
      $row.find('.total-qty-badge').text(tot + ' × ' + colors + ' colors = ' + (tot * colors));
    }

    // size-chip click
    $(document).on('click', '.size-chip', function() {
      if (!IS_SUPER_ADMIN || HAS_LOCKED_ITEM) return;
      var $chip = $(this);
      var $row = $chip.closest('tr');
      var $sel = $row.find('.size-select');
      var size = String($chip.data('size'));
      var cur = $sel.val() || [];

      if ($chip.hasClass('active')) {
        cur = cur.filter(function(s) {
          return s !== size;
        });
      } else {
        cur.push(size);
      }
      $sel.val(cur);
      rebuildSizePanel($row);
    });

    // stepper handlers
    $(document).on('click', '.stepper-btn', function() {
      if (HAS_LOCKED_ITEM) return;
      var $btn = $(this);
      var $input = $btn.siblings('input.size-qty');
      var val = parseFloat($input.val()) || 0;
      $input.val($btn.hasClass('plus') ? val + 1 : Math.max(0, val - 1));
      var $row = $btn.closest('tr');
      updateTotalQtyBadge($row);
      updateRowQty($row);
      recalc();
    });
    $(document).on('input', '.size-qty', function() {
      if (HAS_LOCKED_ITEM) return;
      var $row = $(this).closest('tr');
      updateTotalQtyBadge($row);
      updateRowQty($row);
      recalc();
    });

    function syncSizeQtyInputs($row) {
      if (HAS_LOCKED_ITEM) return;
      var idx = rowIndex($row);
      var sizes = $row.find('.size-select').val() || [];
      var oldValues = {};

      $row.find('.size-qty').each(function() {
        oldValues[$(this).closest('.size-qty-row').data('size')] = $(this).val();
      });

      var html = sizes.map(function(size) {
        var safeSize = escapeHtml(size);
        var value = oldValues[size] || '';
        return '<div class="input-group input-group-sm mb-1 size-qty-row" data-size="' + safeSize + '">' +
          '<div class="input-group-prepend"><span class="input-group-text">' + safeSize + '</span></div>' +
          '<input type="number" step="1" min="0" name="items[' + idx + '][size_quantities][' + safeSize + ']" class="form-control size-qty" value="' + escapeHtml(value) + '" placeholder="Qty">' +
          '</div>';
      }).join('');

      $row.find('.size-qty-wrapper').html(html);
      updateRowQty($row);
    }

    function updateRowQty($row) {
      if (!$row.find('.size-qty').length) return;
      var sizeSum = 0;
      $row.find('.size-qty').each(function() {
        sizeSum += parseFloat($(this).val()) || 0;
      });
      var colors = colorCount($row);
      $row.find('.qty').val(sizeSum * colors);
    }

    // ── Recalculate totals ───────────────────────────────────────────────────
    function recalc() {
      var subtotal = 0;
      $('#itemsTable tbody tr').each(function() {
        updateRowQty($(this));
        var qty = parseFloat($(this).find('.qty').val()) || 0;
        var rate = parseFloat($(this).find('.rate').val()) || 0;
        var tax = parseFloat($(this).find('.tax').val()) || 0;
        var fp = rate + (rate * tax / 100);
        var tot = fp * qty;
        $(this).find('.total').val(tot.toFixed(2));
        subtotal += tot;
      });
      $('#subtotal').val(subtotal.toFixed(2));
      var markdownPercent = parseFloat($('#markdown').val()) || 0;
      var discountPercent = parseFloat($('#discount').val()) || 0;
      var discountAmount = subtotal * discountPercent / 100;
      var grand = subtotal -
        (subtotal * markdownPercent / 100) -
        discountAmount +
        (parseFloat($('#adjustment').val()) || 0);
      $('#grand_total').val(grand.toFixed(2));
    }

    $(document).on('input', '.size-qty,.rate,.tax', recalc);
    // Color change → re-run qty × color multiplication and update totals
    $(document).on('change', '.color-select', function() {
      if (HAS_LOCKED_ITEM) return;
      var $row = $(this).closest('tr');
      var $cs = $(this);

      var vals = $cs.val() || [];
      if (vals.length > 1) {
        vals = [vals[0]];
        $cs.val(vals);
        try {
          $cs.trigger('change.select2');
        } catch (e) {}
      }

      // Clear sizes + reset stock data
      $row.find('.size-select').val([]);
      $row.find('.size-qty-wrapper').hide().html('');
      $row.attr('data-available-sizes', '[]');
      $row.attr('data-stock-map', '{}');

      // Reset variant summary cell
      $row.find('.variant-table-summary').html(
        '<span class="variant-empty-text">No Variants Added</span>' +
        '<button type="button" class="variant-edit-btn">' +
        '<i class="fas fa-pencil-alt mr-1"></i>Add Variants</button>'
      );

      var itemId = String($row.find('.item-id-hidden').val() || '').trim();
      var colorId = vals.length ? String(vals[0]) : null;

      if (!itemId || !colorId) {
        updateRowQty($row);
        recalc();
        return;
      }

      $.ajax({
        url: '/api/item-variants/sizes-by-color',
        type: 'GET',
        dataType: 'json',
        data: {
          item_id: itemId,
          color_id: colorId
        },
        success: function(res) {
          var sizes = (res && res.sizes) ? res.sizes : [];

          var availableSizes = sizes.map(function(s) {
            return String(s.label);
          });
          var stockMap = {};
          sizes.forEach(function(s) {
            stockMap[String(s.label)] = parseInt(s.available_qty, 10) || 0;
          });

          $row.attr('data-available-sizes', JSON.stringify(availableSizes));
          $row.attr('data-stock-map', JSON.stringify(stockMap));

          var $sz = $row.find('.size-select');
          $sz.html(availableSizes.map(function(s) {
            return '<option value="' + esc(s) + '">' + esc(variantSizeLabel(s, stockMap)) + '</option>';
          }).join(''));

          if (IS_SUPER_ADMIN && !HAS_LOCKED_ITEM) {
            $row.find('.size-chips-wrap').html(
              availableSizes.map(function(s) {
                return '<button type="button" class="size-chip" data-size="' + esc(s) + '">' + esc(s) + '</button>';
              }).join('')
            );
          }

          if (activeVariantRow && activeVariantRow.get(0) === $row.get(0)) {
            drawerSizes = [];
            drawerQtys = {};
            renderVariantDrawer();
          }
        },
        error: function() {
          $row.attr('data-available-sizes', '[]');
          $row.attr('data-stock-map', '{}');
        }
      });

      updateRowQty($row);
      recalc();
    });
    $(document).on('change', '.size-select', function() {
      if (HAS_LOCKED_ITEM) return;
      var $row = $(this).closest('tr');
      if (IS_SUPER_ADMIN) {
        rebuildSizePanel($row);
      } else {
        syncSizeQtyInputs($row);
      }
      recalc();
    });
    $('#markdown,#discount,#adjustment').on('input', recalc);

    // ── Auto-fill row when item selected ─────────────────────────────────────
    $(document).on('change', '.article-select', function() {
      if (HAS_LOCKED_ITEM) return;
      var $row = $(this).closest('tr');
      var id = $(this).val();
      if (!id) return;

      var found = itemByArticle(id);
      if (!found) {
        var $opt = $(this).find('option:selected');
        found = {
          id: $opt.data('id') || null,
          article_number: $opt.text().trim(),
          name: $opt.data('name') || '',
          rate: parseFloat($opt.data('rate')) || 0,
          tax: parseFloat($opt.data('tax')) || 0,
          desc: $opt.data('desc') || ''
        };
      }

      $row.find('.item-id-hidden').val(found.id || '');
      $row.find('.item-name-input').val(found.name || found.article_number || '');
      $row.find('.rate').val(found.rate || 0);
      $row.find('.tax').val(found.tax || 0);
      if (!$row.find('.desc').val()) {
        $row.find('.desc').val(found.desc || '');
      }
      // populate color select from the selected article's colors
      populateColorSelect($row, found.colors || [], []);

      // Keep sizes empty until a color is selected; the color change handler loads only sizes available for that color.
      $row.attr('data-available-sizes', '[]');
      $row.attr('data-stock-map', '{}');
      var sizeChoices = [];
      var sizeOpts = '';
      var $size = $row.find('.size-select');
      if ($size.hasClass('select2-hidden-accessible')) {
        $size.select2('destroy');
      }
      $size.html(sizeOpts);
      if (IS_SUPER_ADMIN && !HAS_LOCKED_ITEM) {
        var $chips = $row.find('.size-chips-wrap');
        $chips.empty();
        $row.find('.size-qty-wrapper').hide().html('');
      } else {
        $size.select2({
          placeholder: 'Sizes',
          width: '100%'
        });
        syncSizeQtyInputs($row);
      }

      if (($row.find('.color-select').val() || []).length) {
        try {
          $row.find('.color-select').trigger('change');
        } catch (e) {}
      } else {
        $row.find('.size-select').val([]);
        $row.find('.qty').val(0);
        recalc();
      }
    });

    // ── Build a new row ───────────────────────────────────────────────────────
    function buildRow(idx, it) {
      it = it || {};
      var opts = '<option value="">--</option>' + ITEMS.map(function(m) {
        return '<option value="' + (m.article_number || '') + '"' +
          ' data-id="' + (m.id || '') + '"' +
          ' data-rate="' + (m.rate || 0) + '"' +
          ' data-tax="' + (m.tax || 0) + '"' +
          ' data-desc="' + ((m.desc || '').replace(/"/g, '&quot;')) + '"' +
          (it.item_id == m.id ? ' selected' : '') +
          '>' + (m.article_number || '') + '</option>';
      }).join('');

      // color select options (article colors only; no global fallback)
      var colorOpts = colorOptions(it.colors || [], it.color || it.color_id || []);

      // size options (use ALL_SIZES for chips; per-item selected sizes may be prefilled)
      var sizeOpts = (it.sizes && it.sizes.length) ? it.sizes.map(function(s) {
        return '<option value="' + s + '" selected>' + s + '</option>';
      }).join('') : '';
      var sizeChips = '';
      if (IS_SUPER_ADMIN && !HAS_LOCKED_ITEM) {
        sizeChips = ALL_SIZES.map(function(s) {
          return '<button type="button" class="size-chip" data-size="' + escapeHtml(s) + '">' + escapeHtml(s) + '</button>';
        }).join('');
      }
      var sizeOptsFull = ALL_SIZES.map(function(s) {
        return '<option value="' + escapeHtml(s) + '">' + escapeHtml(s) + '</option>';
      }).join('');

      var articleCell = '';
      if (IS_SUPER_ADMIN) {
        articleCell = '<td>' + '<select name="items[' + idx + '][article_number]" class="form-control article-select">' + opts + '</select>' +
          '<input type="hidden" name="items[' + idx + '][item_id]" class="item-id-hidden" value="' + (it.item_id || '') + '">' +
          '<input type="hidden" name="items[' + idx + '][order_item_id]" class="order-item-id-hidden" value="' + (it.id || '') + '">' +
          '</td>';
      } else {
        var artVal = (function() {
          var f = ITEMS.find(function(i) {
            return i.id == it.item_id;
          });
          return f ? f.article_number : (it.article_number || '');
        })();
        articleCell = '<td>' + '<input type="text" class="form-control" value="' + escapeHtml(artVal) + '" readonly>' +
          '<input type="hidden" name="items[' + idx + '][article_number]" value="' + escapeHtml(artVal) + '">' +
          '<input type="hidden" name="items[' + idx + '][item_id]" class="item-id-hidden" value="' + (it.item_id || '') + '">' +
          '<input type="hidden" name="items[' + idx + '][order_item_id]" class="order-item-id-hidden" value="' + (it.id || '') + '">' +
          '</td>';
      }
      return '<tr>' +
        '<td><input type="text" name="items[' + idx + '][item_name]" class="form-control item-name-input" value="' + (it.item_name || '') + '" readonly></td>' +
        (IS_SUPER_ADMIN ? '<td><select name="items[' + idx + '][color][]" class="form-control color-select" multiple>' + colorOpts + '</select></td>' : (function() {
          var sel = normalizeSelected(it.color || it.color_id || []);
          var names = sel.map(function(id) {
            var m = (COLORS || []).find(function(c) {
              return String(c.id) == String(id);
            });
            return m ? (m.color_code || m.name) : '';
          }).filter(Boolean).join(', ');
          var hidden = sel.map(function(id) {
            return '<input type="hidden" name="items[' + idx + '][color][]" value="' + escapeHtml(id) + '">';
          }).join('');
          return '<td>' + '<input type="text" class="form-control color-read" readonly value="' + escapeHtml(names) + '">' + hidden + '</td>';
        })()) +
        '<td>' +
        '<select name="items[' + idx + '][sizes][]" class="size-select d-none" multiple>' + sizeOptsFull + '</select>' +
        '<div class="size-chips-wrap">' + sizeChips + '</div>' +
        (IS_SUPER_ADMIN ? '<div class="size-qty-wrapper size-qty-panel" style="display:none;"></div>' : '<div class="form-control size-readonly-box" readonly>' + (it.sizes ? normalizeSelected(it.sizes).join(', ') : '') + '</div>') +
        '</td>' +
        '<td><input type="text"   name="items[' + idx + '][description]" class="form-control desc"        value="' + (it.description || '') + '" readonly></td>' +
        '<td><input type="number" step="0.01" name="items[' + idx + '][quantity]"    class="form-control qty"         value="' + (it.quantity || 0) + '" readonly></td>' +
        '<td><input type="number" step="0.01" name="items[' + idx + '][rate]"        class="form-control rate"        value="' + (it.rate || 0) + '" readonly></td>' +
        '<td><input type="number" step="0.01" name="items[' + idx + '][tax_rate]"    class="form-control tax"         value="' + (it.tax_rate || 0) + '" readonly></td>' +
        '<td><input type="number" step="0.01" name="items[' + idx + '][total]"       class="form-control total"       value="' + (it.total || 0) + '" readonly></td>' +
        '<td>' +
        '<select name="items[' + idx + '][status]" class="form-control status-select">' + ['pending', 'draft', 'confirmed', 'shipped', 'delivered', 'cancelled'].map(function(s) {
          return '<option value="' + s + '"' + ((it.status && it.status == s) ? ' selected' : '') + '>' + (s.charAt(0).toUpperCase() + s.slice(1)) + '</option>';
        }).join('') +
        '</select>' +
        '</td>' +
        (IS_SUPER_ADMIN ? '<td><button type="button" class="btn btn-sm btn-danger remove-item"><i class="fas fa-trash"></i></button></td>' : '<td></td>') +
        '</tr>';
    }

    var rowCounter = $('#itemsTable tbody tr').length;
    // replace selects with hidden fields so the server receives a controlled value.
    if (IS_RETAILER || HAS_LOCKED_ITEM) {
      $('#itemsTable tbody tr').each(function() {
        var $s = $(this).find('.status-select');
        if ($s.length) {
          var name = $s.attr('name');
          var val = $s.val() || 'pending';
          $s.after('<input type="hidden" name="' + name + '" value="' + val + '">');
          $s.remove();
        }
      });
    }

    /* ── Remove Row (last remaining row can never be deleted) ─────────────── */
    function updateRemoveButtonsState() {
      var onlyOneRow = $('#itemsTable tbody tr').length <= 1;
      $('#itemsTable tbody .remove-item')
        .prop('disabled', onlyOneRow || HAS_LOCKED_ITEM)
        .toggleClass('disabled', onlyOneRow || HAS_LOCKED_ITEM);
    }

    $(document).on('click', '.remove-item', function() {
      if (HAS_LOCKED_ITEM || $('#itemsTable tbody tr').length <= 1) {
        return;
      }
      $(this).closest('tr').remove();
      updateRemoveButtonsState();
      recalc();
    });

    $('#addItem').on('click', function() {
      if (HAS_LOCKED_ITEM || !IS_SUPER_ADMIN) {
        alert('Not allowed');
        return;
      }
      $('#itemsTable tbody').append(buildRow(rowCounter));
      // initialize Select2 on newly appended row
      var $new = $('#itemsTable tbody tr:last');
      $new.find('.color-select').select2({
        placeholder: 'Colors',
        width: '100%'
      });
      $new.find('.article-select').select2({
        placeholder: 'Article',
        width: '100%'
      });
      // initialize size chips / panel
      if (IS_SUPER_ADMIN && !HAS_LOCKED_ITEM) {
        $new.find('.size-chips-wrap').html(ALL_SIZES.map(function(s) {
          return '<button type="button" class="size-chip" data-size="' + escapeHtml(s) + '">' + escapeHtml(s) + '</button>';
        }).join(''));
        rebuildSizePanel($new);
      }
      rowCounter++;
      recalc();
      updateRemoveButtonsState();
      if (IS_RETAILER || HAS_LOCKED_ITEM) {
        var $last = $('#itemsTable tbody tr:last');
        var $s = $last.find('.status-select');
        if ($s.length) {
          var name = $s.attr('name');
          var val = $s.val() || 'pending';
          $s.after('<input type="hidden" name="' + name + '" value="' + val + '">');
          // Add visible badge for retailer rows
          $s.next('input[type=hidden]').after('<span class="badge badge-secondary">' + (val.charAt(0).toUpperCase() + val.slice(1)) + '</span>');
          $s.remove();
        }
      }
    });

    // ── Customer → address auto-fill (fetch from server)
    $('#customer_id').on('change', function() {
      var id = $(this).val();
      console.log('[orders.edit] customer changed ->', id);
      if (!id) {
        $('#billing_address').val('');
        $('#shipping_address').val('');
        return;
      }

      var customerUrl = "{{ url('customer') }}"; // absolute base url

      fetch(customerUrl + '/' + id)
        .then(function(res) {
          if (!res.ok) throw new Error('Network response was not ok (' + res.status + ')');
          return res.json();
        })
        .then(function(data) {
          $('#billing_address').val(data.billing_address || '');
          $('#shipping_address').val(data.shipping_address || '');
        })
        .catch(function(err) {
          console.error('Failed to fetch customer addresses', err);
          $('#billing_address').val('');
          $('#shipping_address').val('');
        });
    });

    // ── Mode toggle ──────────────────────────────────────────────────────────
    // $('#modeNormal').on('click', function () {
    //   $(this).addClass('active');
    //   $('#modeSizeRange').removeClass('active');
    //   $('#sizeRangePanel').hide();
    //   $('#normalTable').show();
    // });

    // $('#modeSizeRange').on('click', function () {
    //   $(this).addClass('active');
    //   $('#modeNormal').removeClass('active');
    //   $('#sizeRangePanel').show();
    //   $('#normalTable').hide();
    //   srRecalc();
    // });

    // ── Size Range helpers ───────────────────────────────────────────────────
    // Index-based: works with any label type (XL, 2XL, 32, M …)
    function sizesInRange(from, to) {
      var fi = ALL_SIZES.indexOf(from);
      var ti = ALL_SIZES.indexOf(to);
      if (fi === -1 || ti === -1 || ti < fi) return [];
      return ALL_SIZES.slice(fi, ti + 1);
    }

    function srRecalc() {
      var from = $('#sr_from').val();
      var to = $('#sr_to').val();
      var sets = parseInt($('#sr_sets').val()) || 1;
      var rate = parseFloat($('#sr_rate').val()) || 0;

      var sizes = sizesInRange(from, to);
      var pcsSet = sizes.length;
      var totalPcs = pcsSet * sets;
      var amount = totalPcs * rate;

      $('#sr_chips').html(sizes.map(function(s) {
        return '<span class="badge badge-primary mr-1 mb-1">' + s + '</span>';
      }).join(''));

      if (sizes.length) {
        $('#sr_summary').show().text(
          'Sizes ' + from + ' → ' + to +
          ' = [' + sizes.join(', ') + '] → ' +
          pcsSet + ' pcs/set × ' + sets + ' sets = ' +
          totalPcs + ' total pcs × ₹' + rate.toFixed(0) +
          ' = ₹' + amount.toLocaleString('en-IN')
        );
      } else {
        $('#sr_summary').hide();
      }

      $('#sr_pcs_set').text(pcsSet || '—');
      $('#sr_total_pcs').text(totalPcs || '—');
      $('#sr_amount').text(amount ? '₹' + amount.toLocaleString('en-IN') : '—');
    }

    $('#sr_from,#sr_to,#sr_sets,#sr_rate').on('change input', srRecalc);

    $('#sr_item').on('change', function() {
      var $opt = $(this).find('option:selected');
      $('#sr_rate').val(parseFloat($opt.data('rate')) || 0);
      srRecalc();
    });

    $('#sr_add').on('click', function() {
      if (!IS_SUPER_ADMIN || HAS_LOCKED_ITEM) {
        alert('Not allowed');
        return;
      }
      var $opt = $('#sr_item').find('option:selected');
      var itemId = $('#sr_item').val();
      if (!itemId) {
        alert('Please select a product first.');
        return;
      }

      var itemName = $opt.text().trim();
      var from = $('#sr_from').val();
      var to = $('#sr_to').val();
      var sets = parseInt($('#sr_sets').val()) || 1;
      var rate = parseFloat($('#sr_rate').val()) || 0;
      var taxRate = parseFloat($opt.data('tax')) || 0;
      var sizes = sizesInRange(from, to);

      if (!sizes.length) {
        alert('No valid sizes in that range. Please check Size From / Size To.');
        return;
      }

      var totalPcs = sizes.length * sets;
      var desc = 'Sizes ' + from + '-' + to + ' (' + sizes.join(', ') + ') × ' + sets + ' sets';

      $('#modeNormal').trigger('click');

      var idx = rowCounter++;

      $('#itemsTable tbody').append(buildRow(idx, {
        item_id: itemId,
        item_name: itemName,
        description: desc,
        quantity: totalPcs,
        rate: rate,
        tax_rate: taxRate,
      }));

      var $tr = $('#itemsTable tbody tr:last');
      if ($.fn.select2) {
        $tr.find('.article-select').select2({
          placeholder: 'Article',
          width: '100%'
        });
      }
      $tr.find('.item-name-hidden').val(itemName);
      $tr.find('.item-select').val(itemId);
      // set article-select value (find article_number from ITEMS by id)
      var foundArticle = (function() {
        var f = ITEMS.find(function(x) {
          return x.id == itemId;
        });
        return f ? f.article_number : '';
      })();
      if (foundArticle) {
        $tr.find('.article-select').val(foundArticle).trigger('change');
      }
      $tr.find('.size-select').val(sizes).trigger('change');
      $tr.find('.size-qty').val(sets);
      $tr.append('<input type="hidden" name="items[' + idx + '][size_from]" value="' + from + '">');
      $tr.append('<input type="hidden" name="items[' + idx + '][size_to]"   value="' + to + '">');
      $tr.append('<input type="hidden" name="items[' + idx + '][sets]"      value="' + sets + '">');

      recalc();
      updateRemoveButtonsState();
    });

    var activeVariantRow = null;
    var drawerSizes = [];
    var drawerQtys = {};

    function variantEscape(value) {
      return String(value).replace(/[&<>"']/g, function(ch) {
        return ({
          '&': '&amp;',
          '<': '&lt;',
          '>': '&gt;',
          '"': '&quot;',
          "'": '&#039;'
        })[ch];
      });
    }

    function variantRowLabel($row) {
      var article = $row.find('.article-select').val() || $row.find('input[name$="[article_number]"]').val() || '';
      var item = $row.find('.item-name-input').val() || 'Selected item';
      return article ? item + ' (' + article + ')' : item;
    }

   function variantSizeOptions($row) {
      if (!$row) return [];
      var raw = $row.attr('data-available-sizes');
      if (!raw) return [];
      try {
        var arr = JSON.parse(raw);
        return Array.isArray(arr) ? arr.map(String).filter(Boolean) : [];
      } catch (e) {
        return [];
      }
    }

    function variantSelectedSizes($row) {
      return ($row.find('.size-select').val() || []).map(String);
    }

    function variantQtyMap($row) {
      var qtys = {};
      $row.find('.size-qty').each(function() {
        var size = $(this).closest('[data-size]').data('size');
        if (size !== undefined) {
          qtys[String(size)] = parseFloat($(this).val()) || 0;
        }
      });
      return qtys;
    }

    function preloadRowAvailableSizes($row) {
      var itemId = String($row.find('.item-id-hidden').val() || '').trim();
      var colorVal = $row.find('.color-select').val() || [];
      var colorId = colorVal.length ? String(colorVal[0]) : '';

      if (!itemId || !colorId) {
        return;
      }

      $.ajax({
        url: '/api/item-variants/sizes-by-color',
        type: 'GET',
        dataType: 'json',
        data: { item_id: itemId, color_id: colorId },
        success: function(res) {
          var sizes = (res && res.sizes) ? res.sizes : [];
          var availableSizes = sizes.map(function(s) { return String(s.label); });
          var stockMap = {};
          sizes.forEach(function(s) {
            stockMap[String(s.label)] = parseInt(s.available_qty, 10) || 0;
          });

          // Never let a size that's already saved on this row disappear from
          // the drawer just because it's missing from the stock-list response.
          var savedSizes = ($row.find('.size-select').val() || []).map(String);
          savedSizes.forEach(function(sz) {
            if (availableSizes.indexOf(sz) === -1) availableSizes.push(sz);
          });

          $row.attr('data-available-sizes', JSON.stringify(availableSizes));
          $row.attr('data-stock-map', JSON.stringify(stockMap));
        },
        error: function() {
          var savedSizes = ($row.find('.size-select').val() || []).map(String);
          $row.attr('data-available-sizes', JSON.stringify(savedSizes));
          $row.attr('data-stock-map', '{}');
        }
      });
    }

    function refreshVariantCell($row) {
      if (!IS_SUPER_ADMIN) return;

      var $cell = $row.find('td').has('.size-select').first();
      if (!$cell.length) return;

      var sizes = variantSelectedSizes($row);
      var qtys = variantQtyMap($row);
      var colors = colorCount($row);
      var totalQty = sizes.reduce(function(sum, size) {
        return sum + (parseFloat(qtys[size]) || 0);
      }, 0) * colors;
      var chips = sizes.map(function(size) {
        var qty = qtys[size] || 0;
        return '<span class="variant-mini-chip">' + variantEscape(size) + ' x ' + variantEscape(qty) + '</span>';
      }).join('');
      var summary = sizes.length ?
        '<span class="variant-count-pill">' + sizes.length + ' Variants Added</span><div class="variant-chip-list">' + chips + '</div>' :
        '<span class="variant-empty-text">No Variants Added</span>';
      var buttonText = sizes.length ? 'Edit Variants' : 'Add Variants';

      if ($row.find('.size-select').hasClass('select2-hidden-accessible')) {
        $row.find('.size-select').select2('destroy');
      }
      $row.find('.size-select').addClass('d-none').hide();
      $row.find('.size-chips-wrap,.size-qty-wrapper').hide();
      $cell.find('.variant-table-summary').remove();
      $cell.prepend(
        '<div class="variant-table-summary">' + summary +
        '<button type="button" class="variant-edit-btn"><i class="fas fa-pencil-alt mr-1"></i>' + buttonText + '</button></div>'
      );
    }

    function refreshAllVariantCells() {
      $('#itemsTable tbody tr').each(function() {
        refreshVariantCell($(this));
      });
    }

    function renderVariantDrawer() {
      var stockMap = variantStockMap(activeVariantRow);
      var hasStockMap = Object.keys(stockMap).length > 0;

      $('#variantDrawerSizes').html(
        variantSizeOptions(activeVariantRow).map(function(size) {
          var active = drawerSizes.indexOf(String(size)) !== -1 ? ' active' : '';
          return '<button type="button" class="variant-drawer-size' + active + '"' +
            ' data-size="' + variantEscape(size) + '">' +
            '<span>' + variantEscape(size) + '</span>' +
            '</button>';
        }).join('') || '<div class="variant-size-empty text-muted small">No sizes available for selected color</div>'
      );

      // Read stockMap from activeVariantRow

      var hasStockWarning = false;

      // Render selected list (with stock-warning / stock-ok when stockMap has entry)
      $('#variantSelectedList').html(
        drawerSizes.map(function(size) {
          var qty = drawerQtys[size] || 1;
          var available = (stockMap && stockMap[size] !== undefined) ?
            parseInt(stockMap[size], 10) :
            null;

          var stockLabelHtml = '';
          if (available !== null && !isNaN(available)) {
            if (qty >= available) {
              stockLabelHtml = '<div class="stock-warning text-danger" style="font-size:11px;">&#9888; Only ' +
                variantEscape(available) + ' units available</div>';
              hasStockWarning = true;
            } else {
              stockLabelHtml = '<div class="stock-ok text-success" style="font-size:11px;">&#10003; ' +
                variantEscape(available) + ' available</div>';
            }
          }

          return '<div class="variant-selected-row" data-size="' + variantEscape(size) + '">' +
            '<span class="variant-selected-name">' + variantEscape(size) + '</span>' +
            '<div class="size-stepper">' +
            '<button type="button" class="stepper-btn variant-drawer-minus">-</button>' +
            '<input type="text" class="size-qty variant-drawer-qty" value="' + variantEscape(qty) + '"' +
            ' data-size="' + variantEscape(size) + '" style="width:60px;text-align:center;">' +
            '<button type="button" class="stepper-btn variant-drawer-plus">+</button>' +
            '</div>' +
            stockLabelHtml +
            '</div>';
        }).join('') || '<div class="variant-selected-empty">Select sizes from above</div>'
      );

      // After rendering: check if ANY size drawerQtys[size] > stockMap[size]
      if (stockMap && Object.keys(stockMap).length) {
        var anyExceed = drawerSizes.some(function(size) {
          if (stockMap[size] === undefined) return false;
          return (parseFloat(drawerQtys[size]) || 0) > parseInt(stockMap[size], 10);
        });

        if (anyExceed) {
          $('#variantDrawerBackdrop').attr('data-has-stock-warning', 'true');
          $('#variantSaveBtn').prop('disabled', true).attr('title', 'Fix stock issues before saving');
        } else {
          $('#variantDrawerBackdrop').removeAttr('data-has-stock-warning');
          $('#variantSaveBtn').prop('disabled', false).removeAttr('title');
        }
      }

      // Total = sum of size qtys × colors selected on that row
      var colorMult = activeVariantRow ? colorCount(activeVariantRow) : 1;
      var total = drawerSizes.reduce(function(sum, size) {
        return sum + (parseFloat(drawerQtys[size]) || 0);
      }, 0) * colorMult;

      $('#variantDrawerTotal').text(total +
        (colorMult > 1 ? ' (' + (total / colorMult) + ' × ' + colorMult + ' colors)' : ''));
    }


    function openVariantDrawer($row) {
      if (!IS_SUPER_ADMIN || HAS_LOCKED_ITEM) return;
      activeVariantRow = $row;
      drawerSizes = variantSelectedSizes($row);
      drawerQtys = variantQtyMap($row);
      drawerSizes = drawerSizes.filter(function(size) {
        return variantSizeOptions(activeVariantRow).indexOf(String(size)) !== -1;
      });
      drawerSizes.forEach(function(size) {
        if (!drawerQtys[size]) drawerQtys[size] = 1;
      });
      $('#variantDrawerItem').text(variantRowLabel($row));
      renderVariantDrawer();
      $('#variantDrawerBackdrop').addClass('show').attr('aria-hidden', 'false');
      $('body').addClass('variant-drawer-open');
    }

    function closeVariantDrawer() {
      $('#variantDrawerBackdrop').removeClass('show').attr('aria-hidden', 'true');
      $('body').removeClass('variant-drawer-open');
      activeVariantRow = null;
      drawerSizes = [];
      drawerQtys = {};
    }

    function applyVariantDrawer() {
      if (!activeVariantRow || HAS_LOCKED_ITEM) return;
      activeVariantRow.find('.size-select').val(drawerSizes);
      rebuildSizePanel(activeVariantRow);
      drawerSizes.forEach(function(size) {
        activeVariantRow.find('[data-size]').filter(function() {
          return String($(this).data('size')) === String(size);
        }).find('.size-qty').val(drawerQtys[size] || 1);
      });
      updateTotalQtyBadge(activeVariantRow);
      updateRowQty(activeVariantRow);
      recalc();
      refreshVariantCell(activeVariantRow);
      closeVariantDrawer();
    }

    $(document).on('click', '.variant-edit-btn', function() {
      openVariantDrawer($(this).closest('tr'));
    });
    $(document).on('click', '[data-variant-close]', closeVariantDrawer);
    $('#variantDrawerBackdrop').on('click', function(event) {
      if (event.target === this) closeVariantDrawer();
    });
    $(document).on('click', '.variant-drawer-size', function() {
      if (HAS_LOCKED_ITEM) return;
      var size = String($(this).data('size'));
      if (drawerSizes.indexOf(size) === -1) {
        drawerSizes.push(size);
        drawerQtys[size] = drawerQtys[size] || 1;
      } else {
        drawerSizes = drawerSizes.filter(function(item) {
          return item !== size;
        });
        delete drawerQtys[size];
      }
      renderVariantDrawer();
    });
    $(document).on('click', '.variant-drawer-plus,.variant-drawer-minus', function() {
      if (HAS_LOCKED_ITEM) return;
      var $btn = $(this);
      var size = String($btn.closest('.variant-selected-row').data('size'));
      var qty = parseFloat(drawerQtys[size]) || 0;

      if ($btn.hasClass('variant-drawer-plus')) {
        // Read stockMap from activeVariantRow
        var stockMap = {};
        try {
          if (activeVariantRow) {
            stockMap = JSON.parse(activeVariantRow.attr('data-stock-map') || '{}');
          }
        } catch (e) {
          stockMap = {};
        }

        var available = (stockMap && stockMap[size] !== undefined) ?
          parseInt(stockMap[size], 10) :
          null;

        // If stockMap empty (color not selected yet), skip silently
        if (available !== null && !isNaN(available) && (qty + 1) > available) {
          // Do NOT increment
          var $warning = $btn.closest('.variant-selected-row').find('.stock-warning').first();
          if ($warning.length) {
            $warning.addClass('flash-warning');
            setTimeout(function() {
              $warning.removeClass('flash-warning');
            }, 600);
          }
          return;
        }

        var newQty = qty + 1;
        if (available !== null && !isNaN(available) && newQty > available) {
          newQty = available;
        }
        drawerQtys[size] = newQty;
        renderVariantDrawer();
        return;
      }

      // Decrement normally (min 1)
      drawerQtys[size] = Math.max(1, qty - 1);
      renderVariantDrawer();
    });
    /* ── Drawer qty: direct typing with stock cap ────────────────────────── */
    $(document).on('input', '.variant-drawer-qty', function() {
      if (HAS_LOCKED_ITEM) return;
      var $input = $(this);
      var size = String($input.data('size') ||
        $input.closest('.variant-selected-row').data('size'));
      var val = parseInt($input.val(), 10);

      if (isNaN(val) || val < 1) val = 1;

      // Read stock map
      var stockMap = {};
      try {
        if (activeVariantRow) {
          stockMap = JSON.parse(activeVariantRow.attr('data-stock-map') || '{}');
        }
      } catch (e) {
        stockMap = {};
      }

      var available = (stockMap[size] !== undefined) ?
        parseInt(stockMap[size], 10) : null;

      // Cap at available stock
      if (available !== null && !isNaN(available) && val > available) {
        val = available;
        $input.val(val);
        var $warning = $input.closest('.variant-selected-row').find('.stock-warning');
        if ($warning.length) {
          $warning.addClass('flash-warning');
          setTimeout(function() {
            $warning.removeClass('flash-warning');
          }, 600);
        }
      }

      drawerQtys[size] = val;

      // Update total and warnings without full re-render (preserve focus)
      var hasStockWarning = false;
      Object.keys(drawerQtys).forEach(function(s) {
        if (stockMap[s] !== undefined &&
          (parseFloat(drawerQtys[s]) || 0) > parseInt(stockMap[s], 10)) {
          hasStockWarning = true;
        }
      });

      if (hasStockWarning) {
        $('#variantDrawerBackdrop').attr('data-has-stock-warning', 'true');
        $('#variantSaveBtn').prop('disabled', true)
          .attr('title', 'Fix stock issues before saving');
      } else {
        $('#variantDrawerBackdrop').removeAttr('data-has-stock-warning');
        $('#variantSaveBtn').prop('disabled', false).removeAttr('title');
      }

      var colorMult = activeVariantRow ? colorCount(activeVariantRow) : 1;
      var total = Object.keys(drawerQtys).reduce(function(sum, s) {
        return sum + (parseFloat(drawerQtys[s]) || 0);
      }, 0) * colorMult;
      $('#variantDrawerTotal').text(total +
        (colorMult > 1 ? ' (' + (total / colorMult) + ' × ' + colorMult + ' colors)' : ''));
    });

    $(document).on('blur', '.variant-drawer-qty', function() {
      if (HAS_LOCKED_ITEM) return;
      var $input = $(this);
      var size = String($input.data('size') ||
        $input.closest('.variant-selected-row').data('size'));
      var val = parseInt($input.val(), 10);

      if (isNaN(val) || val < 1) val = 1;

      var stockMap = {};
      try {
        if (activeVariantRow) {
          stockMap = JSON.parse(activeVariantRow.attr('data-stock-map') || '{}');
        }
      } catch (e) {
        stockMap = {};
      }

      var available = (stockMap[size] !== undefined) ?
        parseInt(stockMap[size], 10) : null;

      if (available !== null && !isNaN(available) && val > available) {
        val = available;
      }

      $input.val(val);
      drawerQtys[size] = val;

      // Full re-render after blur to sync all warnings
      renderVariantDrawer();
    });
    $('#variantClearAll').on('click', function() {
      if (HAS_LOCKED_ITEM) return;
      drawerSizes = [];
      drawerQtys = {};
      renderVariantDrawer();
    });
    $('#variantSaveBtn').on('click', applyVariantDrawer);
    $(document).on('change', '.article-select', function() {
      var $row = $(this).closest('tr');
      setTimeout(function() {
        refreshVariantCell($row);
      }, 0);
    });
    $('#addItem').on('click', function() {
      setTimeout(refreshAllVariantCells, 0);
    });

    /* ── Individual item row status change with stock check ─────────────── */
    $(document).on('change', '.status-select', function() {
      if (HAS_LOCKED_ITEM) return;
      var $select = $(this);
      var $row = $select.closest('tr');
      var newStatus = $select.val();
      var oldStatus = $select.data('old-status') || $select.find('option:first').val();
      var orderItemId = $select.data('order-item-id') ||
        $row.find('.order-item-id-hidden').val();

      // Only process if we have an existing saved order item id
      // (new unsaved rows won't have an order item id yet)
      if (!orderItemId) return;

      // If changing TO confirmed — check stock first
      if (newStatus === 'confirmed' && oldStatus !== 'confirmed') {
        $.ajax({
          url: "{{ route('api.item-variants.check-stock') }}",
          type: 'GET',
          data: {
            order_item_id: orderItemId
          },
          success: function(res) {
            if (!res.ok) {
              // Show stock issues and revert dropdown
              var messages = res.issues.map(function(i) {
                return i.message;
              }).join('\n');
              alert('Cannot confirm — stock issues:\n\n' + messages);
              $select.val(oldStatus);
              try {
                $select.trigger('change.select2');
              } catch (e) {}
              return;
            }
            // Stock ok — call updateItemStatus
            updateRowStatus($select, orderItemId, newStatus, oldStatus);
          },
          error: function() {
            alert('Failed to check stock. Please try again.');
            $select.val(oldStatus);
          }
        });
        return;
      }

      // For all other status changes — call updateItemStatus directly
      updateRowStatus($select, orderItemId, newStatus, oldStatus);
    });

    function updateRowStatus($select, orderItemId, newStatus, oldStatus) {
      if (HAS_LOCKED_ITEM) return;
      $.ajax({
        url: '/order-items/' + orderItemId + '/status',
        type: 'POST',
        data: {
          _token: $('meta[name="csrf-token"]').attr('content'),
          status: newStatus
        },
        success: function(res) {
          if (res.success) {
            // Update old status tracker
            $select.data('old-status', newStatus);
            // Show brief success indicator
            var $badge = $('<span class="badge badge-success ml-1">Saved</span>');
            $select.after($badge);
            setTimeout(function() {
              $badge.remove();
            }, 1500);
          } else {
            alert(res.message || 'Failed to update status.');
            $select.val(oldStatus);
          }
        },
        error: function(xhr) {
          var msg = (xhr.responseJSON && xhr.responseJSON.message) ?
            xhr.responseJSON.message :
            'Error updating status.';
          alert(msg);
          $select.val(oldStatus);
        }
      });
    }

    refreshAllVariantCells();
    updateRemoveButtonsState();
    recalc();
    srRecalc();

    if ($.fn.select2) {
      $('.color-select').select2({
        placeholder: 'Colors',
        width: '100%'
      });
      $('.article-select').select2({
        placeholder: 'Article',
        width: '100%'
      });
    }

    // Init size chip states for existing rows
    $('#itemsTable tbody tr').each(function() {
      var $row = $(this);
      var selected = $row.find('.size-select').val() || [];
      if (selected.length) {
        $row.find('.size-chip').each(function() {
          $(this).toggleClass('active', selected.indexOf(String($(this).data('size'))) !== -1);
        });
        // Keep the per-size inputs hidden; the drawer summary is the visible UI.
        if ($row.find('.size-qty-wrapper .size-qty-row').length) {
          $row.find('.size-qty-wrapper').hide();
          updateTotalQtyBadge($row);
        }
      }
    });

    if (IS_SUPER_ADMIN) {
      $('#itemsTable tbody tr').each(function() {
        var $row = $(this);
        if (($row.find('.color-select').val() || []).length) {
          preloadRowAvailableSizes($row);
        }
      });
    }

    // trigger customer change on load to auto-fill addresses
    $('#customer_id').trigger('change');
  });
</script>
@endsection