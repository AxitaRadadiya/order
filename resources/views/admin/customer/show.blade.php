@extends('admin.layouts.app')
@section('title', 'Customer Details')

@section('content')
<div class="customer-detail-container">
    <div class="content-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="d-flex align-items-center">
                        <span class="text-muted font-weight-medium mr-2" style="font-size: 0.9rem;">Dashboard</span>
                        <i class="fas fa-chevron-right text-muted mx-2" style="font-size: 0.75rem;"></i>
                        <a href="{{ route('customers.index') }}" class="text-theme font-weight-bold mx-2" style="font-size: 0.9rem;">Customers</a>
                        <i class="fas fa-chevron-right text-muted mx-2" style="font-size: 0.75rem;"></i>
                        <span class="text-muted font-weight-medium mx-2" style="font-size: 0.9rem;">Customer Details</span>
                    </div>
                </div>
                <div class="col-md-6 text-md-right">
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-custom mr-2"><i class="fas fa-arrow-left mr-1"></i> Back</a>
                    <button type="button" onclick="window.print()" class="btn btn-outline-custom mr-2"><i class="fas fa-print mr-1"></i> Print</button>
                    @if(auth()->check() && auth()->user()->hasPermission('customer-edit'))
                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-create"><i class="fas fa-edit mr-1"></i> Edit Customer</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @php $addr = $customer->address; @endphp

            <!-- Profile Overview Card -->
            <div class="card card-custom mb-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <!-- Profile avatar & contact details -->
                        <div class="col-lg-4 col-md-12 mb-4 mb-lg-0">
                            <div class="d-flex align-items-center flex-wrap flex-sm-nowrap">
                                <div class="avatar-container mr-sm-4 mb-3 mb-sm-0 mx-auto flex-shrink-0">
                                    @if(!empty($customer->profile_image) && file_exists(storage_path('app/public/' . $customer->profile_image)))
                                        <img src="{{ asset('storage/' . $customer->profile_image) }}" alt="Profile" class="avatar-img" />
                                    @else
                                        <i class="fas fa-store fa-2.5x text-purple"></i>
                                    @endif
                                </div>
                                <div class="text-center text-sm-left mx-auto">
                                    <div class="d-flex align-items-center justify-content-center justify-content-sm-start flex-wrap mb-1">
                                        <h3 class="font-weight-bold mb-0 mr-2" style="font-size: 1.35rem; color: #1e1b4b;">{{ $customer->name }}</h3>
                                        <span class="{{ $customer->status === 1 ? 'badge-status-active' : 'badge-status-inactive' }}">
                                            {{ $customer->status === 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                    <div class="contact-list text-left">
                                        <div class="contact-item">
                                            <i class="far fa-envelope text-purple mr-2"></i>
                                            <a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a>
                                        </div>
                                        <div class="contact-item">
                                            <i class="fas fa-phone text-purple mr-2"></i>
                                            <span>{{ $customer->mobile ?? '-' }}</span>
                                        </div>
                                        @if(!empty($customer->alternate_mobile))
                                            <div class="contact-item">
                                                <i class="fas fa-mobile-alt text-purple mr-2"></i>
                                                <span>{{ $customer->alternate_mobile }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2x2 grid fields -->
                        <div class="col-lg-5 col-md-8 mb-4 mb-lg-0 border-left-lg pl-lg-4 border-right-lg pr-lg-4" style="border-color: #f1f5f9 !important;">
                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <div class="info-grid-item">
                                        <div class="info-grid-icon-box">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <div>
                                            <div class="info-grid-label">Company Name</div>
                                            <div class="info-grid-value">{{ $customer->company_name ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <div class="info-grid-item">
                                        <div class="info-grid-icon-box">
                                            <i class="fas fa-file-invoice"></i>
                                        </div>
                                        <div>
                                            <div class="info-grid-label">GST Number</div>
                                            <div class="info-grid-value">{{ $customer->gst_number ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <div class="info-grid-item">
                                        <div class="info-grid-icon-box">
                                            <i class="fas fa-tags"></i>
                                        </div>
                                        <div>
                                            <div class="info-grid-label">Customer Type</div>
                                            <div class="info-grid-value">{{ $customer->role->name ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="info-grid-item">
                                        <div class="info-grid-icon-box">
                                            <i class="fas fa-id-card"></i>
                                        </div>
                                        <div>
                                            <div class="info-grid-label">PAN Number</div>
                                            <div class="info-grid-value">{{ $customer->pan_number ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Shop Image Section -->
                        <div class="col-lg-3 col-md-4">
                            <div class="pl-lg-2">
                                <div class="small font-weight-bold text-muted mb-2">Shop Image</div>
                                <div class="shop-image-container" style="border-radius: 12px; overflow: hidden; height: 120px; background-color: #f0effe; border: 1px solid #ddd6fe;">
                                    @if(!empty($customer->shop_image) && file_exists(storage_path('app/public/' . $customer->shop_image)))
                                        <img src="{{ asset('storage/' . $customer->shop_image) }}" alt="Shop" style="width:100%; height:100%; object-fit:cover;" />
                                    @elseif(!empty($customer->shop_image))
                                        <img src="{{ asset('storage/' . $customer->shop_image) }}" alt="Shop" style="width:100%; height:100%; object-fit:cover;" onerror="this.parentElement.innerHTML='<div class=\'h-100 d-flex align-items-center justify-content-center text-muted\'><i class=\'fas fa-store fa-2x\'></i></div>';" />
                                    @else
                                        <div class="h-100 d-flex align-items-center justify-content-center text-purple">
                                            <i class="fas fa-store fa-2x"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Business Information & Documents Row -->
            <div class="row">
                <!-- Business Information -->
                <div class="col-lg-6 mb-4">
                    <div class="card card-custom h-100">
                        <div class="card-header-custom">
                            <h5 class="mb-0"><i class="fas fa-briefcase mr-2"></i>Business Information</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="list-row-item">
                                <div class="list-row-left">
                                    <div class="list-row-icon-box">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div class="list-row-label">Payment Terms</div>
                                </div>
                                <div class="list-row-value">{{ $customer->payment_terms ? str_replace('_', ' ', ucfirst($customer->payment_terms)) : '-' }}</div>
                            </div>
                            <div class="list-row-item">
                                <div class="list-row-left">
                                    <div class="list-row-icon-box">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div class="list-row-label">Place of Supply</div>
                                </div>
                                <div class="list-row-value">{{ $customer->place_of_supply ?? '-' }}</div>
                            </div>
                            <div class="list-row-item">
                                <div class="list-row-left">
                                    <div class="list-row-icon-box">
                                        <i class="fas fa-map-marked-alt"></i>
                                    </div>
                                    <div class="list-row-label">Google Location</div>
                                </div>
                                <div class="list-row-value">
                                    @if(!empty($customer->google_location_link))
                                        <a href="{{ $customer->google_location_link }}" target="_blank" class="text-purple font-weight-bold">View on Map <i class="fas fa-external-link-alt ml-1" style="font-size: 0.8rem;"></i></a>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents -->
                <div class="col-lg-6 mb-4">
                    <div class="card card-custom h-100">
                        <div class="card-header-custom">
                            <h5 class="mb-0"><i class="fas fa-file-alt mr-2"></i>Documents</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row h-100">
                                <!-- PAN Card -->
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <div class="doc-card">
                                        <div class="doc-title">PAN Card</div>
                                        <div class="doc-preview-box">
                                            @if(!empty($customer->pan_card_image) && file_exists(storage_path('app/public/' . $customer->pan_card_image)))
                                                <img src="{{ asset('storage/' . $customer->pan_card_image) }}" alt="PAN" />
                                            @else
                                                <div class="illustration-container">
                                                    <div class="illustration-pan">
                                                        <div class="pan-line"></div>
                                                        <div class="pan-line short"></div>
                                                        <div class="pan-line shorter"></div>
                                                        <div class="pan-photo"></div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        @if(!empty($customer->pan_card_image))
                                            <a href="{{ asset('storage/' . $customer->pan_card_image) }}" target="_blank" class="btn-doc-view"><i class="fas fa-search-plus mr-1"></i> View Full Size</a>
                                        @else
                                            <button class="btn-doc-view" disabled style="opacity: 0.5; cursor: not-allowed;"><i class="fas fa-search-plus mr-1"></i> View Full Size</button>
                                        @endif
                                    </div>
                                </div>

                                <!-- GST Certificate -->
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <div class="doc-card">
                                        <div class="doc-title">GST Certificate</div>
                                        <div class="doc-preview-box">
                                            @if(!empty($customer->gst_certificate_image) && file_exists(storage_path('app/public/' . $customer->gst_certificate_image)))
                                                <img src="{{ asset('storage/' . $customer->gst_certificate_image) }}" alt="GST" />
                                            @else
                                                <div class="illustration-container">
                                                    <div class="illustration-gst">
                                                        <div class="gst-emblem"></div>
                                                        <div class="gst-line"></div>
                                                        <div class="gst-line short"></div>
                                                        <div class="gst-seal"></div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        @if(!empty($customer->gst_certificate_image))
                                            <a href="{{ asset('storage/' . $customer->gst_certificate_image) }}" target="_blank" class="btn-doc-view"><i class="fas fa-search-plus mr-1"></i> View Full Size</a>
                                        @else
                                            <button class="btn-doc-view" disabled style="opacity: 0.5; cursor: not-allowed;"><i class="fas fa-search-plus mr-1"></i> View Full Size</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Billing & Shipping Addresses -->
            <div class="row">
                <!-- Billing Address -->
                <div class="col-lg-6 mb-4">
                    <div class="card card-custom h-100">
                        <div class="card-header-custom">
                            <h5 class="mb-0"><i class="fas fa-map-marker-alt mr-2"></i>Billing Address</h5>
                        </div>
                        <div class="card-body p-3">
                            <div class="address-table">
                                <div class="address-row">
                                    <div class="address-label-col">
                                        <i class="far fa-user"></i> Attention
                                    </div>
                                    <div class="address-value-col">{{ $addr->billing_attention ?? '-' }}</div>
                                </div>
                                <div class="address-row">
                                    <div class="address-label-col">
                                        <i class="fas fa-road"></i> Street
                                    </div>
                                    <div class="address-value-col">{{ $addr->billing_street ?? '-' }}</div>
                                </div>
                                <div class="address-row">
                                    <div class="address-label-col">
                                        <i class="fas fa-city"></i> City
                                    </div>
                                    <div class="address-value-col">{{ $addr->billing_city ?? '-' }}</div>
                                </div>
                                <div class="address-row">
                                    <div class="address-label-col">
                                        <i class="far fa-map"></i> State
                                    </div>
                                    <div class="address-value-col">{{ $addr->billing_state ?? '-' }}</div>
                                </div>
                                <div class="address-row">
                                    <div class="address-label-col">
                                        <i class="fas fa-globe"></i> Country
                                    </div>
                                    <div class="address-value-col">{{ $addr->billing_country ?? '-' }}</div>
                                </div>
                                <div class="address-row">
                                    <div class="address-label-col">
                                        <i class="fas fa-map-pin"></i> PIN Code
                                    </div>
                                    <div class="address-value-col">{{ $addr->billing_pin_code ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="col-lg-6 mb-4">
                    <div class="card card-custom h-100">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-map-marker-alt mr-2"></i>Shipping Address</h5>
                            <div class="d-flex align-items-center">
                                <!-- <span class="mr-2 text-muted" style="font-size: 0.85rem;">Same as Billing</span> -->
                                <!-- <div class="custom-control custom-switch custom-switch-purple">
                                    <input type="checkbox" class="custom-control-input" id="sameAsBilling" {{ $addr && $addr->same_as ? 'checked' : '' }} disabled>
                                    <label class="custom-control-label" for="sameAsBilling"></label>
                                </div> -->
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="address-table">
                                <div class="address-row">
                                    <div class="address-label-col">
                                        <i class="far fa-user"></i> Attention
                                    </div>
                                    <div class="address-value-col">{{ $addr->shipping_attention ?? '-' }}</div>
                                </div>
                                <div class="address-row">
                                    <div class="address-label-col">
                                        <i class="fas fa-road"></i> Street
                                    </div>
                                    <div class="address-value-col">{{ $addr->shipping_street ?? '-' }}</div>
                                </div>
                                <div class="address-row">
                                    <div class="address-label-col">
                                        <i class="fas fa-city"></i> City
                                    </div>
                                    <div class="address-value-col">{{ $addr->shipping_city ?? '-' }}</div>
                                </div>
                                <div class="address-row">
                                    <div class="address-label-col">
                                        <i class="far fa-map"></i> State
                                    </div>
                                    <div class="address-value-col">{{ $addr->shipping_state ?? '-' }}</div>
                                </div>
                                <div class="address-row">
                                    <div class="address-label-col">
                                        <i class="fas fa-globe"></i> Country
                                    </div>
                                    <div class="address-value-col">{{ $addr->shipping_country ?? '-' }}</div>
                                </div>
                                <div class="address-row">
                                    <div class="address-label-col">
                                        <i class="fas fa-map-pin"></i> PIN Code
                                    </div>
                                    <div class="address-value-col">{{ $addr->shipping_pin_code ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users and Retailers Tables -->
            <div class="row">
                <!-- Users Created Table -->
                <div class="col-lg-6 mb-4">
                    <div class="card card-custom h-100">
                        <div class="card-header-custom d-flex flex-wrap justify-content-between align-items-center">
                            <h5 class="mb-0 mr-3">
                                <i class="fas fa-users mr-2"></i>Users List
                                <span id="usersCountBadge" class="badge-count-purple ml-2">{{ $createdUsers->total() ?? 0 }}</span>
                            </h5>
                            @if(method_exists($createdUsers, 'total') && $createdUsers->total() > 0)
                                <div style="max-width: 250px; flex: 1;">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                                        </div>
                                        <input type="text" id="searchUsersInput" class="form-control border-left-0" placeholder="Search users...">
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="card-body p-0">
                            @if(!method_exists($createdUsers, 'total') || $createdUsers->isEmpty())
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-user-slash fa-2x mb-3 d-block text-gray-300 text-purple" style="opacity: 0.5;"></i>
                                    No users have been created by this profile yet.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table id="usersTable" class="table table-custom mb-0 w-100">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;">Sr No.</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Mobile</th>
                                                <th>Role</th>
                                                <th>Status</th>
                                                <th class="text-right" style="width: 120px;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($createdUsers as $i => $user)
                                            <tr>
                                                <td>{{ $createdUsers->firstItem() + $loop->index }}</td>
                                                <td class="font-weight-bold">{{ $user->name }}</td>
                                                <td><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
                                                <td>{{ $user->mobile ?? '-' }}</td>
                                                <td>{{ $user->role->name ?? '-' }}</td>
                                                <td>
                                                    <span class="{{ $user->status ? 'badge-status-active' : 'badge-status-inactive' }}">
                                                        {{ $user->status ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td class="text-right">
                                                    <div class="action-btn-group">
                                                        <a href="{{ route('users.show', $user->id) }}" class="action-btn-item action-btn-view" title="View"><i class="fas fa-eye"></i></a>
                                                        <a href="{{ route('users.edit', $user->id) }}" class="action-btn-item action-btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                                                        <button type="button" class="action-btn-item action-btn-delete btn-delete-user" data-id="{{ $user->id }}" data-name="{{ $user->name }}" title="Delete"><i class="fas fa-trash"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($createdUsers->hasPages())
                                    <div class="customer-list-dt-bottom">
                                        <div class="d-flex justify-content-between align-items-center w-100 px-1">
                                            <small class="text-muted">
                                                Showing {{ $createdUsers->firstItem() }} – {{ $createdUsers->lastItem() }} of {{ $createdUsers->total() }}
                                            </small>
                                            <div class="customer-list-pagination pagination">
                                                {{ $createdUsers->appends(array_merge(request()->query(), ['retailers_page' => $retailersUnderDistributor->currentPage()]))->links('pagination::bootstrap-4') }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Retailers Table -->
                <div class="col-lg-6 mb-4">
                    <div class="card card-custom h-100">
                        <div class="card-header-custom d-flex flex-wrap justify-content-between align-items-center">
                            <h5 class="mb-0 mr-3">
                                <i class="fas fa-store mr-2"></i>Retailers List
                                <span id="retailersCountBadge" class="badge-count-purple ml-2">{{ $retailersUnderDistributor->total() ?? 0 }}</span>
                            </h5>
                            @if(method_exists($retailersUnderDistributor, 'total') && $retailersUnderDistributor->total() > 0)
                                <div style="max-width: 250px; flex: 1;">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                                        </div>
                                        <input type="text" id="searchRetailersInput" class="form-control border-left-0" placeholder="Search retailers...">
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="card-body p-0">
                            @if(!method_exists($retailersUnderDistributor, 'total') || $retailersUnderDistributor->isEmpty())
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-store-slash fa-2x mb-3 d-block text-purple" style="opacity: 0.5;"></i>
                                    No retailers found under this distributor.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table id="retailersTable" class="table table-custom mb-0 w-100">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;">Sr No.</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Mobile</th>
                                                <th>Status</th>
                                                <th class="text-right" style="width: 100px;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($retailersUnderDistributor as $i => $retailer)
                                            <tr>
                                                <td>{{ $retailersUnderDistributor->firstItem() + $loop->index }}</td>
                                                <td class="font-weight-bold">{{ $retailer->name }}</td>
                                                <td><a href="mailto:{{ $retailer->email }}">{{ $retailer->email }}</a></td>
                                                <td>{{ $retailer->mobile ?? '-' }}</td>
                                                <td>
                                                    <span class="{{ $retailer->status ? 'badge-status-active' : 'badge-status-inactive' }}">
                                                        {{ $retailer->status ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td class="text-right">
                                                    <div class="action-btn-group">
                                                        <a href="{{ route('customers.show', $retailer->id) }}" class="action-btn-item action-btn-view" title="View"><i class="fas fa-eye"></i></a>
                                                        <a href="{{ route('customers.edit', $retailer->id) }}" class="action-btn-item action-btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($retailersUnderDistributor->hasPages())
                                    <div class="customer-list-dt-bottom">
                                        <div class="d-flex justify-content-between align-items-center w-100 px-1">
                                            <small class="text-muted">
                                                Showing {{ $retailersUnderDistributor->firstItem() }} – {{ $retailersUnderDistributor->lastItem() }} of {{ $retailersUnderDistributor->total() }}
                                            </small>
                                            <div class="customer-list-pagination pagination">
                                                {{ $retailersUnderDistributor->appends(array_merge(request()->query(), ['users_page' => $createdUsers->currentPage()]))->links('pagination::bootstrap-4') }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Metadata -->
            <div class="footer-meta-box mb-4">
                <div class="footer-meta-item">
                    <i class="far fa-calendar-alt mr-2"></i>
                    <span>Added On : {{ optional($customer->created_at)->format('d M, Y h:i A') ?? '-' }}</span>
                </div>
                <div class="footer-meta-item">
                    <i class="far fa-edit mr-2"></i>
                    <span>Last Updated : {{ optional($customer->updated_at)->format('d M, Y h:i A') ?? '-' }}</span>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle mr-1"></i>Confirm Delete
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <p>Delete <strong id="delName"></strong>?</p>
                <small class="text-muted">This action cannot be undone.</small>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

