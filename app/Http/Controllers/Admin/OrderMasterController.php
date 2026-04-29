<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\OrderMaster;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Item;
use App\Models\Size;
use App\Models\Color;
use Yajra\DataTables\Facades\DataTables;

class OrderMasterController extends Controller
{
    // -----------------------------------------------------------------------
    // Index  (renders page shell; DataTables loads rows via orderList())
    // -----------------------------------------------------------------------
    public function index()
    {
        return view('admin.orders.index');
    }

    // -----------------------------------------------------------------------
    // DataTables AJAX endpoint   GET /orders-list
    // Route name: orders.list   (matches web.php + script.blade.php)
    //
    // Column keys MUST match script.blade.php load_order() columns:
    //   id | order_number | name | date | total | status | action
    // -----------------------------------------------------------------------
    public function orderList(Request $request)
    {
        $orders = OrderMaster::query()
            ->with('customer')
            ->select('order_masters.*');

        return DataTables::of($orders)
            ->addColumn('name', function (OrderMaster $order) {
                return $order->customer->name ?? $order->customer->email ?? '-';
            })
            ->addColumn('order_number', function (OrderMaster $order) {
                return $order->order_number
                    ?? 'ORD-' . str_pad($order->id, 5, '0', STR_PAD_LEFT);
            })
            ->addColumn('total', function (OrderMaster $order) {
                return '&#8377; ' . number_format($order->grand_total ?? 0, 2);
            })
            ->addColumn('status', function (OrderMaster $order) {
                $map = [
                    'pending'   => 'warning',
                    'confirmed' => 'info',
                    'shipped'   => 'primary',
                    'delivered' => 'success',
                ];
                $color = $map[$order->status] ?? 'secondary';
                return '<span class="badge badge-' . $color . '">'
                    . ucfirst($order->status ?? '-') . '</span>';
            })
            ->addColumn('action', function (OrderMaster $order) {
                $show   = route('orders.show',    $order);
                $edit   = route('orders.edit',    $order);
                $delete = route('orders.destroy', $order);
                $csrf   = csrf_field();
                $method = method_field('DELETE');

                return '
                    <a href="' . $show . '" class="btn btn-xs btn-info" title="View">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="' . $edit . '" class="btn btn-xs btn-primary" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form method="POST" action="' . $delete . '" style="display:inline">
                        ' . $csrf . $method . '
                        <button type="button" class="btn btn-xs btn-danger deleteButton" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                ';
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->whereHas('customer', function ($q) use ($keyword) {
                    $q->where('name',  'like', "%{$keyword}%")
                      ->orWhere('email', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['total', 'status', 'action'])
            ->make(true);
    }

    // -----------------------------------------------------------------------
    // Customer address lookup   GET /customers/{user}/addresses
    // Route name: customers.addresses
    // Returns billing + shipping JSON for the create/edit JS address autofill
    // -----------------------------------------------------------------------
    public function customerAddresses(User $user)
    {
        $addr     = $user->address ?? null;
        $billing  = $addr->billing_address  ?? $addr->address ?? $user->billing_address  ?? $user->address ?? '';
        $shipping = $addr->shipping_address ?? $addr->address ?? $user->shipping_address ?? $billing;

        return response()->json([
            'billing'  => $billing,
            'shipping' => $shipping,
        ]);
    }

    // -----------------------------------------------------------------------
    // Create / Store
    // -----------------------------------------------------------------------
    public function create()
    {
        return view('admin.orders.create', $this->viewData());
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date'    => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $order = OrderMaster::create($this->orderFields($request));
            $this->syncItems($order, $request->input('items', []));
            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()])
                ->with($this->viewData());
        }
    }

    // -----------------------------------------------------------------------
    // Show
    // -----------------------------------------------------------------------
    public function show(OrderMaster $order)
    {
        $order->load('items', 'customer');

        return view('admin.orders.show', compact('order'));
    }

    // -----------------------------------------------------------------------
    // Edit / Update
    // -----------------------------------------------------------------------
    public function edit(OrderMaster $order)
    {
        $order->load('items');

        return view('admin.orders.edit', array_merge(
            $this->viewData(),
            compact('order')
        ));
    }

    public function update(Request $request, OrderMaster $order)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date'    => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $order->update($this->orderFields($request));
            $order->items()->delete();
            $this->syncItems($order, $request->input('items', []));
            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Order updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            $order->load('items');

            return back()
                ->withInput()
                ->with('error', $e->getMessage())
                ->with(array_merge($this->viewData(), compact('order')));
        }
    }

    // -----------------------------------------------------------------------
    // Destroy
    // -----------------------------------------------------------------------
    public function destroy(OrderMaster $order)
    {
        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Order deleted.');
    }

    // -----------------------------------------------------------------------
    // Private helpers
    // -----------------------------------------------------------------------

    private function viewData(): array
    {
        $customers = User::with('address')->orderBy('name')->get();
        $items     = Item::orderBy('name')->get();

        // Address lookup map injected into JS: { "1": {billing, shipping}, ... }
        $customersJson = $customers->mapWithKeys(function (User $u) {
            $addr     = $u->address ?? null;
            $billing  = $addr->billing_address  ?? $addr->address ?? $u->billing_address  ?? $u->address ?? '';
            $shipping = $addr->shipping_address ?? $addr->address ?? $u->shipping_address ?? $billing;

            return [(string) $u->id => [
                'billing'  => $billing,
                'shipping' => $shipping,
            ]];
        });

        return [
            'customers'     => $customers,
            'customersJson' => $customersJson,
            'items'         => $items,
            'itemsJson'     => $this->buildItemsJson($items),
            'sizesJson'     => Size::activeLabels(),
            'colors'        => Color::orderBy('name')->get(),
        ];
    }

    private function orderFields(Request $request): array
    {
        return $request->only([
            'user_id',
            'date',
            'expected_date',
            'eway_bill_number',
            'transport_number',
            'lr_number',
            'billing_address',
            'shipping_address',
            'subtotal',
            'discount',
            'adjustment',
            'grand_total',
            'terms',
            'notes',
            'status',
        ]);
    }

    /**
     * JS keys used: id, article_number, name, desc, rate, tax, sizes, color_id, color
     */
    private function buildItemsJson($items): \Illuminate\Support\Collection
    {
        return $items->map(function (Item $item) {
            $sizes = [];
            if (!empty($item->sizes)) {
                $sizes = is_array($item->sizes)
                    ? $item->sizes
                    : array_map('trim', explode(',', $item->sizes));
            }

            return [
                'id'             => $item->id,
                'article_number' => $item->article_number ?? $item->sku ?? '',
                'name'           => $item->name,
                'unit'           => $item->unit ?? '',
                'rate'           => $item->price ?? 0,
                'tax'            => $item->tax_percent ?? 0,
                'desc'           => $item->description ?? '',
                'color_id'       => optional($item->color)->id,
                'color'          => optional($item->color)->name,
                'sizes'          => $sizes,
            ];
        })->values();
    }

    private function syncItems(OrderMaster $order, array $items): void
    {
        foreach ($items as $it) {
            $itemId   = $it['item_id']   ?? null;
            $itemName = $it['item_name'] ?? null;

            if ($itemId && empty($itemName)) {
                $itemName = Item::find($itemId)?->name;
            }

            if (empty($itemId) && empty($itemName) && empty($it['description'])) {
                continue;
            }

            $sizeVal = null;
            if (!empty($it['sizes'])) {
                $sizeVal = is_array($it['sizes'])
                    ? implode(',', $it['sizes'])
                    : $it['sizes'];
            } elseif (!empty($it['size_from']) || !empty($it['size_to'])) {
                $sizeVal = trim(($it['size_from'] ?? '') . '-' . ($it['size_to'] ?? ''), '-');
            }

            $order->items()->create([
                'item_id'     => $itemId  ?: null,
                'item_name'   => $itemName,
                'description' => $it['description'] ?? null,
                'color'       => $it['color']       ?? $it['color_id'] ?? null,
                'size'        => $sizeVal,
                'quantity'    => $it['quantity']    ?? 0,
                'rate'        => $it['rate']        ?? 0,
                'tax_rate'    => $it['tax_rate']    ?? 0,
                'total'       => $it['total']       ?? 0,
                'size_from'   => $it['size_from']   ?? null,
                'size_to'     => $it['size_to']     ?? null,
                'sets'        => $it['sets']        ?? null,
            ]);
        }
    }
}