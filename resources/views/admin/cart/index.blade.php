@extends('admin.layouts.app')

@section('title', 'Cart')

@section('content')

<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0">Your Cart</h1>
    </div>
</div>

<section class="content">
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            @if($items->isEmpty())
                <div class="alert alert-info">Your cart is empty.</div>
            @else
                <div class="card">
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Color</th>
                                    <th>Size</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $it)
                                <tr data-entry="{{ $it['entry_id'] }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($it['image'])
                                                <img src="{{ $it['image'] }}" alt="" style="width:60px;height:60px;object-fit:cover;margin-right:12px;">
                                            @endif
                                            <div>{{ $it['name'] }}</div>
                                        </div>
                                    </td>
                                    <td class="">
                                        @php
                                            $colorNames = $it['color_name'] ?? null;
                                            if (!$colorNames && isset($it['color'])) {
                                                $colorNames = is_array($it['color']) ? implode(', ', $it['color']) : $it['color'];
                                            }
                                        @endphp
                                        {{ $colorNames ?? '-' }}
                                    </td>
                                    <td class="">
                                        @php
                                            $sizeNames = $it['size_name'] ?? null;
                                            if (!$sizeNames && isset($it['size'])) {
                                                $sizeNames = is_array($it['size']) ? implode(', ', $it['size']) : $it['size'];
                                            }
                                            if (!$sizeNames && isset($it['sizes'])) {
                                                $sizeNames = is_array($it['sizes']) ? implode(', ', $it['sizes']) : $it['sizes'];
                                            }
                                        @endphp
                                        {{ $sizeNames ?? '-' }}
                                    </td>
                                    <td class="">₹ {{ number_format($it['price'],2) }}</td>
                                    <td class="text-center">
                                        <div class="input-group input-group-sm justify-content-center" style="width:120px;">
                                            <div class="input-group-prepend">
                                                <button class="btn btn-outline-secondary btn-decrease" data-entry="{{ $it['entry_id'] }}">-</button>
                                            </div>
                                            <input type="text" class="form-control text-center qty-input" value="{{ $it['qty'] }}" data-entry="{{ $it['entry_id'] }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary btn-increase" data-entry="{{ $it['entry_id'] }}">+</button>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="">₹ {{ number_format($it['price'] * $it['qty'],2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end align-items-center">
                            <div class="mr-3">Subtotal:</div>
                            <div class="h5">₹ {{ number_format($subtotal,2) }}</div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify">
                        <a href="{{ route('catalog') }}" class="btn-secondary mr-2">Continue shopping</a>
                        <a href="{{ route('orders.create', ['from_cart' => 1]) }}" class="btn-create">Proceed to Create Order</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
</section>

<script>
document.addEventListener('click', function(e){
    if(e.target.matches('.btn-increase')){
        const id = e.target.dataset.entry;
        const input = document.querySelector('.qty-input[data-entry="'+id+'"]');
        const qty = parseInt(input.value) || 0;
        updateQty(id, qty + 1);
    }
    if(e.target.matches('.btn-decrease')){
        const id = e.target.dataset.entry;
        const input = document.querySelector('.qty-input[data-entry="'+id+'"]');
        const qty = parseInt(input.value) || 0;
        updateQty(id, Math.max(0, qty - 1));
    }
});

function updateQty(itemId, qty){
    const tpl = "{{ route('cart.update', ['cart' => '__ID__']) }}";
    const url = tpl.replace('__ID__', itemId);

    fetch(url, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ qty: qty })
    }).then(r=>r.json()).then(data=>{
        if(data.success){
            location.reload();
        } else {
            alert(data.message || 'Failed to update cart');
        }
    });
}

function removeItem(itemId){
    const tpl = "{{ route('cart.destroy', ['cart' => '__ID__']) }}";
    const url = tpl.replace('__ID__', itemId);
    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    }).then(r=>r.json()).then(data=>{
        if(data.success){ location.reload(); } else { alert('Failed to remove item'); }
    });
}
</script>

@endsection
