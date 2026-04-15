@extends('admin.layouts.app')
@section('title', 'Item Master')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">Item Master</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Item Master</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">Item Master</h4>

                @php
                    // Allowed tabs — default to 'category'
                    $allowedTabs = ['category', 'group', 'size', 'color'];
                    $activeTab   = in_array(request()->get('tab'), $allowedTabs)
                                    ? request()->get('tab')
                                    : 'category';
                @endphp

                <ul class="nav nav-tabs mb-3" id="masterTab" role="tablist">

                    <li class="nav-item">
                        <a href="#category" data-toggle="tab" aria-expanded="true"
                           class="nav-link {{ $activeTab === 'category' ? 'active' : '' }}">
                            <i class="mdi mdi-home-variant d-lg-none d-block"></i>
                            <span class="d-none d-lg-block">Category</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#group" data-toggle="tab" aria-expanded="false"
                           class="nav-link {{ $activeTab === 'group' ? 'active' : '' }}">
                            <i class="mdi mdi-account-group d-lg-none d-block"></i>
                            <span class="d-none d-lg-block">Group</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#size" data-toggle="tab" aria-expanded="false"
                           class="nav-link {{ $activeTab === 'size' ? 'active' : '' }}">
                            <i class="mdi mdi-ruler d-lg-none d-block"></i>
                            <span class="d-none d-lg-block">Size</span>
                        </a>
                    </li>
                     <li class="nav-item">
                        <a href="#color" data-toggle="tab" aria-expanded="false"
                           class="nav-link {{ $activeTab === 'color' ? 'active' : '' }}">
                            <i class="mdi mdi-palette d-lg-none d-block"></i>
                            <span class="d-none d-lg-block">Color</span>
                        </a>
                    </li>

                </ul>

                <div class="tab-content">

                    <div class="tab-pane {{ $activeTab === 'category' ? 'show active' : '' }}" id="category">
                        @include('admin.item-master.category.index')
                    </div>

                    <div class="tab-pane {{ $activeTab === 'group' ? 'show active' : '' }}" id="group">
                        @include('admin.item-master.group.index')
                    </div>

                    <div class="tab-pane {{ $activeTab === 'size' ? 'show active' : '' }}" id="size">
                        @include('admin.item-master.size.index')
                    </div>
                    <div class="tab-pane {{ $activeTab === 'color' ? 'show active' : '' }}" id="color">
                        @include('admin.item-master.color.index')
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#masterTab a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var tab = $(e.target).attr('href').replace('#', '');
            var url = new URL(window.location.href);
            url.searchParams.set('tab', tab);
            window.history.pushState(null, '', url.toString());
        });
    });
</script>

@endsection