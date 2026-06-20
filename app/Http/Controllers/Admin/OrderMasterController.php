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
use App\Models\ItemVariant;
use App\Models\InventoryLog;
use App\Models\Setting;
use App\Models\TaxMaster;

use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class OrderMasterController extends Controller
{
    // Add this property to prevent double deduction
    private $skipStockDeduction = false;
    // Add this property to track pending stock deductions
    private $pendingStockDeductions = [];

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
                    // Super-admin: no additional restrictions
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        $totalData = $query->count();
        $totalFiltered = $totalData;

        $limit  = (int) $request->input('length', 10);
        $start  = (int) $request->input('start', 0);
        $search = $request->input('search.value');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
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

        $auth = auth()->user();
        $canView = $auth?->hasPermission('order-view') ?? false;
        $canEdit = $auth?->hasPermission('order-edit') ?? false;
        $canDelete = $auth?->hasPermission('order-delete') ?? false;
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
                <div class="dropdown-menu">';

            if ($canView) {
                $actions .= '<a class="dropdown-item" href="' . $viewUrl . '">View</a>';
            }
            if ($canEdit) {
                $actions .= '<a class="dropdown-item" href="' . $editUrl . '">Edit</a>';
            }
            if ($canDelete) {
                $actions .= '
                    <form method="POST" action="' . $deleteUrl . '">
                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="dropdown-item text-danger deleteButton">
                            Delete
                        </button>
                    </form>';
            }

            $actions .= '</div></div>';

            try {
                $au = auth()->user();
                if ($au && $au->hasRole('distributor') && ($order->approval_level ?? 0) == 0 && $order->distributor_id == $au->id && $order->user_id != $au->id) {
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
                if ($au && $au->hasRole(['super-admin','superadmin']) && (($order->approval_level ?? 0) >= 1) && (($order->approval_level ?? 0) < 2)) {
                    $approveSuperUrl = route('orders.approve.superadmin', $order->id);
                    $actions = '<div class="btn-group">
                        <button type="button" class="btn btn-sm" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="' . $viewUrl . '">View</a>
                            <a class="dropdown-item" href="' . $editUrl . '">Edit</a>
                            <form method="POST" action="' . $approveSuperUrl . '">
                                <input type="hidden" name="_token" value="' . csrf_token() . '">
                                <button type="submit" class="dropdown-item text-primary">Super Admin Approve</button>
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
                'distributor_approved' => (function() use ($order) {
                    if (empty($order->distributor_id)) {
                        return '<span class="badge badge-secondary">N/A</span>';
                    }
                    $lvl = (int) ($order->approval_level ?? 0);
                    if ($lvl >= 2) {
                        return '<span class="badge badge-primary">Superadmin Approved</span>';
                    }
                    if ($lvl === 1) {
                        return '<span class="badge badge-success">Distributor Approved</span>';
                    }
                    return '<span class="badge badge-warning">Not Approved</span>';
                })(),
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
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('order-create')) {
            abort(403);
        }
        $data = $this->viewData();
        $data['terms'] = Setting::getValue('terms_and_conditions', '');

        $data['pre_item_id'] = $request->query('item_id');

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

                $sizesVal = null;
                $sizeQuantities = null;
                if (!empty($entry['size'])) {
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
                    'quantity' => $entry['qty'] ?? ($entry['quantity'] ?? 0),
                    'description' => $entry['description'] ?? ($itemModel?->description ?? ''),
                    'color' => $entry['color_id'] ?? $entry['color'] ?? null,
                    'sizes' => $sizesVal,
                    'size_quantities' => $sizeQuantities,
                ];
            }
            $data['pre_items'] = array_values($pre);
        }

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
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('order-create')) {
            abort(403);
        }
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date'    => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $fields = $this->orderFields($request);
            $authUser = auth()->user();

            if ($authUser && $authUser->hasRole('retailer')) {
                 $fields['distributor_id']        = $authUser->distributor_id;
                 $fields['distributor_approved']  = false;
                 $fields['approval_level']        = 0;
                $fields['visible_to_superadmin'] = false;
            } elseif ($authUser && $authUser->hasRole('distributor')) {
                $fields['distributor_id']           = $authUser->id;
                $fields['distributor_approved']     = true;
                $fields['distributor_approved_at']  = now();
                $fields['approval_level']           = 1;
                $fields['visible_to_superadmin']    = true;
            } elseif ($authUser && $authUser->hasRole(['super-admin', 'superadmin'])) {
                $fields['distributor_approved']     = true;
                $fields['distributor_approved_at']  = now();
                $fields['approval_level']           = 2;
                $fields['visible_to_superadmin']    = true;
            }
            elseif ($authUser && ($authUser->hasRole('super-admin') || $authUser->hasRole('superadmin'))) {
                $fields['approval_level'] = 2;
                $fields['distributor_id'] = $authUser->id;
                $fields['visible_to_superadmin'] = true;
            }
            
            // Handle tax selection
            if ($request->filled('tax_id')) {
                $tax = TaxMaster::find($request->tax_id);
                if ($tax) {
                    $fields['tax_id'] = $tax->id;
                    $fields['tax_percentage'] = $tax->tax_percentage;
                }
            }
            
            $order = OrderMaster::create($fields);
            $subtotal = $this->syncItems($order, $request->input('items', []));

            $itemStatuses = $order->items()->pluck('status')->filter()->toArray();
            $unique = array_values(array_unique($itemStatuses));
            if (count($unique) === 1 && !empty($unique[0])) {
                $orderStatus = $unique[0];
            } else {
                $orderStatus = $request->input('status', $order->status ?? 'pending');
            }

            $authUser = auth()->user();
            if ($authUser && $authUser->hasRole('retailer')) {
                $orderStatus = 'pending';
            }

            $allDelivered = !empty($itemStatuses) && count(array_filter($itemStatuses, fn($s) => $s !== 'delivered')) === 0;
            if ($orderStatus === 'delivered' && !$allDelivered) {
                DB::rollBack();
                return back()
                    ->withInput()
                    ->withErrors(['status' => 'Cannot set overall order status to Delivered unless all article statuses are Delivered.'])
                    ->with($this->viewData());
            }

            $order->update(array_merge($this->calculatedTotals($request, $subtotal), ['status' => $orderStatus]));

            DB::commit();

            if ($request->input('from_cart')) {
                session()->forget('cart');
            }
            return redirect()->route('orders.index')->with('success', 'Order created successfully.');
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
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('order-view')) {
            abort(403);
        }
        $order->load('items', 'customer', 'tax');

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

        $colorsById = Color::pluck('color_code', 'id');
        $colorNameById = Color::pluck('name', 'id');

        return view('admin.orders.show', compact('order', 'colorsById', 'colorNameById'));
    }

    // -----------------------------------------------------------------------
    // Edit / Update
    // -----------------------------------------------------------------------
    public function edit(OrderMaster $order)
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('order-edit')) {
            abort(403);
        }
        $order->load('items');

        $user = auth()->user();
        if ($user) {
            if ($user->hasRole('retailer') && $order->user_id !== $user->id) {
                abort(403);
            }
            if ($user->hasRole('retailer') && (($order->approval_level ?? 0) >= 1)) {
                return back()->with('error', 'Order has been approved and cannot be edited.');
            }
            if ($user->hasRole('distributor')) {
                $retailerIds = User::where('distributor_id', $user->id)->pluck('id')->toArray();
                if ($order->user_id !== $user->id && !in_array($order->user_id, $retailerIds)) {
                    abort(403);
                }
                if ((int)($order->approval_level ?? 0) >= 2) {
                    return back()->with('error', 'Order has been finalized by super-admin and cannot be edited.');
                }
            }
        }

        if (isset($order->status) && strtolower($order->status) === 'delivered') {
            return back()->with('error', 'Order has been delivered and cannot be edited.');
        }
        $data = array_merge(
            $this->viewData(),
            compact('order')
        );
        $data['terms'] = Setting::getValue('terms_and_conditions', '');

        return view('admin.orders.edit', $data);
    }

    public function update(Request $request, OrderMaster $order)
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('order-edit')) {
            abort(403);
        }
        $user = auth()->user();
        if ($user) {
            if ($user->hasRole('retailer') && $order->user_id !== $user->id) {
                abort(403);
            }
            if ($user->hasRole('retailer') && (($order->approval_level ?? 0) >= 1)) {
                return back()->with('error', 'Order has been approved and cannot be edited.');
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
            // Get existing order items BEFORE deleting them
            $existingItems = $order->items()->get()->keyBy('id');
            
            // Reset pending deductions
            $this->pendingStockDeductions = [];
            
            // Process status changes and handle stock deductions/restorations
            $itemsInput = $request->input('items', []);
            
            foreach ($itemsInput as $itemData) {
                if (isset($itemData['order_item_id']) && isset($itemData['status'])) {
                    $existingItem = $existingItems->get($itemData['order_item_id']);
                    if ($existingItem) {
                        $oldStatus = strtolower($existingItem->status);
                        $newStatus = strtolower($itemData['status']);
                        
                        $wasStockDeducted = in_array($oldStatus, ['confirmed', 'shipped', 'delivered']);
                        $willBeStockDeducted = in_array($newStatus, ['confirmed', 'shipped', 'delivered']);
                        $isCancelling = ($newStatus === 'canceled' || $newStatus === 'cancelled');
                        $isUnCancelling = ($oldStatus === 'canceled' || $oldStatus === 'cancelled') && 
                                        ($newStatus !== 'canceled' && $newStatus !== 'cancelled');
                        
                        // Case 1: Moving from non-deducted to deducted → DEDUCT
                        if (!$wasStockDeducted && $willBeStockDeducted && !$isUnCancelling) {
                            $this->pendingStockDeductions[] = [
                                'item' => $existingItem,
                                'status' => $newStatus
                            ];
                        }
                        
                        // Case 2: Moving from deducted to cancelled → RESTORE (do immediately)
                        if ($isCancelling && $wasStockDeducted) {
                            $this->restoreStockForOrderItem($existingItem);
                        }
                        
                        // Case 3: Moving from cancelled to deducted → DEDUCT
                        if ($isUnCancelling && $willBeStockDeducted) {
                            $this->pendingStockDeductions[] = [
                                'item' => $existingItem,
                                'status' => $newStatus
                            ];
                        }
                    }
                }
            }

            // Handle tax selection
            $orderFields = $this->orderFields($request);
            if ($request->filled('tax_id')) {
                $tax = TaxMaster::find($request->tax_id);
                if ($tax) {
                    $orderFields['tax_id'] = $tax->id;
                    $orderFields['tax_percentage'] = $tax->tax_percentage;
                }
            } else {
                $orderFields['tax_id'] = null;
                $orderFields['tax_percentage'] = null;
            }
            
            $order->update($orderFields);
            
            $authUser = auth()->user();
            if ($authUser && $authUser->hasRole('distributor') && empty($order->distributor_id)) {
                $order->distributor_id = $authUser->id;
                $order->save();
            }
            if ($authUser && ($authUser->hasRole('super-admin') || $authUser->hasRole('superadmin'))) {
                $order->distributor_id = $authUser->id;
                $order->approval_level = 2;
                $order->visible_to_superadmin = true;
                $order->save();
            }

            // Delete old items
            $order->items()->delete();
            
            // Set flag to prevent syncItems from deducting stock
            $this->skipStockDeduction = true;
            
            // Recreate items with new data
            $subtotal = $this->syncItems($order, $request->input('items', []));

            // Now process pending stock deductions for items that need it
            if (!empty($this->pendingStockDeductions)) {
                // Get the newly created items
                $newItems = $order->items()->get();
                
                foreach ($this->pendingStockDeductions as $pending) {
                    $existingItem = $pending['item'];
                    $newStatus = $pending['status'];
                    
                    // Find the matching new item (by item_id and color/size)
                    $newItem = $newItems->first(function($item) use ($existingItem) {
                        return $item->item_id == $existingItem->item_id && 
                               $item->color == $existingItem->color &&
                               $item->size == $existingItem->size;
                    });
                    
                    if ($newItem) {
                        // Update the new item's status to what it should be
                        $newItem->status = $newStatus;
                        $newItem->save();
                        
                        // Deduct stock for this item
                        $this->deductStockForOrderItem($newItem);
                    }
                }
            }

            // Determine order status from items
            $itemStatuses = $order->items()->pluck('status')->filter()->toArray();
            $unique = array_values(array_unique($itemStatuses));
            if (count($unique) === 1 && !empty($unique[0])) {
                $orderStatus = $unique[0];
            } else {
                $orderStatus = $request->input('status', $order->status ?? 'pending');
            }

            $authUser = auth()->user();
            if ($authUser && $authUser->hasRole('retailer')) {
                $orderStatus = $order->status ?? 'pending';
            }

            $allDelivered = !empty($itemStatuses) && count(array_filter($itemStatuses, fn($s) => $s !== 'delivered')) === 0;
            if ($orderStatus === 'delivered' && !$allDelivered) {
                DB::rollBack();
                $order->load('items');
                return back()
                    ->withInput()
                    ->withErrors(['status' => 'Cannot set overall order status to Delivered unless all article statuses are Delivered.'])
                    ->with(array_merge($this->viewData(), compact('order')));
            }

            $order->update(array_merge($this->calculatedTotals($request, $subtotal), ['status' => $orderStatus]));

            DB::commit();

            return redirect()->route('orders.index')->with('success', 'Order updated successfully.');
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

        if (empty($order->distributor_id) || $order->distributor_id != $user->id) {
            abort(403);
        }

        if (($order->approval_level ?? 0) >= 1) {
            return redirect()->back()->with('info', 'Order already approved by distributor.');
        }

        $order->update([
            'distributor_approved' => true,
            'distributor_approved_at' => now(),
            'approval_level' => 1,
            'visible_to_superadmin' => true,
        ]);

        return redirect()->back()->with('success', 'Order approved and sent to superadmin.');
    }

    /**
     * Super-admin finalizes an order (sets approval_level = 2).
     */
    public function approveBySuperAdmin(Request $request, OrderMaster $order)
    {
        $user = auth()->user();
        if (!$user || !($user->hasRole('super-admin') || $user->hasRole('superadmin'))) {
            abort(403);
        }

        if (($order->approval_level ?? 0) >= 2) {
            return redirect()->back()->with('info', 'Order already finalized by super-admin.');
        }

        $order->update([
            'approval_level' => 2,
            'visible_to_superadmin' => true,
        ]);

        return redirect()->back()->with('success', 'Order finalized by super-admin.');
    }

    // -----------------------------------------------------------------------
    // Destroy
    // -----------------------------------------------------------------------
    public function destroy(OrderMaster $order)
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('order-delete')) {
            abort(403);
        }
        $user = auth()->user();
        if ($user && $user->hasRole(['retailer', 'distributor']) && $order->user_id !== $user->id) {
            return redirect()->back()->with('error', 'You are not allowed to delete this order.');
        }

        $order->load('items');

        foreach ($order->items as $item) {
            $wasStockDeducted = in_array($item->status, ['confirmed', 'shipped', 'delivered']);
            if ($wasStockDeducted) {
                try {
                    $this->restoreStockForOrderItem($item);
                } catch (\Exception $e) {
                    \Log::error("Failed to restore stock for order item #{$item->id} during order deletion: " . $e->getMessage());
                }
            }
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
        $items     = Item::with(['variants.color'])->orderBy('name')->get();
        $taxes     = TaxMaster::orderBy('tax_percentage')->get();

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
            'taxes'         => $taxes,
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
            'markdown',
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

            $variantColors = $item->variants
                ->map(fn ($variant) => $variant->color)
                ->filter()
                ->unique('id')
                ->values();

            $itemColors = $variantColors->isNotEmpty() ? $variantColors : $item->colors;

            return [
                'id'             => $item->id,
                'article_number' => $item->article_number ?? $item->sku ?? '',
                'name'           => $item->name,
                'unit'           => $item->unit ?? '',
                'rate'           => $item->price ?? 0,
                'desc'           => $item->description ?? '',
                'colors'         => $itemColors->map(fn ($color) => [
                    'id'         => $color->id,
                    'name'       => $color->name,
                    'color_code' => $color->color_code,
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

            $articleNumber = $it['article_number'] ?? null;
            if (empty($articleNumber) && $itemId) {
                $itemModel = Item::find($itemId);
                $articleNumber = $itemModel?->article_number ?? $itemModel?->sku ?? null;
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
            // REMOVED: Individual item tax calculation
            // Tax is now applied at order level only
            $total = round($rate * $quantity, 2);
            $subtotal += $total;

            $status = $it['status'] ?? 'pending';
            if ($authUser && $authUser->hasRole('retailer')) {
                $status = 'pending';
            }

            $orderItem = $order->items()->create([
                'article_number' => $articleNumber ?? null,
                'item_id'     => $itemId  ?: null,
                'item_name'   => $itemName,
                'description' => $it['description'] ?? null,
                'color'       => $this->normalizeColors($it),
                'size'        => $sizeVal,
                'size_quantities' => !empty($sizeQuantities) ? $sizeQuantities : null,
                'quantity'    => $quantity,
                'rate'        => $rate,
                'total'       => $total,
                'status'      => $status,
                'size_from'   => $it['size_from']   ?? null,
                'size_to'     => $it['size_to']     ?? null,
                'sets'        => $it['sets']        ?? null,
            ]);

            $shouldDeduct = in_array(strtolower($status), ['confirmed', 'shipped', 'delivered'], true);
            $isCancelled = in_array(strtolower($status), ['canceled', 'cancelled']);
            
            if ($shouldDeduct && !$this->skipStockDeduction && !$isCancelled) {
                $this->deductStockForOrderItem($orderItem);
            }
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
        $markdownPercent = (float) $request->input('markdown', 0);
        $markdownAmount = $subtotal * $markdownPercent / 100;
        $afterMarkdown = $subtotal - $markdownAmount;
        $discountPercent = (float) $request->input('discount', 0);
        $discountAmount = $afterMarkdown * $discountPercent / 100;
        $adjustment = (float) $request->input('adjustment', 0);
        
        // Get tax percentage from the selected tax
        $taxPercentage = 0;
        if ($request->filled('tax_id')) {
            $tax = TaxMaster::find($request->tax_id);
            if ($tax) {
                $taxPercentage = $tax->tax_percentage;
            }
        }
        
        // Calculate after markdown and discount, then apply tax
        $afterDiscount = $afterMarkdown - $discountAmount;
        $taxAmount = $afterDiscount * $taxPercentage / 100;
        $grandTotal = $afterDiscount + $taxAmount + $adjustment;

        return [
            'subtotal' => round($subtotal, 2),
            'markdown' => round($markdownPercent, 2),
            'discount' => round($discountPercent, 2),
            'grand_total' => round($grandTotal, 2),
        ];
    }

    private function deductStockForOrderItem(\App\Models\OrderItem $orderItem): void
    {
        DB::transaction(function () use ($orderItem) {
            $itemId = $orderItem->item_id;

            $colorIds = array_values(array_filter(array_map('trim', explode(',', (string) ($orderItem->color ?? ''))), fn ($v) => $v !== ''));
            $colorId = $colorIds[0] ?? null;

            $sizeQuantities = is_array($orderItem->size_quantities) ? $orderItem->size_quantities : [];

            if (empty($sizeQuantities)) {
                \Log::warning('No size quantities found for order item #' . $orderItem->id);
                return;
            }

            foreach ($sizeQuantities as $sizeLabel => $orderQty) {
                $sizeLabel = (string) $sizeLabel;
                $orderQty = (int) $orderQty;
                if ($orderQty <= 0) {
                    continue;
                }

                $size = Size::where('name', $sizeLabel)->first();
                if (!$size) {
                    throw new \Exception("Size '{$sizeLabel}' not found");
                }

                $variant = ItemVariant::where('item_id', $itemId)
                    ->where('color_id', $colorId)
                    ->where('size_id', $size->id)
                    ->first();

                if (!$variant) {
                    throw new \Exception("Variant not found for item #{$itemId} color #{$colorId} size {$sizeLabel}");
                }

                // Check if enough stock is available
                if ($variant->quantity < $orderQty) {
                    throw new \Exception(
                        "Insufficient stock for size {$sizeLabel}. Available: {$variant->quantity}, Ordered: {$orderQty}"
                    );
                }

                // Update variant table columns
                $variant->sold_quantity = $variant->sold_quantity + $orderQty;
                $variant->quantity = $variant->quantity - $orderQty;  // Current stock
                $variant->save();

                // Create inventory log
                InventoryLog::create([
                    'type' => 'deduct',
                    'qty' => $orderQty,
                    'item_variant_id' => $variant->id,
                    'order_master_id' => $orderItem->order_master_id,
                    'note' => 'Order confirmed - stock deducted',
                    'created_by' => auth()->id(),
                ]);

                \Log::info("Stock deducted for variant ID {$variant->id}: -{$orderQty}");
            }
        });
    }

    private function restoreStockForOrderItem(\App\Models\OrderItem $orderItem): void
    {
        DB::transaction(function () use ($orderItem) {
            $itemId = $orderItem->item_id;
            
            $colorIds = array_values(array_filter(array_map('trim', explode(',', (string) ($orderItem->color ?? ''))), fn ($v) => $v !== ''));
            
            if (empty($colorIds)) {
                \Log::warning('No color found for order item #' . $orderItem->id);
                return;
            }
            
            $colorId = $colorIds[0] ?? null;
            $sizeQuantities = is_array($orderItem->size_quantities) ? $orderItem->size_quantities : [];
            
            if (empty($sizeQuantities)) {
                // Legacy format handling
                $singleQty = (int) $orderItem->quantity;
                if ($singleQty <= 0) {
                    return;
                }
                
                $size = Size::where('name', $orderItem->size)->first();
                if (!$size) {
                    \Log::error("Size '{$orderItem->size}' not found");
                    return;
                }
                
                $variant = ItemVariant::where('item_id', $itemId)
                    ->where('color_id', $colorId)
                    ->where('size_id', $size->id)
                    ->first();
                        
                if (!$variant) {
                    \Log::error("Variant not found");
                    return;
                }
                
                // Update variant table columns
                $variant->sold_quantity = $variant->sold_quantity - $singleQty;
                $variant->quantity = $variant->quantity + $singleQty;  // Current stock
                $variant->save();
                
                InventoryLog::create([
                    'type' => 'restore',
                    'qty' => $singleQty,
                    'item_variant_id' => $variant->id,
                    'order_master_id' => $orderItem->order_master_id,
                    'note' => 'Stock restored - order item cancelled',
                    'created_by' => auth()->id(),
                ]);
                
                return;
            }
            
            // Process each size quantity
            foreach ($sizeQuantities as $sizeLabel => $orderQty) {
                $sizeLabel = (string) $sizeLabel;
                $orderQty = (int) $orderQty;
                if ($orderQty <= 0) {
                    continue;
                }
                
                $size = Size::where('name', $sizeLabel)->first();
                if (!$size) {
                    \Log::error("Size '{$sizeLabel}' not found");
                    continue;
                }
                
                $variant = ItemVariant::where('item_id', $itemId)
                    ->where('color_id', $colorId)
                    ->where('size_id', $size->id)
                    ->first();
                        
                if (!$variant) {
                    \Log::error("Variant not found");
                    continue;
                }
                
                // Update variant table columns
                $variant->sold_quantity = $variant->sold_quantity - $orderQty;
                $variant->quantity = $variant->quantity + $orderQty;  // Current stock
                $variant->save();
                
                InventoryLog::create([
                    'type' => 'restore',
                    'qty' => $orderQty,
                    'item_variant_id' => $variant->id,
                    'order_master_id' => $orderItem->order_master_id,
                    'note' => 'Stock restored - order item cancelled',
                    'created_by' => auth()->id(),
                ]);
            }
        });
    }

    public function checkStock(Request $request)
    {
        $request->validate([
            'order_item_id' => 'required|integer|exists:order_items,id',
        ]);

        $orderItem = OrderItem::findOrFail($request->input('order_item_id'));

        $itemId = $orderItem->item_id;
        $colorIds = array_values(array_filter(array_map('trim', explode(',', (string) ($orderItem->color ?? ''))), fn ($v) => $v !== ''));
        $colorId = $colorIds[0] ?? null;

        $sizeQuantities = is_array($orderItem->size_quantities) ? $orderItem->size_quantities : [];
        $issues = [];

        foreach ($sizeQuantities as $sizeLabel => $orderQty) {
            $sizeLabel = (string) $sizeLabel;
            $orderQty = (int) $orderQty;
            if ($orderQty <= 0) {
                continue;
            }

            $size = Size::where('name', $sizeLabel)->first();
            if (!$size) {
                $issues[] = [
                    'size'     => $sizeLabel,
                    'available' => 0,
                    'ordered'  => $orderQty,
                    'message'  => "Size '{$sizeLabel}' not found",
                ];
                continue;
            }

            $variant = ItemVariant::where('item_id', $itemId)
                ->where('color_id', $colorId)
                ->where('size_id', $size->id)
                ->first();

            if (!$variant) {
                $issues[] = [
                    'size'     => $sizeLabel,
                    'available' => 0,
                    'ordered'  => $orderQty,
                    'message'  => "Variant not found",
                ];
                continue;
            }

            // Use quantity (current stock) directly
            $available = (int) $variant->quantity;
            if ($available < $orderQty) {
                $issues[] = [
                    'size'     => $sizeLabel,
                    'available' => $available,
                    'ordered'  => $orderQty,
                    'message'  => "Only {$available} available for size {$sizeLabel}",
                ];
            }
        }

        return response()->json([
            'ok'     => empty($issues),
            'issues' => $issues,
        ]);
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

        $oldStatus = strtolower((string) $orderItem->status);
        $newStatus = strtolower((string) $request->input('status'));

        // Check if the old status represents a "stock deducted" state
        $wasStockDeducted = in_array($oldStatus, ['confirmed', 'shipped', 'delivered']);
        
        // Check if the new status represents a "stock deducted" state
        $willBeStockDeducted = in_array($newStatus, ['confirmed', 'shipped', 'delivered']);
        
        // Check if we are canceling (any status -> cancelled)
        $isCancelling = ($newStatus === 'canceled' || $newStatus === 'cancelled');
        
        // Check if we are moving from cancelled to something else
        $isUnCancelling = ($oldStatus === 'canceled' || $oldStatus === 'cancelled') && 
                        ($newStatus !== 'canceled' && $newStatus !== 'cancelled');
        
        // Center checkpoint: once stock is deducted, cannot go back to pending/draft
        if ($wasStockDeducted && in_array($newStatus, ['pending', 'draft'])) {
            return response()->json([
                'success' => false,
                'message' => "Invalid status transition from {$oldStatus} to {$newStatus}",
            ], 422);
        }
        
        // Must be confirmed before reaching higher levels
        if (in_array($newStatus, ['shipped', 'delivered'], true) && $oldStatus !== 'confirmed') {
            return response()->json([
                'success' => false,
                'message' => "Cannot move to {$newStatus} unless current status is confirmed.",
            ], 422);
        }
        
        // pending -> shipped not allowed
        if ($oldStatus === 'pending' && $newStatus === 'shipped') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot move from pending to shipped. Confirmed is required first.',
            ], 422);
        }
        
        // ========== STOCK RESTORATION: Cancelling an item ==========
        // If moving to cancelled AND stock was previously deducted, restore stock
        if ($isCancelling && $wasStockDeducted) {
            try {
                $this->restoreStockForOrderItem($orderItem);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
        }
        
        // ========== STOCK DEDUCTION: Moving from cancelled to active ==========
        // If moving from cancelled to any status that requires stock deduction
        if ($isUnCancelling && $willBeStockDeducted) {
            try {
                $this->deductStockForOrderItem($orderItem);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
        }
        
        // ========== STOCK DEDUCTION: Moving from non-deducted to deducted ==========
        // If moving from non-deducted to a status that requires deduction (e.g., draft -> confirmed)
        if (!$wasStockDeducted && $willBeStockDeducted && !$isUnCancelling) {
            try {
                $this->deductStockForOrderItem($orderItem);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
        }
        
        // ========== UPDATE STATUS ==========
        $orderItem->status = $newStatus;
        $orderItem->save();

        
        $this->updateOrderStatusFromItems($order);

        // Log the status change
        \Log::info("Order item #{$orderItem->id} status changed from {$oldStatus} to {$newStatus}", [
            'order_id' => $order->id,
            'item_id' => $orderItem->item_id,
            'user_id' => auth()->id()
        ]);

        return response()->json(['success' => true, 'status' => $orderItem->status]);
    }

    private function updateOrderStatusFromItems(OrderMaster $order)
    {
        $itemStatuses = $order->items()->pluck('status')->filter()->toArray();
        $unique = array_values(array_unique($itemStatuses));
        
        if (count($unique) === 1 && !empty($unique[0])) {
            $order->status = $unique[0];
            $order->save();
        }
    }
}