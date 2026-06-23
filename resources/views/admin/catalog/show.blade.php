@extends('admin.layouts.app')
@section('title', $item->name)

@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Product Details</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('catalog') }}">Catalog</a></li>
                    <li class="breadcrumb-item active">{{ $item->name }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
<div class="container-fluid product-detail-container">
    <div class="row">
        {{-- LEFT: Image Gallery --}}
        <div class="col-md-6">
            <div class="product-gallery">
                @php
                    $images = [];
                    if (!empty($item->images) && is_array($item->images)) {
                        $images = $item->images;
                    } elseif (!empty($item->image)) {
                        $images = [$item->image];
                    }
                @endphp
                
                @if(!empty($images))
                    <img id="mainImage" src="{{ asset('storage/' . $images[0]) }}" alt="{{ $item->name }}">
                    @if(count($images) > 1)
                        <div class="thumbnails">
                            @foreach($images as $idx => $img)
                                <img src="{{ asset('storage/' . $img) }}" 
                                     alt="Thumb {{ $idx + 1 }}"
                                     class="{{ $idx === 0 ? 'active' : '' }}"
                                     onclick="changeMainImage(this, '{{ asset('storage/' . $img) }}')">
                            @endforeach
                        </div>
                    @endif
                @else
                    <div style="padding: 60px 0; color: #999;">
                        <i class="fas fa-image fa-3x d-block mb-2"></i>
                        No image available
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT: Product Info --}}
        <div class="col-md-6">
            <div class="product-info">
                {{-- Product Title & Price --}}
                <div class="product-title-catalog">{{ $item->name }}</div>
                <div class="product-article">Article: {{ $item->article_number ?? '-' }}</div>
                <div class="product-price">₹{{ number_format($item->price, 2) }}</div>
                
                @if($item->description)
                    <div class="product-description">{{ $item->description }}</div>
                @endif

                {{-- UPPER SECTION: Product Details --}}
                <div class="product-details-section">
                    <div class="detail-row">
                        <span class="detail-label">Category</span>
                        <span class="detail-value">{{ optional($item->category)->name ?? '-' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Sub Category</span>
                        <span class="detail-value">{{ optional($item->subCategory)->name ?? '-' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Group</span>
                        <span class="detail-value">{{ optional($item->group)->name ?? '-' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Sub Group</span>
                        <span class="detail-value">{{ optional($item->subGroup)->name ?? '-' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Tax</span>
                        <span class="detail-value">{{ $item->tax_percent ?? 0 }}%</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status</span>
                        <span class="detail-value">{{ $item->status ? 'Active' : 'Inactive' }}</span>
                    </div>
                </div>

                {{-- LOWER SECTION: Color & Size Selection --}}
                @if(auth()->check() && auth()->user()->hasRole(['retailer', 'distributor']))
                    <div class="selection-section">
                        <div class="section-title">Add to Cart</div>
                        
                        <div id="addToCartForm">
                            @csrf
                            <input type="hidden" name="item_id" value="{{ $item->id }}" id="itemId">

                            {{-- COLORS --}}
                            @php
                                $uniqueColors = $item->variants->pluck('color')->filter()->unique('id')->values();
                            @endphp
                            @if($uniqueColors->isNotEmpty())
                                <div class="option-section">
                                    <span class="option-label">Select Colors <span class="text-muted">(Multiple allowed)</span></span>
                                    <div class="d-flex flex-wrap" id="colorOptions">
                                        @foreach($uniqueColors as $color)
                                            <button type="button" 
                                                    class="color-option-btn" 
                                                    data-color-id="{{ $color->id }}"
                                                    data-color-code="{{ $color->color_code ?? '#ccc' }}"
                                                    data-color-name="{{ $color->name ?? $color->color_code }}">
                                                <span class="color-dot" style="background:{{ $color->color_code ?? '#ccc' }};"></span>
                                                {{ $color->color_code ?? $color->name }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- SIZES --}}
                            <div class="option-section" id="sizeSection" style="display:none;">
                                <span class="option-label">
                                    Select Sizes 
                                    <span class="text-muted">(Click a color first)</span>
                                    <span id="currentColorIndicator" class="current-color-indicator" style="display:none;"></span>
                                </span>
                                <div id="sizeOptionsContainer"></div>
                            </div>

                            {{-- Selected Items --}}
                            <div class="selected-sizes-section" id="selectedSizesSection">
                                <div class="option-label">Selected Items:</div>
                                <div id="selectedSizesList"></div>
                            </div>

                            {{-- Feedback --}}
                            <div id="feedback" class="feedback-message"></div>

                            {{-- Buttons --}}
                            <div class="action-buttons">
                                <button type="button" id="addToCartBtn" class="btn-add-cart" disabled>
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </button>
                                <button type="button" id="buyNowBtn" class="btn-buy-now" disabled>
                                    <i class="fas fa-bolt"></i> Buy Now
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</section>

@endsection

@section('pageScript')
<script>
$(document).ready(function() {
    let selectedItems = {};
    let currentColorId = null;
    const itemId = {{ $item->id }};

    
    window.changeMainImage = function(thumb, src) {
        document.getElementById('mainImage').src = src;
        document.querySelectorAll('.thumbnails img').forEach(img => img.classList.remove('active'));
        thumb.classList.add('active');
    };

    // =============================================
    // COLOR SELECTION
    // =============================================
    $('.color-option-btn').on('click', function() {
        const $btn = $(this);
        const colorId = $btn.data('color-id');
        const colorName = $btn.data('color-name');
        const colorCode = $btn.data('color-code');

        // Toggle "currently viewing" state, NOT cart-selection state
        if (currentColorId == colorId) {
            // Clicking the same color again collapses the size panel
            currentColorId = null;
            $('#sizeSection').hide();
            $('#sizeOptionsContainer').empty();
            $('#currentColorIndicator').hide();
            $('.color-option-btn').removeClass('viewing');
        } else {
            currentColorId = colorId;

            if (!selectedItems[colorId]) {
                selectedItems[colorId] = {
                    colorName: colorName,
                    colorCode: colorCode,
                    sizes: {},
                    selectedSizes: []
                };
            }

            $('.color-option-btn').removeClass('viewing');
            $btn.addClass('viewing');

            $('#sizeSection').show();
            showSizesForColor(colorId);
        }

        updateColorButtonStates();
    });

    // =============================================
    // SHOW SIZES FOR A SPECIFIC COLOR
    // =============================================
    function showSizesForColor(colorId) {
        const color = selectedItems[colorId];
        if (!color) return;
        
        $('#currentColorIndicator').show().text(color.colorName);
        
        if (Object.keys(color.sizes).length > 0) {
            renderSizeOptions(colorId);
            return;
        }
        
        const $container = $('#sizeOptionsContainer');
        $container.html('<span class="loading-text"><i class="fas fa-spinner fa-spin"></i> Loading sizes...</span>');
        $('#sizeSection').show();
        
        $.ajax({
            url: '{{ url("/api/item-variants/sizes-by-color") }}',
            method: 'GET',
            data: {
                item_id: itemId,
                color_id: colorId
            },
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                const sizes = response.sizes || [];
                if (sizes.length === 0) {
                    $container.html('<span class="no-sizes-text">No sizes available for this color</span>');
                    return;
                }
                
                sizes.forEach(function(s) {
                    const stock = parseInt(s.available_qty) || 0;
                    if (stock > 0) {
                        // Store size as string
                        color.sizes[String(s.label)] = stock;
                    }
                });
                
                renderSizeOptions(colorId);
            },
            error: function() {
                $container.html('<span class="no-sizes-text" style="color:red;">Failed to load sizes</span>');
            }
        });
    }

    // =============================================
    // RENDER SIZE OPTIONS
    // =============================================
    function renderSizeOptions(colorId) {
        const color = selectedItems[colorId];
        if (!color) return;
        
        const $container = $('#sizeOptionsContainer');
        const sizeNames = Object.keys(color.sizes).sort();
        
        if (sizeNames.length === 0) {
            $container.html('<span class="no-sizes-text">No sizes available for this color</span>');
            return;
        }
        
        let html = `<div style="margin-bottom:8px;padding:8px 0;">
            <div style="font-weight:600;font-size:13px;margin-bottom:8px;">
                <span class="color-dot" style="display:inline-block;width:12px;height:12px;border-radius:50%;background:${color.colorCode || '#ccc'};border:1px solid #ddd;margin-right:6px;vertical-align:middle;"></span>
                ${color.colorName}
            </div>
            <div class="d-flex flex-wrap">`;
        
        sizeNames.forEach(function(size) {
            const stock = color.sizes[size];
            const isSelected = color.selectedSizes && color.selectedSizes.indexOf(size) !== -1;
            
            html += `<button type="button" 
                            class="size-option-btn ${isSelected ? 'active' : ''} ${stock > 0 ? '' : 'out-of-stock'}" 
                            ${stock > 0 ? '' : 'disabled'}
                            data-color-id="${colorId}"
                            data-size="${String(size)}"
                            data-stock="${stock}">
                        ${size}
                    </button>`;

        });
        
        html += `</div></div>`;
        $container.html(html);
    }

    // =============================================
    // SIZE SELECTION
    // =============================================

    $(document).on('click', '.size-option-btn:not(.out-of-stock):not(:disabled)', function() {
        const $btn = $(this);

        const colorId = $btn.data('color-id');
        const size = String($btn.data('size')); // Convert to string
        
        if (!selectedItems[colorId]) return;
        
        if ($btn.hasClass('active')) {
            $btn.removeClass('active');
            const index = selectedItems[colorId].selectedSizes.indexOf(size);
            if (index !== -1) {
                selectedItems[colorId].selectedSizes.splice(index, 1);
            }
        } else {
            $btn.addClass('active');
            if (!selectedItems[colorId].selectedSizes) {
                selectedItems[colorId].selectedSizes = [];
            }
            if (selectedItems[colorId].selectedSizes.indexOf(size) === -1) {
                selectedItems[colorId].selectedSizes.push(size);
            }
        }
        
        updateSelectedSizesDisplay();
        updateColorButtonStates();
        updateButtonState();
    });

    // =============================================
    // UPDATE SELECTED SIZES DISPLAY
    // =============================================
    function updateSelectedSizesDisplay() {
        const $section = $('#selectedSizesSection');
        const $list = $('#selectedSizesList');
        const colorIds = Object.keys(selectedItems);
        
        let hasSelection = false;
        colorIds.forEach(function(colorId) {
            if (selectedItems[colorId].selectedSizes && selectedItems[colorId].selectedSizes.length > 0) {
                hasSelection = true;
            }
        });
        
        if (!hasSelection) {
            $section.removeClass('show');
            $list.empty();
            return;
        }
        
        $section.addClass('show');
        $list.empty();
        
        colorIds.forEach(function(colorId) {
            const color = selectedItems[colorId];
            const selectedSizes = color.selectedSizes || [];
            
            if (selectedSizes.length === 0) return;
            
            const validSizes = selectedSizes.filter(function(size) {
                return color.sizes[size] !== undefined;
            });
            
            if (validSizes.length === 0) return;
            
            const $group = $(`
                <div class="color-group">
                    <div class="color-group-header">
                        <span class="color-dot" style="display:inline-block;width:12px;height:12px;border-radius:50%;background:${color.colorCode || '#ccc'};border:1px solid #ddd;margin-right:6px;vertical-align:middle;"></span>
                        ${color.colorName}
                        <span style="font-size:11px;color:#888;font-weight:400;">(${validSizes.length})</span>
                    </div>
                    <div class="color-group-sizes">
                        ${validSizes.map(function(size) {
                            return `<span class="selected-size-item">
                                ${size}
                                <span class="remove-size" data-color-id="${colorId}" data-size="${String(size)}">&times;</span>
                            </span>`;
                        }).join('')}
                    </div>
                </div>
            `);
            $list.append($group);
        });
    }

    // =============================================
    // REMOVE SIZE
    // =============================================
    $(document).on('click', '.remove-size', function() {
        const colorId = $(this).data('color-id');
        const size = String($(this).data('size'));
        
        if (selectedItems[colorId]) {
            const index = selectedItems[colorId].selectedSizes.indexOf(size);
            if (index !== -1) {
                selectedItems[colorId].selectedSizes.splice(index, 1);
            }
            $(`.size-option-btn[data-color-id="${colorId}"][data-size="${size}"]`).removeClass('active');
        }
        
        updateSelectedSizesDisplay();
        updateColorButtonStates();
        updateButtonState();
    });

    // =============================================
    // UPDATE BUTTON STATE
    // =============================================
    function updateButtonState() {
        let hasSelection = false;
        const colorIds = Object.keys(selectedItems);
        colorIds.forEach(function(colorId) {
            const selectedSizes = selectedItems[colorId].selectedSizes || [];
            if (selectedSizes.length > 0) {
                hasSelection = true;
            }
        });
        $('#addToCartBtn, #buyNowBtn').prop('disabled', !hasSelection);
    }

    // =============================================
    // GET ALL SELECTED ITEMS
    // =============================================
    function getSelectedItems() {
        const items = [];
        const colorIds = Object.keys(selectedItems);
        
        colorIds.forEach(function(colorId) {
            const color = selectedItems[colorId];
            const selectedSizes = color.selectedSizes || [];
            selectedSizes.forEach(function(size) {
                if (color.sizes[size] !== undefined) {
                    items.push({
                        item_id: parseInt(itemId),
                        color_id: parseInt(colorId),
                        size: String(size), // Convert to string
                        qty: 1
                    });
                }
            });
        });
        
        return items;
    }

    // =============================================
    // ADD TO CART
    // =============================================
    $('#addToCartBtn').on('click', function() {
    const $btn = $(this);
    const items = getSelectedItems();

    if (items.length === 0) {
        showFeedback('Please select at least one item.', 'error');
        return;
    }

    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Checking stock...');

    validateStockBeforeSubmit(function(allAvailable) {
        if (!allAvailable) {
            showFeedback('Some selected sizes just went out of stock and were removed. Please review and try again.', 'error');
            $btn.html('<i class="fas fa-cart-plus"></i> Add to Cart');
            updateButtonState();
            return;
        }

        const items = getSelectedItems();
        if (items.length === 0) {
            showFeedback('Please select at least one item.', 'error');
            $btn.html('<i class="fas fa-cart-plus"></i> Add to Cart');
            return;
        }

        $btn.html('<i class="fas fa-spinner fa-spin"></i> Adding...');

        let successCount = 0;
        let errorCount = 0;
        let errors = [];

        function processNext(index) {
            if (index >= items.length) {
                if (successCount > 0 && errorCount === 0) {
                    showFeedback('All items added to cart!', 'success');
                    setTimeout(function() { location.reload(); }, 1000);
                } else if (successCount > 0 && errorCount > 0) {
                    showFeedback('Added ' + successCount + ' items. ' + errorCount + ' failed.', 'error');
                    $btn.prop('disabled', false).html('<i class="fas fa-cart-plus"></i> Add to Cart');
                } else {
                    showFeedback(errors.join('\n'), 'error');
                    $btn.prop('disabled', false).html('<i class="fas fa-cart-plus"></i> Add to Cart');
                }

                selectedItems = {};
                currentColorId = null;
                $('.color-option-btn').removeClass('active viewing');
                $('.size-option-btn').removeClass('active');
                updateSelectedSizesDisplay();
                updateButtonState();
                $('#sizeOptionsContainer').empty();
                $('#sizeSection').hide();
                $('#currentColorIndicator').hide();
                return;
            }

            const item = items[index];

            $.ajax({
                url: '{{ route("cart.store") }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                data: JSON.stringify({
                    item_id: parseInt(item.item_id),
                    color_id: parseInt(item.color_id),
                    size: String(item.size),
                    qty: parseInt(item.qty) || 1
                }),
                contentType: 'application/json',
                success: function(response) {
                    if (response.success) {
                        successCount++;
                    } else {
                        errorCount++;
                        errors.push(item.size + ': ' + (response.message || 'Failed'));
                    }
                    processNext(index + 1);
                },
                error: function(xhr) {
                        errorCount++;
                        let msg = 'Failed to add ' + item.size;
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                msg = item.size + ': ' + response.message;
                            }
                        } catch(e) {}
                        errors.push(msg);
                        processNext(index + 1);
                    }
                });
            }

            processNext(0);
        });
    });

    // =============================================
    // BUY NOW
    // =============================================
    $('#buyNowBtn').on('click', function() {
        const items = getSelectedItems();
        
        if (items.length === 0) {
            showFeedback('Please select at least one item.', 'error');
            return;
        }

        let successCount = 0;
        let errorCount = 0;

        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

        function processNext(index) {
            if (index >= items.length) {
                if (successCount > 0 && errorCount === 0) {
                    window.location.href = '{{ route("orders.create", ["from_cart" => 1]) }}';
                } else if (successCount > 0 && errorCount > 0) {
                    showFeedback('Added ' + successCount + ' items. ' + errorCount + ' failed.', 'error');
                    $('#buyNowBtn').prop('disabled', false).html('<i class="fas fa-bolt"></i> Buy Now');
                } else {
                    showFeedback('Failed to add items.', 'error');
                    $('#buyNowBtn').prop('disabled', false).html('<i class="fas fa-bolt"></i> Buy Now');
                }
                return;
            }

            const item = items[index];
            
            $.ajax({
                url: '{{ route("cart.store") }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                data: JSON.stringify({
                    item_id: parseInt(item.item_id),
                    color_id: parseInt(item.color_id),
                    size: String(item.size), // Convert to string
                    qty: parseInt(item.qty) || 1
                }),
                contentType: 'application/json',
                success: function(response) {
                    if (response.success) {
                        successCount++;
                    } else {
                        errorCount++;
                    }
                    processNext(index + 1);
                },
                error: function() {
                    errorCount++;
                    processNext(index + 1);
                }
            });
        }

        processNext(0);
    });

    function updateColorButtonStates() {
        $('.color-option-btn').each(function() {
            const $btn = $(this);
            const colorId = $btn.data('color-id');
            const hasSelectedSizes = selectedItems[colorId] &&
                selectedItems[colorId].selectedSizes &&
                selectedItems[colorId].selectedSizes.length > 0;

            $btn.toggleClass('active', !!hasSelectedSizes);
        });
    }

    // =============================================
    // HELPER FUNCTIONS
    // =============================================
    function showFeedback(message, type) {
        const $feedback = $('#feedback');
        if (!message) {
            $feedback.hide().removeClass('success error');
            return;
        }
        $feedback.removeClass('success error').addClass(type).text(message).show();
        if (type === 'success') {
            setTimeout(function() { $feedback.fadeOut(); }, 3000);
        }
    }

    function disableSize(colorId, size) {
        const color = selectedItems[colorId];
        if (!color) return;

        color.sizes[size] = 0;

        const idx = (color.selectedSizes || []).indexOf(size);
        if (idx !== -1) {
            color.selectedSizes.splice(idx, 1);
        }

        $(`.size-option-btn[data-color-id="${colorId}"][data-size="${size}"]`)
            .removeClass('active')
            .addClass('out-of-stock')
            .prop('disabled', true)
            .attr('data-stock', 0);

        updateSelectedSizesDisplay();
        updateColorButtonStates();
        updateButtonState();
    }

    function validateStockBeforeSubmit(callback) {
        const colorIds = Object.keys(selectedItems).filter(function(colorId) {
            return selectedItems[colorId].selectedSizes && selectedItems[colorId].selectedSizes.length > 0;
        });

        if (colorIds.length === 0) {
            callback(true);
            return;
        }

        let pending = colorIds.length;
        let removedAny = false;

        colorIds.forEach(function(colorId) {
            $.ajax({
                url: '{{ url("/api/item-variants/sizes-by-color") }}',
                method: 'GET',
                data: { item_id: itemId, color_id: colorId },
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    const sizes = response.sizes || [];
                    const liveStock = {};
                    sizes.forEach(function(s) {
                        liveStock[String(s.label)] = parseInt(s.available_qty) || 0;
                    });

                    const color = selectedItems[colorId];
                    (color.selectedSizes || []).slice().forEach(function(size) {
                        const currentStock = liveStock[size] !== undefined ? liveStock[size] : 0;
                        color.sizes[size] = currentStock;
                        if (currentStock <= 0) {
                            disableSize(colorId, size);
                            removedAny = true;
                        }
                    });
                },
                complete: function() {
                    pending--;
                    if (pending === 0) {
                        callback(!removedAny);
                    }
                }
            });
        });
    }
});
</script>
@endsection