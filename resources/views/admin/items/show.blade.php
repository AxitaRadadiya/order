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
		  <li class="breadcrumb-item active">show</li>
		</ol>
	  </div>
	</div>
  </div>
</div>

<div class="pull-card">
  <div class="container-fluid">
    <div class="main-card mt-4">
      <div class="main-card-head d-flex justify-content-end align-items-center mb-2">
     <!--   <div class="main-card-title"><i class="fas fa-eye"></i> {{ $item->name }}</div>-->
        <a href="{{ route('items.index') }}" class="btn-cancel mr-1"><i class="fas fa-arrow-left"></i> Back</a>
        <a href="{{ route('items.edit', $item->id) }}" class="btn-submit"><i class="fas fa-edit"></i> Edit</a>
      </div>
      <div class="main-card-body">
        <div class="row">
          <div class="col-md-4">
            @php
              // Support both single and multiple images (array or JSON column)
              $images = [];
              if (!empty($item->images) && is_array($item->images)) {
                $images = $item->images;
              } elseif (!empty($item->images) && is_string($item->images)) {
                $decoded = json_decode($item->images, true);
                if (is_array($decoded)) $images = $decoded;
              } elseif (!empty($item->image)) {
                $images = [$item->image];
              }
              // Filter out non-existing images
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
                  // Optional: highlight selected thumbnail
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
            <p class="text-muted">Item Code: {{ $item->item_code ?? '-' }}</p>
            <p>{{ $item->description }}</p>

            <dl class="row">
              <dt class="col-sm-4">Category</dt>
              <dd class="col-sm-8">{{ optional($item->category)->name ?? '-' }}</dd>

              <dt class="col-sm-4">Sub Category</dt>
              <dd class="col-sm-8">{{ $item->sub_category ?? '-' }}</dd>

              <dt class="col-sm-4">Group</dt>
              <dd class="col-sm-8">{{ optional($item->group)->name ?? '-' }}</dd>

              <dt class="col-sm-4">Sub Group</dt>
              <dd class="col-sm-8">{{ $item->sub_group ?? '-' }}</dd>

              <dt class="col-sm-4">Unit</dt>
              <dd class="col-sm-8">{{ $item->unit ?? '-' }}</dd>

              <dt class="col-sm-4">Price</dt>
              <dd class="col-sm-8">{{ number_format($item->price,2) }}</dd>

              <dt class="col-sm-4">Tax</dt>
              <dd class="col-sm-8">{{ $item->tax_percent }}%</dd>

              <dt class="col-sm-4">Status</dt>
              <dd class="col-sm-8">{{ $item->status ? 'Active' : 'Inactive' }}</dd>
            </dl>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
