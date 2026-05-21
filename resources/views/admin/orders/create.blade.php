@extends('admin.layouts.app')
@section('title', 'Create Order')

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
      @if(!empty($pre_items))
        <input type="hidden" name="from_cart" value="1">
      @endif

      <div class="card"style="padding:10px;">
        <div class="card-body">

          {{-- ── Customer / Dates ────────────────────────────────────────── --}}
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Customer Name <span class="text-danger">*</span></label>
                <select name="user_id" id="customer_id" class="form-control" required>
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

          {{-- ── Mode Toggle ───────────────────────────────────────────── --}}
          <!-- <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="m-0">Items</h5>
            <div class="btn-group btn-group-sm">
              <button type="button" id="modeNormal" class="btn btn-outline-secondary active">Normal</button>
              <button type="button" id="modeSizeRange" class="btn btn-outline-secondary">Size Range</button>
            </div>
          </div> -->

          {{-- ── Normal Items Table ────────────────────────────────────── --}}
          <div id="normalTable">
            <div class="table-responsive" style="overflow-x:auto;">
              <table class="table table-sm table-bordered" id="itemTable">
              <thead class="thead-light">
                <tr>
                  <th>Article Number</th>
                  <th>Item</thh=>
                  <th>Color</th>
                  <th>Size(s)</th>
                  <th>Description</th>
                  <th>Qty</th>
                  <th>MRP</th>
                  <th>Tax %</thwidth=>
                  <th>Total</thwidth=>
                  <th>Status</thdth=>
                  <th>Action</thwidth=>
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
                        data-tax="{{ $itm->tax_percent ?? 0 }}"
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
                    {{-- Color select / readonly for non-super-admin --}}
                    @php
                      $selectedColors = $it['color'] ?? $it['color_id'] ?? [];
                      if (!is_array($selectedColors)) {
                        $selectedColors = explode(',', $selectedColors);
                      }
                      $selectedColors = array_map('trim', $selectedColors);
                      $rowItem = !empty($it['item_id'] ?? null) ? $items->firstWhere('id', $it['item_id']) : null;
                      $rowColors = ($rowItem && $rowItem->colors->isNotEmpty()) ? $rowItem->colors : $colors;
                    @endphp
                    @if(auth()->user() && auth()->user()->hasRole('super-admin'))
                      <select name="items[{{ $i }}][color][]" class="form-control color-select select2" multiple>
                        @foreach($rowColors as $col)
                        <option value="{{ $col->id }}" {{ in_array((string) $col->id, $selectedColors) ? 'selected' : '' }}>{{ $col->name }}</option>
                        @endforeach
                      </select>
                    @else
                      @php
                        $selectedNames = [];
                        foreach($rowColors as $col) {
                          if (in_array((string)$col->id, $selectedColors)) $selectedNames[] = $col->name;
                        }
                      @endphp
                      <input type="text" class="form-control color-read" readonly value="{{ implode(', ', $selectedNames) }}">
                      @foreach($selectedColors as $sc)
                        <input type="hidden" name="items[{{ $i }}][color][]" value="{{ $sc }}">
                      @endforeach
                    @endif
                  </td>
                  <td>
                    {{-- Size multi-select --}}
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
                  <td><input type="text" name="items[{{ $i }}][description]" class="form-control desc" value="{{ $it['description'] ?? '' }}" readonly></td>
                  <td><input type="number" step="0.01" name="items[{{ $i }}][quantity]" class="form-control qty" value="{{ $it['quantity'] ?? 0 }}" readonly></td>
                  <td><input type="number" step="0.01" name="items[{{ $i }}][rate]" class="form-control rate" value="{{ $it['rate'] ?? 0 }}" readonly></td>
                  <td><input type="number" step="0.01" name="items[{{ $i }}][tax_rate]" class="form-control tax" value="{{ $it['tax_rate'] ?? 0 }}" readonly></td>
                  <td><input type="number" step="0.01" name="items[{{ $i }}][total]" class="form-control total" value="{{ $it['total'] ?? 0 }}" readonly></td>
                  <td>
                    @if(auth()->user() && auth()->user()->hasRole('retailer'))
                      <input type="hidden" name="items[{{ $i }}][status]" value="{{ $it['status'] ?? 'pending' }}">
                      <span class="badge badge-secondary">{{ ucfirst($it['status'] ?? 'pending') }}</span>
                    @else
                      <select name="items[{{ $i }}][status]" class="form-control status-select">
                        @foreach(['pending','draft','confirmed','shipped','delivered'] as $st)
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
                        data-tax="{{ $itm->tax_percent??0 }}" data-desc="{{ $itm->description??'' }}">
                        {{ $itm->article_number }}
                      </option>
                      @endforeach
                    </select>
                    <input type="hidden" name="items[0][item_id]" class="item-id-hidden" value="">
                  </td>
                  <td><input type="text" name="items[0][item_name]" class="form-control item-name-input" value="" readonly></td>
                  <td>
                    @if(auth()->user() && auth()->user()->hasRole('super-admin'))
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
                  <td><input type="text" name="items[0][description]" class="form-control desc" readonly></td>
                  <td><input type="number" step="0.01" name="items[0][quantity]" class="form-control qty" value="0" readonly></td>
                  <td><input type="number" step="0.01" name="items[0][rate]" class="form-control rate" value="0" readonly></td>
                  <td><input type="number" step="0.01" name="items[0][tax_rate]" class="form-control tax" value="0" readonly></td>
                  <td>
                      <input type="number" step="0.01" name="items[0][total]" class="form-control total" value="0" readonly>
                    
                  </td>
                  <td>
                    @if(auth()->user() && auth()->user()->hasRole('retailer'))
                      <input type="hidden" name="items[0][status]" value="pending">
                      <span class="badge badge-secondary">Pending</span>
                    @else
                      <select name="items[0][status]" class="form-control status-select" style="font-size:12px!important;">
                        @foreach(['pending','draft','confirmed','shipped','delivered'] as $st)
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
            @if(auth()->user() && auth()->user()->hasRole('super-admin'))
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
              <div class="card" style="padding:10px;">
                <div class="card-body p-2">
                  <div class="d-flex justify-content-between py-1">
                    <strong>Sub Total</strong>
                    <input type="text" name="subtotal" id="subtotal"
                      class="form-control form-control-sm w-50 text-right"
                      readonly value="{{ old('subtotal', 0) }}">
                  </div>
                  <div class="d-flex justify-content-between py-1">
                    <strong>Discount</strong>
                    <input type="number" step="0.01" name="discount" id="discount"
                      class="form-control form-control-sm w-50 text-right"
                      value="{{ old('discount', 0) }}">
                  </div>
                  <div class="d-flex justify-content-between py-1">
                    <strong>Adjustment</strong>
                    <input type="number" step="0.01" name="adjustment" id="adjustment"
                      class="form-control form-control-sm w-50 text-right"
                      value="{{ old('adjustment', 0) }}">
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
            <textarea name="terms" class="form-control" rows="2">{{ old('terms') }}</textarea>
          </div>
          <div class="form-group col-md-6">
            <label>Notes</label>
            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
          </div>
          
          <div class="form-group col-md-3">
            <label>Status</label>
            @if(auth()->user() && auth()->user()->hasRole('retailer'))
              <input type="hidden" name="status" value="pending">
              <div><span class="badge badge-secondary">Pending</span></div>
            @else
              <select name="status" class="form-control">
                <option value="pending" {{ old('status','pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="draft" {{ old('status','pending') == 'draft'     ? 'selected' : '' }}>Draft</option>
                <option value="confirmed" {{ old('status','pending') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="shipped" {{ old('status','pending') == 'shipped'   ? 'selected' : '' }}>Shipped</option>
                <option value="delivered" {{ old('status','pending') == 'delivered' ? 'selected' : '' }}>Delivered</option>
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

@endsection

@section('pageScript')
<script>
$(function () {

  var ALL_SIZES  = @json($sizesJson);
  var ITEMS      = @json($itemsJson);
  var COLORS     = @json($colors);
  var IS_RETAILER= @json(optional(auth()->user())->hasRole('retailer') ?? false);
  var IS_SUPER_ADMIN = @json(optional(auth()->user())->hasRole('super-admin') ?? false);

  /* ── helpers ──────────────────────────────────────────────────────────── */
  function itemByArticle(val) {
    return ITEMS.find(function(i){ return i.article_number == val || i.id == val; });
  }
  function rowIndex($row) {
    var name = $row.find('.qty').attr('name') || '';
    var m = name.match(/items\[(\d+)\]/);
    return m ? m[1] : 0;
  }
  function esc(v) {
    return String(v).replace(/[&<>"']/g, function(c){
      return({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'})[c];
    });
  }
  function normalizeArr(v) {
    if (!v) return [];
    return Array.isArray(v) ? v.map(String) : String(v).split(',').map(function(x){ return x.trim(); });
  }
  function colorOpts(colors, sel) {
    sel = normalizeArr(sel);
    colors = (colors && colors.length) ? colors : COLORS;
    return colors.map(function(c){
      return '<option value="'+esc(c.id)+'"'+(sel.indexOf(String(c.id))!==-1?' selected':'')+'>'+esc(c.name)+'</option>';
    }).join('');
  }
  function populateColorSelect($row, colors, sel) {
    var $cs = $row.find('.color-select');
    sel = sel || ($cs.length ? $cs.val() : []) || [];
    if ($cs.length) {
      if ($cs.hasClass('select2-hidden-accessible')) $cs.select2('destroy');
      $cs.html(colorOpts(colors, sel));
      $cs.select2({ placeholder:'Colors…', width:'100%' });
    } else {
      // no select present (non-super-admin) — show readonly text and hidden inputs
      var selArr = normalizeArr(sel);
      var names = selArr.map(function(id){ var c = COLORS.find(function(x){ return String(x.id) == String(id); }); return c?c.name : ''; }).filter(Boolean).join(', ');
      var $rd = $row.find('.color-read');
      if ($rd.length) $rd.val(names);
      // locate color cell (closest td containing color-read or color-select), fallback to index 2
      var $cell = $row.find('td').has('.color-read');
      if (!$cell.length) $cell = $row.find('td').has('.color-select');
      if (!$cell.length) $cell = $row.find('td').eq(2);
      if ($cell.length) {
        $cell.find('input[type=hidden][name$="[color][]"]').remove();
        selArr.forEach(function(id){
          $cell.append('<input type="hidden" name="items['+rowIndex($row)+'][color][]" value="'+esc(id)+'">');
        });
      }
    }
  }

  /* ── Recalc totals ────────────────────────────────────────────────────── */
  function recalc() {
    var sub = 0;
    $('#itemTable tbody tr').each(function(){
      var $tr = $(this);
      var qty  = parseFloat($tr.find('.qty').val()) || 0;
      var rate = parseFloat($tr.find('.rate').val()) || 0;
      var tax  = parseFloat($tr.find('.tax').val()) || 0;
      var tot  = rate * qty * (1 + tax / 100);
      $tr.find('.total').val(tot.toFixed(2));
      sub += tot;
    });
    $('#subtotal').val(sub.toFixed(2));
    var grand = sub - (parseFloat($('#discount').val())||0) + (parseFloat($('#adjustment').val())||0);
    $('#grand_total').val(grand.toFixed(2));
  }

  /* ── Size chip UI ─────────────────────────────────────────────────────── */
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
      var sz = $(this).closest('.size-qty-item').data('size');
      oldQtys[sz] = $(this).val();
    });

    var html = selected.map(function(sz){
      var q = oldQtys[sz] || 0;
      return '<div class="size-qty-item" data-size="'+esc(sz)+'">'
        +'<span class="size-qty-label">'+esc(sz)+'</span>'
        +'<div class="size-stepper">'
        +(IS_SUPER_ADMIN
                ? '<button type="button" class="stepper-btn minus">−</button>'
                : '')
        +(IS_SUPER_ADMIN ?'<input type="text" step="1" min="0" name="items['+idx+'][size_quantities]['+esc(sz)+']" class="size-qty" value="'+esc(q)+'" readonly>': '')
        +(IS_SUPER_ADMIN
                ? '<button type="button" class="stepper-btn plus">+</button>'
                : '')
        +'</input>'
        +'</div>';
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
    $row.find('.total-qty-badge').text(tot);
  }

  function updateRowQty($row) {
    var q = 0;
    $row.find('.size-qty').each(function(){ q += parseFloat($(this).val())||0; });
    $row.find('.qty').val(q);
  }

  /* ── Size chip click ──────────────────────────────────────────────────── */
  $(document).on('click', '.size-chip', function(){
    var $chip = $(this);
    var $row  = $chip.closest('tr');
    var $sel  = $row.find('.size-select');
    var size  = String($chip.data('size'));
    var cur   = $sel.val() || [];

    if (!IS_SUPER_ADMIN) {
      return;
    }

    if ($chip.hasClass('active')) {
      cur = cur.filter(function(s){ return s !== size; });
    } else {
      cur.push(size);
    }
    $sel.val(cur);
    rebuildSizePanel($row);
  });

  /* ── Stepper +/- ──────────────────────────────────────────────────────── */
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

  $(document).on('input', '.rate,.tax', recalc);
  $('#discount,#adjustment').on('input', recalc);

  /* ── Article select → auto-fill row ──────────────────────────────────── */
  $(document).on('change', '.article-select', function(){
    var $row = $(this).closest('tr');
    var val  = $(this).val();
    if (!val) return;

    var found = itemByArticle(val);
    if (!found) {
      var $opt = $(this).find('option:selected');
      found = { id:$opt.data('id')||null, name:$opt.data('name')||'', rate:parseFloat($opt.data('rate'))||0, tax:parseFloat($opt.data('tax'))||0, desc:$opt.data('desc')||'' };
    }
    $row.find('.item-id-hidden').val(found.id||'');
    $row.find('.item-name-input').val(found.name||found.article_number||'');
    $row.find('.rate').val(found.rate||0);
    $row.find('.tax').val(found.tax||0);
    if (!$row.find('.desc').val()) $row.find('.desc').val(found.desc||'');

    populateColorSelect($row, found.colors||[]);

    var sizeChoices = (found.sizes && found.sizes.length) ? found.sizes : ALL_SIZES;
    var $sz = $row.find('.size-select');
    if ($sz.hasClass('select2-hidden-accessible')) $sz.select2('destroy');
    $sz.html(sizeChoices.map(function(s){ return '<option value="'+s+'">'+s+'</option>'; }).join(''));

    // rebuild chips
    var $chips = $row.find('.size-chips-wrap');
    if (IS_SUPER_ADMIN) {
    $chips.html(sizeChoices.map(function(s){
      return '<button type="button" class="size-chip" data-size="'+esc(s)+'">'+esc(s)+'</button>';
    }).join(''));
    } else {
      $chips.empty();
    }

    rebuildSizePanel($row);
    recalc();
  });

  /* ── Build new row HTML ──────────────────────────────────────────────── */
  function buildRow(idx, it) {
    it = it || {};
    var artOpts = '<option value="">--</option>' + ITEMS.map(function(m){
      return '<option value="'+esc(m.article_number||'')+'"'
        +' data-id="'+(m.id||'')+'"'
        +' data-rate="'+(m.rate||0)+'"'
        +' data-tax="'+(m.tax||0)+'"'
        +' data-desc="'+(String(m.desc||'').replace(/"/g,'&quot;'))+'"'
        +(it.item_id==m.id?' selected':'')+'>'+esc(m.article_number||'')+'</option>';
    }).join('');

    var colOpts = colorOpts(it.colors||COLORS, it.color||it.color_id||[]);

    var sizeChips = '';

    if (IS_SUPER_ADMIN) {
      sizeChips = ALL_SIZES.map(function(s){
        return '<button type="button" class="size-chip" data-size="'+esc(s)+'">'+esc(s)+'</button>';
      }).join('');
    }
    var sizeOpts = ALL_SIZES.map(function(s){ return '<option value="'+s+'">'+s+'</option>'; }).join('');

    var statusSel = IS_RETAILER
      ? '<input type="hidden" name="items['+idx+'][status]" value="pending"><span class="badge badge-secondary">Pending</span>'
      : '<select name="items['+idx+'][status]" class="form-control status-select" style="font-size:12px!important;">'
        +['pending','draft','confirmed','shipped','delivered'].map(function(s){
          return '<option value="'+s+'"'+(it.status&&it.status==s?' selected':'')+'>'+s.charAt(0).toUpperCase()+s.slice(1)+'</option>';
        }).join('')+'</select>';

    return '<tr>'
      +'<td>'
        +(
            IS_SUPER_ADMIN
            ? '<select name="items['+idx+'][article_number]" class="form-control article-select">'
                + artOpts +
              '</select>'
            : '<input type="text" class="form-control" value="'+esc(
              (function () {
                  var found = ITEMS.find(function(i){
                      return i.id == it.item_id;
                  });
                  return found ? found.article_number : '';
              })()
          )+'" readonly>'
          + '<input type="hidden" name="items['+idx+'][article_number]" value="'+esc(
              (function () {
                  var found = ITEMS.find(function(i){
                      return i.id == it.item_id;
                  });
                  return found ? found.article_number : '';
              })()
          )+'">'
        )
        +'<input type="hidden" name="items['+idx+'][item_id]" class="item-id-hidden" value="'+(it.item_id||'')+'">'
      +'</td>'
      +'<td><input type="text" name="items['+idx+'][item_name]" class="form-control item-name-input" value="'+(it.item_name||'')+'" readonly></td>'
      +(
        IS_SUPER_ADMIN
        ? '<td class="color-cell"><select name="items['+idx+'][color][]" class="form-control color-select" multiple>'+colOpts+'</select></td>'
        : (function(){
            var sel = normalizeArr(it.color||it.color_id||[]);
            var names = sel.map(function(id){ var c = COLORS.find(function(x){ return String(x.id)==String(id); }); return c?c.name:''; }).filter(Boolean).join(', ');
            var hidden = sel.map(function(id){ return '<input type="hidden" name="items['+idx+'][color][]" value="'+esc(id)+'">'; }).join('');
            return '<td class="color-cell"><input type="text" class="form-control color-read" readonly value="'+esc(names)+'">'+hidden+'</td>';
          })()
      )
      +'<td>'
        +'<select name="items['+idx+'][sizes][]" class="size-select d-none" multiple>'+sizeOpts+'</select>'
        +'<div class="size-chips-wrap">'+sizeChips+'</div>'
        +(
            IS_SUPER_ADMIN
            ? '<div class="size-qty-wrapper size-qty-panel" style="display:none;"></div>'
            : '<div class="form-control size-readonly-box" readonly>'
                + (it.sizes ? normalizeArr(it.sizes).join(', ') : '')
              + '</div>'
          )
      +'</td>'
      +'<td><input type="text" name="items['+idx+'][description]" class="form-control desc" value="'+(it.description||'')+'" readonly></td>'
      +'<td><input type="number" step="0.01" name="items['+idx+'][quantity]" class="form-control qty" value="'+(it.quantity||0)+'" readonly color:var(--brand)!important;"></td>'
      +'<td><input type="number" step="0.01" name="items['+idx+'][rate]" class="form-control rate" value="'+(it.rate||0)+'" readonly></td>'
      +'<td><input type="number" step="0.01" name="items['+idx+'][tax_rate]" class="form-control tax" value="'+(it.tax_rate||0)+'" readonly></td>'
      +'<td><input type="number" step="0.01" name="items['+idx+'][total]" class="form-control total" value="'+(it.total||0)+'" readonly></td>'
      +'<td>'+statusSel+'</td>'
      +'<td><button type="button" class="btn btn-sm btn-danger remove-item"><i class="fas fa-trash"></i></button></td>'
      +'</tr>';
  }

  var rowCounter = $('#itemTable tbody tr').length;

  /* ── Add Row ─────────────────────────────────────────────────────────── */
  $('#addItem').on('click', function(){
    if (!IS_SUPER_ADMIN) {
      alert('Not allowed');
      return;
    }
    $('#itemTable tbody').append(buildRow(rowCounter));
    var $new = $('#itemTable tbody tr:last');
    $new.find('.color-select').select2({ placeholder:'Colors…', width:'100%' });
    rowCounter++;
    recalc();
    // Update all row numbers
    updateRowNumbers();
  });

  /* ── Remove Row ─────────────────────────────────────────────────────── */
  $(document).on('click', '.remove-item', function(){
    if ($('#itemTable tbody tr').length <= 1) return;
    $(this).closest('tr').remove();
    updateRowNumbers();
    recalc();
  });

  function updateRowNumbers() {
    $('#itemTable tbody tr').each(function(i){
      $(this).find('.row-num').text(String(i+1).padStart(2,'0'));
    });
  }

  /* ── Customer → addresses*/
  $('#customer_id').on('change', function(){
    var id = $(this).val();
    if (!id) { $('#billing_address,#shipping_address').val(''); return; }
    fetch("{{ url('customer') }}/" + id)
      .then(function(r){ return r.ok ? r.json() : Promise.reject(r.status); })
      .then(function(d){
        $('#billing_address').val(d.billing_address||'');
        $('#shipping_address').val(d.shipping_address||'');
      })
      .catch(function(){ $('#billing_address,#shipping_address').val(''); });
  });

  /* ── Init Select2 on existing rows ──────────────────────────────────── */
  if ($.fn.select2) {
    $('.color-select').select2({ placeholder:'Colors…', width:'100%' });
  }

  // Init size chip states for old() data rows
  $('#itemTable tbody tr').each(function(){
    var $row = $(this);
    var selected = $row.find('.size-select').val() || [];
    if (selected.length) {
      $row.find('.size-chip').each(function(){
        $(this).toggleClass('active', selected.indexOf(String($(this).data('size'))) !== -1);
      });
      // Ensure panel is visible (it was rendered server-side)
      if ($row.find('.size-qty-wrapper .size-qty-item').length) {
        $row.find('.size-qty-wrapper').show();
        updateTotalQtyBadge($row);
      }
    }
  });

  /* ── PRE-FILL from controller ─────────────────────────────────────────── */
  var PRE_ITEM_ID = @json($pre_item_id ?? null);
  var PRE_USER_ID = @json($pre_user_id ?? null);
  var PRE_ITEMS   = @json($pre_items ?? null);

  if (PRE_USER_ID) {
    $('#customer_id').val(PRE_USER_ID).trigger('change');
  } else {
    $('#customer_id').trigger('change');
  }

  if (PRE_ITEM_ID) {
    var $first = $('#itemTable tbody tr').first();
    if ($first.length) {
      var $sel  = $first.find('.article-select');
      var found = ITEMS.find(function(i){ return i.id == PRE_ITEM_ID; });
      if (found) $sel.val(found.article_number).trigger('change');
    }
  }

  if (Array.isArray(PRE_ITEMS) && PRE_ITEMS.length) {
    $('#itemTable tbody tr').each(function(){
      var $r = $(this);
      if (!$r.find('.article-select').val() && !$r.find('.item-id-hidden').val() && !$r.find('.item-name-input').val()) $r.remove();
    });
    rowCounter = $('#itemTable tbody tr').length;

    PRE_ITEMS.forEach(function(it){
      var idx = rowCounter++;
      var rd = {
        item_id: it.item_id||it.id||null, item_name: it.item_name||it.name||'',
        rate: it.rate||0, tax_rate: it.tax_rate||0,
        quantity: it.quantity||it.qty||0, description: it.description||'',
        color: it.color||it.color_id||null,
        sizes: it.sizes||it.size||null, size_quantities: it.size_quantities||null
      };
      $('#itemTable tbody').append(buildRow(idx, rd));
      var $tr = $('#itemTable tbody tr:last');

      if ($.fn.select2) $tr.find('.color-select').select2({ placeholder:'Colors…', width:'100%' });

      if (rd.item_id) {
        var fa = (function(){ var f=ITEMS.find(function(x){ return x.id==rd.item_id; }); return f?f.article_number:''; })();
        if (fa) $tr.find('.article-select').val(fa).trigger('change');
      }
      if (rd.color) {
        try { $tr.find('.color-select').val(normalizeArr(rd.color)).trigger('change.select2'); } catch(e){}
      }
      if (rd.sizes) {
        var sv = Array.isArray(rd.sizes) ? rd.sizes : String(rd.sizes).split(',').map(function(s){ return s.trim(); });
        $tr.find('.size-select').val(sv);
        rebuildSizePanel($tr);
        if (rd.size_quantities && typeof rd.size_quantities==='object') {
          Object.keys(rd.size_quantities).forEach(function(sz){
            $tr.find('.size-qty-item[data-size="'+sz+'"] .size-qty').val(rd.size_quantities[sz]);
          });
          updateTotalQtyBadge($tr);
          updateRowQty($tr);
        }
      }

      try {
        var finalQty = 0;
        if (rd.size_quantities && typeof rd.size_quantities === 'object') {
          Object.keys(rd.size_quantities).forEach(function(sz){ finalQty += parseFloat(rd.size_quantities[sz])||0; });
        }
        if (!finalQty) {
          finalQty = parseFloat(rd.quantity)||parseFloat(rd.qty)||0;
        }
        $tr.find('.qty').val(finalQty);
      } catch(e) {
        // ignore
      }
    });
    updateRowNumbers();
    recalc();
  }

  recalc();
});
</script>
@endsection