@extends('layouts.frontend')

@section('title','Products')

@section('content')

<section class="py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>All Products</h2>
  </div>

  <div class="row">

    @forelse($items as $item)
    <div class="col-md-3 mb-4">
      <div class="card shadow-sm h-100">

        {{-- IMAGE --}}
        <div style="height:150px; display:flex; align-items:center; justify-content:center; background:#e1e0f345;">
          <img src="{{ $item->image_urls[0] ?? asset('no-image.png') }}"
               style="max-height:100px; object-fit:contain;">
        </div>

        {{-- BODY --}}
        <div class="card-body text-center">

          <small class="text-muted d-block">
            {{ $item->category->name ?? 'General' }}
          </small>

          <h6 class="mt-1">{{ $item->name }}</h6>

          <p class="text-danger font-weight-bold">
            ₹{{ number_format($item->price,2) }}
          </p>

          <a href="{{ route('products.show',$item) }}"
   class="btn btn-sm btn-create">
   View Details
</a>

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

</section>

@endsection