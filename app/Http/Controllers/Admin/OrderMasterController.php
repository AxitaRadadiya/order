<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\OrderMaster;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Item;
use App\Models\Size;
use App\Models\Color;
use App\Models\ItemVariant;
use App\Models\InventoryLog;
use App\Models\Setting;
use App\Models\TaxMaster;
use Carbon\Carbon;

class OrderMasterController extends Controller
{
    // -----------------------------------------------------------------------
    // Constants
    // -----------------------------------------------------------------------

    /** Statuses that trigger stock deduction from inventory */
    private const STOCK_DEDUCTED_STATUSES = ['confirmed'];

    /** Statuses that are considered "cancelled" */
    private const CANCELLED_STATUSES = ['canceled', 'cancelled'];

    // -----------------------------------------------------------------------
    // Index
    // -----------------------------------------------------------------------

    public function index()
    {
        return view('admin.orders.index');
    }

    public function orderList(Request $request)
    {
        $user  = auth()->user();
        $query = OrderMaster::with('customer');

        // Scope query based on role
        $this->applyRoleScope($query, $user);

        $totalData = $query->count();

        $limit      = (int) $request->input('length', 10);
        $start      = (int) $request->input('start', 0);
        $search     = $request->input('search.value');
        $customer   = trim((string) $request->input('customer_name'));
        $status     = trim((string) $request->input('status'));
        $dateFrom   = trim((string) $request->input('date_from'));
        $dateTo     = trim((string) $request->input('date_to'));

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('date', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn ($q2) => $q2
                      ->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                  );
            });
        }

        if (!empty($customer)) {
            $query->where(function ($q) use ($customer) {
                $q->whereHas('customer', fn ($q2) => $q2
                    ->where('name', 'like', "%{$customer}%")
                    ->orWhere('email', 'like', "%{$customer}%")
                );
            });
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($dateFrom)) {
            $query->whereDate('date', '>=', $dateFrom);
        }

        if (!empty($dateTo)) {
            $query->whereDate('date', '<=', $dateTo);
        }

        $totalFiltered = $query->count();

        $orders = $query->offset($start)->limit($limit)->orderByDesc('id')->get();

        $canView   = $user?->hasPermission('order-view')   ?? false;
        $canEdit   = $user?->hasPermission('order-edit')   ?? false;
        $canDelete = $user?->hasPermission('order-delete') ?? false;

        $data = $orders->values()->map(function (OrderMaster $order, int $idx) use (
            $start, $user, $canView, $canEdit, $canDelete
        ) {
            return [
                'id'                   => $start + $idx + 1,
                'order_number'         => $order->order_number ?? 'ORD-' . str_pad($order->id, 5, '0', STR_PAD_LEFT),
                'name'                 => optional($order->customer)->name ?? optional($order->customer)->email ?? '-',
                'date'                 => $order->date ? Carbon::parse($order->date)->format('d/m/y') : '',
                'total'                => '₹ ' . number_format((float) ($order->grand_total ?? 0), 2),
                'distributor_approved' => $this->approvalBadge($order),
                'status'               => $this->statusBadge($order->status),
                'action'               => $this->buildActionMenu($order, $user, $canView, $canEdit, $canDelete),
            ];
        })->all();

        return response()->json([
            'draw'            => (int) $request->input('draw'),
            'recordsTotal'    => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data'            => $data,
        ]);
    }

    public function report()
    {
        $user = auth()->user();
        abort_unless($user?->hasPermission('order-view') ?? false, 403);

        return view('admin.orders.report');
    }

  public function orderReportPage(Request $request)
{
    $query = OrderItem::query()
        ->join('order_masters', 'order_masters.id', '=', 'order_items.order_master_id')
        ->leftJoin('users', 'users.id', '=', 'order_masters.user_id')
        ->select(
            'order_items.*',
            'order_masters.id as order_id',
            'order_masters.date as order_date',
            'users.name as customer_name'
        );

    if ($request->filled('customer_name')) {
        $query->where('users.name', 'like', '%' . $request->customer_name . '%');
    }

    if ($request->filled('article_number')) {
        $query->where('order_items.article_number', 'like', '%' . $request->article_number . '%');
    }

    if ($request->filled('status')) {
        $query->where('order_items.status', $request->status);
    }
    if ($request->filled('from_date')) {
    $query->whereDate('order_masters.date', '>=', $request->from_date);
    }

    if ($request->filled('to_date')) {
        $query->whereDate('order_masters.date', '<=', $request->to_date);
    }

    $reportData = $query->orderByDesc('order_masters.id')->paginate(20);

    return view('admin.orders.report-orders', compact('reportData'));
}

    public function reportData(Request $request)
    {
        $user = auth()->user();
        abort_unless($user?->hasPermission('order-view') ?? false, 403);

        $search = trim((string) $request->input('search.value', $request->input('search')));

        $baseCountQuery = OrderItem::query()
            ->select('article_number', 'item_name', 'color', 'size')
            ->groupBy('article_number', 'item_name', 'color', 'size');

        $totalData = $baseCountQuery->get()->count();

        $countQuery = OrderItem::query()
            ->select('article_number', 'item_name', 'color', 'size')
            ->when($search, fn ($q) => $q->where(function ($q2) use ($search) {
                $q2->where('article_number', 'like', "%{$search}%")
                   ->orWhere('item_name', 'like', "%{$search}%")
                   ->orWhere('color', 'like', "%{$search}%")
                   ->orWhere('size', 'like', "%{$search}%");
            }))
            ->groupBy('article_number', 'item_name', 'color', 'size');

        $totalFiltered = $countQuery->get()->count();

        $limit  = (int) $request->input('length', 10);
        $start  = (int) $request->input('start', 0);

        $query = OrderItem::query()
            ->selectRaw(
                'article_number,
                item_name,
                color,
                size,
                SUM(quantity) as total_quantity,
                SUM(CASE WHEN status IN ("shipped", "partial_dispatch") THEN quantity ELSE 0 END) as dispatched_quantity,
                SUM(CASE WHEN status = "cancelled" THEN quantity ELSE 0 END) as negative_quantity'
            )
            ->when($search, fn ($q) => $q->where(function ($q2) use ($search) {
                $q2->where('article_number', 'like', "%{$search}%")
                   ->orWhere('item_name', 'like', "%{$search}%")
                   ->orWhere('color', 'like', "%{$search}%")
                   ->orWhere('size', 'like', "%{$search}%");
            }))
            ->groupBy('article_number', 'item_name', 'color', 'size')
            ->orderByDesc('total_quantity')
            ->offset($start)
            ->limit($limit);

        $items = $query->get();

        $data = $items->map(function (OrderItem $item, int $idx) use ($start) {
            return [
                'id'                  => $start + $idx + 1,
                'article_number'      => $item->article_number ?: '-',
                'item_name'           => $item->item_name ?: '-',
                'color'               => $item->color ?: '-',
                'size'                => $item->size ?: '-',
                'total_quantity'      => (float) $item->total_quantity,
                'dispatched_quantity' => (float) $item->dispatched_quantity,
                'negative_quantity'   => (float) $item->negative_quantity,
            ];
        })->all();

        return response()->json([
            'draw'            => (int) $request->input('draw'),
            'recordsTotal'    => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data'            => $data,
        ]);
    }

    public function reportOrder(Request $request)
    {
        $user = auth()->user();
        abort_unless($user?->hasPermission('order-view') ?? false, 403);

        $request->validate([
            'order_id' => 'required|exists:order_masters,id',
        ]);

        $order = OrderMaster::with('items')->findOrFail($request->order_id);

        $items = $order->items->map(function (OrderItem $item) {
            return [
                'article_number' => $item->article_number ?: '-',
                'item_name'      => $item->item_name ?: '-',
                'quantity'       => (float) $item->quantity,
                'status'         => $item->status ?: 'pending',
            ];
        });

        $statuses = [
            'pending'          => 0,
            'confirmed'        => 0,
            'shipped'          => 0,
            'partial_dispatch' => 0,
            'cancelled'        => 0,
        ];

        foreach ($order->items as $item) {
            $status = $item->status ?: 'pending';
            if (!array_key_exists($status, $statuses)) {
                $statuses[$status] = 0;
            }
            $statuses[$status] += (float) $item->quantity;
        }

        return response()->json([
            'order_number'      => $order->order_number ?? 'ORD-' . str_pad($order->id, 5, '0', STR_PAD_LEFT),
            'total_items'       => $order->items->count(),
            'distinct_articles' => $order->items->pluck('article_number')->filter()->unique()->count(),
            'total_quantity'    => $order->items->sum('quantity'),
            'statuses'          => $statuses,
            'items'             => $items,
        ]);
    }

    public function customerAddresses(User $user)
    {
        $addr     = $user->address ?? null;
        $billing  = $addr->billing_address ?? $addr->address ?? $user->billing_address ?? $user->address ?? '';
        $shipping = $addr->shipping_address ?? $addr->address ?? $user->shipping_address ?? $billing;

        return response()->json(compact('billing', 'shipping'));
    }

    // -----------------------------------------------------------------------
    // Create / Store
    // -----------------------------------------------------------------------

    public function create(Request $request)
    {
        $user = auth()->user();
        abort_unless($user?->hasPermission('order-create'), 403);

        $data                = $this->viewData();
        $data['terms']       = Setting::getValue('terms_and_conditions', '');
        $data['pre_item_id'] = $request->query('item_id');
        $data['pre_items']   = $request->query('from_cart') ? $this->preItemsFromCart() : null;
        $data['pre_user_id'] = $user->hasRole(['retailer', 'distributor']) ? $user->id : null;

        return view('admin.orders.create', $data);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        abort_unless($user?->hasPermission('order-create'), 403);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date'    => 'required|date',
        ]);

        $items = $request->input('items', []);
        $request->merge(['items' => $items]);

        DB::beginTransaction();
        try {
            $fields = $this->orderFields($request);
            $fields = array_merge($fields, $this->approvalFields($user));

            // Handle tax
            $tax = $request->filled('tax_id') ? TaxMaster::find($request->tax_id) : null;
            if ($tax) {
                $fields['tax_id']         = $tax->id;
                $fields['tax_percentage'] = $tax->tax_percentage;
            }

            $order    = OrderMaster::create($fields);
            $subtotal = $this->syncItems($order, $request->input('items', []));

            $orderStatus = $this->deriveOrderStatus($order, $request, $user);

            $order->update(array_merge(
                $this->calculatedTotals($request, $subtotal, $tax),
                ['status' => $orderStatus]
            ));

            DB::commit();

            if ($request->input('from_cart')) {
                session()->forget('cart');
            }

            return redirect()->route('orders.index')->with('success', 'Order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['error' => $e->getMessage()])
                ->with($this->viewData());
        }
    }

    // -----------------------------------------------------------------------
    // Show
    // -----------------------------------------------------------------------

    public function show(OrderMaster $order)
    {
        $user = auth()->user();
        abort_unless($user?->hasPermission('order-view'), 403);
        $this->authorizeOrderAccess($order, $user);

        $order->load('items', 'customer', 'tax');

        $colorsById    = Color::pluck('color_code', 'id');
        $colorNameById = Color::pluck('name', 'id');

        return view('admin.orders.show', compact('order', 'colorsById', 'colorNameById'));
    }

    // -----------------------------------------------------------------------
    // Edit / Update
    // -----------------------------------------------------------------------

    public function edit(OrderMaster $order)
    {
        $user = auth()->user();
        abort_unless($user?->hasPermission('order-edit'), 403);
        $this->authorizeOrderEdit($order, $user);

        $order->load('items');

        // Lock form fields when any item is shipped/delivered/cancelled.
        // Confirmed items remain editable (can still be moved to shipped).
        $hardLockedStatuses = ["shipped", "delivered", "cancelled"];
        $hasLockedItem = $order->items->contains(
            fn ($item) => in_array(strtolower($item->status ?? ""), $hardLockedStatuses)
        );


        // Lock form fields when any item is shipped/delivered/cancelled.
        // Confirmed items remain editable (can still be moved to shipped).
        $hardLockedStatuses = ['shipped', 'delivered', 'cancelled'];
        $hasLockedItem = $order->items->contains(
            fn ($item) => in_array(strtolower($item->status ?? ''), $hardLockedStatuses)
        );

        $data                  = array_merge($this->viewData(), compact('order'));
        $data['terms']         = Setting::getValue('terms_and_conditions', '');
        $data['hasLockedItem'] = $hasLockedItem;

        return view('admin.orders.edit', $data);
    }

    public function update(Request $request, OrderMaster $order)
    {
        $user = auth()->user();
        abort_unless($user?->hasPermission('order-edit'), 403);
        $this->authorizeOrderEdit($order, $user);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date'    => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            // Capture existing items before deleting
            $existingItems = $order->items()->get()->keyBy('id');

            // Determine which stock changes need to happen
            $pendingDeductions = [];
            foreach ($request->input('items', []) as $itemData) {
                if (!isset($itemData['order_item_id'], $itemData['status'])) {
                    continue;
                }

                $existing = $existingItems->get($itemData['order_item_id']);
                if (!$existing) {
                    continue;
                }

                $oldStatus = strtolower($existing->status);
                $newStatus = strtolower($itemData['status']);
                $result    = $this->classifyStockTransition($oldStatus, $newStatus);

                if ($result['restore']) {
                    $this->restoreStockForOrderItem($existing);
                }

                if ($result['deduct']) {
                    $pendingDeductions[] = ['item' => $existing, 'status' => $newStatus];
                }
            }

            // Update order fields
            $orderFields = $this->orderFields($request);
            $tax         = $request->filled('tax_id') ? TaxMaster::find($request->tax_id) : null;
            $orderFields['tax_id']         = $tax?->id;
            $orderFields['tax_percentage'] = $tax?->tax_percentage;

            $order->update($orderFields);
            $this->applySelfAssignmentOnUpdate($order, $user);

            // Recreate items (stock deduction skipped inside syncItems)
            $order->items()->delete();
            $subtotal = $this->syncItems($order, $request->input('items', []), skipStockDeduction: true);

            // Process pending stock deductions on the new items
            if (!empty($pendingDeductions)) {
                $newItems = $order->items()->get();
                foreach ($pendingDeductions as ['item' => $oldItem, 'status' => $newStatus]) {
                    $newItem = $newItems->first(fn ($i) =>
                        $i->item_id == $oldItem->item_id &&
                        $i->color   == $oldItem->color   &&
                        $i->size    == $oldItem->size
                    );
                    if ($newItem) {
                        $newItem->update(['status' => $newStatus]);
                        $this->deductStockForOrderItem($newItem);
                    }
                }
            }

            $orderStatus = $this->deriveOrderStatus($order, $request, $user, isUpdate: true);

            $order->update(array_merge(
                $this->calculatedTotals($request, $subtotal, $tax),
                ['status' => $orderStatus]
            ));

            DB::commit();

            return redirect()->route('orders.index')->with('success', 'Order updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            $order->load('items');
            return back()->withInput()
                ->with('error', $e->getMessage())
                ->with(array_merge($this->viewData(), compact('order')));
        }
    }

    // -----------------------------------------------------------------------
    // Approval
    // -----------------------------------------------------------------------

    public function approveByDistributor(Request $request, OrderMaster $order)
    {
        $user = auth()->user();
        abort_unless($user?->hasRole('distributor'), 403);
        abort_unless(!empty($order->distributor_id) && $order->distributor_id == $user->id, 403);

        if (($order->approval_level ?? 0) >= 1) {
            return redirect()->back()->with('info', 'Order already approved by distributor.');
        }

        $order->update([
            'distributor_approved'    => true,
            'distributor_approved_at' => now(),
            'approval_level'          => 1,
            'visible_to_superadmin'   => true,
        ]);

        return redirect()->back()->with('success', 'Order approved and sent to superadmin.');
    }

    public function approveBySuperAdmin(Request $request, OrderMaster $order)
    {
        $user = auth()->user();
        abort_unless($user?->hasRole(['super-admin', 'superadmin']), 403);

        if (($order->approval_level ?? 0) >= 2) {
            return redirect()->back()->with('info', 'Order already finalized by super-admin.');
        }

        $order->update([
            'approval_level'        => 2,
            'visible_to_superadmin' => true,
        ]);

        return redirect()->back()->with('success', 'Order finalized by super-admin.');
    }

    // -----------------------------------------------------------------------
    // Destroy
    // -----------------------------------------------------------------------

    public function destroy(OrderMaster $order)
    {
        $user = auth()->user();
        abort_unless($user?->hasPermission('order-delete'), 403);

        if ($user->hasRole(['retailer', 'distributor']) && $order->user_id !== $user->id) {
            return redirect()->back()->with('error', 'You are not allowed to delete this order.');
        }

        $order->load('items');

        foreach ($order->items as $item) {
            if ($this->isStockDeductedStatus($item->status)) {
                try {
                    $this->restoreStockForOrderItem($item);
                } catch (\Exception $e) {
                    Log::error("Failed to restore stock for order item #{$item->id}: " . $e->getMessage());
                }
            }
        }

        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Order deleted.');
    }

    // -----------------------------------------------------------------------
    // AJAX — Item Status Update
    // -----------------------------------------------------------------------

    public function updateItemStatus(Request $request, OrderItem $orderItem)
    {
        $request->validate(['status' => 'required|string']);

        $user  = auth()->user();
        $order = $orderItem->order;

        // Role guards
        abort_if($user?->hasRole('retailer'), 403);

        if ($user?->hasRole('distributor')) {
            $retailerIds = User::where('distributor_id', $user->id)->pluck('id')->toArray();
            abort_unless($order->user_id === $user->id || in_array($order->user_id, $retailerIds), 403);
        }

        $oldStatus = strtolower((string) $orderItem->status);
        $newStatus = strtolower((string) $request->input('status'));

        // Validate transitions
        if ($error = $this->validateStatusTransition($oldStatus, $newStatus)) {
            return response()->json(['success' => false, 'message' => $error], 422);
        }

        // Additional server-side stock validation when attempting to mark as shipped
        if ($newStatus === 'shipped') {
            $checkReq = new Request(['order_item_id' => $orderItem->id]);
            $checkRes = $this->checkStock($checkReq);
            $checkData = json_decode($checkRes->getContent(), true);
            if (empty($checkData['ok'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock to mark as shipped.',
                    'issues'  => $checkData['issues'] ?? []
                ], 422);
            }
        }

        // Apply stock changes
        $transition = $this->classifyStockTransition($oldStatus, $newStatus);

        try {
            if ($transition['restore']) {
                $this->restoreStockForOrderItem($orderItem);
            }
            if ($transition['deduct']) {
                $this->deductStockForOrderItem($orderItem);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        $orderItem->update(['status' => $newStatus]);
        $this->syncOrderStatusFromItems($order);

        Log::info("Order item #{$orderItem->id} status: {$oldStatus} → {$newStatus}", [
            'order_id' => $order->id,
            'user_id'  => auth()->id(),
        ]);

        return response()->json(['success' => true, 'status' => $orderItem->status]);
    }

    // -----------------------------------------------------------------------
    // AJAX — Stock Check
    // -----------------------------------------------------------------------

    public function checkStock(Request $request)
    {
        $request->validate(['order_item_id' => 'required|integer|exists:order_items,id']);

        $orderItem      = OrderItem::findOrFail($request->input('order_item_id'));
        $colorId        = $this->firstColorId($orderItem->color);
        $sizeQuantities = is_array($orderItem->size_quantities) ? $orderItem->size_quantities : [];
        $issues         = [];

        foreach ($sizeQuantities as $sizeLabel => $orderQty) {
            $sizeLabel = (string) $sizeLabel;
            $orderQty  = (int) $orderQty;

            if ($orderQty <= 0) {
                continue;
            }

            $size = Size::where('name', $sizeLabel)->first();
            if (!$size) {
                $issues[] = ['size' => $sizeLabel, 'available' => 0, 'ordered' => $orderQty, 'message' => "Size '{$sizeLabel}' not found"];
                continue;
            }

            $variant = ItemVariant::where('item_id', $orderItem->item_id)
                ->where('color_id', $colorId)
                ->where('size_id', $size->id)
                ->first();

            if (!$variant) {
                $issues[] = ['size' => $sizeLabel, 'available' => 0, 'ordered' => $orderQty, 'message' => 'Variant not found'];
                continue;
            }

            $available = (int) $variant->quantity;
            if ($available < $orderQty) {
                $issues[] = ['size' => $sizeLabel, 'available' => $available, 'ordered' => $orderQty, 'message' => "Only {$available} available for size {$sizeLabel}"];
            }
        }

        return response()->json(['ok' => empty($issues), 'issues' => $issues]);
    }

    // -----------------------------------------------------------------------
    // Private — Authorization helpers
    // -----------------------------------------------------------------------

    private function applyRoleScope($query, ?User $user): void
    {
        if (!$user) return;

        if ($user->hasRole('retailer')) {
            $query->where('user_id', $user->id);
        } elseif ($user->hasRole('distributor')) {
            $retailerIds = User::where('distributor_id', $user->id)->pluck('id')->toArray();
            $query->whereIn('user_id', array_merge([$user->id], $retailerIds));
        }
        // super-admin: no restriction
    }

    private function authorizeOrderAccess(OrderMaster $order, ?User $user): void
    {
        if (!$user) return;

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

    private function authorizeOrderEdit(OrderMaster $order, ?User $user): void
    {
        $this->authorizeOrderAccess($order, $user);

        if ($user?->hasRole('retailer') && ($order->approval_level ?? 0) >= 1) {
            abort(redirect()->back()->with('error', 'Order has been approved and cannot be edited.'));
        }

        if ($user?->hasRole('distributor') && ($order->approval_level ?? 0) >= 2) {
            abort(redirect()->back()->with('error', 'Order has been finalized by super-admin and cannot be edited.'));
        }

        if (strtolower($order->status ?? '') === 'delivered') {
            abort(redirect()->back()->with('error', 'Order has been delivered and cannot be edited.'));
        }
    }

    // -----------------------------------------------------------------------
    // Private — Data helpers
    // -----------------------------------------------------------------------

    private function viewData(): array
    {
        $user           = auth()->user();
        $customersQuery = User::with('address')
            ->whereHas('role', fn ($q) => $q->whereIn('name', ['retailer', 'distributor']))
            ->orderBy('name');

        if ($user?->hasRole('retailer')) {
            $customersQuery->where('id', $user->id);
        } elseif ($user?->hasRole('distributor')) {
            $customersQuery->where(fn ($q) => $q->where('id', $user->id)->orWhere('distributor_id', $user->id));
        }

        $customers = $customersQuery->get();
        $items     = Item::with(['variants.color'])->orderBy('name')->get();
        $taxes     = TaxMaster::orderBy('tax_percentage')->get();

        $customersJson = $customers->mapWithKeys(function (User $u) {
            $addr     = $u->address ?? null;
            $billing  = $addr->billing_address ?? $addr->address ?? $u->billing_address ?? $u->address ?? '';
            $shipping = $addr->shipping_address ?? $addr->address ?? $u->shipping_address ?? $billing;
            return [(string) $u->id => compact('billing', 'shipping')];
        });

        return [
            'customers'     => $customers,
            'customersJson' => $customersJson,
            'items'         => $items,
            'itemsJson'     => $this->buildItemsJson($items),
            'sizesJson'     => Size::activeLabels(),
            'colors'        => Color::orderBy('name')->get(),
            'taxes'         => $taxes,
        ];
    }

    private function orderFields(Request $request): array
    {
        return $request->only([
            'user_id', 'date', 'expected_date', 'eway_bill_number', 'transport_number',
            'lr_number', 'billing_address', 'shipping_address', 'subtotal', 'discount',
            'markdown', 'adjustment', 'grand_total', 'terms', 'notes', 'status',
        ]);
    }

    /**
     * Returns the approval-related fields to set when creating an order, based on role.
     */
    private function approvalFields(?User $user): array
    {
        if ($user?->hasRole('retailer')) {
            return [
                'distributor_id'        => $user->distributor_id,
                'distributor_approved'  => false,
                'approval_level'        => 0,
                'visible_to_superadmin' => false,
            ];
        }

        if ($user?->hasRole('distributor')) {
            return [
                'distributor_id'          => $user->id,
                'distributor_approved'    => true,
                'distributor_approved_at' => now(),
                'approval_level'          => 1,
                'visible_to_superadmin'   => true,
            ];
        }

        if ($user?->hasRole(['super-admin', 'superadmin'])) {
            return [
                'distributor_approved'    => true,
                'distributor_approved_at' => now(),
                'approval_level'          => 2,
                'visible_to_superadmin'   => true,
            ];
        }

        return [];
    }

    private function applySelfAssignmentOnUpdate(OrderMaster $order, ?User $user): void
    {
        if ($user?->hasRole('distributor') && empty($order->distributor_id)) {
            $order->update(['distributor_id' => $user->id]);
        }

        if ($user?->hasRole(['super-admin', 'superadmin'])) {
            $order->update([
                'approval_level'        => 2,
                'visible_to_superadmin' => true,
            ]);
        }
    }

    private function preItemsFromCart(): array
    {
        $pre = [];
        foreach (session()->get('cart', []) as $entry) {
            $itemModel = !empty($entry['item_id']) ? Item::find($entry['item_id']) : null;

            if (!empty($entry['size_quantities']) && is_array($entry['size_quantities'])) {
                $sizesVal       = array_keys($entry['size_quantities']);
                $sizeQuantities = $entry['size_quantities'];
            } elseif (!empty($entry['size'])) {
                $sizesVal       = [$entry['size']];
                $sizeQuantities = [$entry['size'] => $entry['qty'] ?? $entry['quantity'] ?? 0];
            } else {
                $sizesVal = $sizeQuantities = null;
            }

            $pre[] = [
                'item_id'         => $entry['item_id'] ?? null,
                'item_name'       => $entry['name'] ?? $itemModel?->name ?? '',
                'rate'            => $entry['price'] ?? $itemModel?->price ?? 0,
                'quantity'        => $entry['qty'] ?? $entry['quantity'] ?? 0,
                'description'     => $entry['description'] ?? $itemModel?->description ?? '',
                'color'           => $entry['color_id'] ?? $entry['color'] ?? null,
                'sizes'           => $sizesVal,
                'size_quantities' => $sizeQuantities,
            ];
        }
        return array_values($pre);
    }

    private function buildItemsJson($items): \Illuminate\Support\Collection
    {
        return $items->map(function (Item $item) {
            $sizes = !empty($item->sizes)
                ? (is_array($item->sizes) ? $item->sizes : array_map('trim', explode(',', $item->sizes)))
                : [];

            $variantColors = $item->variants->map(fn ($v) => $v->color)->filter()->unique('id')->values();
            $itemColors    = $variantColors->isNotEmpty() ? $variantColors : $item->colors;

            return [
                'id'             => $item->id,
                'article_number' => $item->article_number ?? $item->sku ?? '',
                'name'           => $item->name,
                'unit'           => $item->unit ?? '',
                'rate'           => $item->price ?? 0,
                'desc'           => $item->description ?? '',
                'colors'         => $itemColors->map(fn ($c) => [
                    'id'         => $c->id,
                    'name'       => $c->name,
                    'color_code' => $c->color_code,
                ])->values(),
                'sizes'          => $sizes,
            ];
        })->values();
    }

    // -----------------------------------------------------------------------
    // Private — Order status helpers
    // -----------------------------------------------------------------------

    /**
     * Derive the correct overall order status from item statuses + request input.
     */
    private function deriveOrderStatus(OrderMaster $order, Request $request, ?User $user, bool $isUpdate = false): string
    {
        $itemStatuses = $order->items()->pluck('status')->filter()->toArray();
        $unique       = array_values(array_unique($itemStatuses));

        if (count($unique) === 1 && !empty($unique[0])) {
            $status = $unique[0];
        } else {
            $fallback = $isUpdate ? ($order->status ?? 'pending') : 'pending';
            $status   = $request->input('status', $fallback);
        }

        // Retailers cannot set status beyond pending
        if ($user?->hasRole('retailer')) {
            $status = $isUpdate ? ($order->status ?? 'pending') : 'pending';
        }

        return $status;
    }

    private function allItemsDelivered(OrderMaster $order): bool
    {
        $statuses = $order->items()->pluck('status')->filter()->toArray();
        return !empty($statuses) && count(array_filter($statuses, fn ($s) => $s !== 'delivered')) === 0;
    }

    private function syncOrderStatusFromItems(OrderMaster $order): void
    {
        $unique = array_values(array_unique(
            $order->items()->pluck('status')->filter()->toArray()
        ));

        if (count($unique) === 1 && !empty($unique[0])) {
            $order->update(['status' => $unique[0]]);
        }
    }

    // -----------------------------------------------------------------------
    // Private — Stock transition helpers
    // -----------------------------------------------------------------------

    /**
     * Classify what stock action (if any) is needed for a status change.
     * Returns ['deduct' => bool, 'restore' => bool].
     */
    private function classifyStockTransition(string $oldStatus, string $newStatus): array
    {
        $wasDeducted    = $this->isStockDeductedStatus($oldStatus);
        $willDeduct     = $this->isStockDeductedStatus($newStatus);
        $isCancelling   = $this->isCancelledStatus($newStatus);
        $isUnCancelling = $this->isCancelledStatus($oldStatus) && !$this->isCancelledStatus($newStatus);

        return [
            'restore' => $isCancelling && $wasDeducted,
            'deduct'  => ($willDeduct && !$wasDeducted) || ($isUnCancelling && $willDeduct),
        ];
    }

    /**
     * Validate status transition rules.
     * Returns an error string if invalid, or null if valid.
     */
    private function validateStatusTransition(string $oldStatus, string $newStatus): ?string
    {
        $wasDeducted = $this->isStockDeductedStatus($oldStatus);

        if ($wasDeducted && $newStatus === 'pending') {
            return "Cannot move from {$oldStatus} back to {$newStatus} once stock is deducted.";
        }

        if ($newStatus === 'shipped' && $oldStatus !== 'confirmed') {
            return "Cannot move to {$newStatus} unless current status is 'confirmed'.";
        }

        return null;
    }

    private function isStockDeductedStatus(string $status): bool
    {
        return in_array(strtolower($status), self::STOCK_DEDUCTED_STATUSES, true);
    }

    private function isCancelledStatus(string $status): bool
    {
        return in_array(strtolower($status), self::CANCELLED_STATUSES, true);
    }

    // -----------------------------------------------------------------------
    // Private — Item sync
    // -----------------------------------------------------------------------

    private function syncItems(OrderMaster $order, array $items, bool $skipStockDeduction = false): float
    {
        $subtotal = 0.0;
        $user     = auth()->user();

        foreach ($items as $it) {
            $itemId   = $it['item_id']   ?? null;
            $itemName = $it['item_name'] ?? null;

            if ($itemId && empty($itemName)) {
                $itemName = Item::find($itemId)?->name;
            }

            if (empty($itemId) && empty($itemName) && empty($it['description'])) {
                continue;
            }

            $articleNumber = $it['article_number'] ?? null;
            if (empty($articleNumber) && $itemId) {
                $m             = Item::find($itemId);
                $articleNumber = $m?->article_number ?? $m?->sku ?? null;
            }

            $sizeQuantities = $this->normalizeSizeQuantities($it);

            if (!empty($sizeQuantities)) {
                $sizeVal  = implode(',', array_keys($sizeQuantities));
                $quantity = array_sum($sizeQuantities);
            } elseif (!empty($it['sizes'])) {
                $selectedSizes = array_values(array_filter(
                    array_map('trim', is_array($it['sizes']) ? $it['sizes'] : explode(',', $it['sizes'])),
                    fn ($s) => $s !== ''
                ));
                $sizeVal  = implode(',', $selectedSizes);
                $quantity = (float) ($it['quantity'] ?? 0);
            } elseif (!empty($it['size_from']) || !empty($it['size_to'])) {
                $sizeVal  = trim(($it['size_from'] ?? '') . '-' . ($it['size_to'] ?? ''), '-');
                $quantity = (float) ($it['quantity'] ?? 0);
            } else {
                $sizeVal  = null;
                $quantity = (float) ($it['quantity'] ?? 0);
            }

            $rate     = (float) ($it['rate'] ?? 0);
            $total    = round($rate * $quantity, 2);
            $subtotal += $total;

            $status = $it['status'] ?? 'pending';
            if ($user?->hasRole('retailer')) {
                $status = 'pending';
            }

            $orderItem = $order->items()->create([
                'article_number'  => $articleNumber,
                'item_id'         => $itemId ?: null,
                'item_name'       => $itemName,
                'description'     => $it['description'] ?? null,
                'color'           => $this->normalizeColors($it),
                'size'            => $sizeVal,
                'size_quantities' => !empty($sizeQuantities) ? $sizeQuantities : null,
                'quantity'        => $quantity,
                'rate'            => $rate,
                'total'           => $total,
                'status'          => $status,
                'size_from'       => $it['size_from'] ?? null,
                'size_to'         => $it['size_to']   ?? null,
                'sets'            => $it['sets']       ?? null,
            ]);

            if (!$skipStockDeduction && $this->isStockDeductedStatus($status) && !$this->isCancelledStatus($status)) {
                $this->deductStockForOrderItem($orderItem);
            }
        }

        return $subtotal;
    }

    private function normalizeSizeQuantities(array $item): array
    {
        $selectedSizes = is_array($item['sizes'] ?? []) ? ($item['sizes'] ?? []) : explode(',', $item['sizes'] ?? '');
        $selectedSizes = array_values(array_filter(array_map('trim', $selectedSizes), fn ($s) => $s !== ''));

        $submitted = $item['size_quantities'] ?? [];
        if (!is_array($submitted)) {
            return [];
        }

        $quantities = [];
        foreach ($selectedSizes as $size) {
            $qty = (float) ($submitted[$size] ?? 0);
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

        $colors = array_values(array_filter(array_map('trim', $colors), fn ($c) => $c !== ''));

        return !empty($colors) ? implode(',', $colors) : null;
    }

    private function firstColorId(?string $colorString): ?int
    {
        $ids = array_values(array_filter(
            array_map('trim', explode(',', (string) $colorString)),
            fn ($v) => $v !== ''
        ));
        return !empty($ids) ? (int) $ids[0] : null;
    }

    // -----------------------------------------------------------------------
    // Private — Financial calculation
    // -----------------------------------------------------------------------

    private function calculatedTotals(Request $request, float $subtotal, ?TaxMaster $tax = null): array
    {
        $markdownPercent = (float) $request->input('markdown', 0);
        $discountPercent = (float) $request->input('discount', 0);
        $adjustment      = (float) $request->input('adjustment', 0);
        $taxPercentage   = $tax?->tax_percentage ?? 0.0;

        // Order: Markdown → Discount → Tax → Adjustment
        $afterMarkdown = $subtotal - ($subtotal * $markdownPercent / 100);
        $afterDiscount = $afterMarkdown - ($afterMarkdown * $discountPercent / 100);
        $taxAmount     = $afterDiscount * $taxPercentage / 100;
        $grandTotal    = $afterDiscount + $taxAmount + $adjustment;

        return [
            'subtotal'    => round($subtotal, 2),
            'markdown'    => round($markdownPercent, 2),
            'discount'    => round($discountPercent, 2),
            'grand_total' => round($grandTotal, 2),
        ];
    }

    // -----------------------------------------------------------------------
    // Private — Stock management
    // -----------------------------------------------------------------------

    private function deductStockForOrderItem(OrderItem $orderItem): void
    {
        DB::transaction(function () use ($orderItem) {
            $colorId        = $this->firstColorId($orderItem->color);
            $sizeQuantities = is_array($orderItem->size_quantities) ? $orderItem->size_quantities : [];

            if (empty($sizeQuantities)) {
                Log::warning("No size quantities for order item #{$orderItem->id}");
                return;
            }

            foreach ($sizeQuantities as $sizeLabel => $orderQty) {
                $sizeLabel = (string) $sizeLabel;
                $orderQty  = (int) $orderQty;

                if ($orderQty <= 0) continue;

                $size = Size::where('name', $sizeLabel)->firstOrFail();

                $variant = ItemVariant::where('item_id', $orderItem->item_id)
                    ->where('color_id', $colorId)
                    ->where('size_id', $size->id)
                    ->first();

                if (!$variant) {
                    throw new \Exception("Variant not found for item #{$orderItem->item_id}, color #{$colorId}, size {$sizeLabel}");
                }

                if ($variant->quantity < $orderQty) {
                    Log::warning("Stock overdrawn for order item #{$orderItem->id}, variant #{$variant->id}. Available: {$variant->quantity}, Ordered: {$orderQty}");
                }

                $variant->increment('sold_quantity', $orderQty);
                $variant->decrement('quantity', $orderQty);

                InventoryLog::create([
                    'type'            => 'deduct',
                    'qty'             => $orderQty,
                    'item_variant_id' => $variant->id,
                    'order_master_id' => $orderItem->order_master_id,
                    'note'            => 'Order confirmed - stock deducted',
                    'created_by'      => auth()->id(),
                ]);

                Log::info("Stock deducted: variant #{$variant->id} -{$orderQty}");
            }
        });
    }

    private function restoreStockForOrderItem(OrderItem $orderItem): void
    {
        DB::transaction(function () use ($orderItem) {
            $colorId = $this->firstColorId($orderItem->color);

            if (!$colorId) {
                Log::warning("No color found for order item #{$orderItem->id}, skipping restore.");
                return;
            }

            $sizeQuantities = is_array($orderItem->size_quantities) ? $orderItem->size_quantities : [];

            // Legacy format: single size + quantity
            if (empty($sizeQuantities)) {
                $singleQty = (int) $orderItem->quantity;
                if ($singleQty <= 0) return;

                $size = Size::where('name', $orderItem->size)->first();
                if (!$size) {
                    Log::error("Size '{$orderItem->size}' not found for legacy restore on item #{$orderItem->id}");
                    return;
                }

                $variant = ItemVariant::where('item_id', $orderItem->item_id)
                    ->where('color_id', $colorId)
                    ->where('size_id', $size->id)
                    ->first();

                if (!$variant) {
                    Log::error("Variant not found for legacy restore on item #{$orderItem->id}");
                    return;
                }

                $variant->decrement('sold_quantity', $singleQty);
                $variant->increment('quantity', $singleQty);

                InventoryLog::create([
                    'type'            => 'restore',
                    'qty'             => $singleQty,
                    'item_variant_id' => $variant->id,
                    'order_master_id' => $orderItem->order_master_id,
                    'note'            => 'Stock restored - order item cancelled',
                    'created_by'      => auth()->id(),
                ]);

                return;
            }

            // Modern format: size_quantities map
            foreach ($sizeQuantities as $sizeLabel => $orderQty) {
                $sizeLabel = (string) $sizeLabel;
                $orderQty  = (int) $orderQty;

                if ($orderQty <= 0) continue;

                $size = Size::where('name', $sizeLabel)->first();
                if (!$size) {
                    Log::error("Size '{$sizeLabel}' not found during restore on item #{$orderItem->id}");
                    continue;
                }

                $variant = ItemVariant::where('item_id', $orderItem->item_id)
                    ->where('color_id', $colorId)
                    ->where('size_id', $size->id)
                    ->first();

                if (!$variant) {
                    Log::error("Variant not found during restore on item #{$orderItem->id}");
                    continue;
                }

                $variant->decrement('sold_quantity', $orderQty);
                $variant->increment('quantity', $orderQty);

                InventoryLog::create([
                    'type'            => 'restore',
                    'qty'             => $orderQty,
                    'item_variant_id' => $variant->id,
                    'order_master_id' => $orderItem->order_master_id,
                    'note'            => 'Stock restored - order item cancelled',
                    'created_by'      => auth()->id(),
                ]);
            }
        });
    }

    // -----------------------------------------------------------------------
    // Private — HTML builders (ideally move to Blade partials)
    // -----------------------------------------------------------------------

    private function statusBadge(?string $status): string
    {
        $map   = [
            'pending'          => 'warning',
            'confirmed'        => 'info',
            'shipped'          => 'primary',
            'partial_dispatch' => 'secondary',
            'cancelled'        => 'danger',
        ];
        $color = $map[$status] ?? 'secondary';
        return '<span class="badge badge-' . $color . '">' . ucfirst($status ?? 'pending') . '</span>';
    }

    private function approvalBadge(OrderMaster $order): string
    {
        if (empty($order->distributor_id)) {
            return '<span class="badge badge-secondary">N/A</span>';
        }
        $level = (int) ($order->approval_level ?? 0);
        if ($level >= 2) return '<span class="badge badge-primary">Superadmin Approved</span>';
        if ($level === 1) return '<span class="badge badge-success">Distributor Approved</span>';
        return '<span class="badge badge-warning">Not Approved</span>';
    }

    private function buildActionMenu(OrderMaster $order, ?User $user, bool $canView, bool $canEdit, bool $canDelete): string
    {
        $viewUrl   = route('orders.show', $order->id);
        $editUrl   = route('orders.edit', $order->id);
        $deleteUrl = route('orders.destroy', $order->id);
        $token     = csrf_token();

        // Distributor pending-approval override
        if ($user?->hasRole('distributor') && ($order->approval_level ?? 0) == 0
            && $order->distributor_id == $user->id && $order->user_id != $user->id
        ) {
            $approveUrl = route('orders.approve.distributor', $order->id);
            return $this->dropdownWrap("
                <a class='dropdown-item' href='{$viewUrl}'>View</a>
                <a class='dropdown-item' href='{$editUrl}'>Edit</a>
                <form method='POST' action='{$approveUrl}'><input type='hidden' name='_token' value='{$token}'>
                    <button type='submit' class='dropdown-item text-success'>Approve</button></form>
                <form method='POST' action='{$deleteUrl}'><input type='hidden' name='_token' value='{$token}'>
                    <input type='hidden' name='_method' value='DELETE'>
                    <button type='submit' class='dropdown-item text-danger deleteButton'>Delete</button></form>
            ");
        }

        // Super-admin final-approval override
        if ($user?->hasRole(['super-admin', 'superadmin']) && ($order->approval_level ?? 0) >= 1 && ($order->approval_level ?? 0) < 2) {
            $approveUrl = route('orders.approve.superadmin', $order->id);
            return $this->dropdownWrap("
                <a class='dropdown-item' href='{$viewUrl}'>View</a>
                <a class='dropdown-item' href='{$editUrl}'>Edit</a>
                <form method='POST' action='{$approveUrl}'><input type='hidden' name='_token' value='{$token}'>
                    <button type='submit' class='dropdown-item text-primary'>Super Admin Approve</button></form>
                <form method='POST' action='{$deleteUrl}'><input type='hidden' name='_token' value='{$token}'>
                    <input type='hidden' name='_method' value='DELETE'>
                    <button type='submit' class='dropdown-item text-danger deleteButton'>Delete</button></form>
            ");
        }

        // Default permission-based menu
        $items = '';
        if ($canView)   $items .= "<a class='dropdown-item' href='{$viewUrl}'>View</a>";
        if ($canEdit)   $items .= "<a class='dropdown-item' href='{$editUrl}'>Edit</a>";
        if ($canDelete) $items .= "<form method='POST' action='{$deleteUrl}'><input type='hidden' name='_token' value='{$token}'>
            <input type='hidden' name='_method' value='DELETE'>
            <button type='submit' class='dropdown-item text-danger deleteButton'>Delete</button></form>";

        return $this->dropdownWrap($items);
    }

    private function dropdownWrap(string $items): string
    {
        return "<div class='btn-group'>
            <button type='button' class='btn btn-sm' data-toggle='dropdown'>
                <i class='fas fa-ellipsis-v'></i>
            </button>
            <div class='dropdown-menu'>{$items}</div>
        </div>";
    }
}