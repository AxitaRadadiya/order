@extends('layouts.frontend')

@section('title', $item->name)

@section('content')

<div class="container mt-4">

  <div class="row">

    {{-- PRODUCT IMAGE --}}
    <div class="col-md-6">

      @php
        $images = [];

        if (!empty($item->images)) {
            if (is_array($item->images)) {
                $images = $item->images;
            } else {
                $images = json_decode($item->images, true) ?? [];
            }
        }
      @endphp

      {{-- MAIN IMAGE --}}
      <div class="text-center mb-3">
        <img id="mainImage"
             src="{{ isset($images[0]) ? asset('storage/'.$images[0]) : asset('no-image.png') }}"
             class="img-fluid border rounded"
             style="max-height:350px;">
      </div>

      {{-- THUMBNAILS --}}
      <div class="d-flex flex-wrap justify-content-center">
        @foreach($images as $img)
          <img src="{{ asset('storage/'.$img) }}"
               onclick="changeImage(this)"
               class="img-thumbnail m-1"
               style="width:70px; height:70px; object-fit:cover; cursor:pointer;">
        @endforeach
      </div>

    </div>

    {{-- PRODUCT DETAILS --}}
    <div class="col-md-6">

      <h3>{{ $item->name }}</h3>

      <p class="text-muted">
        Category: {{ $item->category->name ?? 'General' }}
      </p>

      <h4 class="text-danger">
        ₹{{ number_format($item->price,2) }}
      </h4>

      <p class="mt-3">
        {{ $item->description }}
      </p>

      {{-- EXTRA DETAILS --}}
      <ul class="list-group mt-3">
        <li class="list-group-item">
          <strong>Item Code:</strong> {{ $item->item_code ?? '-' }}
        </li>
        <li class="list-group-item">
          <strong>Article Number:</strong> {{ $item->article_number ?? '-' }}
        </li>
        <li class="list-group-item">
          <strong>Status:</strong> {{ $item->status ? 'Available' : 'Out of Stock' }}
        </li>
      </ul>

      {{-- BUTTONS --}}
      <div class="mt-4">
        <a href="{{ route('products') }}" class="btn btn-secondary">Back</a>
        <button class="btn btn-primary">Add to Cart</button>
      </div>

    </div>

  </div>

</div>

{{-- IMAGE SWITCH SCRIPT --}}
<script>
function changeImage(el) {
    document.getElementById('mainImage').src = el.src;
}
</script>

@endsection