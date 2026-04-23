@extends('admin.layouts.app')
@section('title', 'Catalog')
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0"><i class="mr-2 text-teal"></i>Catalog</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a
                ></li>
            <li class="breadcrumb-item active">Catalog</li>
            </ol>
        </div>
        </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title
                "><i class="fas fa-th-large mr-1 text-teal"></i>Product Catalog</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($items as $item)
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 shadow-sm catalog-card">
                            {{-- IMAGE --}}
                            <div class="catalog-img">
                                <img src="{{ $item->image_url }}" alt="">
                            </div>
                            {{-- BODY --}}
                            <div class="card-body">
                                <h6 class="mb-1">{{ $item->name }}</h6>
                                <small class="text-muted
                                ">{{ $item->category->name ?? '-' }}</small>
                                {{-- COLORS --}}
                                <div class="mt-2">
                                    @foreach($item->colors as $color)
                                        <span class="badge badge-light border">{{
                                            $color->name
                                        }}</span>
                                    @endforeach
                                </div>
                                {{-- SIZES --}}
                                <div class="mt-2">
                                    @foreach($item->sizes ?? [] as $size)
                                        <span class="badge badge-secondary">{{
                                            $size
                                        }}</span>
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
            </div>
        </div>
    </div>
</section>
@endsection
