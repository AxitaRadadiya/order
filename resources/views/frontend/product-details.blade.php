@extends('layouts.frontend')

@section('title', $item->name)

@section('content')

<div class="container py-5">

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
        <a href="{{ route('products') }}"class="btn btn-secondary px-4"> Back</a>

    <div class="row g-4">
      {{-- PRODUCT IMAGES --}}
      
        <div class="col-lg-5">
          <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body text-center">

                    {{-- MAIN IMAGE --}}
                    <img id="mainImage"
                         src="{{ isset($images[0]) ? asset('storage/'.$images[0]) : asset('no-image.png') }}"
                         class="img-fluid rounded"
                         alt="{{ $item->name }}">

                </div>
            </div>

            {{-- THUMBNAILS --}}
            @if(count($images))
                <div class="d-flex flex-wrap gap-2 justify-content-center mt-3">
                    @foreach($images as $img)
                        <img src="{{ asset('storage/'.$img) }}"
                             onclick="changeImage(this)"
                             class="img-thumbnail rounded"
                             style="width:80px;height:80px;object-fit:cover;cursor:pointer;">
                    @endforeach
                </div>
            @endif

        </div>

        {{-- PRODUCT DETAILS --}}
        <div class="col-lg-7">

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    {{-- TITLE --}}
                    <div class="mb-3">
                        <h2 class="fw-bold mb-1">
                            {{ $item->name }}
                        </h2>                       
                    </div>

                    {{-- PRICE --}}
                    <div class="mb-2">
                        <h3 class="text-danger fw-bold mb-0">
                            ₹{{ number_format($item->price, 2) }}
                        </h3>
                    </div>

                    {{-- DESCRIPTION --}}
                    <div class="mb-2">
                        <p class="text-muted mb-0">
                            {{ $item->description ?? 'No description available.' }}
                        </p>
                    </div>

                    {{-- PRODUCT DETAILS --}}
                    <div class="card border rounded-4">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4 fw-semibold">Item Name</div>
                                <div class="col-md-8">{{ $item->name }}</div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4 fw-semibold">Article Number</div>
                                <div class="col-md-8">
                                    {{ $item->article_number ?? '-' }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4 fw-semibold">Colors</div>
                                <div class="col-md-8">
                                    @if(!empty($item->colors) && $item->colors->count())
                                        {{ $item->colors->pluck('name')->implode(', ') }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4 fw-semibold">Sizes</div>
                                <div class="col-md-8">

                                    @php
                                        $sizes = is_array($item->sizes)
                                            ? $item->sizes
                                            : (is_string($item->sizes)
                                                ? explode(',', $item->sizes)
                                                : []);

                                        $sizes = array_map('trim', $sizes ?: []);
                                    @endphp

                                    {{ empty($sizes) ? '-' : implode(', ', $sizes) }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4 fw-semibold">Category</div>
                                <div class="col-md-8">
                                    {{ optional($item->category)->name ?? '-' }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4 fw-semibold">Sub Category</div>
                                <div class="col-md-8">
                                    {{ $item->sub_category ?? '-' }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4 fw-semibold">Price</div>
                                <div class="col-md-8">
                                    ₹{{ number_format($item->price, 2) }}
                                </div>
                            </div>


                        </div>
                    </div>

                    {{-- BUTTON --}}
                    <div class="mt-4">
                      
                    </div>

                </div>
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