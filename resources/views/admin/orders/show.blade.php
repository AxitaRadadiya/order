@extends('admin.layouts.app')
@section('title','Order #'.$order->id)

@section('content')
@php
  $statusKey = strtolower(str_replace(' ', '-', $order->status ?? 'default'));
  $statusClass = in_array($statusKey, ['pending','confirmed','shipped','partial_dispatch','cancelled'])
    ? 'order-status-' . $statusKey : 'order-status-default';
  $statusIcons = [
    'pending'          => 'fa-clock',
    'confirmed'        => 'fa-check-circle',
    'shipped'          => 'fa-truck',
    'partial_dispatch' => 'fa-truck-loading',
    'cancelled'        => 'fa-times-circle',
  ];
  $statusIcon = $statusIcons[$statusKey] ?? 'fa-circle';

  $subtotal = (float) ($order->subtotal ?: $order->items->sum('total'));
  $markdownPercent = (float) ($order->markdown ?? 0);
  $markdownAmount = round($subtotal * $markdownPercent / 100, 2);
  $afterMarkdown = $subtotal - $markdownAmount;
  $discountPercent = (float) ($order->discount ?? 0);
  $discountAmount = round($afterMarkdown * $discountPercent / 100, 2);
  $adjustment = (float) ($order->adjustment ?? 0);
  
  // Calculate tax
  $taxPercent = (float) ($order->tax_percentage ?? 0);
  $afterDiscount = $afterMarkdown - $discountAmount;
  $taxAmount = round($afterDiscount * $taxPercent / 100, 2);
  $taxName = 'No Tax';
  if ($order->tax_id && $order->tax) {
      $taxName = $order->tax->tax_name;
  }
@endphp

<div class="order-detail-container">
    <div class="content-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="d-flex align-items-center flex-wrap">
                        <span class="text-muted font-weight-medium mr-2" style="font-size: 0.9rem;">Dashboard</span>
                        <i class="fas fa-chevron-right text-muted mx-2" style="font-size: 0.75rem;"></i>
                        <a href="{{ route('orders.index') }}" class="text-theme font-weight-bold mx-2" style="font-size: 0.9rem;">Orders</a>
                        <i class="fas fa-chevron-right text-muted mx-2" style="font-size: 0.75rem;"></i>
                        <span class="text-muted font-weight-medium mx-2" style="font-size: 0.9rem;">Order #{{ $order->id }}</span>
                    </div>
                </div>
                <div class="col-md-6 text-md-right">
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-custom mr-2">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                    @if(auth()->check() && auth()->user()->hasPermission('order-edit'))
                    <a href="{{ route('orders.edit', $order) }}" class="btn btn-create">
                        <i class="fas fa-edit mr-1"></i> Edit Order
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <section class="content pb-4">
        <div class="container-fluid">

            {{-- Order Information --}}
            <div class="order-card">
                <div class="order-card-header">
                    <i class="fas fa-file-invoice"></i>
                    <h5>Order Information</h5>
                </div>
                <div class="order-card-body flush">
                    <div class="order-info-grid">
                        <div class="order-info-cell">
                            <div class="order-info-label">Order ID</div>
                            <div class="order-info-value">#{{ $order->id }}</div>
                        </div>
                        <div class="order-info-cell">
                            <div class="order-info-label">Customer</div>
                            <div class="order-info-value">{{ $order->customer?->name ?? '—' }}</div>
                        </div>
                        <div class="order-info-cell">
                            <div class="order-info-label">Order Date</div>
                            <div class="order-info-value">{{ $order->date?->format('d M Y') ?? '—' }}</div>
                        </div>
                        <div class="order-info-cell">
                            <div class="order-info-label">Expected Delivery</div>
                            <div class="order-info-value">{{ $order->expected_date?->format('d M Y') ?? '—' }}</div>
                        </div>
                        <div class="order-info-cell">
                            <div class="order-info-label">E-way Bill No.</div>
                            <div class="order-info-value">{{ $order->eway_bill_number ?: '—' }}</div>
                        </div>
                        <div class="order-info-cell">
                            <div class="order-info-label">Transport No.</div>
                            <div class="order-info-value">{{ $order->transport_number ?: '—' }}</div>
                        </div>
                        <div class="order-info-cell">
                            <div class="order-info-label">LR Number</div>
                            <div class="order-info-value">{{ $order->lr_number ?: '—' }}</div>
                        </div>
                        <div class="order-info-cell">
                            <div class="order-info-label">Status</div>
                            <div class="order-info-value">
                                <span class="order-status-badge {{ $statusClass }}">
                                    <i class="fas {{ $statusIcon }}" style="font-size:10px;"></i>
                                    {{ ucfirst($order->status ?? '—') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Addresses --}}
            <div class="order-card">
                <div class="order-card-header">
                    <i class="fas fa-map-marker-alt"></i>
                    <h5>Addresses</h5>
                </div>
                <div class="order-card-body">
                    <div class="order-addr-grid">
                        <div class="order-addr-box">
                            <div class="order-addr-label">
                                <i class="fas fa-file-invoice" style="font-size:12px;"></i> Billing Address
                            </div>
                            <div class="order-addr-value">{{ $order->billing_address ?: '—' }}</div>
                        </div>
                        <div class="order-addr-box">
                            <div class="order-addr-label">
                                <i class="fas fa-truck" style="font-size:12px;"></i> Shipping Address
                            </div>
                            <div class="order-addr-value">{{ $order->shipping_address ?: '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Items --}}
            <div class="order-card">
                <div class="order-card-header">
                    <i class="fas fa-box"></i>
                    <h5>Items</h5>
                </div>
                <div class="order-card-body flush">
                    <div class="table-responsive">
                        <table class="order-items-table">
                            <thead>
                                <tr>
                                    <th>Article No.</th>
                                    <th>Item</th>
                                    <th>Color</th>
                                    <th>Size</th>
                                    <th>Description</th>
                                    <th>Qty</th>
                                    <th>MRP</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $it)
                                    @php
                                        $sizeText = $it->size;
                                        if (!empty($it->size_quantities)) {
                                            $sizeText = collect($it->size_quantities)
                                                ->map(fn($qty, $size) => $size . ': ' . (int) round((float) $qty))
                                                ->implode(', ');
                                        }
                                        $colorText = collect(explode(',', (string) $it->color))
                                            ->map(fn($c) => trim($c))
                                            ->filter()
                                            ->map(fn($c) => $colorsById[$c] ?? $c)
                                            ->implode(', ');

                                        $itStatusKey = strtolower($it->status ?? 'default');
                                        $itStatusClass = in_array($itStatusKey, ['pending','confirmed','shipped','partial_dispatch','cancelled'])
                                            ? 'order-status-' . $itStatusKey : 'order-status-default';
                                    @endphp
                                    <tr>
                                        <td><span class="order-chip-article">{{ $it->article_number }}</span></td>
                                        <td class="font-weight-bold">{{ $it->item_name }}</td>
                                        <td><span class="order-chip-color">{{ $colorText ?: '—' }}</span></td>
                                        <td>{{ $sizeText ?: '—' }}</td>
                                        <td class="text-muted" style="font-size:0.8rem;">{{ $it->description ?: '—' }}</td>
                                        <td class="font-weight-bold">{{ number_format((float) $it->quantity, 0) }}</td>
                                        <td>₹{{ number_format($it->rate, 2) }}</td>
                                        <td class="font-weight-bold">₹{{ number_format($it->total, 2) }}</td>
                                        <td><span class="order-status-badge {{ $itStatusClass }}">{{ ucfirst($it->status ?? '—') }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="order-card-body" style="border-top: 1px solid #f1f5f9;">
                        <div class="order-footer-layout">
                            <div class="order-footer-grid">
                                <div>
                                    <div class="order-footer-label">Terms &amp; Conditions</div>
                                    <div class="order-footer-value {{ empty($order->terms) ? 'empty' : '' }}">
                                        {{ $order->terms ?: 'No terms specified' }}
                                    </div>
                                </div>
                                <div>
                                    <div class="order-footer-label">Notes</div>
                                    <div class="order-footer-value {{ empty($order->notes) ? 'empty' : '' }}">
                                        {{ $order->notes ?: 'No notes added' }}
                                    </div>
                                </div>
                            </div>

                            <div class="order-totals-summary">
                                <div class="order-totals-row subtotal">
                                    <span class="label">Sub Total</span>
                                    <span class="value">₹{{ number_format($subtotal, 2) }}</span>
                                </div>
                                <div class="order-totals-row {{ $markdownAmount > 0 ? 'deduction' : '' }}">
                                    <span class="label">Mark Down ({{ rtrim(rtrim(number_format($markdownPercent, 2), '0'), '.') }}%)</span>
                                    <span class="value">{{ $markdownAmount > 0 ? '− ' : '' }}₹{{ number_format($markdownAmount, 2) }}</span>
                                </div>
                                <div class="order-totals-row {{ $discountAmount > 0 ? 'deduction' : '' }}">
                                    <span class="label">Discount ({{ rtrim(rtrim(number_format($discountPercent, 2), '0'), '.') }}%)</span>
                                    <span class="value">{{ $discountAmount > 0 ? '− ' : '' }}₹{{ number_format($discountAmount, 2) }}</span>
                                </div>
                                <!-- Tax Row - NEW -->
                                <div class="order-totals-row tax {{ $taxAmount > 0 ? 'addition' : '' }}">
                                    <span class="label">
                                        Tax 
                                        @if($taxPercent > 0)
                                            ({{ rtrim(rtrim(number_format($taxPercent, 2), '0'), '.') }}%)
                                        @endif
                                        @if($taxName != 'No Tax')
                                            <span class="text-muted" style="font-weight:400; font-size:11px;">{{ $taxName }}</span>
                                        @endif
                                    </span>
                                    <span class="value">
                                        @if($taxAmount > 0)+ @endif
                                        ₹{{ number_format($taxAmount, 2) }}
                                    </span>
                                </div>
                                <div class="order-totals-row {{ $adjustment > 0 ? 'addition' : ($adjustment < 0 ? 'deduction' : '') }}">
                                    <span class="label">Adjustment</span>
                                    <span class="value">
                                        @if($adjustment > 0)+ @elseif($adjustment < 0)− @endif
                                        ₹{{ number_format(abs($adjustment), 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="order-grand-total-wrap">
                            <div class="order-grand-total-box">
                                <span class="order-grand-total-label">Grand Total</span>
                                <span class="order-grand-total-amount">₹{{ number_format($order->grand_total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection
