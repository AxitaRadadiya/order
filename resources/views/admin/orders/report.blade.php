@extends('admin.layouts.app')
@section('title', 'Order Reports')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Order Reports</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Order Reports</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="card card-default mb-4">
    <div class="card-header">
      <h3 class="card-title">Item-wise Report</h3>
    </div>
    <div class="card-body">
      <div class="row mb-3">
        <div class="col-md-4">
          <div class="form-group">
            <label for="filter_search">Search article / item</label>
            <input type="text" id="filter_search" class="form-control" placeholder="Search article or item name">
          </div>
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <button id="report_search" class="btn btn-create mr-2">Search</button>
          <button id="report_reset" class="btn btn-secondary">Reset</button>
        </div>
      </div>

      <table id="reportTable" class="table table-bordered table-hover table-sm w-100">
        <thead>
          <tr>
            <th>#</th>
            <th>Article Number</th>
            <th>Item Name</th>
            <th>Color</th>
            <th>Size</th>
            <th>Total Qty</th>
            <th>Dispatch Qty</th>
            <th>- Qty</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

</div>
</div>
@endsection

@section('pageScript')
<script>
$(function() {
  function loadReportTable() {
    if ($.fn.dataTable.isDataTable('#reportTable')) {
      $('#reportTable').DataTable().clear().destroy();
      $('#reportTable tbody').empty();
    }

    var search = $('#filter_search').val();

    $('#reportTable').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      paging: true,
      lengthChange: true,
      searching: false,
      ordering: true,
      info: true,
      order: [[3, 'desc']],
      ajax: {
        url: '{{ route('reports.items.data') }}',
        type: 'GET',
        data: {
          search: search,
        }
      },
      columns: [
        { data: 'id' },
        { data: 'article_number' },
        { data: 'item_name' },
        { data: 'color' },
        { data: 'size' },
        { data: 'total_quantity' },
        { data: 'dispatched_quantity' },
        { data: 'negative_quantity' }
      ],
      columnDefs: [{ orderable: false, targets: [0] }]
    });
  }

  loadReportTable();

  $('#report_search').on('click', function(e) {
    e.preventDefault();
    loadReportTable();
  });

  $('#report_reset').on('click', function(e) {
    e.preventDefault();
    $('#filter_search').val('');
    loadReportTable();
  });
});
</script>
@endsection
