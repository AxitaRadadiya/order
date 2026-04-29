@extends('layouts.frontend')

@section('title', $category->name)

@section('content')

<div class="container py-4">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-white p-2">
      <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
      <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
    </ol>
  </nav>

  <div class="row mb-4">
    <div class="col-12">
      <h2 class="mb-0">{{ $category->name }}</h2>
      @if(!empty($category->description))
        <p class="text-muted">{{ $category->description }}</p>
      @endif
    </div>
  </div>

  {{-- Products grid --}}
  <div class="row">
    @forelse($items as $item)
      <div class="col-lg-3 col-md-4 col-6 mb-4">
        <div class="card product-card shadow-sm h-100">
          <div class="product-img d-flex align-items-center justify-content-center bg-light" style="height:200px;">
            <img src="{{ $item->image_urls[0] ?? asset('no-image.png') }}" alt="{{ $item->name }}" style="max-height:180px; max-width:100%; object-fit:contain;">
          </div>
          <div class="card-body text-center">
            <small class="text-muted d-block">{{ $item->category->name ?? 'General' }}</small>
            <h6 class="product-title mt-1">{{ $item->name }}</h6>
            <p class="text-danger font-weight-bold">₹{{ number_format($item->price,2) }}</p>
            <a href="{{ route('products.show', $item) }}" class="btn btn-sm btn-outline-primary">View Details</a>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12 text-center">
        <p>No products found in this category.</p>
      </div>
    @endforelse
  </div>

  <div class="d-flex justify-content-center">
    {{ $items->links() }}
  </div>
</div>

@endsection

@push('styles')
<style>
.product-card { border-radius: 8px; }
.product-img img { width:auto; max-width:100%; height: auto; }
</style>
@endpush
