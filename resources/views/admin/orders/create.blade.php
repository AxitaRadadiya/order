@extends('admin.layouts.app')
@section('title', 'Create Order')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6"><h1 class="m-0">Create Order</h1></div>
      <div class="col-sm-6 text-right">
        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
          <i class="fas fa-arrow-left"></i> Back
        </a>
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

      <div class="card card-outline card-light">
        <div class="card-body">

          {{-- ── Client / Dates ────────────────────────────────────────── --}}
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Select Client <span class="text-danger">*</span></label>
                <select name="customer_id" id="customer_id" class="form-control" required>
                  <option value="">-- Select Client --</option>
                  @foreach($customers as $c)
                    <option value="{{ $c->id }}"
                      data-billing="{{ trim(($c->address->billing_street  ?? '').' '.
                                          ($c->address->billing_city    ?? '').' '.
                                          ($c->address->billing_state   ?? '').' '.
                                          ($c->address->billing_country ?? '')) }}"
                      {{ old('customer_id') == $c->id ? 'selected' : '' }}>
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
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="m-0">Items</h5>
            <div class="btn-group btn-group-sm">
              <button type="button" id="modeNormal"    class="btn btn-outline-secondary active">Normal</button>
              <button type="button" id="modeSizeRange" class="btn btn-outline-secondary">Size Range</button>
            </div>
          </div>

          {{-- ── Normal Items Table ────────────────────────────────────── --}}
          <div id="normalTable">
            <table class="table table-sm table-bordered" id="itemsTable">
              <thead class="thead-light">
                <tr>
                  <th style="min-width:160px">Item</th>
                  <th>Description</th>
                  <th width="70">Unit</th>
                  <th width="80">Qty</th>
                  <th width="90">Rate</th>
                  <th width="70">Tax %</th>
                  <th width="100">Unit Price</th>
                  <th width="100">Total</th>
                  <th width="40"></th>
                </tr>
              </thead>
              <tbody>
                @if(old('items'))
                  @foreach(old('items') as $i => $it)
                    <tr>
                      <td>
                        <select name="items[{{ $i }}][item_id]" class="form-control item-select">
                          <option value="">--</option>
                          @foreach($items as $itm)
                            <option value="{{ $itm->id }}"
                              data-rate="{{ $itm->price }}"
                              data-unit="{{ $itm->unit ?? '' }}"
                              data-tax="{{ $itm->tax_percent ?? 0 }}"
                              data-desc="{{ $itm->description ?? '' }}"
                              {{ ($it['item_id'] ?? '') == $itm->id ? 'selected' : '' }}>
                              {{ $itm->name }}
                            </option>
                          @endforeach
                        </select>
                        <input type="hidden" name="items[{{ $i }}][item_name]"
                               class="item-name-hidden" value="{{ $it['item_name'] ?? '' }}">
                      </td>
                      <td><input type="text"   name="items[{{ $i }}][description]" class="form-control desc"        value="{{ $it['description'] ?? '' }}"></td>
                      <td><input type="text"   name="items[{{ $i }}][unit]"        class="form-control unit"        value="{{ $it['unit'] ?? '' }}"></td>
                      <td><input type="number" step="0.01" name="items[{{ $i }}][quantity]"    class="form-control qty"         value="{{ $it['quantity'] ?? 1 }}"></td>
                      <td><input type="number" step="0.01" name="items[{{ $i }}][rate]"        class="form-control rate"        value="{{ $it['rate'] ?? 0 }}"></td>
                      <td><input type="number" step="0.01" name="items[{{ $i }}][tax_rate]"    class="form-control tax"         value="{{ $it['tax_rate'] ?? 0 }}"></td>
                      <td><input type="number" step="0.01" name="items[{{ $i }}][final_price]" class="form-control final_price" value="{{ $it['final_price'] ?? 0 }}" readonly></td>
                      <td><input type="number" step="0.01" name="items[{{ $i }}][total]"       class="form-control total"       value="{{ $it['total'] ?? 0 }}" readonly></td>
                      <td><button type="button" class="btn btn-sm btn-danger remove-item">&times;</button></td>
                    </tr>
                  @endforeach
                @else
                  <tr>
                    <td>
                      <select name="items[0][item_id]" class="form-control item-select">
                        <option value="">--</option>
                        @foreach($items as $itm)
                          <option value="{{ $itm->id }}"
                            data-rate="{{ $itm->price }}"
                            data-unit="{{ $itm->unit ?? '' }}"
                            data-tax="{{ $itm->tax_percent ?? 0 }}"
                            data-desc="{{ $itm->description ?? '' }}">
                            {{ $itm->name }}
                          </option>
                        @endforeach
                      </select>
                      <input type="hidden" name="items[0][item_name]" class="item-name-hidden" value="">
                    </td>
                    <td><input type="text"   name="items[0][description]" class="form-control desc"></td>
                    <td><input type="text"   name="items[0][unit]"        class="form-control unit"></td>
                    <td><input type="number" step="0.01" name="items[0][quantity]"    class="form-control qty"         value="1"></td>
                    <td><input type="number" step="0.01" name="items[0][rate]"        class="form-control rate"        value="0"></td>
                    <td><input type="number" step="0.01" name="items[0][tax_rate]"    class="form-control tax"         value="0"></td>
                    <td><input type="number" step="0.01" name="items[0][final_price]" class="form-control final_price" value="0" readonly></td>
                    <td><input type="number" step="0.01" name="items[0][total]"       class="form-control total"       value="0" readonly></td>
                    <td><button type="button" class="btn btn-sm btn-danger remove-item">&times;</button></td>
                  </tr>
                @endif
              </tbody>
            </table>
            <div class="text-right mb-3">
              <button type="button" id="addItem" class="btn btn-sm btn-success">
                <i class="fas fa-plus"></i> Add Row
              </button>
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
                            data-unit="{{ $itm->unit ?? '' }}"
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
                      <label>Rate (₹/pc)</label>
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
          <div class="form-group">
            <label>Terms &amp; Conditions</label>
            <textarea name="terms" class="form-control" rows="2">{{ old('terms') }}</textarea>
          </div>
          <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
              <option value="draft"     {{ old('status','draft') == 'draft'     ? 'selected' : '' }}>Draft</option>
              <option value="confirmed" {{ old('status','draft') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
              <option value="shipped"   {{ old('status','draft') == 'shipped'   ? 'selected' : '' }}>Shipped</option>
              <option value="delivered" {{ old('status','draft') == 'delivered' ? 'selected' : '' }}>Delivered</option>
            </select>
          </div>

        </div>
      </div>

      <div class="card">
        <div class="card-body text-right">
          <a href="{{ route('orders.index') }}" class="btn btn-secondary mr-2">Cancel</a>
          <button class="btn btn-primary">
            <i class="fas fa-save"></i> Save Order
          </button>
        </div>
      </div>

    </form>
  </div>
</section>
@endsection

@push('scripts')
<script>
$(function () {

  // ALL_SIZES comes from DB via controller → $sizesJson (array of label strings)
  var ALL_SIZES = @json($sizesJson);

  var ITEMS = @json($itemsJson);

  function itemById(id) {
    return ITEMS.find(function(i) { return i.id == id; });
  }

  // ── Recalculate totals ───────────────────────────────────────────────────
  function recalc() {
    var subtotal = 0;
    $('#itemsTable tbody tr').each(function () {
      var qty  = parseFloat($(this).find('.qty').val())  || 0;
      var rate = parseFloat($(this).find('.rate').val()) || 0;
      var tax  = parseFloat($(this).find('.tax').val())  || 0;
      var fp   = rate + (rate * tax / 100);
      var tot  = fp * qty;
      $(this).find('.final_price').val(fp.toFixed(2));
      $(this).find('.total').val(tot.toFixed(2));
      subtotal += tot;
    });
    $('#subtotal').val(subtotal.toFixed(2));
    var grand = subtotal
              - (parseFloat($('#discount').val())   || 0)
              + (parseFloat($('#adjustment').val()) || 0);
    $('#grand_total').val(grand.toFixed(2));
  }

  $(document).on('input', '.qty,.rate,.tax', recalc);
  $('#discount,#adjustment').on('input', recalc);

  // ── Auto-fill row when item selected ─────────────────────────────────────
  $(document).on('change', '.item-select', function () {
    var $row  = $(this).closest('tr');
    var id    = $(this).val();
    if (!id) return;

    var found = itemById(id);
    if (!found) {
      var $opt = $(this).find('option:selected');
      found = {
        id:   id,
        name: $opt.text().trim(),
        unit: $opt.data('unit') || '',
        rate: parseFloat($opt.data('rate')) || 0,
        tax:  parseFloat($opt.data('tax'))  || 0,
        desc: $opt.data('desc') || ''
      };
    }

    $row.find('.item-name-hidden').val(found.name || '');
    $row.find('.unit').val(found.unit || '');
    $row.find('.rate').val(found.rate || 0);
    $row.find('.tax').val(found.tax  || 0);
    if (!$row.find('.desc').val()) {
      $row.find('.desc').val(found.desc || '');
    }
    recalc();
  });

  // ── Build a new row ───────────────────────────────────────────────────────
  function buildRow(idx, it) {
    it = it || {};
    var opts = '<option value="">--</option>' + ITEMS.map(function(m) {
      return '<option value="' + m.id + '"' +
        ' data-rate="' + (m.rate || 0) + '"' +
        ' data-unit="' + (m.unit || '') + '"' +
        ' data-tax="'  + (m.tax  || 0) + '"' +
        ' data-desc="' + ((m.desc || '').replace(/"/g, '&quot;')) + '"' +
        (it.item_id == m.id ? ' selected' : '') +
        '>' + m.name + '</option>';
    }).join('');

    return '<tr>' +
      '<td>' +
        '<select name="items[' + idx + '][item_id]" class="form-control item-select">' + opts + '</select>' +
        '<input type="hidden" name="items[' + idx + '][item_name]" class="item-name-hidden" value="' + (it.item_name || '') + '">' +
      '</td>' +
      '<td><input type="text"   name="items[' + idx + '][description]" class="form-control desc"        value="' + (it.description || '') + '"></td>' +
      '<td><input type="text"   name="items[' + idx + '][unit]"        class="form-control unit"        value="' + (it.unit        || '') + '"></td>' +
      '<td><input type="number" step="0.01" name="items[' + idx + '][quantity]"    class="form-control qty"         value="' + (it.quantity    || 1)   + '"></td>' +
      '<td><input type="number" step="0.01" name="items[' + idx + '][rate]"        class="form-control rate"        value="' + (it.rate        || 0)   + '"></td>' +
      '<td><input type="number" step="0.01" name="items[' + idx + '][tax_rate]"    class="form-control tax"         value="' + (it.tax_rate    || 0)   + '"></td>' +
      '<td><input type="number" step="0.01" name="items[' + idx + '][final_price]" class="form-control final_price" value="' + (it.final_price || 0)   + '" readonly></td>' +
      '<td><input type="number" step="0.01" name="items[' + idx + '][total]"       class="form-control total"       value="' + (it.total       || 0)   + '" readonly></td>' +
      '<td><button type="button" class="btn btn-sm btn-danger remove-item">&times;</button></td>' +
    '</tr>';
  }

  var rowCounter = $('#itemsTable tbody tr').length;

  $('#addItem').on('click', function () {
    $('#itemsTable tbody').append(buildRow(rowCounter));
    rowCounter++;
    recalc();
  });

  $(document).on('click', '.remove-item', function () {
    $(this).closest('tr').remove();
    recalc();
  });

  // ── Customer → address auto-fill ─────────────────────────────────────────
  $('#customer_id').on('change', function () {
    var billing = $(this).find('option:selected').data('billing') || '';
    $('#billing_address').val(billing);
    $('#shipping_address').val(billing);
  });

  // ── Mode toggle ──────────────────────────────────────────────────────────
  $('#modeNormal').on('click', function () {
    $(this).addClass('active');
    $('#modeSizeRange').removeClass('active');
    $('#sizeRangePanel').hide();
    $('#normalTable').show();
  });

  $('#modeSizeRange').on('click', function () {
    $(this).addClass('active');
    $('#modeNormal').removeClass('active');
    $('#sizeRangePanel').show();
    $('#normalTable').hide();
    srRecalc();
  });

  // ── Size Range helpers ───────────────────────────────────────────────────
  // Uses index-based comparison so works with ANY label (XL, 2XL, 32 …)
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
    var $opt     = $('#sr_item').find('option:selected');
    var itemId   = $('#sr_item').val();
    if (!itemId) { alert('Please select a product first.'); return; }

    var itemName = $opt.text().trim();
    var from     = $('#sr_from').val();
    var to       = $('#sr_to').val();
    var sets     = parseInt($('#sr_sets').val())    || 1;
    var rate     = parseFloat($('#sr_rate').val())  || 0;
    var taxRate  = parseFloat($opt.data('tax'))     || 0;
    var unit     = $opt.data('unit') || 'pc';
    var sizes    = sizesInRange(from, to);

    if (!sizes.length) { alert('No valid sizes in that range. Please check Size From / Size To.'); return; }

    var totalPcs = sizes.length * sets;
    var desc     = 'Sizes ' + from + '-' + to + ' (' + sizes.join(', ') + ') × ' + sets + ' sets';

    // Switch back to normal view
    $('#modeNormal').trigger('click');

    var idx = rowCounter++;

    $('#itemsTable tbody').append(buildRow(idx, {
      item_id:     itemId,
      item_name:   itemName,
      description: desc,
      unit:        unit,
      quantity:    totalPcs,
      rate:        rate,
      tax_rate:    taxRate,
    }));

    // Set the select value and hidden size fields on the newly appended row
    var $tr = $('#itemsTable tbody tr:last');
    $tr.find('.item-name-hidden').val(itemName);
    $tr.find('.item-select').val(itemId);
    $tr.append('<input type="hidden" name="items[' + idx + '][size_from]" value="' + from  + '">');
    $tr.append('<input type="hidden" name="items[' + idx + '][size_to]"   value="' + to    + '">');
    $tr.append('<input type="hidden" name="items[' + idx + '][sets]"      value="' + sets  + '">');

    recalc();
  });

  // Init
  recalc();
  srRecalc();
});
</script>
@endpush