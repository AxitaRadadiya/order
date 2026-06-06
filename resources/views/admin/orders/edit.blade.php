@extends('admin.layouts.app')
@section('title', 'Edit Order')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6"><h1 class="m-0">Edit Order</h1></div>
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
                <select name="user_id" id="customer_id" class="form-control select2">
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
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" class="form-control"
                       value="{{ old('date', $order->date?->format('Y-m-d')) }}">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Expected Date</label>
                <input type="date" name="expected_date" class="form-control"
                       value="{{ old('expected_date', $order->expected_date?->format('Y-m-d')) }}">
              </div>
            </div>
          </div>

          {{-- ── Transport ─────────────────────────────────────────────── --}}
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>E-way Bill Number</label>
                <input type="text" name="eway_bill_number" class="form-control"
                       value="{{ old('eway_bill_number', $order->eway_bill_number) }}">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Transport Number</label>
                <input type="text" name="transport_number" class="form-control"
                       value="{{ old('transport_number', $order->transport_number) }}">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>LR Number</label>
                <input type="text" name="lr_number" class="form-control"
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
                          class="form-control" rows="2" readonly>{{ old('billing_address', $order->billing_address) }}</textarea>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Shipping Address</label>
                <textarea name="shipping_address" id="shipping_address"
                          class="form-control" rows="2">{{ old('shipping_address', $order->shipping_address) }}</textarea>
              </div>
            </div>
          </div>

          {{-- ── Mode Toggle ───────────────────────────────────────────── --}}
          <!-- <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="m-0">Items</h5>
            <div class="btn-group btn-group-sm">
              <button type="button" id="modeNormal"    class="btn btn-outline-secondary active">Normal</button>
              <button type="button" id="modeSizeRange" class="btn btn-outline-secondary">Size Range</button>
            </div>
          </div> -->

          {{-- ── Normal Items Table ────────────────────────────────────── --}}
          <div id="normalTable">
            <table class="table table-sm table-bordered" id="itemsTable">
              <thead class="thead-light">
                <tr>
                  <th>Article Number</th>
                  <th>Item</th>
                  <th>Color</th>
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
                    $isArr      = is_array($it);
                    $articleNum = $isArr ? ($it['article_number'] ?? '') : ($it->article_number ?? '');
                    $itemId     = $isArr ? ($it['item_id']     ?? '') : ($it->item_id     ?? '');
                    $itemName   = $isArr ? ($it['item_name']   ?? '') : ($it->item_name   ?? '');
                    $color      = $isArr ? ($it['color']       ?? '') : ($it->color       ?? '');
                    $size       = $isArr ? ($it['size']        ?? '') : ($it->size        ?? '');
                    $desc       = $isArr ? ($it['description'] ?? '') : ($it->description ?? '');
                    $qty        = $isArr ? ($it['quantity']    ?? 1)  : ($it->quantity    ?? 1);
                    $rate       = $isArr ? ($it['rate']        ?? 0)  : ($it->rate        ?? 0);
                    $taxRate    = $isArr ? ($it['tax_rate']    ?? 0)  : ($it->tax_rate    ?? 0);
                    $total      = $isArr ? ($it['total']       ?? 0)  : ($it->total       ?? 0);
                  @endphp
                  <tr>
                    <td>
                      @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']))
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
                      @else
                        <input type="text" class="form-control" value="{{ ($items->firstWhere('id', $itemId)->article_number ?? $articleNum) }}" readonly>
                        <input type="hidden" name="items[{{ $i }}][article_number]" value="{{ ($items->firstWhere('id', $itemId)->article_number ?? $articleNum) }}">
                        <input type="hidden" name="items[{{ $i }}][item_id]" class="item-id-hidden" value="{{ $itemId }}">
                        <input type="hidden" name="items[{{ $i }}][order_item_id]" class="order-item-id-hidden" value="{{ $isArr ? ($it['id'] ?? '') : ($it->id ?? '') }}">
                      @endif
                    </td>
                    <td>
                      <input type="text" name="items[{{ $i }}][item_name]" class="form-control item-name-input" value="{{ $itemName }}" readonly>
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
                        $rowColors = ($rowItem && $rowItem->colors->isNotEmpty()) ? $rowItem->colors : $colors;
                        $selectedColorNames = [];
                        foreach ($selectedColors as $sc) {
                          $cobj = $rowColors->firstWhere('id', $sc) ?: $colors->firstWhere('id', $sc);
                          if ($cobj) { $selectedColorNames[] = $cobj->name; }
                        }
                      @endphp
                      @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']))
                        <select name="items[{{ $i }}][color][]" class="form-control color-select select2" multiple>
                          @foreach($rowColors as $col)
                            <option value="{{ $col->id }}" {{ in_array((string) $col->id, $selectedColors) ? 'selected' : '' }}>{{ $col->name }}</option>
                          @endforeach
                        </select>
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
                      @else
                        <div class="form-control size-readonly-box" readonly>{{ implode(', ', $selectedSizes) }}</div>
                      @endif
                    </td>
                    <td><input type="text"   name="items[{{ $i }}][description]" class="form-control desc" value="{{ $desc }}" readonly></td>
                    <td><input type="number" step="0.01" name="items[{{ $i }}][quantity]"    class="form-control qty"         value="{{ $qty }}" readonly></td>
                    <td><input type="number" step="0.01" name="items[{{ $i }}][rate]"        class="form-control rate"        value="{{ $rate }}" readonly></td>
                    <td><input type="number" step="0.01" name="items[{{ $i }}][tax_rate]"    class="form-control tax"         value="{{ $taxRate }}" readonly></td>
                    <td><input type="number" step="0.01" name="items[{{ $i }}][total]"       class="form-control total"       value="{{ $total }}" readonly></td>
                    @php
                      $selectedStatus = $isArr ? ($it['status'] ?? '') : ($it->status ?? '');
                    @endphp
                    <td>
                      @if(auth()->user() && auth()->user()->hasRole('retailer'))
                        <input type="hidden" name="items[{{ $i }}][status]" value="{{ $selectedStatus ?: 'pending' }}">
                        <span class="badge badge-secondary">{{ ucfirst($selectedStatus ?: 'pending') }}</span>
                      @else
                        <select name="items[{{ $i }}][status]" class="form-control status-select">
                          @foreach(['pending','draft','confirmed','shipped','delivered','cancelled'] as $st)
                            <option value="{{ $st }}" {{ ($selectedStatus == $st) ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                          @endforeach
                        </select>
                      @endif
                    </td>
                      <td><button type="button" class="btn btn-sm btn-danger remove-item"><i class="fas fa-trash"></i></button></td>
                    
                  </tr>
                @endforeach

                @if($rowItems->isEmpty())
                  <tr>
                    <td>
                      @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']))
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
                      @else
                        <input type="text" class="form-control" value="" readonly>
                        <input type="hidden" name="items[0][article_number]" value="">
                        <input type="hidden" name="items[0][item_id]" class="item-id-hidden" value="">
                      @endif
                    </td>
                    <td>
                      <input type="text" name="items[0][item_name]" class="form-control item-name-input" value="" readonly>
                    </td>
                    <td>
                      @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']))
                        <select name="items[0][color][]" class="form-control color-select select2" multiple>
                          @foreach($colors as $col)
                            <option value="{{ $col->id }}">{{ $col->name }}</option>
                          @endforeach
                        </select>
                      @else
                        <input type="text" class="form-control color-read" readonly value="">
                      @endif
                    </td>
                    <td>
                      @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']))
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
                      @else
                        <div class="form-control size-readonly-box" readonly></div>
                      @endif
                    </td>
                    <td><input type="text"   name="items[0][description]" class="form-control desc" readonly></td>
                    <td><input type="number" step="0.01" name="items[0][quantity]"    class="form-control qty"         value="0" readonly></td>
                    <td><input type="number" step="0.01" name="items[0][rate]"        class="form-control rate"        value="0" readonly></td>
                    <td><input type="number" step="0.01" name="items[0][tax_rate]"    class="form-control tax"         value="0" readonly></td>
                    <td><input type="number" step="0.01" name="items[0][total]"       class="form-control total"       value="0" readonly></td>
                    <td>
                      @if(auth()->user() && auth()->user()->hasRole('retailer'))
                        <input type="hidden" name="items[0][status]" value="pending">
                        <span class="badge badge-secondary">Pending</span>
                      @else
                        <select name="items[0][status]" class="form-control status-select">
                          @foreach(['pending','draft','confirmed','shipped','delivered','cancelled'] as $st)
                            <option value="{{ $st }}">{{ ucfirst($st) }}</option>
                          @endforeach
                        </select>
                      @endif
                    </td>
                    @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']))
                      <td><button type="button" class="btn btn-sm btn-danger remove-item"><i class="fas fa-trash"></i></button></td>
                    @else
                      <td></td>
                    @endif
                  </tr>
                @endif
              </tbody>
            </table>
            <div class="text-right mb-3">
              @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']))
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
                    @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']))
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
                    <strong>Discount</strong>
                    @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']))
                      <input type="number" step="0.01" name="discount" id="discount"
                             class="form-control form-control-sm w-50 text-right"
                             value="{{ old('discount', $order->discount ?? 0) }}">
                    @else
                      <input type="number" step="0.01" name="discount" id="discount"
                             class="form-control form-control-sm w-50 text-right" readonly
                             value="{{ old('discount', $order->discount ?? 0) }}">
                    @endif
                  </div>
                  <div class="d-flex justify-content-between py-1">
                    <strong>Adjustment</strong>
                    @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']))
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
            <textarea name="terms" class="form-control" rows="2">{{ old('terms', $order->terms) }}</textarea>
          </div>
          <div class="form-group col-md-6">
            <label>Notes</label>
            <textarea name="notes" class="form-control" rows="2">{{ old('notes', $order->notes) }}</textarea>
          </div>
          <div class="form-group col-md-3">
            <label>Status</label>
            @if(auth()->user() && auth()->user()->hasRole('retailer'))
              <input type="hidden" name="status" value="{{ old('status', $order->status) }}">
              <div><span class="badge badge-secondary">{{ ucfirst(old('status', $order->status) ?? 'pending') }}</span></div>
            @else
              <select name="status" class="form-control">
                @foreach(['pending' => 'Pending', 'draft' => 'Draft', 'confirmed' => 'Confirmed', 'shipped' => 'Shipped', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled'] as $val => $label)
                  <option value="{{ $val }}" {{ old('status', $order->status) == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
              </select>
            @endif
          </div>
          </div>

        </div>
      </div>

      
      <div class="mt-2 mb-2 mr-3 text-right">
        <a href="{{ route('orders.index') }}" class="btn-cancel mr-2"><i class="fas fa-times"></i> Cancel</a>
        <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Update Order</button>
      </div>

    </form>
  </div>
</section>
@if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']))
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
<script>
$(function () {

  // ALL_SIZES comes from DB via controller → $sizesJson (array of label strings)
  var ALL_SIZES = @json($sizesJson);

  var ITEMS = @json($itemsJson);
  var COLORS = @json($colors);
  var IS_RETAILER = @json(optional(auth()->user())->hasRole('retailer') ?? false);
  var IS_SUPER_ADMIN = @json(optional(auth()->user())->hasRole(['super-admin', 'superadmin']) ?? false);
  var IS_DISTRIBUTOR = @json(optional(auth()->user())->hasRole('distributor') ?? false);

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
      return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[ch];
    });
  }

  function normalizeSelected(value) {
    if (!value) return [];
    return Array.isArray(value) ? value.map(String) : String(value).split(',').map(function(v) { return v.trim(); });
  }

  function colorOptions(colors, selected) {
    selected = normalizeSelected(selected);
    colors = (colors && colors.length) ? colors : COLORS;

    return colors.map(function(c) {
      var id = String(c.id);
      return '<option value="' + escapeHtml(id) + '"' +
        (selected.indexOf(id) !== -1 ? ' selected' : '') +
        '>' + escapeHtml(c.name) + '</option>';
    }).join('');
  }

  function populateColorSelect($row, colors, sel) {
    var $cs = $row.find('.color-select');
    sel = sel || ($cs.length ? $cs.val() : []) || [];
    if ($cs.length) {
      if ($cs.hasClass('select2-hidden-accessible')) $cs.select2('destroy');
      $cs.html(colorOptions(colors, sel));
      $cs.select2({ placeholder: 'Colors', width: '100%' });
    } else {
      // no select present (non-super-admin) — show readonly text and hidden inputs
      var selArr = normalizeSelected(sel);
      var names = selArr.map(function(id){ var c = COLORS.find(function(x){ return String(x.id) == String(id); }); return c?c.name : ''; }).filter(Boolean).join(', ');
      var $rd = $row.find('.color-read');
      if ($rd.length) $rd.val(names);
      var $cell = $row.find('td').has('.color-read');
      if (!$cell.length) $cell = $row.find('td').has('.color-select');
      if (!$cell.length) $cell = $row.find('td').eq(2);
      if ($cell.length) {
        $cell.find('input[type=hidden][name$="[color][]"]').remove();
        selArr.forEach(function(id){
          $cell.append('<input type="hidden" name="items['+rowIndex($row)+'][color][]" value="'+escapeHtml(id)+'">');
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
    var $panel  = $row.find('.size-qty-wrapper');
    var selected = $select.val() || [];

    // Sync chip active states
    $row.find('.size-chip').each(function(){
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
    $panel.find('.size-qty').each(function(){
      var sz = $(this).closest('.size-qty-row').data('size');
      oldQtys[sz] = $(this).val();
    });

    var html = selected.map(function(sz){
      var q = oldQtys[sz] || 0;
      return '<div class="size-qty-row" data-size="'+escapeHtml(sz)+'">'
        + '<div class="input-group input-group-sm mb-1">'
        + '<div class="input-group-prepend"><span class="input-group-text">'+escapeHtml(sz)+'</span></div>'
        + '<div class="size-stepper">'
        + (IS_SUPER_ADMIN ? '<button type="button" class="stepper-btn minus">−</button>' : '')
        + (IS_SUPER_ADMIN ? '<input type="text" step="1" min="0" name="items['+idx+'][size_quantities]['+escapeHtml(sz)+']" class="size-qty" value="'+escapeHtml(q)+'" readonly>' : '<input type="text" name="items['+idx+'][size_quantities]['+escapeHtml(sz)+']" class="size-qty" value="'+escapeHtml(q)+'" readonly>')
        + (IS_SUPER_ADMIN ? '<button type="button" class="stepper-btn plus">+</button>' : '')
        + '</div></div></div>';
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
    $row.find('.size-qty').each(function(){ tot += parseFloat($(this).val())||0; });
    var colors = colorCount($row);
    $row.find('.total-qty-badge').text(tot + ' × ' + colors + ' colors = ' + (tot * colors));
  }

  // size-chip click
  $(document).on('click', '.size-chip', function(){
    var $chip = $(this);
    var $row  = $chip.closest('tr');
    var $sel  = $row.find('.size-select');
    var size  = String($chip.data('size'));
    var cur   = $sel.val() || [];

    if (!IS_SUPER_ADMIN) return;

    if ($chip.hasClass('active')) {
      cur = cur.filter(function(s){ return s !== size; });
    } else {
      cur.push(size);
    }
    $sel.val(cur);
    rebuildSizePanel($row);
  });

  // stepper handlers
  $(document).on('click', '.stepper-btn', function(){
    var $btn   = $(this);
    var $input = $btn.siblings('input.size-qty');
    var val    = parseFloat($input.val()) || 0;
    $input.val($btn.hasClass('plus') ? val+1 : Math.max(0, val-1));
    var $row = $btn.closest('tr');
    updateTotalQtyBadge($row);
    updateRowQty($row);
    recalc();
  });
  $(document).on('input', '.size-qty', function(){
    var $row = $(this).closest('tr');
    updateTotalQtyBadge($row);
    updateRowQty($row);
    recalc();
  });

  function syncSizeQtyInputs($row) {
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
    $row.find('.size-qty').each(function(){ sizeSum += parseFloat($(this).val()) || 0; });
    var colors = colorCount($row);
    $row.find('.qty').val(sizeSum * colors);
  }

  // ── Recalculate totals ───────────────────────────────────────────────────
  function recalc() {
    var subtotal = 0;
    $('#itemsTable tbody tr').each(function () {
      updateRowQty($(this));
      var qty  = parseFloat($(this).find('.qty').val())  || 0;
      var rate = parseFloat($(this).find('.rate').val()) || 0;
      var tax  = parseFloat($(this).find('.tax').val())  || 0;
      var fp   = rate + (rate * tax / 100);
      var tot  = fp * qty;
      $(this).find('.total').val(tot.toFixed(2));
      subtotal += tot;
    });
    $('#subtotal').val(subtotal.toFixed(2));
    var markdownPercent = parseFloat($('#markdown').val()) || 0;
    var grand = subtotal
              - (subtotal * markdownPercent / 100)
              - (parseFloat($('#discount').val())   || 0)
              + (parseFloat($('#adjustment').val()) || 0);
    $('#grand_total').val(grand.toFixed(2));
  }

  $(document).on('input', '.size-qty,.rate,.tax', recalc);
  // Color change → re-run qty × color multiplication and update totals
  $(document).on('change', '.color-select', function(){
    var $row = $(this).closest('tr');
    updateTotalQtyBadge($row);
    updateRowQty($row);
    recalc();
  });
  $(document).on('change', '.size-select', function() {
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
  $(document).on('change', '.article-select', function () {
    var $row  = $(this).closest('tr');
    var id    = $(this).val();
    if (!id) return;

    var found = itemByArticle(id);
    if (!found) {
      var $opt = $(this).find('option:selected');
      found = {
        id:   $opt.data('id') || null,
        article_number: $opt.text().trim(),
        name: $opt.data('name') || '',
        rate: parseFloat($opt.data('rate')) || 0,
        tax:  parseFloat($opt.data('tax'))  || 0,
        desc: $opt.data('desc') || ''
      };
    }

    $row.find('.item-id-hidden').val(found.id || '');
    $row.find('.item-name-input').val(found.name || found.article_number || '');
    $row.find('.rate').val(found.rate || 0);
    $row.find('.tax').val(found.tax  || 0);
    if (!$row.find('.desc').val()) {
      $row.find('.desc').val(found.desc || '');
    }
    // populate color select from the selected article's colors
    populateColorSelect($row, found.colors || []);

    // populate size select (use item's sizes if present else ALL_SIZES)
    var sizeChoices = (found.sizes && found.sizes.length) ? found.sizes : ALL_SIZES;
    var sizeOpts = sizeChoices.map(function(s){ return '<option value="' + s + '">' + s + '</option>'; }).join('');
    var $size = $row.find('.size-select');
    if ($size.hasClass('select2-hidden-accessible')) { $size.select2('destroy'); }
    $size.html(sizeOpts);
    if (IS_SUPER_ADMIN) {
      // build chips
      var $chips = $row.find('.size-chips-wrap');
      $chips.html(sizeChoices.map(function(s){ return '<button type="button" class="size-chip" data-size="'+escapeHtml(s)+'">'+escapeHtml(s)+'</button>'; }).join(''));
      rebuildSizePanel($row);
    } else {
      $size.select2({ placeholder: 'Sizes', width: '100%' });
      syncSizeQtyInputs($row);
    }
    recalc();
  });

  // ── Build a new row ───────────────────────────────────────────────────────
  function buildRow(idx, it) {
    it = it || {};
    var opts = '<option value="">--</option>' + ITEMS.map(function(m) {
      return '<option value="' + (m.article_number || '') + '"' +
        ' data-id="' + (m.id || '') + '"' +
        ' data-rate="' + (m.rate || 0) + '"' +
        ' data-tax="'  + (m.tax  || 0) + '"' +
        ' data-desc="' + ((m.desc || '').replace(/"/g, '&quot;')) + '"' +
        (it.item_id == m.id ? ' selected' : '') +
        '>' + (m.article_number || '') + '</option>';
    }).join('');

    // color select options (article colors when available, otherwise global COLORS)
    var colorOpts = colorOptions(it.colors || COLORS, it.color || it.color_id || []);

    // size options (use ALL_SIZES for chips; per-item selected sizes may be prefilled)
    var sizeOpts = (it.sizes && it.sizes.length) ? it.sizes.map(function(s){ return '<option value="' + s + '" selected>' + s + '</option>'; }).join('') : '';
    var sizeChips = '';
    if (IS_SUPER_ADMIN) {
      sizeChips = ALL_SIZES.map(function(s){ return '<button type="button" class="size-chip" data-size="'+escapeHtml(s)+'">'+escapeHtml(s)+'</button>'; }).join('');
    }
    var sizeOptsFull = ALL_SIZES.map(function(s){ return '<option value="'+escapeHtml(s)+'">'+escapeHtml(s)+'</option>'; }).join('');

    var articleCell = '';
    if (IS_SUPER_ADMIN) {
      articleCell = '<td>' + '<select name="items[' + idx + '][article_number]" class="form-control article-select">' + opts + '</select>'
        + '<input type="hidden" name="items[' + idx + '][item_id]" class="item-id-hidden" value="' + (it.item_id || '') + '">'
        + '<input type="hidden" name="items[' + idx + '][order_item_id]" class="order-item-id-hidden" value="' + (it.id || '') + '">'
        + '</td>';
    } else {
      var artVal = (function(){
        var f = ITEMS.find(function(i){ return i.id == it.item_id; });
        return f ? f.article_number : (it.article_number || '');
      })();
      articleCell = '<td>' + '<input type="text" class="form-control" value="' + escapeHtml(artVal) + '" readonly>'
        + '<input type="hidden" name="items[' + idx + '][article_number]" value="' + escapeHtml(artVal) + '">'
        + '<input type="hidden" name="items[' + idx + '][item_id]" class="item-id-hidden" value="' + (it.item_id || '') + '">'
        + '<input type="hidden" name="items[' + idx + '][order_item_id]" class="order-item-id-hidden" value="' + (it.id || '') + '">'
        + '</td>';
    }
      '<td><input type="text" name="items[' + idx + '][item_name]" class="form-control item-name-input" value="' + (it.item_name || '') + '" readonly></td>' +
      (IS_SUPER_ADMIN ? '<td><select name="items[' + idx + '][color][]" class="form-control color-select" multiple>' + colorOpts + '</select></td>' : (function(){
          var sel = normalizeSelected(it.color || it.color_id || []);
          var names = sel.map(function(id){ var m = (COLORS || []).find(function(c){ return String(c.id) == String(id); }); return m?m.name:''; }).filter(Boolean).join(', ');
          var hidden = sel.map(function(id){ return '<input type="hidden" name="items['+idx+'][color][]" value="'+escapeHtml(id)+'">'; }).join('');
          return '<td>' + '<input type="text" class="form-control color-read" readonly value="' + escapeHtml(names) + '">' + hidden + '</td>';
        })()) +
      '<td>'
        + '<select name="items[' + idx + '][sizes][]" class="size-select d-none" multiple>' + sizeOptsFull + '</select>'
        + '<div class="size-chips-wrap">' + sizeChips + '</div>'
        + (IS_SUPER_ADMIN ? '<div class="size-qty-wrapper size-qty-panel" style="display:none;"></div>' : '<div class="form-control size-readonly-box" readonly>' + (it.sizes ? normalizeSelected(it.sizes).join(', ') : '') + '</div>')
      + '</td>' +
      '<td><input type="text"   name="items[' + idx + '][description]" class="form-control desc"        value="' + (it.description || '') + '" readonly></td>' +
      '<td><input type="number" step="0.01" name="items[' + idx + '][quantity]"    class="form-control qty"         value="' + (it.quantity || 0) + '" readonly></td>' +
      '<td><input type="number" step="0.01" name="items[' + idx + '][rate]"        class="form-control rate"        value="' + (it.rate || 0) + '" readonly></td>' +
      '<td><input type="number" step="0.01" name="items[' + idx + '][tax_rate]"    class="form-control tax"         value="' + (it.tax_rate || 0) + '" readonly></td>' +
      '<td><input type="number" step="0.01" name="items[' + idx + '][total]"       class="form-control total"       value="' + (it.total || 0) + '" readonly></td>' +
      '<td>' +
        '<select name="items[' + idx + '][status]" class="form-control status-select">' +
          ['pending','draft','confirmed','shipped','delivered','cancelled'].map(function(s){
            return '<option value="' + s + '"' + ((it.status && it.status == s) ? ' selected' : '') + '>' + (s.charAt(0).toUpperCase() + s.slice(1)) + '</option>';
          }).join('') +
        '</select>' +
      '</td>' +
      (IS_SUPER_ADMIN ? '<td><button type="button" class="btn btn-sm btn-danger remove-item"><i class="fas fa-trash"></i></button></td>' : '<td></td>') +
    '</tr>';
  }

  var rowCounter = $('#itemsTable tbody tr').length;
  // replace selects with hidden fields so the server receives a controlled value.
  if (IS_RETAILER) {
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

  $('#addItem').on('click', function () {
    $('#itemsTable tbody').append(buildRow(rowCounter));
    // initialize Select2 on newly appended row
    var $new = $('#itemsTable tbody tr:last');
    $new.find('.color-select').select2({ placeholder: 'Colors', width: '100%' });
    $new.find('.article-select').select2({ placeholder: 'Article', width: '100%' });
    // initialize size chips / panel
    if (IS_SUPER_ADMIN) {
      $new.find('.size-chips-wrap').html(ALL_SIZES.map(function(s){ return '<button type="button" class="size-chip" data-size="'+escapeHtml(s)+'">'+escapeHtml(s)+'</button>'; }).join(''));
      rebuildSizePanel($new);
    }
    rowCounter++;
    recalc();
    if (IS_RETAILER) {
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

  $(document).on('click', '.remove-item', function () {
    $(this).closest('tr').remove();
    recalc();
  });

  // ── Customer → address auto-fill (fetch from server)
  $('#customer_id').on('change', function () {
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
    var to   = $('#sr_to').val();
    var sets = parseInt($('#sr_sets').val())    || 1;
    var rate = parseFloat($('#sr_rate').val())  || 0;

    var sizes    = sizesInRange(from, to);
    var pcsSet   = sizes.length;
    var totalPcs = pcsSet * sets;
    var amount   = totalPcs * rate;

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

  $('#sr_item').on('change', function () {
    var $opt = $(this).find('option:selected');
    $('#sr_rate').val(parseFloat($opt.data('rate')) || 0);
    srRecalc();
  });

  $('#sr_add').on('click', function () {
    if (!IS_SUPER_ADMIN) { alert('Not allowed'); return; }
    var $opt     = $('#sr_item').find('option:selected');
    var itemId   = $('#sr_item').val();
    if (!itemId) { alert('Please select a product first.'); return; }

    var itemName = $opt.text().trim();
    var from     = $('#sr_from').val();
    var to       = $('#sr_to').val();
    var sets     = parseInt($('#sr_sets').val())    || 1;
    var rate     = parseFloat($('#sr_rate').val())  || 0;
    var taxRate  = parseFloat($opt.data('tax'))     || 0;
    var sizes    = sizesInRange(from, to);

    if (!sizes.length) { alert('No valid sizes in that range. Please check Size From / Size To.'); return; }

    var totalPcs = sizes.length * sets;
    var desc     = 'Sizes ' + from + '-' + to + ' (' + sizes.join(', ') + ') × ' + sets + ' sets';

    $('#modeNormal').trigger('click');

    var idx = rowCounter++;

    $('#itemsTable tbody').append(buildRow(idx, {
      item_id:     itemId,
      item_name:   itemName,
      description: desc,
      quantity:    totalPcs,
      rate:        rate,
      tax_rate:    taxRate,
    }));

    var $tr = $('#itemsTable tbody tr:last');
    if ($.fn.select2) { $tr.find('.article-select').select2({ placeholder: 'Article', width: '100%' }); }
    $tr.find('.item-name-hidden').val(itemName);
    $tr.find('.item-select').val(itemId);
    // set article-select value (find article_number from ITEMS by id)
    var foundArticle = (function(){ var f = ITEMS.find(function(x){ return x.id == itemId; }); return f ? f.article_number : ''; })();
    if (foundArticle) { $tr.find('.article-select').val(foundArticle).trigger('change'); }
    $tr.find('.size-select').val(sizes).trigger('change');
    $tr.find('.size-qty').val(sets);
    $tr.append('<input type="hidden" name="items[' + idx + '][size_from]" value="' + from  + '">');
    $tr.append('<input type="hidden" name="items[' + idx + '][size_to]"   value="' + to    + '">');
    $tr.append('<input type="hidden" name="items[' + idx + '][sets]"      value="' + sets  + '">');

    recalc();
  });

  var activeVariantRow = null;
  var drawerSizes = [];
  var drawerQtys = {};

  function variantEscape(value) {
    return String(value).replace(/[&<>"']/g, function(ch) {
      return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[ch];
    });
  }

  function variantRowLabel($row) {
    var article = $row.find('.article-select').val() || $row.find('input[name$="[article_number]"]').val() || '';
    var item = $row.find('.item-name-input').val() || 'Selected item';
    return article ? item + ' (' + article + ')' : item;
  }

  function variantSizeOptions($row) {
    var opts = [];
    $row.find('.size-select option').each(function() {
      opts.push(String($(this).val()));
    });
    return opts.length ? opts : ALL_SIZES.map(String);
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
    var summary = sizes.length
      ? '<span class="variant-count-pill">' + sizes.length + ' Variants Added</span><div class="variant-chip-list">' + chips + '</div>'
      : '<span class="variant-empty-text">No Variants Added</span>';
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
    $('#variantDrawerSizes').html(variantSizeOptions(activeVariantRow).map(function(size) {
      var active = drawerSizes.indexOf(String(size)) !== -1 ? ' active' : '';
      return '<button type="button" class="variant-drawer-size' + active + '" data-size="' + variantEscape(size) + '">' + variantEscape(size) + '</button>';
    }).join(''));

    $('#variantSelectedList').html(drawerSizes.map(function(size) {
      var qty = drawerQtys[size] || 1;
      return '<div class="variant-selected-row" data-size="' + variantEscape(size) + '">' +
        '<span class="variant-selected-name">' + variantEscape(size) + '</span>' +
        '<div class="size-stepper">' +
          '<button type="button" class="stepper-btn variant-drawer-minus">-</button>' +
          '<input type="text" class="size-qty variant-drawer-qty" value="' + variantEscape(qty) + '" readonly>' +
          '<button type="button" class="stepper-btn variant-drawer-plus">+</button>' +
        '</div>' +
      '</div>';
    }).join('') || '<div class="variant-selected-empty">Select sizes from above</div>');

    // Total = sum of size qtys × colors selected on that row
    var colorMult = activeVariantRow ? colorCount(activeVariantRow) : 1;
    var total = drawerSizes.reduce(function(sum, size) {
      return sum + (parseFloat(drawerQtys[size]) || 0);
    }, 0) * colorMult;
    $('#variantDrawerTotal').text(total + (colorMult > 1 ? ' ('+( total/colorMult )+' × '+colorMult+' colors)' : ''));
  }

  function openVariantDrawer($row) {
    if (!IS_SUPER_ADMIN) return;
    activeVariantRow = $row;
    drawerSizes = variantSelectedSizes($row);
    drawerQtys = variantQtyMap($row);
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
    if (!activeVariantRow) return;
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
    var size = String($(this).data('size'));
    if (drawerSizes.indexOf(size) === -1) {
      drawerSizes.push(size);
      drawerQtys[size] = drawerQtys[size] || 1;
    } else {
      drawerSizes = drawerSizes.filter(function(item) { return item !== size; });
      delete drawerQtys[size];
    }
    renderVariantDrawer();
  });
  $(document).on('click', '.variant-drawer-plus,.variant-drawer-minus', function() {
    var size = String($(this).closest('.variant-selected-row').data('size'));
    var qty = parseFloat(drawerQtys[size]) || 0;
    drawerQtys[size] = $(this).hasClass('variant-drawer-plus') ? qty + 1 : Math.max(1, qty - 1);
    renderVariantDrawer();
  });
  $('#variantClearAll').on('click', function() {
    drawerSizes = [];
    drawerQtys = {};
    renderVariantDrawer();
  });
  $('#variantSaveBtn').on('click', applyVariantDrawer);
  $(document).on('change', '.article-select', function() {
    var $row = $(this).closest('tr');
    setTimeout(function() { refreshVariantCell($row); }, 0);
  });
  $('#addItem').on('click', function() {
    setTimeout(refreshAllVariantCells, 0);
  });

  refreshAllVariantCells();
  recalc();
  srRecalc();

  if ($.fn.select2) {
    $('.color-select').select2({ placeholder: 'Colors', width: '100%' });
    $('.article-select').select2({ placeholder: 'Article', width: '100%' });
  }

  // Init size chip states for existing rows
  $('#itemsTable tbody tr').each(function(){
    var $row = $(this);
    var selected = $row.find('.size-select').val() || [];
    if (selected.length) {
      $row.find('.size-chip').each(function(){
        $(this).toggleClass('active', selected.indexOf(String($(this).data('size'))) !== -1);
      });
      // Keep the per-size inputs hidden; the drawer summary is the visible UI.
      if ($row.find('.size-qty-wrapper .size-qty-row').length) {
        $row.find('.size-qty-wrapper').hide();
        updateTotalQtyBadge($row);
      }
    }
  });

  // trigger customer change on load to auto-fill addresses
    $('#customer_id').trigger('change');
});
</script>
@endsection
