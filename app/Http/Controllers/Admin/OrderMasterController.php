<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\OrderMaster;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Size;

class OrderMasterController extends Controller
{
    public function index()
    {
        $orders = OrderMaster::with('customer')->latest()->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }

    public function create()
    {
        $customers = Customer::with('address')->orderBy('name')->get();
        $items     = Item::orderBy('name')->get();
        $itemsJson = $this->buildItemsJson($items);
        $sizesJson = Size::activeLabels(); // dynamic from DB

        return view('admin.orders.create', compact('customers', 'items', 'itemsJson', 'sizesJson'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date'        => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $data  = $request->only([
                'customer_id', 'date', 'expected_date',
                'eway_bill_number', 'transport_number', 'lr_number',
                'billing_address', 'shipping_address',
                'subtotal', 'discount', 'adjustment', 'grand_total',
                'terms', 'notes', 'status',
            ]);
            $order = OrderMaster::create($data);

            $this->syncItems($order, $request->input('items', []));

            DB::commit();
            return redirect()->route('orders.index')->with('success', 'Order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(OrderMaster $order)
    {
        $order->load('items', 'customer');
        return view('admin.orders.show', compact('order'));
    }

    public function edit(OrderMaster $order)
    {
        $customers = Customer::with('address')->orderBy('name')->get();
        $items     = Item::orderBy('name')->get();
        $itemsJson = $this->buildItemsJson($items);
        $sizesJson = Size::activeLabels(); // dynamic from DB
        $order->load('items');

        return view('admin.orders.edit', compact('order', 'customers', 'items', 'itemsJson', 'sizesJson'));
    }

    public function update(Request $request, OrderMaster $order)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date'        => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->only([
                'customer_id', 'date', 'expected_date',
                'eway_bill_number', 'transport_number', 'lr_number',
                'billing_address', 'shipping_address',
                'subtotal', 'discount', 'adjustment', 'grand_total',
                'terms', 'notes', 'status',
            ]);
            $order->update($data);

            $order->items()->delete();
            $this->syncItems($order, $request->input('items', []));

            DB::commit();
            return redirect()->route('orders.index')->with('success', 'Order updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(OrderMaster $order)
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function buildItemsJson($items): \Illuminate\Support\Collection
    {
        return $items->map(function ($i) {
            return [
                'id'   => $i->id,
                'name' => $i->name,
                'unit' => $i->unit ?? '',
                'rate' => $i->price,
                'tax'  => $i->tax_percent ?? 0,
                'desc' => $i->description ?? '',
            ];
        })->values();
    }

    private function syncItems(OrderMaster $order, array $items): void
    {
        foreach ($items as $it) {
            $itemId   = $it['item_id']   ?? null;
            $itemName = $it['item_name'] ?? null;

            if ($itemId && empty($itemName)) {
                $master   = Item::find($itemId);
                $itemName = $master?->name;
            }

            if (empty($itemId) && empty($itemName) && empty($it['description'])) {
                continue;
            }

            $order->items()->create([
                'item_id'     => $itemId ?: null,
                'item_name'   => $itemName,
                'description' => $it['description'] ?? null,
                'unit'        => $it['unit']        ?? null,
                'quantity'    => $it['quantity']    ?? 0,
                'rate'        => $it['rate']        ?? 0,
                'tax_rate'    => $it['tax_rate']    ?? 0,
                'final_price' => $it['final_price'] ?? 0,
                'total'       => $it['total']       ?? 0,
                'size_from'   => $it['size_from']   ?? null,
                'size_to'     => $it['size_to']     ?? null,
                'sets'        => $it['sets']        ?? null,
            ]);
        }
    }
}