@extends('layouts.frontend')

@section('title','Home')

@section('content')

{{-- HERO SECTION --}}
<section class="hero text-center">
  <div class="container">
    <h1 class="display-4 font-weight-bold">Welcome to My Store</h1>
    <p class="lead">Discover amazing products at the best prices</p>
    <a href="{{ route('products') }}" class="btn-create btn-lg mt-3">
      Shop Now
    </a>
  </div>
</section>

{{-- FEATURED PRODUCTS --}}
<section class="py-5">
  <div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Featured Products</h2>
      <a href="{{ route('products') }}" class="btn btn-create btn-sm">
        View All
      </a>
    </div>

    <div class="row">
      @forelse($items as $item)
      <div class="col-md-3 mb-4">
        <div class="card product-card shadow-sm h-100">

          {{-- IMAGE --}}
          <div class="product-img">
            <img src="{{ $item->image_urls[0] ?? asset('no-image.png') }}">
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


@push('styles')
<style>
.hero {
    padding: 80px 0;
    background: linear-gradient(to right, #f8f9fa, #e9ecef);
}

.product-card {
    border-radius: 12px;
    transition: 0.3s;
}

.product-card:hover {
    transform: translateY(-5px);
}

.product-img {
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
}

.product-img img {
    max-height: 170px;
    object-fit: contain;
}

.product-title {
    height: 40px;
    overflow: hidden;
}
</style>
@endpush