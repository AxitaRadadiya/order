@extends('admin.layouts.app')

@section('title', 'Catalog')

@section('content')


<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0">Product Catalog</h1>
    </div>
</div>

<section class="content">
<div class="container-fluid">

    <div class="row">

        @forelse($items as $item)
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">

            {{-- CLICKABLE CARD --}}
            <div class="product-link" onclick="window.location='{{ route('catalog.show', $item->id) }}'" role="link" tabindex="0" style="cursor:pointer;">
                <div class="card product-card shadow-sm">
                    {{-- IMAGE --}}
                    <div class="product-img">
                        @if(!empty($item->image_urls))
                            <img src="{{ $item->image_urls[0] }}" alt="{{ $item->name }}">
                        @else
                            <img src="{{ asset('no-image.png') }}" alt="no-image" class="no-image-placeholder">
                        @endif
                    </div>

                    {{-- BODY --}}
                    <div class="card-body text-center d-flex flex-column align-items-center">

                        <h6 class="product-title w-100">
                            {{ $item->name }}
                        </h6>

                        {{-- PRICE --}}
                        <div class="mts-2">
                            <span class="price">
                                ₹{{ number_format($item->price, 2) }}
                            </span>
                        </div>

                        <div class="card-footer-spacer"></div>

                        {{-- Add to Cart button for retailers/distributors — navigate to show page to choose options --}}
                        @if(auth()->check() && auth()->user()->hasRole(['retailer', 'distributor']))
                            <a href="{{ route('catalog.show', $item->id) }}" class="btn btn-create btn-add-cart mt-2">
                                <i class="fas fa-cart-plus"></i>
                                <span>Add Cart</span>
                            </a>
                        @endif

                    </div>

                </div>

            </div>

        </div>

        @empty
        <div class="col-12 text-center">
            <p>No items found</p>
        </div>
        @endforelse

    </div>

    {{-- PAGINATION --}}
    <div class="mt-3">
        {{ $items->links() }}
    </div>

</div>
</section>

@endsection