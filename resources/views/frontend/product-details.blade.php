@extends('layouts.frontend')

@section('title', $item->name)

@section('content')

<div class="container py-3">

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

    {{-- Elegant back button navigation --}}
    <div aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb custom-breadcrumb justify-content-end">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('products') }}">Products</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ $item->name }}
            </li>
        </ol>
    </div>

    <div class="row">
        {{-- PRODUCT IMAGES --}}
        <div class="col-lg-5 mb-4 mb-lg-0">
            <div class="product-image-container">
                {{-- MAIN IMAGE --}}
                <img id="mainImage"
                     src="{{ isset($images[0]) ? asset('storage/'.$images[0]) : asset('no-image.png') }}"
                     class="img-fluid"
                     alt="{{ $item->name }}">
            </div>

            {{-- THUMBNAILS --}}
            @if(count($images))
                <div class="thumbnail-gallery">
                    @foreach($images as $img)
                        <img src="{{ asset('storage/'.$img) }}"
                             onclick="changeImage(this)"
                             class="thumb-img {{ $loop->first ? 'active-thumb' : '' }}"
                             alt="{{ $item->name }} Thumbnail">
                    @endforeach
                </div>
            @endif
        </div>

        {{-- PRODUCT DETAILS --}}
        <div class="col-lg-7">
            <div class="card product-details-card">
                <div class="card-body p-4 p-md-5">
                    {{-- TITLE --}}
                    <h1 class="product-title-detail">
                        {{ $item->name }}
                    </h1>                       

                    {{-- PRICE --}}
                    <div class="product-price-detail">
                        ₹{{ number_format($item->price, 2) }}
                    </div>

                    {{-- DESCRIPTION --}}
                    @if(!empty($item->description))
                        <div class="description-box">
                            <div class="description-title">Description</div>
                            <p class="description-text">
                                {{ $item->description }}
                            </p>
                        </div>
                    @endif

                    {{-- PRODUCT DETAILS --}}
                    <div class="spec-table mt-4">
                        <div class="spec-row">
                            <div class="spec-label">Item Name</div>
                            <div class="spec-value">{{ $item->name }}</div>
                        </div>

                        <div class="spec-row">
                            <div class="spec-label">Article Number</div>
                            <div class="spec-value">
                                {{ $item->article_number ?? '-' }}
                            </div>
                        </div>

                        <div class="spec-row">
                            <div class="spec-label">Colors</div>
                            <div class="spec-value">
                                @php
                                    $colors = $item->variants
                                        ->pluck('color.name')
                                        ->unique()
                                        ->filter();
                                @endphp
                                @if($colors->count())
                                    @foreach($colors as $color)
                                        <span class="badge-light-custom mr-1 mb-1">{{ $color }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>

                        <div class="spec-row">
                            <div class="spec-label">Sizes</div>
                            <div class="spec-value">
                                @php
                                    $sizes = $item->variants
                                        ->pluck('size.name')
                                        ->unique()
                                        ->filter();
                                @endphp
                                @if($sizes->count())
                                    @foreach($sizes as $size)
                                        <span class="badge-light-custom mr-1 mb-1">{{ $size }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>

                        <div class="spec-row">
                            <div class="spec-label">Category</div>
                            <div class="spec-value">
                                {{ optional($item->category)->name ?? '-' }}
                            </div>
                        </div>

                        <div class="spec-row">
                            <div class="spec-label">Sub Category</div>
                            <div class="spec-value">
                                {{ optional($item->subCategory)->name ?? '-' }}
                            </div>
                        </div>

                        <div class="spec-row">
                            <div class="spec-label">Price</div>
                            <div class="spec-value">
                                ₹{{ number_format($item->price, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- IMAGE SWITCH SCRIPT --}}
@push('scripts')
<script>
    function changeImage(el) {
        document.getElementById('mainImage').src = el.src;
        document.querySelectorAll('.thumb-img').forEach(function(thumb) {
            thumb.classList.remove('active-thumb');
        });
        el.classList.add('active-thumb');
    }
</script>
@endpush

@endsection