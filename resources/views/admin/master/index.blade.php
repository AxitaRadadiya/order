@extends('admin.layouts.app')
@section('title', 'Master Manage')
@section('content')
   <div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18"></h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Master Manage</li>
                </ol>
            </div> 
        </div>
    </div>
</div>     

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">Master Manage</h4>
                @php 
                    $activeTab = request()->get('tab','country');
                    $firstTabRendered = false; 
                @endphp
                <ul class="nav nav-tabs mb-3">
                    
                      
                    <li class="nav-item">
                        <a href="#country" data-toggle="tab" aria-expanded="true" class="nav-link {{ ($activeTab === 'country' || (!$activeTab && !$firstTabRendered)) ? 'active' : '' }} ">
                            <i class="mdi mdi-home-variant d-lg-none d-block"></i>
                            <span class="d-none d-lg-block">Country</span>
                        </a>
                    </li>
                    @php $firstTabRendered = true; @endphp
                

                    <li class="nav-item">
                        <a href="#state" data-toggle="tab" aria-expanded="true" class="nav-link {{ ($activeTab === 'state' || (!$activeTab && !$firstTabRendered)) ? 'active' : '' }}">
                            <i class="mdi mdi-account-circle d-lg-none d-block"></i>
                            <span class="d-none d-lg-block">State</span>
                        </a>
                    </li>
                    @php $firstTabRendered = true; @endphp
                     
                     
                    <li class="nav-item">
                        <a href="#city" data-toggle="tab" aria-expanded="false" class="nav-link {{ ($activeTab === 'city' || (!$activeTab && !$firstTabRendered)) ? 'active' : '' }} ">
                            <i class="mdi mdi-settings-outline d-lg-none d-block"></i>
                            <span class="d-none d-lg-block">City</span>
                        </a>
                    </li>
                    @php $firstTabRendered = true; @endphp    
                </ul>
                <div class="tab-content">
                    <div class="tab-pane {{ ($activeTab === 'country' || (!$activeTab && !$firstTabRendered))  ? 'show active' : '' }} " id="country">
                        <div>
                            @include('admin.master.country.index')
                        </div>
                    </div>
                    @php $firstTabRendered  = true; @endphp
                     
                    <div class="tab-pane {{ ($activeTab === 'state' || (!$activeTab && !$firstTabRendered)) ? 'show active' : '' }}" id="state">
                        <div>
                            @include('admin.master.state.index')
                        </div>
                    </div>
                    @php $firstTabRendered = true; @endphp

                    <div class="tab-pane {{ ($activeTab === 'city' || (!$activeTab && !$firstTabRendered)) ? 'show active' : '' }}" id="city">
                        <div>
                            @include('admin.master.city.index')
                        </div>
                    </div>
                    @php $firstTabRendered = true; @endphp
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection
