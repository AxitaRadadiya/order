@extends('admin.layouts.app')
@section('title', 'Item Details')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Item Details</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('items.index') }}">Items</a></li>
          <li class="breadcrumb-item active">Show</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="pull-card">
  <div class="container-fluid">
    <div class="main-card mt-4">
      <div class="main-card-head d-flex justify-content-end align-items-center mb-2">
        <a href="{{ route('items.index') }}" class="btn-cancel mr-1"><i class="fas fa-arrow-left"></i> Back</a>
        <a href="{{ route('items.edit', $item->id) }}" class="btn-submit"><i class="fas fa-edit"></i> Edit</a>
      </div>
      <div class="main-card-body">
        <div class="row">
          <div class="col-md-4">
            @php
              $images = [];
              if (!empty($item->images) && is_array($item->images)) {
                $images = $item->images;
              } elseif (!empty($item->images) && is_string($item->images)) {
                $decoded = json_decode($item->images, true);
                if (is_array($decoded)) $images = $decoded;
              } elseif (!empty($item->image)) {
                $images = [$item->image];
              }
              $images = array_values(array_filter($images, function($img) {
                return $img && file_exists(public_path('storage/' . $img));
              }));
            @endphp
            @if(!empty($images))
              <div class="mb-3 text-center">
                <img id="mainProductImage" src="{{ asset('storage/' . $images[0]) }}" alt="Main Image" class="img-fluid rounded border" style="max-width: 100%; max-height: 250px;">
              </div>
              <div class="d-flex flex-wrap gap-2 justify-content-center">
                @foreach($images as $idx => $img)
                  <img src="{{ asset('storage/' . $img) }}" alt="Thumbnail {{ $idx+1 }}" class="img-thumbnail m-1 product-thumb" style="width: 60px; height: 60px; object-fit: cover; cursor: pointer; border:2px solid #eee;" onclick="setMainImage('{{ asset('storage/' . $img) }}', this)">
                @endforeach
              </div>
              <script>
                function setMainImage(src, thumb) {
                  document.getElementById('mainProductImage').src = src;
                  document.querySelectorAll('.product-thumb').forEach(function(img) {
                    img.style.border = '2px solid #eee';
                  });
                  thumb.style.border = '2px solid #dcdfe1';
                }
              </script>
            @else
              <div class="border rounded p-5 text-center text-muted">No image</div>
            @endif
          </div>
          <div class="col-md-8">
            <h4>{{ $item->name }}</h4>
            <p class="text-muted">Article Number: {{ $item->article_number ?? '-' }}</p>
            <p class="text-muted">MRP: {{ number_format($item->price, 2) }}</p>

            @if($item->variants->count())
                <hr>
                <h5 class="mt-3">Item Variants</h5>
                <div class="table-responsive">
                <table class="table table-bordered mt-2">
                    <thead>
                        <tr>
                            <th>Color Code</th>
                            <th>Size</th>
                            <th>Total Production</th>
                            <th>Total Sold</th>
                            <th>Current Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalProduction = 0;
                            $totalSold = 0;
                            $totalStock = 0;
                        @endphp
                        @foreach($item->variants as $variant)
                            @php
                                $totalProduction += $variant->total_production;
                                $totalSold += $variant->total_sold;
                                $totalStock += $variant->current_stock;

                                if ($variant->current_stock == 0) {
                                    $rowStyle = 'background-color: #ffe6e6;';
                                    $stockOutput = '<span class="badge badge-danger">Out of Stock</span>';
                                } elseif ($variant->current_stock <= 10) {
                                    $rowStyle = 'background-color: #fff3cd;';
                                    $stockOutput = '<span class="badge badge-warning">Low Stock</span>';
                                } else {
                                    $rowStyle = '';
                                    $stockOutput = '<span style="color: green;">' . $variant->current_stock . '</span>';
                                }
                            @endphp
                            <tr style="{{ $rowStyle }}">
                                <td>{{ optional($variant->color)->color_code ?? '-' }}</td>
                                <td>{{ optional($variant->size)->name ?? '-' }}</td>
                                <td>{{ $variant->total_production }}</td>
                                <td>{{ $variant->total_sold }}</td>
                                <td>{!! $stockOutput !!}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td>Total</td>
                            <td></td>
                            <td>{{ $totalProduction }}</td>
                            <td>{{ $totalSold }}</td>
                            <td>{{ $totalStock }}</td>
                        </tr>
                    </tfoot>
                </table>
                </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
