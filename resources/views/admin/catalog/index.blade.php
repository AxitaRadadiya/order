@extends('admin.layouts.app')

@section('title', 'Catalog')

@section('content')

<style>
.product-card {
    border-radius: 12px;
    overflow: hidden;
    transition: 0.3s;
    cursor: pointer;
}

.product-card:hover {
    transform: translateY(-5px);
}

.product-img {
    height: 220px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
}

.product-img img {
    max-height: 180px;
    object-fit: contain;
}

.product-title {
    font-size: 14px;
    height: 40px;
    overflow: hidden;
}

.product-link {
    text-decoration: none;
    color: inherit;
}
</style>

<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0">Product Catalog</h1>
    </div>
</div>

<section class="content">
<div class="container-fluid">

    <div class="row">

        @forelse($items as $item)
        <div class="col-md-3 mb-4">

            {{-- CLICKABLE CARD --}}
            <div class="product-link" onclick="window.location='{{ route('catalog.show', $item->id) }}'" style="cursor:pointer;">

                <div class="card product-card shadow-sm">

                    {{-- IMAGE --}}
                    <div class="product-img">
                        @if(!empty($item->image_urls))
                            <img src="{{ $item->image_urls[0] }}" alt="product">
                        @else
                            <img src="{{ asset('no-image.png') }}" alt="no-image">
                        @endif
                    </div>

                    {{-- BODY --}}
                    <div class="card-body text-center">

                        <small class="text-muted d-block">
                            {{ $item->category->name ?? 'Common Good' }}
                        </small>

                        <h6 class="product-title">
                            {{ $item->name }}
                        </h6>

                        {{-- COLORS --}}
                     <!--   <div class="mb-2">
                            @foreach($item->colors as $color)
                                <span class="badge badge-light border">
                                    {{ $color->name }}
                                </span>
                            @endforeach
                        </div>

                        {{-- SIZES --}}
                        <div class="mb-2">
                            @foreach($item->sizes ?? [] as $size)
                                <span class="badge badge-secondary">
                                    {{ $size }}
                                </span>
                            @endforeach
                        </div>-->   


                        {{-- PRICE --}}
                        <div>
                            <span class="text-danger font-weight-bold">
                                ₹{{ number_format($item->price, 2) }}
                            </span>
                        </div>

                        {{-- Add Order button for retailers/distributors --}}
                        @if(auth()->check() && auth()->user()->hasRole(['retailer', 'distributor']))
                            <a href="{{ route('orders.create', ['item_id' => $item->id]) }}" class="btn btn-sm btn-primary mt-2" onclick="event.stopPropagation();">
                                <i class="fas fa-cart-plus"></i> Add Order
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