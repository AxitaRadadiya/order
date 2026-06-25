@extends('admin.layouts.app')
@section('title', 'Create Order')

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
    padding: 6px 14px;
    margin: 4px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease;
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
</style>
@endsection

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Create Order</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Orders</a></li>
          <li class="breadcrumb-item active">Create</li>
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

    <form action="{{ route('orders.store') }}" method="POST">
      @csrf
      @php
      $lockedStatus = auth()->check() && auth()->user()->hasRole(['retailer','distributor','super-admin','superadmin']);
      @endphp
      @if(!empty($pre_items))
      <input type="hidden" name="from_cart" value="1">
      @endif

      <div class="card" style="padding:10px;">
        <div class="card-body">

          {{-- ── Customer / Dates ────────────────────────────────────────── --}}
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Customer Name <span class="text-danger">*</span></label>
                <select name="user_id" id="customer_id" class="form-control select2" required>
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
                    {{ old('user_id') == $c->id ? 'selected' : '' }}>
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
                  value="{{ old('date', date('Y-m-d')) }}">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Expected Date</label>
                <input type="date" name="expected_date" class="form-control"
                  value="{{ old('expected_date') }}">
              </div>
            </div>
          </div>

          {{-- ── Transport ─────────────────────────────────────────────── --}}
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>E-way Bill Number</label>
                <input type="text" name="eway_bill_number" class="form-control"
                  value="{{ old('eway_bill_number') }}">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Transport Number</label>
                <input type="text" name="transport_number" class="form-control"
                  value="{{ old('transport_number') }}">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>LR Number</label>
                <input type="text" name="lr_number" class="form-control"
                  value="{{ old('lr_number') }}">
              </div>
            </div>
          </div>

          {{-- ── Addresses ─────────────────────────────────────────────── --}}
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Billing Address</label>
                <textarea name="billing_address" id="billing_address"
                  class="form-control" rows="2" readonly>{{ old('billing_address') }}</textarea>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Shipping Address</label>
                <textarea name="shipping_address" id="shipping_address"
                  class="form-control" rows="2">{{ old('shipping_address') }}</textarea>
              </div>
            </div>
          </div>

          {{-- ── Normal Items Table ────────────────────────────────────── --}}
          <div id="normalTable">
            <div class="table-responsive" style="overflow-x:auto;">
              <table class="table table-sm table-bordered" id="itemTable">
                <thead class="thead-light">
                  <tr>
                    <th>Article Number</th>
                    <th>Item</th>
                    <th>Color code</th>
                    <th>Size(s)</th>
                    <th>Qty</th>
                    <th>MRP</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @if(old('items'))
                  @foreach(old('items') as $i => $it)
                  <tr>
                    <td class="color-cell">
                      <select name="items[{{ $i }}][article_number]" class="form-control article-select">
                        <option value="">--</option>
                        @foreach($items as $itm)
                        <option value="{{ $itm->article_number }}"
                          data-id="{{ $itm->id }}"
                          data-rate="{{ $itm->price }}"
                          data-desc="{{ $itm->description ?? '' }}"
                          {{ (isset($it['item_id']) && $it['item_id'] == $itm->id) ? 'selected' : '' }}>
                          {{ $itm->article_number }}
                        </option>
                        @endforeach
                      </select>
                      <input type="hidden" name="items[{{ $i }}][item_id]" class="item-id-hidden" value="{{ $it['item_id'] ?? '' }}">
                    </td>
                    <td class="color-cell">
                      <input type="text" name="items[{{ $i }}][item_name]" class="form-control item-name-input" value="{{ $it['item_name'] ?? '' }}" readonly>
                    </td>
                    <td>
                      @php
                      $selectedColors = $it['color'] ?? $it['color_id'] ?? [];
                      if (!is_array($selectedColors)) {
                      $selectedColors = explode(',', $selectedColors);
                      }
                      $selectedColors = array_map('trim', $selectedColors);
                      $rowItem = !empty($it['item_id'] ?? null) ? $items->firstWhere('id', $it['item_id']) : null;
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
                      @endphp
                      @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']))
                      <select name="items[{{ $i }}][color][]" class="form-control color-select select2" multiple> @foreach($rowColors as $col)
                        <option value="{{ $col->id }}" {{ in_array((string)$col->id, $selectedColors) ? 'selected' : '' }}>{{ $col->color_code ?? $col->name }}</option>
                        @endforeach
                      </select>
                      @else
                      @php
                      $selectedNames = [];
                      foreach($rowColors as $col) {
                      if (in_array((string)$col->id, $selectedColors)) $selectedNames[] = $col->color_code ?? $col->name;
                      }
                      @endphp
                      <input type="text" class="form-control color-read" readonly value="{{ implode(', ', $selectedNames) }}">
                      @foreach($selectedColors as $sc)
                      <input type="hidden" name="items[{{ $i }}][color][]" value="{{ $sc }}">
                      @endforeach
                      @endif
                    </td>
                    <td>
                      @php
                      $selectedSizes = [];
                      if (!empty($it['sizes'])) {
                      $selectedSizes = is_array($it['sizes']) ? $it['sizes'] : explode(',', $it['sizes']);
                      $selectedSizes = array_map('trim', $selectedSizes);
                      }
                      $sizeQuantities = $it['size_quantities'] ?? [];
                      @endphp
                      <select name="items[{{ $i }}][sizes][]" class="form-control size-select select2" multiple>
                        @foreach($sizesJson as $sz)
                        <option value="{{ $sz }}" {{ in_array($sz, $selectedSizes) ? 'selected' : '' }}>{{ $sz }}</option>
                        @endforeach
                      </select>
                      <div class="size-qty-wrapper mt-2">
                        @foreach($selectedSizes as $selectedSize)
                        <div class="input-group input-group-sm mb-1 size-qty-row" data-size="{{ $selectedSize }}">
                          <div class="input-group-prepend"><span class="input-group-text">{{ $selectedSize }}</span></div>
                          <input type="text" step="1" min="0" name="items[{{ $i }}][size_quantities][{{ $selectedSize }}]" class="form-control size-qty" value="{{ $sizeQuantities[$selectedSize] ?? '' }}" placeholder="Qty">
                        </div>
                        @endforeach
                      </div>
                    </td>
                    <td><input type="number" step="0.01" name="items[{{ $i }}][quantity]" class="form-control qty" value="{{ $it['quantity'] ?? 0 }}" readonly></td>
                    <td><input type="number" step="0.01" name="items[{{ $i }}][rate]" class="form-control rate" value="{{ $it['rate'] ?? 0 }}" readonly></td>
                    <td><input type="number" step="0.01" name="items[{{ $i }}][total]" class="form-control total" value="{{ $it['total'] ?? 0 }}" readonly></td>
                    <td>
                      @if($lockedStatus)
                      <input type="hidden" name="items[{{ $i }}][status]" value="{{ $it['status'] ?? 'pending' }}">
                      <span class="badge badge-secondary">{{ ucfirst($it['status'] ?? 'pending') }}</span>
                      @else
                      <select name="items[{{ $i }}][status]" class="form-control status-select">
                        @foreach(['pending','confirmed','shipped','cancelled'] as $st)
                        <option value="{{ $st }}" {{ (isset($it['status']) && $it['status'] == $st) ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                        @endforeach
                      </select>
                      @endif
                    </td>
                    <td><button type="button" class="btn btn-sm btn-danger remove-item"><i class="fas fa-trash"></i></button></td>
                  </tr>
                  @endforeach
                  @else
                  {{-- Default empty first row --}}
                  <tr>
                    <td>
                      <select name="items[0][article_number]" class="form-control article-select">
                        <option value="">--</option>
                        @foreach($items as $itm)
                        <option value="{{ $itm->article_number }}"
                          data-id="{{ $itm->id }}" data-rate="{{ $itm->price }}"
                          data-desc="{{ $itm->description??'' }}">
                          {{ $itm->article_number }}
                        </option>
                        @endforeach
                      </select>
                      <input type="hidden" name="items[0][item_id]" class="item-id-hidden" value="">
                    </td>
                    <td><input type="text" name="items[0][item_name]" class="form-control item-name-input" value="" readonly></td>
                    <td>
                      @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']))
                      <select name="items[0][color][]" class="form-control color-select select2" multiple>
                        <option value="">--</option>
                      </select>
                      @else
                      <input type="text" class="form-control color-read" readonly value="">
                      @endif
                    </td>
                    <td>
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
                    </td>
                    <td><input type="number" step="0.01" name="items[0][quantity]" class="form-control qty" value="0" readonly></td>
                    <td><input type="number" step="0.01" name="items[0][rate]" class="form-control rate" value="0" readonly></td>
                    <td>
                      <input type="number" step="0.01" name="items[0][total]" class="form-control total" value="0" readonly>
                    </td>
                    <td>
                      @if($lockedStatus)
                      <input type="hidden" name="items[0][status]" value="pending">
                      <span class="badge badge-secondary">Pending</span>
                      @else
                      <select name="items[0][status]" class="form-control status-select" style="font-size:12px!important;">
                        @foreach(['pending','confirmed','shipped','cancelled'] as $st)
                        <option value="{{ $st }}">{{ ucfirst($st) }}</option>
                        @endforeach
                      </select>
                      @endif
                    </td>
                    <td><button type="button" class="deleteButton btn btn-sm btn-danger border-0"><i class="fas fa-trash"></i></button></td>
                  </tr>
                  @endif
                </tbody>
              </table>
            </div>
            @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']))
            <div class="text-right mb-3">
              <button type="button" id="addItem" class="btn btn-sm btn-create">
                <i class="fas fa-plus"></i> Add Row
              </button>
            </div>
            @endif
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
                          data-rate="{{ $itm->price }}">
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
              <div class="card" style="padding:10px;">
                <div class="card-body p-2">
                  <div class="d-flex justify-content-between py-1">
                    <strong>Sub Total</strong>
                    <input type="text" name="subtotal" id="subtotal"
                      class="form-control form-control-sm w-50 text-right"
                      readonly value="{{ old('subtotal', 0) }}">
                  </div>
                  <div class="d-flex justify-content-between py-1">
                    <strong>Mark Down (%)</strong>
                    @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']))
                    <input type="number" step="0.01" min="0" max="100" name="markdown" id="markdown"
                      class="form-control form-control-sm w-50 text-right"
                      value="{{ old('markdown', 0) }}">
                    @else
                    <input type="number" step="0.01" min="0" max="100" name="markdown" id="markdown"
                      class="form-control form-control-sm w-50 text-right" readonly
                      value="{{ old('markdown', 0) }}">
                    @endif
                  </div>
                  <div class="d-flex justify-content-between py-1">
                    <strong>Discount (%)</strong>
                    @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']))
                    <input type="number" step="0.01" min="0" max="100" name="discount" id="discount"
                      class="form-control form-control-sm w-50 text-right"
                      value="{{ old('discount', 0) }}">
                    @else
                    <input type="number" step="0.01" min="0" max="100" name="discount" id="discount"
                      class="form-control form-control-sm w-50 text-right" readonly
                      value="{{ old('discount', 0) }}">
                    @endif
                  </div>
                  <div class="d-flex justify-content-between py-1">
                    <strong>Tax</strong>
                    @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']))
                    <select name="tax_id" id="tax_id" class="form-control form-control-sm w-50 text-right">
                      <option value="">No Tax</option>
                      @foreach($taxes as $tax)
                      <option value="{{ $tax->id }}"
                        data-percentage="{{ $tax->tax_percentage }}"
                        {{ old('tax_id') == $tax->id ? 'selected' : '' }}>
                        {{ $tax->tax_name }} ({{ $tax->tax_percentage }}%)
                      </option>
                      @endforeach
                    </select>
                    @else
                    <span class="form-control form-control-sm w-50 text-right" style="background:#e9ecef; display:inline-block;">
                      No Tax
                    </span>
                    <input type="hidden" name="tax_id" value="">
                    @endif
                  </div>
                  <div class="d-flex justify-content-between py-1">
                    <strong>Adjustment</strong>
                    @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'superadmin']))
                    <input type="number" step="0.01" name="adjustment" id="adjustment"
                      class="form-control form-control-sm w-50 text-right"
                      value="{{ old('adjustment', 0) }}">
                    @else
                    <input type="number" step="0.01" name="adjustment" id="adjustment"
                      class="form-control form-control-sm w-50 text-right" readonly
                      value="{{ old('adjustment', 0) }}">
                    @endif
                  </div>
                  <hr class="my-2">
                  <div class="d-flex justify-content-between py-1">
                    <strong>Grand Total</strong>
                    <input type="text" name="grand_total" id="grand_total"
                      class="form-control form-control-sm w-50 text-right font-weight-bold"
                      readonly value="{{ old('grand_total', 0) }}">
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- ── Terms / Notes / Status ───────────────────────────────── --}}
          <div class="row">
            <div class="form-group col-md-6">
              <label>Terms &amp; Conditions</label>
              <textarea name="terms" class="form-control" rows="2">{{ old('terms', $terms ?? '') }}</textarea>
            </div>
            <div class="form-group col-md-6">
              <label>Notes</label>
              <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
            </div>

            <div class="form-group col-md-3">
              <label>Status</label>
              @if($lockedStatus)
              <input type="hidden" name="status" value="pending">
              <div><span class="badge badge-secondary">Pending</span></div>
              @else
              <select name="status" class="form-control">
                <option value="pending" {{ old('status','pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ old('status','pending') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="shipped" {{ old('status','pending') == 'shipped'   ? 'selected' : '' }}>Shipped</option>
                <option value="partial_dispatch" {{ old('status','pending') == 'partial_dispatch' ? 'selected' : '' }}>Partial Dispatch</option>
                <option value="cancelled" {{ old('status','pending') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
              </select>
              @endif
            </div>
          </div>

        </div>
      </div>

      <div class="mt-2 mb-2 mr-3 text-right">
        <a href="{{ route('orders.index') }}" class="btn-cancel mr-2"><i class="fas fa-times mr-1"></i>Cancel</a>
        <button type="submit" class="btn-submit"><i class="fas fa-save mr-1"></i>Save Order</button>
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
      <div class="mb-3">
        <label class="variant-drawer-label mb-1">Color</label>
        <select id="variantDrawerColor" class="form-control"></select>
      </div>
      <div class="variant-drawer-sizes" id="variantDrawerSizes"></div>
      <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="variant-drawer-label mb-0">Selected Size</span>
        <button type="button" class="btn btn-link btn-sm p-0 text-danger" id="variantClearAll">Clear All</button>
      </div>
      <div class="variant-selected-list" id="variantSelectedList"></div>
      <div class="variant-total-row">
        Total Quantity
        <span class="variant-total-badge" id="variantDrawerTotal">0</span>
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
    0% {
      background-color: transparent;
    }

    30% {
      background-color: #ffe0e0;
    }

    100% {
      background-color: transparent;
    }
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

  /* Size drawer buttons — unselected */
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

  /* Size drawer buttons — selected */
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
</style>
<script>
  $(function() {

    var ALL_SIZES = @json($sizesJson);
    var ITEMS = @json($itemsJson);
    var COLORS = @json($colors);
    var IS_RETAILER = @json(optional(auth()->user())->hasRole('retailer') ?? false);
    var IS_SUPER_ADMIN = @json(optional(auth()->user())->hasRole(['super-admin', 'superadmin']) ?? false);
    var IS_DISTRIBUTOR = @json(optional(auth()->user())->hasRole('distributor') ?? false);
    var IS_LOCKED_STATUS = @json($lockedStatus ?? false);

    /* ── helpers ──────────────────────────────────────────────────────────── */
    function itemByArticle(val) {
      return ITEMS.find(function(i) {
        return i.article_number == val || i.id == val;
      });
    }

    function rowIndex($row) {
      var name = $row.find('.qty').attr('name') || '';
      var m = name.match(/items\[(\d+)\]/);
      return m ? m[1] : 0;
    }

    function esc(v) {
      return String(v).replace(/[&<>"']/g, function(c) {
        return ({
          '&': '&amp;',
          '<': '&lt;',
          '>': '&gt;',
          '"': '&quot;',
          "'": '&#039;'
        })[c];
      });
    }

    function normalizeArr(v) {
      if (!v) return [];
      return Array.isArray(v) ? v.map(String) : String(v).split(',').map(function(x) {
        return x.trim();
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
    
    function colorCount($row) {
      var $cs = $row.find('.color-select');
      if ($cs.length) {
        var val = $cs.val();
        if (Array.isArray(val)) {
          return val.length;
        }
        return val ? 1 : 0;
      }
      // non-super-admin: count hidden color inputs
      var hidden = $row.find('input[type=hidden][name$="[color][]"]').length;
      return hidden;
    }

    function colorOpts(colors, sel) {
      sel = normalizeArr(sel);
      // Use only the passed colors (article-specific). Never fall back to global COLORS.
      // Build <option> list for Select2 dropdown.
      colors = (colors && colors.length) ? colors : [];

      return colors.map(function(c) {
        var id = String(c.id);
        return '<option value="' + esc(id) + '"' +
          (sel.indexOf(id) !== -1 ? ' selected' : '') +
          '>' + esc(c.color_code ? c.color_code : c.name) +
          '</option>';
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
      var selArr = normalizeArr(sel).filter(function(id) {
        return availableIds.indexOf(id) !== -1;
      });
      if ($cs.length) {
        // Temporarily detach the change listener so rebuilding Select2 does NOT fire recalc
      $cs.off('change.colorrebuild');
        if ($cs.hasClass('select2-hidden-accessible')) $cs.select2('destroy');
        // If colors are empty, keep a placeholder option so Select2 doesn't render empty.
        var html = colorOpts(colors, selArr);
        if (!html) {
          html = '<option value="">--</option>';
        }
        $cs.html(html);
        $cs.select2({
          placeholder: 'Colors…',
          width: '100%'
        });

      } else {
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
            $cell.append('<input type="hidden" name="items[' + rowIndex($row) + '][color][]" value="' + esc(id) + '">');
          });
        }
      }
    }

    /* ── Recalc totals (NO ITEM TAX) ────────────────────────────────────── */

    function recalc() {
      var sub = 0;
      $('#itemTable tbody tr').each(function() {
        var $tr = $(this);
        var qty = parseFloat($tr.find('.qty').val()) || 0;
        var rate = parseFloat($tr.find('.rate').val()) || 0;
        // No individual item tax calculation
        var tot = rate * qty;
        $tr.find('.total').val(tot.toFixed(2));
        sub += tot;
      });
      $('#subtotal').val(sub.toFixed(2));
      
      var markdownPercent = parseFloat($('#markdown').val()) || 0;
      var discountPercent = parseFloat($('#discount').val()) || 0;
      
      // Get tax percentage from the selected tax dropdown
      var taxPercent = 0;
      var $taxSelect = $('#tax_id');
      if ($taxSelect.length && $taxSelect.val()) {
        var selectedOption = $taxSelect.find('option:selected');
        taxPercent = parseFloat(selectedOption.data('percentage')) || 0;
      }
      
      // Calculate after markdown and discount
      var afterMarkdown = sub - (sub * markdownPercent / 100);
      var afterDiscount = afterMarkdown - (afterMarkdown * discountPercent / 100);
      var taxAmount = afterDiscount * taxPercent / 100;
      var grand = afterDiscount + taxAmount + (parseFloat($('#adjustment').val()) || 0);
      
      $('#grand_total').val(grand.toFixed(2));
    }

    /* ── Size chip UI ─────────────────────────────────────────────────────── */
    function rebuildSizePanel($row) {
      var idx = rowIndex($row);
      var $select = $row.find('.size-select');
      var $panel = $row.find('.size-qty-wrapper');
      var selected = ($select.val() || []).map(String);

      // Sync chip active states
      $row.find('.size-chip').each(function() {
        var s = String($(this).data('size'));
        $(this).toggleClass('active', selected.indexOf(s) !== -1);
      });

      if (!selected.length) {
        $panel.hide().html('');
        $row.find('.qty').val(0); // no sizes → qty is 0
        recalc();
        return;
      }

      // Snapshot existing qty values BEFORE we wipe the panel
      var oldQtys = {};
      $panel.find('.size-qty-item').each(function() {
        var sz = String($(this).data('size'));
        oldQtys[sz] = $(this).find('.size-qty').val();
      });

      var html = selected.map(function(sz) {
        var q = (oldQtys[sz] !== undefined && oldQtys[sz] !== '') ? oldQtys[sz] : 0;
        return '<div class="size-qty-item" data-size="' + esc(sz) + '">' +
          '<span class="size-qty-label">' + esc(sz) + '</span>' +
          '<div class="size-stepper">' +
          (IS_SUPER_ADMIN ? '<button type="button" class="stepper-btn minus">−</button>' : '') +
          (IS_SUPER_ADMIN ?
            '<input type="text" step="1" min="0" name="items[' + idx + '][size_quantities][' + esc(sz) + ']" class="size-qty" value="' + esc(q) + '">' :
            '') +
          (IS_SUPER_ADMIN ? '<button type="button" class="stepper-btn plus">+</button>' : '') +
          '</div>' +
          '</div>';
      }).join('');

      if (IS_SUPER_ADMIN) {
        html += '<div class="size-qty-total"><small>Total</small><span class="total-qty-badge">0</span></div>';
      }

      $panel.html(html).show();
      updateTotalQtyBadge($row);
      updateRowQty($row); // qty = sum(size qtys) × colorCount
      recalc();
    }

    // Badge shows the raw size-qty sum (per-color piece count) for clarity
    function updateTotalQtyBadge($row) {
      var tot = 0;
      $row.find('.size-qty').each(function() {
        tot += parseFloat($(this).val()) || 0;
      });
      $row.find('.total-qty-badge').text(tot + ' × ' + colorCount($row) + ' colors = ' + (tot * colorCount($row)));
    }

    function updateRowQty($row) {
      var sizeSum = 0;
      $row.find('.size-qty').each(function() {
        sizeSum += parseFloat($(this).val()) || 0;
      });
      var colors = colorCount($row);
      $row.find('.qty').val(sizeSum * colors);
    }

    /* ── Size chip click ──────────────────────────────────────────────────── */
    $(document).on('click', '.size-chip', function() {
      if (!IS_SUPER_ADMIN) return;
      var $chip = $(this);
      var $row = $chip.closest('tr');
      var $sel = $row.find('.size-select');
      var size = String($chip.data('size'));
      var cur = ($sel.val() || []).map(String);

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

    /* ── Stepper +/- ──────────────────────────────────────────────────────── */
    $(document).on('click', '.stepper-btn', function() {
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
      var $row = $(this).closest('tr');
      updateTotalQtyBadge($row);
      updateRowQty($row);
      recalc();
    });

    // Rate changes recalculate totals
    $(document).on('input', '.rate', recalc);

    // Tax dropdown change event
    $(document).on('change', '#tax_id', function() {
      recalc();
    });

    // Other input events
    $('#markdown,#discount,#adjustment').on('input', recalc);

    // Color change → enforce only ONE color per row + load available sizes by item+color.
    $(document).on('change', '.color-select', function() {
      var $row = $(this).closest('tr');
      var $cs = $(this);

      // a) Enforce single color only
      var vals = normalizeArr($cs.val());

      // b) Immediately when color changes: clear size qty + reset summary
      $row.find('.size-select').val([]);
      $row.find('.size-qty-wrapper').hide().empty();
      $row.attr('data-available-sizes', JSON.stringify([]));
      $row.attr('data-stock-map', JSON.stringify({}));

      // show empty variant summary
      $row.find('.variant-table-summary').remove();
      if (IS_SUPER_ADMIN) {
        $row.find('td').eq(3).prepend(
          '<div class="variant-table-summary">' +
          '<span class="variant-empty-text">No Variants Added</span>' +
          '<button type="button" class="variant-edit-btn">' +
          '<i class="fas fa-pencil-alt mr-1"></i>Add Variants' +
          '</button>' +
          '</div>'
        );
      }

      // c) read item_id and color_id
      var itemId = ($row.find('.item-id-hidden').val() || '').trim();
      var colorId = (vals && vals.length) ? String(vals[0]) : '';

      // d) if empty → just update qty and return
      if (!itemId || !colorId) {
        updateRowQty($row);
        recalc();
        return;
      }

      // e) AJAX load sizes
      $.ajax({
        url: '/api/item-variants/sizes-by-color',
        method: 'GET',
        data: {
          item_id: itemId,
          color_id: colorId
        },
        success: function(resp) {
          var sizes = Array.isArray(resp && resp.sizes) ? resp.sizes : [];

          var availableSizes = sizes.map(function(s) {
            return String(s.label);
          });

          var stockMap = {};
          sizes.forEach(function(s) {
            var label = String(s.label);
            stockMap[label] = parseFloat(s.available_qty) || 0;
          });

          $row.attr('data-available-sizes', JSON.stringify(availableSizes));
          $row.attr('data-stock-map', JSON.stringify(stockMap));

          // Replace .size-select options with available sizes
          var $sz = $row.find('.size-select');
          if ($sz.hasClass('select2-hidden-accessible')) $sz.select2('destroy');
          $sz.html(availableSizes.map(function(sz) {
            return '<option value="' + esc(sz) + '">' + esc(variantSizeLabel(sz, stockMap)) + '</option>';
          }).join(''));

          // Rebuild .size-chips-wrap only if IS_SUPER_ADMIN
          if (IS_SUPER_ADMIN) {
            var $chips = $row.find('.size-chips-wrap');
            $chips.html(availableSizes.map(function(sz) {
              return '<button type="button" class="size-chip" data-size="' + esc(sz) + '">' + esc(sz) + '</button>';
            }).join(''));

            // if drawer is currently open for this row
            if (activeVariantRow && activeVariantRow[0] === $row[0]) {
              drawerSizes = [];
              drawerQtys = {};
              renderVariantDrawer();
            }
          }
        },
        error: function() {
          $row.attr('data-available-sizes', JSON.stringify([]));
          $row.attr('data-stock-map', JSON.stringify({}));
        },
        complete: function() {
          updateRowQty($row);
          recalc();
        }
      });
    });


    /* ── Article select → auto-fill row ──────────────────────────────────── */
    $(document).on('change', '.article-select', function() {
      var $row = $(this).closest('tr');
      var val = $(this).val();
      if (!val) return;

      var found = itemByArticle(val);
      if (!found) {
        var $opt = $(this).find('option:selected');
        found = {
          id: $opt.data('id') || null,
          name: $opt.data('name') || '',
          rate: parseFloat($opt.data('rate')) || 0,
          desc: $opt.data('desc') || ''
        };
      }

      $row.find('.item-id-hidden').val(found.id || '');
      $row.find('.item-name-input').val(found.name || found.article_number || '');
      $row.find('.rate').val(found.rate || 0);
      if (!$row.find('.desc').val()) $row.find('.desc').val(found.desc || '');

      // Populate colors from this selected item ONLY (many-to-many)
      populateColorSelect($row, found.colors || [], []);

      // Keep sizes empty until a color is selected; the color change handler loads only sizes available for that color.
      $row.attr('data-available-sizes', JSON.stringify([]));
      $row.attr('data-stock-map', JSON.stringify({}));
      var sizeChoices = [];
      var $sz = $row.find('.size-select');
      if ($sz.hasClass('select2-hidden-accessible')) $sz.select2('destroy');
      $sz.html('');

      // Rebuild chips
      var $chips = $row.find('.size-chips-wrap');
      if (IS_SUPER_ADMIN) {
        $chips.empty();
      } else {
        $chips.empty();
      }

      // BUG FIX #6: When article changes, clear size panel and reset qty to 0
      // so stale qty from a previous item doesn't carry over.
      $row.find('.size-select').val([]);
      $row.find('.size-qty-wrapper').hide().html('');
      $row.find('.qty').val(0);

      if (($row.find('.color-select').val() || []).length) {
        try {
          $row.find('.color-select').trigger('change');
        } catch (e) {}
      } else {
        rebuildSizePanel($row);
      }
      recalc();
    });

    /* ── Build new row HTML ──────────────────────────────────────────────── */
    function buildRow(idx, it) {
      it = it || {};
      var artOpts = '<option value="">--</option>' + ITEMS.map(function(m) {
        return '<option value="' + esc(m.article_number || '') + '"' +
          ' data-id="' + (m.id || '') + '"' +
          ' data-rate="' + (m.rate || 0) + '"' +
          ' data-desc="' + (String(m.desc || '').replace(/"/g, '&quot;')) + '"' +
          (it.item_id == m.id ? ' selected' : '') +
          '>' + esc(m.article_number || '') + '</option>';
      }).join('');

      var colOpts = colorOpts(it.colors || [], it.color || it.color_id || []);

      var sizeChips = IS_SUPER_ADMIN ?
        ALL_SIZES.map(function(s) {
          return '<button type="button" class="size-chip" data-size="' + esc(s) + '">' + esc(s) + '</button>';
        }).join('') :
        '';

      var sizeOpts = ALL_SIZES.map(function(s) {
        return '<option value="' + s + '">' + s + '</option>';
      }).join('');

      var statusSel = IS_LOCKED_STATUS ?
        '<input type="hidden" name="items[' + idx + '][status]" value="pending"><span class="badge badge-secondary">Pending</span>' :
        '<select name="items[' + idx + '][status]" class="form-control status-select" style="font-size:12px!important;">' +
        ['pending', 'confirmed', 'shipped', 'cancelled'].map(function(s) {
          return '<option value="' + s + '"' + (it.status && it.status == s ? ' selected' : '') + '>' + s.charAt(0).toUpperCase() + s.slice(1) + '</option>';
        }).join('') +
        '</select>';

      return '<tr>' +
        '<td>' +
        (IS_SUPER_ADMIN ?
          '<select name="items[' + idx + '][article_number]" class="form-control article-select">' + artOpts + '</select>' :
          (function() {
            var found = ITEMS.find(function(i) {
              return i.id == it.item_id;
            });
            var artNum = found ? found.article_number : '';
            return '<input type="text" class="form-control" value="' + esc(artNum) + '" readonly>' +
              '<input type="hidden" name="items[' + idx + '][article_number]" value="' + esc(artNum) + '">';
          })()
        ) +
        '<input type="hidden" name="items[' + idx + '][item_id]" class="item-id-hidden" value="' + (it.item_id || '') + '">' +
        '</td>' +
        '<td><input type="text" name="items[' + idx + '][item_name]" class="form-control item-name-input" value="' + (it.item_name || '') + '" readonly></td>' +
        (IS_SUPER_ADMIN ?
          '<td class="color-cell"><select name="items[' + idx + '][color][]" class="form-control color-select">' + colOpts + '</select></td>' :
          (function() {
            var sel = normalizeArr(it.color || it.color_id || []);
            var names = sel.map(function(id) {
              var c = COLORS.find(function(x) {
                return String(x.id) == String(id);
              });
              return c ? (c.color_code ? c.color_code : c.name) : '';
            }).filter(Boolean).join(', ');
            var hidden = sel.map(function(id) {
              return '<input type="hidden" name="items[' + idx + '][color][]" value="' + esc(id) + '">';
            }).join('');
            return '<td class="color-cell"><input type="text" class="form-control color-read" readonly value="' + esc(names) + '">' + hidden + '</td>';
          })()
        ) +
        '<td>' +
        '<select name="items[' + idx + '][sizes][]" class="size-select d-none" multiple>' + sizeOpts + '</select>' +
        '<div class="size-chips-wrap">' + sizeChips + '</div>' +
        (IS_SUPER_ADMIN ?
          '<div class="size-qty-wrapper size-qty-panel" style="display:none;"></div>' :
          '<div class="form-control size-readonly-box" readonly>' +
          (it.sizes ? normalizeArr(it.sizes).join(', ') : '') +
          '</div>'
        ) +
        '</td>' +
        '<td><input type="number" step="0.01" name="items[' + idx + '][quantity]" class="form-control qty" value="' + (it.quantity || 0) + '" readonly></td>' +
        '<td><input type="number" step="0.01" name="items[' + idx + '][rate]" class="form-control rate" value="' + (it.rate || 0) + '" readonly></td>' +
        '<td><input type="number" step="0.01" name="items[' + idx + '][total]" class="form-control total" value="' + (it.total || 0) + '" readonly></td>' +
        '<td>' + statusSel + '</td>' +
        '<td><button type="button" class="btn btn-sm btn-danger remove-item"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
    }

    var rowCounter = $('#itemTable tbody tr').length;

    /* ── Add Row ─────────────────────────────────────────────────────────── */
    $('#addItem').on('click', function() {
      if (!IS_SUPER_ADMIN) {
        alert('Not allowed');
        return;
      }
      $('#itemTable tbody').append(buildRow(rowCounter));
      var $new = $('#itemTable tbody tr:last');
      $new.find('.color-select').select2({
        placeholder: 'Colors…',
        width: '100%'
      });
      $new.find('.article-select').select2({
        placeholder: 'Article',
        width: '100%'
      });
      rowCounter++;
      recalc();
      updateRowNumbers();
      updateRemoveButtonsState();
    });

    /* ── Remove Row ─────────────────────────────────────────────────────── */
    function updateRemoveButtonsState() {
      var onlyOneRow = $('#itemTable tbody tr').length <= 1;
      $('#itemTable tbody .remove-item, #itemTable tbody .deleteButton')
        .prop('disabled', onlyOneRow)
        .toggleClass('disabled', onlyOneRow);
    }

    $(document).on('click', '.remove-item, .deleteButton', function() {
      if ($('#itemTable tbody tr').length <= 1) return;
      $(this).closest('tr').remove();
      updateRowNumbers();
      updateRemoveButtonsState();
      recalc();
    });

    function updateRowNumbers() {
      $('#itemTable tbody tr').each(function(i) {
        $(this).find('.row-num').text(String(i + 1).padStart(2, '0'));
      });
    }

    /* ── Customer → addresses ────────────────────────────────────────────── */
    $('#customer_id').on('change', function() {
      var id = $(this).val();
      if (!id) {
        $('#billing_address,#shipping_address').val('');
        return;
      }
      fetch("{{ url('customer') }}/" + id)
        .then(function(r) {
          return r.ok ? r.json() : Promise.reject(r.status);
        })
        .then(function(d) {
          $('#billing_address').val(d.billing_address || '');
          $('#shipping_address').val(d.shipping_address || '');
        })
        .catch(function() {
          $('#billing_address,#shipping_address').val('');
        });
    });

    /* ── Init Select2 on existing rows ──────────────────────────────────── */
    if ($.fn.select2) {
      // We explicitly do NOT call .trigger('change') here to avoid spurious qty updates.
      $('.color-select').each(function() {
        $(this).select2({
          placeholder: 'Colors…',
          width: '100%'
        });
      });
      $('.article-select').each(function() {
        $(this).select2({
          placeholder: 'Article',
          width: '100%'
        });
      });
      $('#itemTable tbody tr').each(function() {
        var $row = $(this);
        if (($row.find('.color-select').val() || []).length) {
          setTimeout(function() {
            $row.find('.color-select').trigger('change');
          }, 0);
        }
      });
    }

    $(document).on('change', '.status-select', function() {
      var $select = $(this);
      var newStatus = $select.val();
      if (newStatus !== 'shipped') {
        return;
      }

      var $row = $select.closest('tr');
      var stockMap = {};
      try {
        stockMap = JSON.parse($row.attr('data-stock-map') || '{}');
      } catch (e) {
        stockMap = {};
      }

      var sizes = [];
      $row.find('.size-qty').each(function() {
        var $input = $(this);
        var size = String($input.closest('.size-qty-item').data('size') || $input.closest('.size-qty-row').data('size') || '');
        var qty = parseInt($input.val(), 10) || 0;
        if (size) {
          sizes.push({ size: size, qty: qty });
        }
      });

      if (!sizes.length || !Object.keys(stockMap).length) {
        alert('Please select item color and sizes before marking shipped.');
        $select.val('confirmed');
        return;
      }

      var insufficient = sizes.some(function(entry) {
        var available = stockMap[entry.size] !== undefined ? parseInt(stockMap[entry.size], 10) : null;
        return available === null || entry.qty > available;
      });

      if (insufficient) {
        alert('Stock is insufficient for shipping. Order will still be saved with the selected status.');
      }
    });

    // Init size chip states for old() data rows
    $('#itemTable tbody tr').each(function() {
      var $row = $(this);
      var selected = ($row.find('.size-select').val() || []).map(String);
      if (selected.length) {
        $row.find('.size-chip').each(function() {
          $(this).toggleClass('active', selected.indexOf(String($(this).data('size'))) !== -1);
        });
        if ($row.find('.size-qty-wrapper .size-qty-item').length) {
          $row.find('.size-qty-wrapper').show();
          updateTotalQtyBadge($row);
          // Ensure qty field reflects actual size-qty sum (not doubled by colors)
          updateRowQty($row);
        }
      }
    });

    /* ── PRE-FILL from controller ─────────────────────────────────────────── */
    var PRE_ITEM_ID = @json($pre_item_id ?? null);
    var PRE_USER_ID = @json($pre_user_id ?? null);
    var PRE_ITEMS = @json($pre_items ?? null);

    if (PRE_USER_ID) {
      $('#customer_id').val(PRE_USER_ID).trigger('change');
    } else {
      $('#customer_id').trigger('change');
    }

    if (PRE_ITEM_ID) {
      var $first = $('#itemTable tbody tr').first();
      if ($first.length) {
        var $sel = $first.find('.article-select');
        var found = ITEMS.find(function(i) {
          return i.id == PRE_ITEM_ID;
        });
        if (found) $sel.val(found.article_number).trigger('change');
      }
    }

    if (Array.isArray(PRE_ITEMS) && PRE_ITEMS.length) {
      $('#itemTable tbody tr').each(function() {
        var $r = $(this);
        if (!$r.find('.article-select').val() && !$r.find('.item-id-hidden').val() && !$r.find('.item-name-input').val()) {
          $r.remove();
        }
      });
      rowCounter = $('#itemTable tbody tr').length;

      PRE_ITEMS.forEach(function(it) {
        var idx = rowCounter++;
        var rd = {
          item_id: it.item_id || it.id || null,
          item_name: it.item_name || it.name || '',
          rate: it.rate || 0,
          quantity: it.quantity || it.qty || 0,
          description: it.description || '',
          color: it.color || it.color_id || null,
          sizes: it.sizes || it.size || null,
          size_quantities: it.size_quantities || null
        };
        $('#itemTable tbody').append(buildRow(idx, rd));
        var $tr = $('#itemTable tbody tr:last');

        if ($.fn.select2) {
          $tr.find('.color-select').select2({
            placeholder: 'Colors…',
            width: '100%'
          });
          $tr.find('.article-select').select2({
            placeholder: 'Article',
            width: '100%'
          });
        }

        if (rd.item_id) {
          var fa = (ITEMS.find(function(x) {
            return x.id == rd.item_id;
          }) || {}).article_number || '';
          if (fa) $tr.find('.article-select').val(fa).trigger('change');
        }

        // Set colors without triggering any qty recalc
        if (rd.color) {
          try {
            $tr.find('.color-select').val(normalizeArr(rd.color));
            if ($tr.find('.color-select').hasClass('select2-hidden-accessible')) {
              $tr.find('.color-select').trigger('change.select2');
            }
          } catch (e) {}
        }

        if (rd.sizes) {
          var sv = Array.isArray(rd.sizes) ?
            rd.sizes :
            String(rd.sizes).split(',').map(function(s) {
              return s.trim();
            });
          $tr.find('.size-select').val(sv);
          rebuildSizePanel($tr);

          // Apply saved size_quantities after panel is built
          if (rd.size_quantities && typeof rd.size_quantities === 'object') {
            Object.keys(rd.size_quantities).forEach(function(sz) {
              $tr.find('.size-qty-item[data-size="' + sz + '"] .size-qty').val(rd.size_quantities[sz]);
            });
            updateTotalQtyBadge($tr);
            updateRowQty($tr); // qty = sum of sizes only
          }
        }

        // BUG FIX #8: Final qty sync — always derive from size_quantities sum.
        // Never multiply by color count.
        (function() {
          var finalQty = 0;
          if (rd.size_quantities && typeof rd.size_quantities === 'object') {
            Object.keys(rd.size_quantities).forEach(function(sz) {
              finalQty += parseFloat(rd.size_quantities[sz]) || 0;
            });
          }
          if (!finalQty) {
            // Fallback to the stored quantity field if no size breakdown available
            finalQty = parseFloat(rd.quantity) || parseFloat(rd.qty) || 0;
          }
          $tr.find('.qty').val(finalQty);
        })();
      });

      updateRowNumbers();
      updateRemoveButtonsState();
      recalc();
    }

    // Trigger change on tax dropdown to set initial percentage
    $(document).ready(function() {
      if ($('#tax_id').length) {
        // Just initialize, no need to trigger change
      }
    });

    /* ── Variant Drawer ──────────────────────────────────────────────────── */
    var activeVariantRow = null;
    var drawerSizes = [];
    var drawerQtys = {};
    var activeVariantColor = null;

    function variantSelectedColors($row) {
      var val = $row.find('.color-select').val();
      if (Array.isArray(val)) {
        return val.map(String).filter(Boolean);
      }
      return val ? [String(val)] : [];
    }

    function variantColorLabel(colorId) {
      colorId = String(colorId);
      var color = (COLORS || []).find(function(c) {
        return String(c.id) === colorId;
      });
      return color ? (color.color_code || color.name || colorId) : colorId;
    }

    function populateVariantDrawerColorSelect($row) {
      var colors = variantSelectedColors($row);
      var $select = $('#variantDrawerColor');
      $select.empty();
      if (!colors.length) {
        $select.append('<option value="">Select color first</option>');
        $select.prop('disabled', true);
        activeVariantColor = null;
        return;
      }
      colors.forEach(function(colorId) {
        $select.append('<option value="' + variantEscape(colorId) + '">' + variantEscape(variantColorLabel(colorId)) + '</option>');
      });
      activeVariantColor = activeVariantColor && colors.indexOf(activeVariantColor) !== -1 ? activeVariantColor : colors[0];
      $select.val(activeVariantColor).prop('disabled', false);
    }

    function loadVariantSizesForColor($row, colorId, callback) {
      var itemId = String($row.find('.item-id-hidden').val() || '').trim();
      if (!itemId || !colorId) {
        $row.attr('data-available-sizes', JSON.stringify([]));
        $row.attr('data-stock-map', JSON.stringify({}));
        if (typeof callback === 'function') callback();
        return;
      }
      $.ajax({
        url: '/api/item-variants/sizes-by-color',
        method: 'GET',
        dataType: 'json',
        data: { item_id: itemId, color_id: colorId },
        success: function(resp) {
          var sizes = Array.isArray(resp && resp.sizes) ? resp.sizes : [];
          var availableSizes = sizes.map(function(s) {
            return String(s.label);
          });
          var stockMap = {};
          sizes.forEach(function(s) {
            stockMap[String(s.label)] = parseFloat(s.available_qty) || 0;
          });
          $row.attr('data-available-sizes', JSON.stringify(availableSizes));
          $row.attr('data-stock-map', JSON.stringify(stockMap));
          if (typeof callback === 'function') callback();
        },
        error: function() {
          $row.attr('data-available-sizes', JSON.stringify([]));
          $row.attr('data-stock-map', JSON.stringify({}));
          if (typeof callback === 'function') callback();
        }
      });
    }

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
        if (size !== undefined) qtys[String(size)] = parseFloat($(this).val()) || 0;
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
      // totalQty = sum of size quantities × number of colors
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
      $('#itemTable tbody tr').each(function() {
        refreshVariantCell($(this));
      });
    }

    function renderVariantDrawer() {
      var stockMap = variantStockMap(activeVariantRow);
      $('#variantDrawerSizes').html(
        variantSizeOptions(activeVariantRow).map(function(size) {
          var active = drawerSizes.indexOf(String(size)) !== -1 ? ' active' : '';
          var label = variantSizeLabel(size, stockMap);
          return '<button type="button" class="variant-drawer-size' + active + '" data-size="' + variantEscape(size) + '">' +
            '<span>' + variantEscape(size) + '</span>' +
            '</button>';
        }).join('') || '<div class="variant-size-empty text-muted small">No sizes available for selected color</div>'
      );

      var hasStockMap = Object.keys(stockMap).length > 0;

      $('#variantSelectedList').html(
        drawerSizes.map(function(size) {
          var qty = drawerQtys[size] || 1;
          var available = hasStockMap && stockMap[size] !== undefined ? parseFloat(stockMap[size]) : null;
          var warningHtml = '';

          if (hasStockMap && available !== null && !isNaN(available)) {
            if (parseFloat(qty) >= available) {
              warningHtml = '<div class="stock-warning text-danger" style="font-size:11px;">⚠ Only ' + esc(available) + ' units available</div>';
            } else {
              warningHtml = '<div class="stock-ok text-success" style="font-size:11px;">✓ ' + esc(available) + ' available</div>';
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
            warningHtml +
            '</div>';
        }).join('') || '<div class="variant-selected-empty">Select sizes from above</div>'
      );

      if (hasStockMap) {
        var hasAnyOver = drawerSizes.some(function(size) {
          var available = stockMap[size];
          if (available === undefined) return false;
          return parseFloat(drawerQtys[size]) > parseFloat(available);
        });

        if (hasAnyOver) {
          $('#variantDrawerBackdrop').attr('data-has-stock-warning', 'true');
        } else {
          $('#variantDrawerBackdrop').removeAttr('data-has-stock-warning');
        }
      } else {
        // skip stock checks silently when stockMap empty
        $('#variantDrawerBackdrop').removeAttr('data-has-stock-warning');
        $('#variantSaveBtn').prop('disabled', false).removeAttr('title');
      }

      // Total = sum of size qtys × colors selected on that row
      var colorMult = activeVariantRow ? colorCount(activeVariantRow) : 1;
      var total = drawerSizes.reduce(function(sum, size) {
        return sum + (parseFloat(drawerQtys[size]) || 0);
      }, 0) * colorMult;
      $('#variantDrawerTotal').text(total + (colorMult > 1 ? ' (' + (total / colorMult) + ' × ' + colorMult + ' colors)' : ''));
    }


    function openVariantDrawer($row) {
      if (!IS_SUPER_ADMIN) return;
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
      populateVariantDrawerColorSelect($row);
      if (activeVariantColor) {
        loadVariantSizesForColor($row, activeVariantColor, function() {
          drawerSizes = drawerSizes.filter(function(size) {
            return variantSizeOptions(activeVariantRow).indexOf(String(size)) !== -1;
          });
          drawerSizes.forEach(function(size) {
            if (!drawerQtys[size]) drawerQtys[size] = 1;
          });
          renderVariantDrawer();
        });
      } else {
        renderVariantDrawer();
      }
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

      // 1. Commit selected sizes to the hidden select
      activeVariantRow.find('.size-select').val(drawerSizes);

      // 2. Wipe panel so rebuildSizePanel starts with clean oldQtys = {}
      activeVariantRow.find('.size-qty-wrapper').html('');

      // 3. Rebuild panel (will find no oldQtys → sets all to 0)
      rebuildSizePanel(activeVariantRow);

      // 4. Apply drawer quantities — these are the authoritative values
      drawerSizes.forEach(function(size) {
        activeVariantRow
          .find('.size-qty-item[data-size="' + size + '"] .size-qty')
          .val(drawerQtys[size] || 1);
      });

      // 5. Recompute totals from the freshly written qty inputs
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
    $('#variantDrawerBackdrop').on('click', function(e) {
      if (e.target === this) closeVariantDrawer();
    });
    $('#variantDrawerColor').on('change', function() {
      if (!activeVariantRow) return;
      activeVariantColor = $(this).val();
      loadVariantSizesForColor(activeVariantRow, activeVariantColor, function() {
        drawerSizes = drawerSizes.filter(function(size) {
          return variantSizeOptions(activeVariantRow).indexOf(String(size)) !== -1;
        });
        drawerSizes.forEach(function(size) {
          if (!drawerQtys[size]) drawerQtys[size] = 1;
        });
        renderVariantDrawer();
      });
    });
    $(document).on('click', '.variant-drawer-size', function() {
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
      var $row = $(this).closest('.variant-selected-row');
      var size = String($row.data('size'));
      var qty = parseFloat(drawerQtys[size]) || 0;

      var rawStock = activeVariantRow ? activeVariantRow.attr('data-stock-map') : null;
      var stockMap = {};
      try {
        stockMap = rawStock ? JSON.parse(rawStock) : {};
      } catch (e) {
        stockMap = {};
      }
      var hasStockMap = stockMap && typeof stockMap === 'object' && Object.keys(stockMap).length > 0;

      if ($(this).hasClass('variant-drawer-plus')) {
        // skip stock checks silently when stockMap empty
        if (hasStockMap && stockMap[size] !== undefined) {
          var available = parseFloat(stockMap[size]);
          if (parseFloat(qty + 1) > available) {
            var $warningDiv = $row.find('.stock-warning').first();
            $warningDiv.addClass('flash-warning');
            setTimeout(function() {
              $warningDiv.removeClass('flash-warning');
            }, 600);
          }
        }
        var newQty = qty + 1;
        drawerQtys[size] = newQty;
        renderVariantDrawer();
        return;
      }

      // minus
      drawerQtys[size] = Math.max(1, qty - 1);
      renderVariantDrawer();
    });

    /* ── Drawer qty: direct typing with stock cap ────────────────────────── */
    $(document).on('input', '.variant-drawer-qty', function() {
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
      } else {
        $('#variantDrawerBackdrop').removeAttr('data-has-stock-warning');
      }

      var colorMult = activeVariantRow ? colorCount(activeVariantRow) : 1;
      var total = Object.keys(drawerQtys).reduce(function(sum, s) {
        return sum + (parseFloat(drawerQtys[s]) || 0);
      }, 0) * colorMult;
      $('#variantDrawerTotal').text(total +
        (colorMult > 1 ? ' (' + (total / colorMult) + ' × ' + colorMult + ' colors)' : ''));
    });

    $(document).on('blur', '.variant-drawer-qty', function() {
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

      $input.val(val);
      drawerQtys[size] = val;

      // Full re-render after blur to sync all warnings
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
      setTimeout(function() {
        refreshVariantCell($row);
      }, 0);
    });
    $('#addItem').on('click', function() {
      setTimeout(refreshAllVariantCells, 0);
    });

    refreshAllVariantCells();
    updateRemoveButtonsState();
    recalc();
  });
</script>
@endsection