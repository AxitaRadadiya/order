@extends('layouts.frontend')

@section('title','Home')

@section('content')

<section class="hero text-center">
  <div class="container front">
    <h1 class="display-4 font-weight-bold" >Welcome to My Store</h1>
    <p class="lead">Discover amazing products at the best prices</p>
    <a href="{{ route('products') }}" class="btn-create btn-lg mt-3">
      Shop Now
    </a>
  </div>
</section>

<section class="py-5">
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

            <img src="{{ $srcMain }}" alt="{{ $item->name }}" class="primary-img">
            @if($srcSecond)
              <img src="{{ $srcSecond }}" alt="{{ $item->name }} - alternate" class="secondary-full">
              <img src="{{ $srcSecond }}" alt="thumb" class="secondary-thumb">
            @endif
          </div>

          {{-- BODY --}}
          <div class="card-body text-center">
            <small class="text-muted d-block">
              {{ $item->category->name ?? 'General' }}
            </small>

            <h6 class="product-title mt-1">
              {{ $item->name }}
            </h6>

            <p class="text-danger font-weight-bold">
              ₹{{ number_format($item->price,2) }}
            </p>

            <a href="{{ route('products.show',$item) }}"
               class="btn btn-sm btn-outline-primary">
               View Details
            </a>
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

{{-- ABOUT SECTION --}}
<section class="py-5 bg-light text-center">
  <div class="container">
    <h2>About Us</h2>
    <p class="text-muted">
      We provide high-quality products with the best pricing and customer experience.
    </p>
  </div>
</section>

{{-- CONTACT SECTION --}}
<section class="py-5 text-center">
  <div class="container">
    <h2>Contact Us</h2>
    <p class="text-muted mb-4">Have questions? Send us a message.</p>

    <div class="row justify-content-center">
      <div class="col-md-6">
        <form>
          <div class="form-group">
            <input type="text" class="form-control" placeholder="Your Name">
          </div>
          <div class="form-group">
            <input type="email" class="form-control" placeholder="Email">
          </div>
          <div class="form-group">
            <textarea class="form-control" rows="4" placeholder="Message"></textarea>
          </div>
          <button class="btn btn-primary btn-block">Send Message</button>
        </form>
      </div>
    </div>

  </div>
</section>

@endsection


<!-- Frontend styles are loaded from public/admin/dist/css/custom.css -->