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
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class OrderMasterController extends Controller
{
    // -----------------------------------------------------------------------
    // Index
    // -----------------------------------------------------------------------
    public function index()
    {
        return view('admin.orders.index');
    }

    public function orderList(Request $request)
    {
        $query = OrderMaster::with('customer');

        // If the logged-in user is a retailer, show only their orders.
        // If the logged-in user is a distributor, show orders for themselves
        // and for any retailers assigned to them.
        try {
            $authUser = auth()->user();
            if ($authUser) {
                if ($authUser->hasRole('retailer')) {
                    $query->where('user_id', $authUser->id);
                } elseif ($authUser->hasRole('distributor')) {
                    $retailerIds = User::where('distributor_id', $authUser->id)->pluck('id')->toArray();
                    $ids = array_merge([$authUser->id], $retailerIds);
                    $query->whereIn('user_id', $ids);
                }
            }
        } catch (\Throwable $e) {
            // ignore and show default
        }

        $totalData = $query->count();
        $totalFiltered = $totalData;

        $limit  = (int) $request->input('length', 10);
        $start  = (int) $request->input('start', 0);
        $search = $request->input('search.value');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                ->orWhere('date', 'like', "%{$search}%")
                ->orWhereHas('customer', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });

            $totalFiltered = $query->count();
        }

        $orders = $query->offset($start)
            ->limit($limit)
            ->orderBy('id', 'desc')
            ->get();

        $data = [];

        foreach ($orders as $idx => $order) {

            $viewUrl   = route('orders.show', $order->id);
            $editUrl   = route('orders.edit', $order->id);
            $deleteUrl = route('orders.destroy', $order->id);

            $orderNumber = $order->order_number
                ?? 'ORD-' . str_pad($order->id, 5, '0', STR_PAD_LEFT);

            $customerName = optional($order->customer)->name
                ?? optional($order->customer)->email
                ?? '-';

           
            $total = '₹ ' . number_format((float) ($order->grand_total ?? 0), 2);

            $statusMap = [
                'pending'   => 'warning',
                'confirmed' => 'info',
                'shipped'   => 'primary',
                'delivered' => 'success',
            ];
            $color = $statusMap[$order->status] ?? 'secondary';

            $status = '<span class="badge badge-' . $color . '">'
                . ucfirst($order->status ?? 'pending') . '</span>';

            $actions = '<div class="btn-group">
                <button type="button" class="btn btn-sm" data-toggle="dropdown">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="' . $viewUrl . '">View</a>
                    <a class="dropdown-item" href="' . $editUrl . '">Edit</a>
                    <form method="POST" action="' . $deleteUrl . '">
                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="dropdown-item text-danger deleteButton">Delete</button>
                    </form>
                </div>
            </div>';

            $data[] = [
                'id'            => $start + $idx + 1,
                'order_number'  => $orderNumber,
                'name'          => $customerName,
                'date'          => $order->date ? Carbon::parse($order->date)->format('d/m/y') : '',
                'total'         => $total,
                'status'        => $status,
                'action'        => $actions,
            ];
        }

        return response()->json([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data'            => $data,
        ]);
    }
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
    public function create(Request $request)
    {
        $data = $this->viewData();

        // Prefill item_id if provided in query (from catalog "Add Order" button)
        $data['pre_item_id'] = $request->query('item_id');

        // If a retailer/distributor is creating the order, default customer to themselves
        $preUser = null;
        try {
            $user = auth()->user();
            if ($user && $user->hasRole(['retailer', 'distributor'])) {
                $preUser = $user->id;
            }
        } catch (\Throwable $e) {
            $preUser = null;
        }

        $data['pre_user_id'] = $preUser;

        return view('admin.orders.create', $data);
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

        // Retailers may only view their own orders. Distributors may view
        // their own orders and orders created by retailers assigned to them.
        $user = auth()->user();
        if ($user) {
            if ($user->hasRole('retailer') && $order->user_id !== $user->id) {
                abort(403);
            }
            if ($user->hasRole('distributor')) {
                $retailerIds = User::where('distributor_id', $user->id)->pluck('id')->toArray();
                if ($order->user_id !== $user->id && !in_array($order->user_id, $retailerIds)) {
                    abort(403);
                }
            }
        }

        return view('admin.orders.show', compact('order'));
    }

    // -----------------------------------------------------------------------
    // Edit / Update
    // -----------------------------------------------------------------------
    public function edit(OrderMaster $order)
    {
        $order->load('items');

        $user = auth()->user();
        if ($user) {
            if ($user->hasRole('retailer') && $order->user_id !== $user->id) {
                abort(403);
            }
            if ($user->hasRole('distributor')) {
                $retailerIds = User::where('distributor_id', $user->id)->pluck('id')->toArray();
                if ($order->user_id !== $user->id && !in_array($order->user_id, $retailerIds)) {
                    abort(403);
                }
            }
        }

        return view('admin.orders.edit', array_merge(
            $this->viewData(),
            compact('order')
        ));
    }

    public function update(Request $request, OrderMaster $order)
    {
        $user = auth()->user();
        if ($user) {
            if ($user->hasRole('retailer') && $order->user_id !== $user->id) {
                abort(403);
            }
            if ($user->hasRole('distributor')) {
                $retailerIds = User::where('distributor_id', $user->id)->pluck('id')->toArray();
                if ($order->user_id !== $user->id && !in_array($order->user_id, $retailerIds)) {
                    abort(403);
                }
            }
        }

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
        $user = auth()->user();
        if ($user && $user->hasRole(['retailer', 'distributor']) && $order->user_id !== $user->id) {
            abort(403);
        }

        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Order deleted.');
    }

    // -----------------------------------------------------------------------
    // Private helpers
    // -----------------------------------------------------------------------

    private function viewData(): array
    {
        $customersQuery = User::with('address')
            ->whereHas('role', function ($q) {
                $q->whereIn('name', ['retailer', 'distributor']);
            })->orderBy('name');

        // For retailer users, restrict customer list to themselves.
        // For distributor users, include the distributor and their retailers.
        try {
            $u = auth()->user();
            if ($u) {
                if ($u->hasRole('retailer')) {
                    $customersQuery->where('id', $u->id);
                } elseif ($u->hasRole('distributor')) {
                    $customersQuery->where(function ($q) use ($u) {
                        $q->where('id', $u->id)
                          ->orWhere('distributor_id', $u->id);
                    });
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        $customers = $customersQuery->get();
        $items     = Item::orderBy('name')->get();

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

            // determine article number: prefer submitted value, otherwise pull from Item model
            $articleNumber = $it['article_number'] ?? null;
            $itemModel = null;
            if (empty($articleNumber) && $itemId) {
                $itemModel = Item::find($itemId);
                $articleNumber = $itemModel->article_number ?? $itemModel->sku ?? null;
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
                'article_number' => $articleNumber ?? null,
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