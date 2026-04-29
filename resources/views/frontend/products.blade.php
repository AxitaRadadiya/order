@extends('layouts.frontend')

@section('title','Products')

@section('content')

<section class="py-4">
  <div class="container front">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2>All Products</h2>
    </div>

    <div class="row">

      @forelse($items as $item)
      <div class="col-md-3 mb-4">
        <div class="card product-card shadow-sm h-100">

          {{-- IMAGE --}}
          <div class="product-img">
            @php
              $images = data_get($item, 'image_urls', []);
              $mainRaw = data_get($images, 0);
              $secondRaw = data_get($images, 1);

              $toSrc = function ($raw) {
                  if (!$raw) return null;
                  if (preg_match('/^https?:\/\//', $raw)) return $raw;
                  return asset('storage/' . ltrim($raw, '/'));
              };

              $srcMain = $toSrc($mainRaw) ?? asset('no-image.png');
              $srcSecond = $toSrc($secondRaw);
            @endphp

            <img src="{{ $srcMain }}" alt="{{ $item->name }}" class="primary-img" loading="lazy">

            @if($srcSecond)
              <img src="{{ $srcSecond }}" alt="{{ $item->name }} - alternate" class="secondary-full" loading="lazy">
              <img src="{{ $srcSecond }}" alt="thumb" class="secondary-thumb" loading="lazy">
            @endif
          </div>

          {{-- BODY --}}
          <div class="card-body text-center">

            <small class="text-muted d-block">
              {{ $item->category->name ?? 'General' }}
            </small>

            <h6 class="product-title mt-1">{{ $item->name }}</h6>

            <p class="text-danger font-weight-bold">
              ₹{{ number_format($item->price,2) }}
            </p>

            <a href="{{ route('products.show',$item) }}" class="btn btn-sm btn-create">View Details</a>

          </div>

        </div>
      </div>
      @empty
        <div class="col-12 text-center">
          <p>No products found</p>
        </div>
      @endforelse

    </div>

    {{-- PAGINATION --}}
    <div class="d-flex justify-content-center mt-4">
      {{ $items->links() }}
    </div>

  </div>
</section>

@endsection