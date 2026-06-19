<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Color;
use App\Models\Size;
use App\Models\ItemVariant;

class CartController extends Controller
{

    private function findVariant($itemId, $colorId, $sizeName)
    {
        if (!$itemId || !$colorId || !$sizeName) {
            return null;
        }

        $sizeModel = Size::where('name', $sizeName)->first();
        if (!$sizeModel) {
            return null;
        }

        return ItemVariant::where('item_id', $itemId)
            ->where('color_id', $colorId)
            ->where('size_id', $sizeModel->id)
            ->first();
    }

    private function stockError($itemId, $colorId, $sizeName, $qty)
    {
        if (!$colorId || !$sizeName) {
            // No variant selection (e.g. item without color/size options) — nothing to check.
            return null;
        }

        $sizeModel = Size::where('name', $sizeName)->first();
        if (!$sizeModel) {
            return "Size '{$sizeName}' not found.";
        }

        $variant = $this->findVariant($itemId, $colorId, $sizeName);
        if (!$variant) {
            return "Variant not found for this color and size combination.";
        }

        if ($qty > $variant->current_stock) {
            return "Only {$variant->current_stock} units available for this size.";
        }

        return null;
    }

    // Show cart items stored in session
    public function index(Request $request)
    {
        $cart = session()->get('cart', []);

        // preload color and size names to display in cart
        $colorIds = array_values(array_filter(array_map(function($e){ return $e['color_id'] ?? null; }, $cart)));
        $colors = Color::whereIn('id', $colorIds)->get()->keyBy('id');

        $items = collect($cart)->map(function ($entry, $id) use ($colors) {
            $out = array_merge(['id' => $id], $entry);
            
            if (!empty($entry['color_id'])) {
                $c = $colors->get($entry['color_id']);
                $out['color_name'] = $c ? $c->name : null;
                $out['color_code'] = $c ? $c->color_code : null;
            }
            
            // size may be stored as string or array; try to keep a human-friendly label
            if (!empty($entry['size'])) {
                $out['size_name'] = is_array($entry['size']) ? implode(', ', $entry['size']) : $entry['size'];
            }
            
            // Get max stock for this variant
            if (!empty($entry['item_id']) && !empty($entry['color_id']) && !empty($entry['size'])) {
                $variant = $this->findVariant($entry['item_id'], $entry['color_id'], $entry['size']);
                if ($variant) {
                    $out['max_stock'] = $variant->current_stock;
                }
            }
            
            return $out;
        })->values();

        $subtotal = $items->reduce(function ($carry, $i) {
            return $carry + ($i['price'] * $i['qty']);
        }, 0);

        return view('admin.cart.index', compact('items', 'subtotal'));
    }

    // Add item to cart (session)
    public function add(Request $request)
    {
        // Log the request for debugging
        \Log::info('Cart Add Request', [
            'all' => $request->all(),
            'item_id' => $request->input('item_id'),
            'color_id' => $request->input('color_id'),
            'size' => $request->input('size'),
            'qty' => $request->input('qty')
        ]);

        $request->validate([
            'item_id' => 'required|integer|exists:items,id',
            'qty' => 'sometimes|integer|min:1',
            'color_id' => 'sometimes|nullable|integer|exists:colors,id',
            'size' => 'sometimes|nullable|string'
        ]);

        $item = Item::find($request->input('item_id'));
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item not found'], 404);
        }

        $qty = max(1, (int) $request->input('qty', 1));
        $colorId = $request->input('color_id');
        $size = $request->input('size');

        // Stock validation
        $error = $this->stockError($item->id, $colorId, $size, $qty);
        if ($error) {
            return response()->json(['success' => false, 'message' => $error], 422);
        }

        $cart = session()->get('cart', []);

        // Try to find existing entry with same item_id + color + size
        $foundKey = null;
        foreach ($cart as $key => $entry) {
            if (isset($entry['item_id']) && $entry['item_id'] == $item->id
                && (isset($entry['color_id']) ? $entry['color_id'] == $colorId : $colorId == null)
                && (isset($entry['size']) ? $entry['size'] == $size : $size == null)) {
                $foundKey = $key;
                break;
            }
        }

        // Get color and size info for display
        $color = $colorId ? Color::find($colorId) : null;

        if ($foundKey) {
            $newQty = $cart[$foundKey]['qty'] + $qty;

            // Check stock again for the combined quantity
            $error = $this->stockError($item->id, $colorId, $size, $newQty);
            if ($error) {
                return response()->json(['success' => false, 'message' => $error], 422);
            }

            $cart[$foundKey]['qty'] = $newQty;
        } else {
            $entryKey = uniqid('c_');
            $cart[$entryKey] = [
                'entry_id' => $entryKey,
                'item_id' => $item->id,
                'name' => $item->name,
                'price' => (float) $item->price,
                'image' => $item->image_urls[0] ?? null,
                'qty' => $qty,
                'color_id' => $colorId,
                'color_name' => $color ? $color->name : null,
                'color_code' => $color ? $color->color_code : null,
                'size' => $size,
                'size_name' => $size,
                'tax_rate' => $item->tax_percent ?? 0,
            ];
        }

        session()->put('cart', $cart);

        $count = array_sum(array_column($cart, 'qty'));
        $subtotal = array_reduce($cart, function ($carry, $i) { 
            return $carry + ($i['price'] * $i['qty']); 
        }, 0);

        return response()->json([
            'success' => true, 
            'count' => $count, 
            'subtotal' => $subtotal,
            'cart' => $cart
        ]);
    }

    // Update item quantity
    public function update(Request $request, $id = null)
    {
        $cart = session()->get('cart', []);

        $entryKey = $id;
        if (!$entryKey) {
            $entryKey = $request->input('entry_key');
        }

        $request->validate([
            'qty' => 'required|integer|min:0'
        ]);

        $qty = (int) $request->input('qty');

        if (!$entryKey || !isset($cart[$entryKey])) {
            return response()->json(['success' => false, 'message' => 'Item not in cart'], 404);
        }

        // Check stock limit
        if ($qty > 0) {
            $entry = $cart[$entryKey];
            $error = $this->stockError($entry['item_id'] ?? null, $entry['color_id'] ?? null, $entry['size'] ?? null, $qty);
            if ($error) {
                return response()->json(['success' => false, 'message' => $error], 422);
            }
        }

        if ($qty <= 0) {
            unset($cart[$entryKey]);
        } else {
            $cart[$entryKey]['qty'] = $qty;
        }

        session()->put('cart', $cart);

        $count = array_sum(array_column($cart, 'qty')) ?: 0;
        $subtotal = array_reduce($cart, function ($carry, $i) { return $carry + ($i['price'] * $i['qty']); }, 0);

        return response()->json(['success' => true, 'count' => $count, 'subtotal' => $subtotal]);
    }

    // Remove item from cart
    public function remove(Request $request)
    {
        $entryKey = $request->input('entry_key') ?: $request->input('item_id');
        $cart = session()->get('cart', []);

        if ($entryKey && isset($cart[$entryKey])) {
            unset($cart[$entryKey]);
            session()->put('cart', $cart);
        }

        $count = array_sum(array_column($cart, 'qty')) ?: 0;
        $subtotal = array_reduce($cart, function ($carry, $i) { return $carry + ($i['price'] * $i['qty']); }, 0);

        return response()->json(['success' => true, 'count' => $count, 'subtotal' => $subtotal]);
    }

    // Clear entire cart
    public function clear(Request $request)
    {
        session()->forget('cart');
        return response()->json(['success' => true, 'message' => 'Cart cleared successfully']);
    }

    public function store(Request $request)
    {
        return $this->add($request);
    }

    public function updateResource(Request $request, $id = null)
    {
        if ($id) {
            $request->merge(['item_id' => $id]);
        }
        return $this->update($request);
    }

    public function destroy($id)
    {
        $req = new Request(['item_id' => $id]);
        return $this->remove($req);
    }

    public function create() 
    {
         return redirect()->route('catalog');
    }
    
    public function show($id) 
    {
         return redirect()->route('catalog.show', $id); 
    }
    
    public function edit($id) 
    {
         return redirect()->route('cart.index'); 
    }
}