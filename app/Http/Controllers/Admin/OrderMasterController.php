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
                } elseif ($authUser->hasRole(['super-admin','superadmin'])) {
                    // Superadmin should only see orders that are visible to superadmin.
                    $query->where('visible_to_superadmin', true);
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

            // If the logged-in user is a distributor and this order was created by one of
            // their retailers and is awaiting distributor approval, show approve action.
            try {
                $au = auth()->user();
                if ($au && $au->hasRole('distributor') && !$order->distributor_approved && $order->distributor_id == $au->id && $order->user_id != $au->id) {
                    $approveUrl = route('orders.approve.distributor', $order->id);
                    $actions = '<div class="btn-group">
                        <button type="button" class="btn btn-sm" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="' . $viewUrl . '">View</a>
                            <a class="dropdown-item" href="' . $editUrl . '">Edit</a>
                            <form method="POST" action="' . $approveUrl . '">
                                <input type="hidden" name="_token" value="' . csrf_token() . '">
                                <button type="submit" class="dropdown-item text-success">Approve</button>
                            </form>
                            <form method="POST" action="' . $deleteUrl . '">
                                <input type="hidden" name="_token" value="' . csrf_token() . '">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="dropdown-item text-danger deleteButton">Delete</button>
                            </form>
                        </div>
                    </div>';
                }
            } catch (\Throwable $e) {
                // ignore
            }

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

        // If called from cart, prepare pre_items from session cart entries
        $data['pre_items'] = null;
        if ($request->query('from_cart')) {
            $cart = session()->get('cart', []);
            $pre = [];
            foreach ($cart as $entry) {
                try {
                    $itemModel = null;
                    if (!empty($entry['item_id'])) {
                        $itemModel = Item::find($entry['item_id']);
                    }
                } catch (\Throwable $e) {
                    $itemModel = null;
                }

                // Normalize size fields: cart may store single 'size' and total 'qty'
                $sizesVal = null;
                $sizeQuantities = null;
                if (!empty($entry['size'])) {
                    // single size selected in cart
                    $sizesVal = [$entry['size']];
                    $sizeQuantities = [$entry['size'] => ($entry['qty'] ?? ($entry['quantity'] ?? 0))];
                } elseif (!empty($entry['size_quantities']) && is_array($entry['size_quantities'])) {
                    $sizesVal = array_keys($entry['size_quantities']);
                    $sizeQuantities = $entry['size_quantities'];
                }

                $pre[] = [
                    'item_id' => $entry['item_id'] ?? null,
                    'item_name' => $entry['name'] ?? ($itemModel?->name ?? ''),
                    'rate' => $entry['price'] ?? ($itemModel?->price ?? 0),
                    'tax_rate' => $itemModel?->tax_percent ?? ($entry['tax_rate'] ?? 0),
                    'quantity' => $entry['qty'] ?? ($entry['quantity'] ?? 0),
                    'description' => $entry['description'] ?? ($itemModel?->description ?? ''),
                    'color' => $entry['color_id'] ?? $entry['color'] ?? null,
                    'sizes' => $sizesVal,
                    'size_quantities' => $sizeQuantities,
                ];
            }
            $data['pre_items'] = array_values($pre);
        }

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
            $fields = $this->orderFields($request);
            $authUser = auth()->user();
            if ($authUser && $authUser->hasRole('retailer')) {
                $fields['distributor_id'] = $authUser->distributor_id;
                $fields['distributor_approved'] = false;
                $fields['visible_to_superadmin'] = false;
            }
            $order = OrderMaster::create($fields);
            $subtotal = $this->syncItems($order, $request->input('items', []));

            // Determine overall order status from item statuses: if all items share the same
            // status, use that as the order status. Otherwise keep submitted order status.
            $itemStatuses = $order->items()->pluck('status')->filter()->toArray();
            $unique = array_values(array_unique($itemStatuses));
            if (count($unique) === 1 && !empty($unique[0])) {
                $orderStatus = $unique[0];
            } else {
                $orderStatus = $request->input('status', $order->status ?? 'pending');
            }

            // Retailers are not allowed to set the overall order status.
            $authUser = auth()->user();
            if ($authUser && $authUser->hasRole('retailer')) {
                $orderStatus = 'pending';
            }

            $order->update(array_merge($this->calculatedTotals($request, $subtotal), ['status' => $orderStatus]));
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
            // Superadmin can only view orders that are approved by distributor
            // if the order requires distributor approval (has distributor_id set).
            if ($user->hasRole(['super-admin','superadmin']) && $order->distributor_id && !$order->distributor_approved) {
                abort(403);
            }
        }

        $colorsById = Color::pluck('name', 'id');

        return view('admin.orders.show', compact('order', 'colorsById'));
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
            $subtotal = $this->syncItems($order, $request->input('items', []));

            // Determine overall order status from item statuses: if all items share the same
            // status, use that as the order status. Otherwise keep submitted order status.
            $itemStatuses = $order->items()->pluck('status')->filter()->toArray();
            $unique = array_values(array_unique($itemStatuses));
            if (count($unique) === 1 && !empty($unique[0])) {
                $orderStatus = $unique[0];
            } else {
                $orderStatus = $request->input('status', $order->status ?? 'pending');
            }

            // Retailers are not allowed to set the overall order status.
            $authUser = auth()->user();
            if ($authUser && $authUser->hasRole('retailer')) {
                $orderStatus = $order->status ?? 'pending';
            }

            $order->update(array_merge($this->calculatedTotals($request, $subtotal), ['status' => $orderStatus]));
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

    /**
     * Distributor approves an order created by their retailer.
     */
    public function approveByDistributor(Request $request, OrderMaster $order)
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('distributor')) {
            abort(403);
        }

        // Only allow the distributor assigned to this order to approve it.
        if (empty($order->distributor_id) || $order->distributor_id != $user->id) {
            abort(403);
        }

        if ($order->distributor_approved) {
            return redirect()->back()->with('info', 'Order already approved.');
        }

        $order->update([
            'distributor_approved' => true,
            'distributor_approved_at' => now(),
            'visible_to_superadmin' => true,
        ]);

        return redirect()->back()->with('success', 'Order approved and sent to superadmin.');
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
        $items     = Item::with('colors')->orderBy('name')->get();

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
                'colors'         => $item->colors->map(fn ($color) => [
                    'id' => $color->id,
                    'name' => $color->name,
                ])->values(),
                'sizes'          => $sizes,
            ];
        })->values();
    }

    private function syncItems(OrderMaster $order, array $items): float
    {
        $subtotal = 0;
        $authUser = auth()->user();

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

            $sizeQuantities = $this->normalizeSizeQuantities($it);

            if (!empty($sizeQuantities)) {
                $sizeVal = implode(',', array_keys($sizeQuantities));
                $quantity = array_sum($sizeQuantities);
            } elseif (!empty($it['sizes'])) {
                $selectedSizes = is_array($it['sizes']) ? $it['sizes'] : explode(',', $it['sizes']);
                $selectedSizes = array_values(array_filter(array_map('trim', $selectedSizes), fn ($size) => $size !== ''));
                $sizeVal = implode(',', $selectedSizes);
                $quantity = (float) ($it['quantity'] ?? 0);
            } elseif (!empty($it['size_from']) || !empty($it['size_to'])) {
                $sizeVal = trim(($it['size_from'] ?? '') . '-' . ($it['size_to'] ?? ''), '-');
                $quantity = (float) ($it['quantity'] ?? 0);
            } else {
                $sizeVal = null;
                $quantity = (float) ($it['quantity'] ?? 0);
            }

            $rate = (float) ($it['rate'] ?? 0);
            $taxRate = (float) ($it['tax_rate'] ?? 0);
            $total = round(($rate + ($rate * $taxRate / 100)) * $quantity, 2);
            $subtotal += $total;

            // If the current user is a retailer, they are not allowed to set item status.
            $status = $it['status'] ?? 'pending';
            if ($authUser && $authUser->hasRole('retailer')) {
                $status = 'pending';
            }

            $order->items()->create([
                'article_number' => $articleNumber ?? null,
                'item_id'     => $itemId  ?: null,
                'item_name'   => $itemName,
                'description' => $it['description'] ?? null,
                'color'       => $this->normalizeColors($it),
                'size'        => $sizeVal,
                'size_quantities' => !empty($sizeQuantities) ? $sizeQuantities : null,
                'quantity'    => $quantity,
                'rate'        => $rate,
                'tax_rate'    => $taxRate,
                'total'       => $total,
                'status'      => $status,
                'size_from'   => $it['size_from']   ?? null,
                'size_to'     => $it['size_to']     ?? null,
                'sets'        => $it['sets']        ?? null,
            ]);
        }

        return $subtotal;
    }

    private function normalizeSizeQuantities(array $item): array
    {
        $selectedSizes = $item['sizes'] ?? [];
        if (!is_array($selectedSizes)) {
            $selectedSizes = explode(',', $selectedSizes);
        }

        $selectedSizes = array_values(array_filter(array_map('trim', $selectedSizes), fn ($size) => $size !== ''));
        $submittedQuantities = $item['size_quantities'] ?? [];
        if (!is_array($submittedQuantities)) {
            return [];
        }

        $quantities = [];
        foreach ($selectedSizes as $size) {
            $qty = (float) ($submittedQuantities[$size] ?? 0);
            if ($qty > 0) {
                $quantities[$size] = $qty;
            }
        }

        return $quantities;
    }

    private function normalizeColors(array $item): ?string
    {
        $colors = $item['color'] ?? $item['color_id'] ?? [];

        if (!is_array($colors)) {
            $colors = explode(',', (string) $colors);
        }

        $colors = array_values(array_filter(array_map('trim', $colors), fn ($color) => $color !== ''));

        return !empty($colors) ? implode(',', $colors) : null;
    }

    private function calculatedTotals(Request $request, float $subtotal): array
    {
        $discount = (float) $request->input('discount', 0);
        $adjustment = (float) $request->input('adjustment', 0);

        return [
            'subtotal' => round($subtotal, 2),
            'grand_total' => round($subtotal - $discount + $adjustment, 2),
        ];
    }

    // -----------------------------------------------------------------------
    // AJAX: update single order item status
    // -----------------------------------------------------------------------
    public function updateItemStatus(Request $request, \App\Models\OrderItem $orderItem)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $order = $orderItem->order;
        $user = auth()->user();
        // Retailers are not allowed to change an item's status at all.
        if ($user && $user->hasRole('retailer')) {
            abort(403);
        }

        // Distributors may act on their own orders or retailers assigned to them.
        if ($user && $user->hasRole('distributor')) {
            $retailerIds = User::where('distributor_id', $user->id)->pluck('id')->toArray();
            if ($order->user_id !== $user->id && !in_array($order->user_id, $retailerIds)) {
                abort(403);
            }
        }

        $orderItem->status = $request->input('status');
        $orderItem->save();

        return response()->json(['success' => true, 'status' => $orderItem->status]);
    }
}
