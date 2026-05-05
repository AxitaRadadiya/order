@extends('admin.layouts.app')
@section('title', 'Item Master')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Item Master</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Item Master</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="row mr-0">
    <div class="col-xl-12">
         @php
                    // Allowed tabs — default to 'category'
                    $allowedTabs = ['category', 'sub-category', 'group', 'sub-group', 'size', 'set', 'color'];
                    $activeTab   = in_array(request()->get('tab'), $allowedTabs)
                                    ? request()->get('tab')
                                    : 'category';
                @endphp

                <ul class="nav nav-tabs mb-3 ml-3" id="masterTab" role="tablist" style="border-bottom: none;">

                    <li class="nav-item mr-1">
                        <a href="#category" data-toggle="tab" aria-expanded="true"
                           class="nav-link {{ $activeTab === 'category' ? 'active' : '' }}">
                            <i class="mdi mdi-home-variant d-lg-none d-block"></i>
                            <span class="d-none d-lg-block font-weight-bold">Category</span>
                        </a>
                    </li>
                     <li class="nav-item mr-1">
                        <a href="#sub-category" data-toggle="tab" aria-expanded="true"
                           class="nav-link {{ $activeTab === 'sub-category' ? 'active' : '' }}">
                            <i class="mdi mdi-home-variant d-lg-none d-block"></i>
                            <span class="d-none d-lg-block font-weight-bold">Sub-Category</span>
                        </a>
                    </li>

                    <li class="nav-item mr-1">
                        <a href="#group" data-toggle="tab" aria-expanded="false"
                           class="nav-link {{ $activeTab === 'group' ? 'active' : '' }}">
                            <i class="mdi mdi-account-group d-lg-none d-block"></i>
                            <span class="d-none d-lg-block font-weight-bold">Group</span>
                        </a>
                    </li>

                    <li class="nav-item mr-1">
                        <a href="#sub-group" data-toggle="tab" aria-expanded="false"
                           class="nav-link {{ $activeTab === 'sub-group' ? 'active' : '' }}">
                            <i class="mdi mdi-account-group d-lg-none d-block"></i>
                            <span class="d-none d-lg-block font-weight-bold">Sub-Group</span>
                        </a>
                    </li>

                    <li class="nav-item mr-1">
                        <a href="#size" data-toggle="tab" aria-expanded="false"
                           class="nav-link {{ $activeTab === 'size' ? 'active' : '' }}">
                            <i class="mdi mdi-ruler d-lg-none d-block"></i>
                            <span class="d-none d-lg-block font-weight-bold">Size</span>
                        </a>
                    </li>
                    <li class="nav-item mr-1">
                        <a href="#set" data-toggle="tab" aria-expanded="false"
                           class="nav-link {{ $activeTab === 'set' ? 'active' : '' }}">
                            <i class="mdi mdi-format-list-bulleted d-lg-none d-block"></i>
                            <span class="d-none d-lg-block font-weight-bold">Set</span>
                        </a>
                    </li>
                     <li class="nav-item">
                        <a href="#color" data-toggle="tab" aria-expanded="false"
                           class="nav-link {{ $activeTab === 'color' ? 'active' : '' }}">
                            <i class="mdi mdi-palette d-lg-none d-block"></i>
                            <span class="d-none d-lg-block font-weight-bold">Color</span>
                        </a>
                    </li>

                </ul>
        <div class="card">
            <div class="card-body" style="padding: 0px !important;">


               

                <div class="tab-content">

                    <div class="tab-pane {{ $activeTab === 'category' ? 'show active' : '' }}" id="category">
                        @include('admin.item-master.category.index')
                    </div>

                    <div class="tab-pane {{ $activeTab === 'sub-category' ? 'show active' : '' }}" id="sub-category">
                        @include('admin.item-master.sub-category.index')
                    </div>

                    <div class="tab-pane {{ $activeTab === 'group' ? 'show active' : '' }}" id="group">
                        @include('admin.item-master.group.index')
                    </div>

                    <div class="tab-pane {{ $activeTab === 'sub-group' ? 'show active' : '' }}" id="sub-group">
                        @include('admin.item-master.sub-group.index')
                    </div>

                    <div class="tab-pane {{ $activeTab === 'size' ? 'show active' : '' }}" id="size">
                        @include('admin.item-master.size.index')
                    </div>
                    <div class="tab-pane {{ $activeTab === 'set' ? 'show active' : '' }}" id="set">
                        @include('admin.item-master.set.index')
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
