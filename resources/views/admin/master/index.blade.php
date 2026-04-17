@extends('admin.layouts.app')
@section('title', 'Master Manage')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="mr-2 text-teal"></i>Master Management</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Master Manage</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="row">
    <div class="col-xl-12">
        @php
                    // Allowed tabs — default to 'country'
                    $allowedTabs = ['country', 'state', 'city'];
                    $activeTab   = in_array(request()->get('tab'), $allowedTabs)
                                    ? request()->get('tab')
                                    : 'country';
                @endphp

                <ul class="nav nav-tabs mb-3" id="masterTab" role="tablist" style="border-bottom: none;">
                    <li class="nav-item">
                        <a href="#country" data-toggle="tab" aria-expanded="false"
                           class="nav-link {{ $activeTab === 'country' ? 'active' : '' }}">
                            <i class="mdi mdi-home-variant d-lg-none d-block"></i>
                            <span class="font-weight-bold">Country</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#state" data-toggle="tab" aria-expanded="false"
                           class="nav-link {{ $activeTab === 'state' ? 'active' : '' }}">
                            <i class="mdi mdi-map d-lg-none d-block"></i>
                            <span class="font-weight-bold">State</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#city" data-toggle="tab" aria-expanded="false"
                           class="nav-link {{ $activeTab === 'city' ? 'active' : '' }}">
                            <i class="mdi mdi-city d-lg-none d-block"></i>
                            <span class="font-weight-bold">City</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#customerType" data-toggle="tab" aria-expanded="false"
                           class="nav-link {{ $activeTab === 'customerType' ? 'active' : '' }}">
                            <i class="mdi mdi-account-group d-lg-none d-block"></i>
                                <span class="font-weight-bold">Customer Type</span>
                        </a>
                    </li>

                </ul>
        <div class="card" style="box-shadow: none;">
            <div class="card-body" style="padding: 0px !important;">

              

                

                <div class="tab-content">

                    <div class="tab-pane {{ $activeTab === 'country' ? 'show active' : '' }}" id="country">
                        @include('admin.master.country.index')
                    </div>

                    <div class="tab-pane {{ $activeTab === 'state' ? 'show active' : '' }}" id="state">
                        @include('admin.master.state.index')
                    </div>

                    <div class="tab-pane {{ $activeTab === 'city' ? 'show active' : '' }}" id="city">
                        @include('admin.master.city.index')
                    </div>

                     <div class="tab-pane {{ $activeTab === 'customerType' ? 'show active' : '' }}" id="customerType">
                        @include('admin.master.customerType.index')
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