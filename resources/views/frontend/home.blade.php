@extends('layouts.frontend')

@section('title','Home')

@section('content')

<section class="top-banner mt-2">
    <a href="{{ route('products') }}">
        <img src="{{ asset('admin/dist/img/top-banner.jpg') }}"
             alt="Promotional Banner"
             class="img-fluid w-100"
             style="max-height:600px; object-fit:cover;">
    </a>
</section>

<!-- <section class="video-banner">
    <div class="video-container">
        <iframe
            src="https://www.youtube.com/embed/1PIHGMkAdag?autoplay=1&mute=1&loop=1&playlist=1PIHGMkAdag&controls=0&showinfo=0&rel=0&modestbranding=1"
            frameborder="0"
            allow="autoplay; fullscreen"
            allowfullscreen>
        </iframe>
    </div>

    <div class="banner-overlay"></div>

    <div class="banner-content">
        <h1>LIVE BY STYLE</h1>
        <p>Streetwear For Every Move</p>

        <a href="{{ route('products') }}" class="btn btn-dark btn-lg">
            Shop Now
        </a>
    </div>
</section> -->

<!-- <section class="hero-banner py-4">
  <div class="container-fluid">
    <div class="row align-items-center" style="background:#ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
      <div class="col-lg-6 text-left px-5">
        <h1 class="display-4 font-weight-bold">Upgrade Your Style</h1>
        <p class="lead">Discover premium denim and curated apparel — great prices, fast shipping.</p>
        <div class="mt-4">
          <a href="{{ route('products') }}" class="btn-create mr-2">Shop Now</a>
        </div>
      </div>
      <div class="col-lg-6 text-center p-0">
          <img src="{{ asset('admin/dist/img/j1.jpg') }}" alt="Jeans" class="img-fluid hero-image" onerror="this.onerror=null;this.style.display='none'">
      </div>
    </div>
  </div>
</section> -->

<section class="py-4">
  <div class="container front">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Featured Products</h2>
      <a href="{{ route('products') }}" class="btn btn-create btn-sm">
        View All
      </a>
    </div>

    <div class="row">
      @forelse($items as $item)
        @if($loop->iteration > 4)
          @break
        @endif
      <div class="col-md-3 mb-4">
        <div class="card product-card shadow-sm h-100">

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

            <a href="{{ route('products.show',$item) }}" class="product-link" aria-label="Open {{ $item->name }} details">
              <img src="{{ $srcMain }}" alt="{{ $item->name }}" class="primary-img">

              @if($srcSecond)
                <img src="{{ $srcSecond }}" alt="{{ $item->name }} - alternate" class="secondary-full">
                <img src="{{ $srcSecond }}" alt="thumb" class="secondary-thumb">
              @endif
            </a>

            <div class="product-overlay" aria-hidden="true">
              <div class="cat-name">{{ $item->category->name ?? 'General' }}</div>
              @if($item->category)
                <a class="cat-link" href="{{ route('products') }}?category_id={{ $item->category->id }}">View category</a>
              @else
                <a class="cat-link" href="{{ route('products') }}">View products</a>
              @endif
            </div>
          </div>

          {{-- BODY --}}
          <div class="card-body text-center" style="padding:0px;">
            <!--<small class="text-muted d-block">
              {{ $item->category->name ?? 'General' }}
            </small>-->

            <h6 class="product-title mt-1">
              {{ $item->name }}
            </h6>

            <p class="text-danger font-weight-bold">
              ₹{{ number_format($item->price,2) }}
            </p>

            
          </div>

        </div>
      </div>
      @empty
      <div class="col-12 text-center">
        <p>No products available</p>
      </div>
      @endforelse
    </div>

  </div>
</section>

@endsection