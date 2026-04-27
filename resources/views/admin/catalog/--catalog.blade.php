@extends('layouts.app')

@section('content')
<div class="container py-4">

    <h3 class="mb-4">Product Catalog</h3>

    <div class="row">

        @forelse($items as $item)
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm catalog-card">

                {{-- IMAGE --}}
                <div class="catalog-img">
                    <!--<img src="{{ $item->image_url }}" alt="">-->
                    <img src="{{ $item->image_urls[0] ?? asset('no-image.png') }}" alt="">
                </div>

                {{-- BODY --}}
                <div class="card-body">

                    <h6 class="mb-1">{{ $item->name }}</h6>

                    <small class="text-muted">
                        {{ $item->category->name ?? '-' }}
                    </small>

                    {{-- COLORS --}}
                    <div class="mt-2">
                        @foreach($item->colors as $color)
                            <span class="badge badge-light border">
                                {{ $color->name }}
                            </span>
                        @endforeach
                    </div>

                    {{-- SIZES --}}
                    <div class="mt-2">
                        @foreach($item->sizes ?? [] as $size)
                            <span class="badge badge-secondary">
                                {{ $size }}
                            </span>
                        @endforeach
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="card-footer bg-white">

                    <strong>₹{{ number_format($item->price, 2) }}</strong>

                </div>

            </div>
        </div>

        @empty
            <div class="col-12 text-center">
                <p>No items found</p>
            </div>
        @endforelse

    </div>

    <div class="mt-3">
        {{ $items->links() }}
    </div>

</div>
@endsection