@extends('admin.layouts.app')
@section('title', 'Item Details')

@section('content')
<div class="content-header">
  <div class="container-fluid">
	<div class="row mb-2">
	  <div class="col-sm-6">
		<h1 class="m-0"></i>Item Details</h1>
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
    <div class="main-card">
      <div class="main-card-head d-flex justify-content-between align-items-center">
        <!--<div class="main-card-title"><i class="fas fa-eye"></i> {{ $item->name }}</div>-->
        <div>
          <a href="{{ route('items.index') }}" class="btn btn-secondary">Back</a>
        </div>
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
              
            @else
              <div class="border rounded p-5 text-center text-muted">No image</div>
            @endif
          </div>
          <div class="col-md-8">
            <h4>{{ $item->name }}</h4>
            <p class="text-muted">Article Number: {{ $item->article_number ?? '-' }}</p>
            <p class="text-muted">Item Code: {{ $item->item_code ?? '-' }}</p>
            <p>{{ $item->description }}</p>

            {{-- Inline Add to Cart form (for retailers/distributors) --}}
            @if(auth()->check() && auth()->user()->hasRole(['retailer', 'distributor']))
            <div class="cards">
              <div class="card-body" style="padding:0px">
                <form id="inlineAddToCartForm">
                  <input type="hidden" name="item_id" id="item_id" value="{{ $item->id }}">

                  @if(!empty($item->colors) && count($item->colors))
                  <div class="form-group">
                    <label>Color</label>
                    <div id="colorOptions" class="d-flex flex-wrap gap-2 align-items-start">
                      @foreach($item->colors as $c)
                        @php
                          $bg = $c->hex ?? $c->code ?? null;
                        @endphp
                        <button type="button" class="color-swatch mr-2" data-id="{{ $c->id }}" title="{{ $c->name }}" aria-pressed="false">
                          <div class="color-circle">
                          @if($bg)
                            <span class="color-fill" style="background:{{ $bg }}"></span>
                          @else
                            <span class="color-initial"></span>
                          @endif
                          </div>
                          <div class="color-name">{{ $c->name }}</div>
                        </button>
                      @endforeach
                    </div>
                    <input type="hidden" id="selected_color" name="color_id" value="">
                  </div>
                  @endif

                  @if(!empty($item->sizes) && count($item->sizes))
                  <div class="form-group">
                    <label>Size</label>
                    <div id="sizeOptions" class="d-flex flex-wrap gap-2 align-items-center">
                      @foreach($item->sizes as $s)
                        <button type="button" class="btn size-option mr-2" data-size="{{ $s }}" aria-pressed="false">
                          <div class="size-label">{{ $s }}</div>
                        </button>
                      @endforeach
                    </div>
                    <input type="hidden" id="selected_size" name="size" value="">
                  </div>
                  @endif

                  <div class="form-group" id="qtyGroup" style="display:none">
                    <label for="qty">Quantity</label>
                    <div class="input-group" style="max-width:140px">
                      <input id="qty" class="form-control text-center" type="number" min="1" value="1">
                    </div>
                  </div>

                  <div id="add_feedback" class="" style="display:none"></div>

                
                </form>
              </div>
            </div>
            @endif

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
              <div class="d-flex gap-2">
                    <button type="button" id="addToCartBtn" class="btn-create mr-2">Add to Cart</button>
                    <button type="button" id="buyNowBtn" class="btn-secondary">Buy Now</button>
                  </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
<script>
  function setMainImage(src, thumb) {
    document.getElementById('mainProductImage').src = src;
      document.querySelectorAll('.product-thumb').forEach(function(img) {
      img.style.border = '2px solid #eee';
      });
  thumb.style.border = '2px solid #dcdfe1';
  }
</script>

