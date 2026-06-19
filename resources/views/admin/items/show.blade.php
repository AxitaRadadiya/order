@extends('admin.layouts.app')
@section('title', 'Item Details')

@section('content')
@php
    $images = [];
    if (!empty($item->images) && is_array($item->images)) {
        $images = $item->images;
    } elseif (!empty($item->images) && is_string($item->images)) {
        $decoded = json_decode($item->images, true);
        if (is_array($decoded)) {
            $images = $decoded;
        }
    } elseif (!empty($item->image)) {
        $images = [$item->image];
    }
    $images = array_values(array_filter($images, fn ($img) => $img && file_exists(public_path('storage/' . $img))));

    $colorMap = [
        'black' => '#1f2937', 'white' => '#f9fafb', 'red' => '#ef4444',
        'blue' => '#3b82f6', 'green' => '#22c55e', 'yellow' => '#eab308',
        'orange' => '#f97316', 'purple' => '#7c3aed', 'pink' => '#ec4899',
        'brown' => '#92400e', 'grey' => '#9ca3af', 'gray' => '#9ca3af',
        'silver' => '#d1d5db', 'navy' => '#1e3a5f', 'beige' => '#d4c4a8',
        'maroon' => '#7f1d1d', 'teal' => '#14b8a6', 'cream' => '#fef3c7',
    ];

    $resolveColor = function ($color) use ($colorMap) {
        if (!$color) {
            return '#d1d5db';
        }
        $code = strtolower(trim($color->color_code ?? ''));
        if (preg_match('/^#?[0-9a-f]{3,8}$/i', $code)) {
            return str_starts_with($code, '#') ? $code : '#' . $code;
        }
        $name = strtolower(trim($color->name ?? ''));
        return $colorMap[$name] ?? ($colorMap[$code] ?? '#d1d5db');
    };

    $taxDisplay = $item->tax_percent
        ? rtrim(rtrim(number_format((float) $item->tax_percent, 2), '0'), '.') . '%'
        : ($item->tax ? rtrim(rtrim(number_format((float) $item->tax->tax_percentage, 2), '0'), '.') . '%' : '-');
@endphp

<div class="item-detail-container">
    <div class="content-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                </div>
                <div class="col-md-6 text-md-right">
                    <a href="{{ route('items.index') }}" class="btn btn-outline-custom mr-2">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                    @if(auth()->check() && auth()->user()->hasPermission('item-edit'))
                        <a href="{{ route('items.edit', $item->id) }}" class="btn btn-create">
                            <i class="fas fa-edit mr-1"></i> Edit Item
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <section class="content pb-4">
        <div class="container-fluid">
            <div class="item-detail-card">
                <div class="row item-detail-layout">
                    {{-- Left: Image Gallery --}}
                    <div class="col-lg-5 col-md-6 mb-4 mb-md-0 item-detail-gallery-col">
                        @if(!empty($images))
                            <div class="item-gallery-main">
                                <img id="mainProductImage"
                                     src="{{ asset('storage/' . $images[0]) }}"
                                     alt="{{ $item->name }}">
                                <button type="button" class="item-gallery-expand" id="expandImageBtn" title="View full image">
                                    <i class="fas fa-expand-alt"></i>
                                </button>
                            </div>

                            @if(count($images) > 1)
                                <div class="item-thumbs-wrap">
                                    <button type="button" class="item-thumb-nav" id="thumbPrev" disabled>
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                    <div class="item-thumbs-viewport">
                                    <div class="item-thumbs-track" id="thumbsTrack">
                                        @foreach($images as $idx => $img)
                                            <div class="item-thumb {{ $idx === 0 ? 'active' : '' }}"
                                                 data-src="{{ asset('storage/' . $img) }}"
                                                 data-index="{{ $idx }}">
                                                <img src="{{ asset('storage/' . $img) }}" alt="Thumb {{ $idx + 1 }}">
                                            </div>
                                        @endforeach
                                    </div>
                                    </div>
                                    <button type="button" class="item-thumb-nav" id="thumbNext">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            @endif
                        @else
                            <div class="item-gallery-main">
                                <div class="item-gallery-no-image">
                                    <i class="fas fa-image"></i>
                                    <span>No image uploaded</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Right: Product Info --}}
                    <div class="col-lg-7 col-md-6 item-detail-info-col">
                        <div class="item-info-header">
                            <div class="item-codes">
                                <div class="item-code-block">
                                    <div class="label">Item Code</div>
                                    <div class="value">{{ $item->item_code ?? '-' }}</div>
                                </div>
                                <div class="item-code-block">
                                    <div class="label">Article Number</div>
                                    <div class="value">{{ $item->article_number ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="item-price-display">
                                <div class="price-label">MRP</div>
                                <div class="price-value">₹{{ number_format($item->price, 2) }}</div>
                            </div>
                        </div>

                        <h1 class="item-title">{{ $item->name }}</h1>

                        @if($item->description)
                            <p class="item-description">{{ $item->description }}</p>
                        @endif

                        <div class="item-info-grid">
                            <div class="item-info-card">
                                <div class="item-info-card-icon"><i class="fas fa-layer-group"></i></div>
                                <div>
                                    <div class="item-info-card-label">Group</div>
                                    <div class="item-info-card-value">{{ $item->group->name ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="item-info-card">
                                <div class="item-info-card-icon"><i class="fas fa-object-group"></i></div>
                                <div>
                                    <div class="item-info-card-label">Sub-Group</div>
                                    <div class="item-info-card-value">{{ $item->subGroup->name ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="item-info-card">
                                <div class="item-info-card-icon"><i class="fas fa-tags"></i></div>
                                <div>
                                    <div class="item-info-card-label">Category</div>
                                    <div class="item-info-card-value">{{ $item->category->name ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="item-info-card">
                                <div class="item-info-card-icon"><i class="fas fa-tag"></i></div>
                                <div>
                                    <div class="item-info-card-label">Sub-Category</div>
                                    <div class="item-info-card-value">{{ $item->subCategory->name ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="item-info-card">
                                <div class="item-info-card-icon"><i class="fas fa-percent"></i></div>
                                <div>
                                    <div class="item-info-card-label">Tax</div>
                                    <div class="item-info-card-value">{{ $taxDisplay }}</div>
                                </div>
                            </div>
                            <div class="item-info-card">
                                <div class="item-info-card-icon"><i class="fas fa-toggle-on"></i></div>
                                <div>
                                    <div class="item-info-card-label">Status</div>
                                    <div class="item-info-card-value">{{ $item->status ? 'Active' : 'Inactive' }}</div>
                                </div>
                            </div>
                            <div class="item-info-card">
                                <div class="item-info-card-icon"><i class="fas fa-globe"></i></div>
                                <div>
                                    <div class="item-info-card-label">Web Visibility</div>
                                    <div class="item-info-card-value">{{ $item->show_item_on_web ? 'Visible' : 'Hidden' }}</div>
                                </div>
                            </div>
                        </div>

                        @if($item->video_link)
                            <div class="item-video-card">
                                <div class="item-info-card-icon"><i class="fas fa-video"></i></div>
                                <div>
                                    <div class="item-info-card-label">Video Link</div>
                                    <a href="{{ $item->video_link }}" target="_blank" rel="noopener noreferrer">{{ $item->video_link }}</a>
                                </div>
                            </div>
                        @endif

                        @if($item->variants->count())
                            <div class="item-variants-section" id="variantsSection">
                                <h2 class="item-variants-title">Item Variants</h2>
                                {{-- Search + per page controls --}}
                                <div class="item-variants-dt-top mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <label class="mb-0" style="font-size:0.85rem; color:#6b7280; font-weight:500;">Show</label>
                                        <select id="variantsPerPage" style="border:1px solid #e9e6ff; border-radius:8px; padding:6px 10px; font-size:0.85rem; color:#374151; background:#fff; min-width:70px;">
                                            <option value="5" selected>5</option>
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                        </select>
                                        <label class="mb-0" style="font-size:0.85rem; color:#6b7280; font-weight:500;">entries</label>
                                    </div>
                                    <div class="input-group input-group-sm" style="max-width:240px;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                                        </div>
                                        <input type="text" id="variantsSearchInput" class="form-control border-left-0" placeholder="Search color, size...">
                                    </div>
                                </div>

                                {{-- Variants table wrapper (AJAX target) --}}
                                <div id="variantsTableWrapper">
                                <div class="table-responsive">
                                    <table id="itemVariantsTable" class="item-variants-table table table-borderless w-100">
                                        <thead>
                                            <tr>
                                                <th>Color</th>
                                                <th>Color Code</th>
                                                <th>Size</th>
                                                <th>Production</th>
                                                <th>Sold</th>
                                                <th>Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($variants as $variant)
                                                @php
                                                    $production = $variant->total_production;
                                                    $sold = $variant->total_sold;
                                                    $stock = $variant->current_stock;
                                                    if ($stock == 0) {
                                                        $qtyClass = 'out';
                                                    } elseif ($stock <= 10) {
                                                        $qtyClass = 'low';
                                                    } else {
                                                        $qtyClass = '';
                                                    }
                                                    $dotColor = $resolveColor($variant->color);
                                                @endphp
                                                <tr data-production="{{ $production }}"
                                                    data-sold="{{ $sold }}"
                                                    data-stock="{{ $stock }}">
                                                    <td>
                                                        <div class="item-color-cell">
                                                            <span class="item-color-dot" style="background-color: {{ $dotColor }};"></span>
                                                            {{ $variant->color->name ?? '-' }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="item-color-code-chip">{{ $variant->color->color_code ?? '-' }}</span>
                                                    </td>
                                                    <td>{{ $variant->size->name ?? '-' }}</td>
                                                    <td data-order="{{ $production }}">{{ number_format($production) }}</td>
                                                    <td data-order="{{ $sold }}">{{ number_format($sold) }}</td>
                                                    <td data-order="{{ $stock }}">
                                                        <span class="item-qty-badge {{ $qtyClass }}">{{ number_format($stock) }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3">Totals</td>
                                                <td>{{ number_format($totalProduction) }}</td>
                                                <td>{{ number_format($totalSold) }}</td>
                                                <td>{{ number_format($totalStock) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                             </div>{{-- end table-responsive --}}
                                @if($variants->hasPages())
                                <div class="item-variants-dt-bottom mt-2">
                                    <small class="text-muted">
                                        Showing {{ $variants->firstItem() }}–{{ $variants->lastItem() }} of {{ $variants->total() }} variants
                                    </small>
                                    <div class="item-variants-pagination pagination">
                                        {{ $variants->appends(request()->query())->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                                @endif
                                </div>{{-- end variantsTableWrapper --}}
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('pageScript')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Gallery ───────────────────────────────────────────────────────
    const mainImage = document.getElementById('mainProductImage');
    const expandBtn = document.getElementById('expandImageBtn');
    const thumbs    = document.querySelectorAll('.item-thumb');
    const track     = document.getElementById('thumbsTrack');
    const prevBtn   = document.getElementById('thumbPrev');
    const nextBtn   = document.getElementById('thumbNext');

    thumbs.forEach(thumb => {
        thumb.addEventListener('click', function () {
            thumbs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const newSrc = this.getAttribute('data-src');
            if (mainImage && newSrc) mainImage.src = newSrc;
        });
    });

    if (expandBtn && mainImage) {
        expandBtn.addEventListener('click', function () {
            window.open(mainImage.src, '_blank');
        });
    }

    if (track && prevBtn && nextBtn) {
        const scrollAmount = 100;
        const updateButtons = () => {
            prevBtn.disabled = track.scrollLeft <= 0;
            nextBtn.disabled = track.scrollLeft >= (track.scrollWidth - track.clientWidth) - 1;
        };
        track.addEventListener('scroll', updateButtons);
        updateButtons();
        prevBtn.addEventListener('click', () => track.scrollBy({ left: -scrollAmount, behavior: 'smooth' }));
        nextBtn.addEventListener('click', () => track.scrollBy({ left: scrollAmount, behavior: 'smooth' }));
    }

    // ── Variants AJAX ─────────────────────────────────────────────────
    const baseUrl       = window.location.pathname;
    let searchTimer     = null;
    let currentPage     = 1;
    let currentSearch   = '';
    let currentPerPage  = 5;

    function loadVariants(page, q, perPage) {
        $.ajax({
            url: baseUrl,
            data: { page: page, q: q, per_page: perPage },
            beforeSend: function () {
                $('#variantsTableWrapper').css('opacity', '0.5');
            },
            success: function (html) {
                const newWrapper = $(html).find('#variantsTableWrapper').html();
                if (newWrapper) {
                    $('#variantsTableWrapper').html(newWrapper).css('opacity', '1');
                } else {
                    $('#variantsTableWrapper').css('opacity', '1');
                }
                // Re-bind pagination clicks after DOM update
                bindPaginationClicks();
            },
            error: function () {
                $('#variantsTableWrapper').css('opacity', '1');
            }
        });
    }

    function bindPaginationClicks() {
        $('#variantsTableWrapper .pagination a').off('click').on('click', function (e) {
            e.preventDefault();
            const href  = $(this).attr('href') || '';
            const match = href.match(/[?&]page=(\d+)/);
            if (match) {
                currentPage = parseInt(match[1]);
                loadVariants(currentPage, currentSearch, currentPerPage);
            }
        });
    }

    // Initial bind
    bindPaginationClicks();

    // Search
    $('#variantsSearchInput').on('keyup', function () {
        clearTimeout(searchTimer);
        const q = $(this).val();
        searchTimer = setTimeout(function () {
            currentSearch = q;
            currentPage   = 1;
            loadVariants(currentPage, currentSearch, currentPerPage);
        }, 400);
    });

    // Per page
    $('#variantsPerPage').on('change', function () {
        currentPerPage = $(this).val();
        currentPage    = 1;
        loadVariants(currentPage, currentSearch, currentPerPage);
    });

});
</script>
@endsection

