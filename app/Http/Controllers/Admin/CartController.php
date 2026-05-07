<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;

class CartController extends Controller
{
    // Show cart items stored in session
    public function index(Request $request)
    {
        $cart = session()->get('cart', []);

        $items = collect($cart)->map(function ($entry, $id) {
            return array_merge(['id' => $id], $entry);
        })->values();

        $subtotal = $items->reduce(function ($carry, $i) {
            return $carry + ($i['price'] * $i['qty']);
        }, 0);

        return view('admin.cart.index', compact('items', 'subtotal'));
    }

    // Add item to cart (session)
    public function add(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer|exists:items,id',
            'qty' => 'sometimes|integer|min:1',
            'color_id' => 'sometimes|nullable|integer',
            'size' => 'sometimes|nullable|string'
        ]);

        $item = Item::find($request->input('item_id'));
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item not found'], 404);
        }

        $qty = max(1, (int) $request->input('qty', 1));

        $colorId = $request->input('color_id');
        $size = $request->input('size');

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

        if ($foundKey) {
            $cart[$foundKey]['qty'] += $qty;
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
                'size' => $size,
            ];
        }

        session()->put('cart', $cart);

        $count = array_sum(array_column($cart, 'qty'));
        $subtotal = array_reduce($cart, function ($carry, $i) { return $carry + ($i['price'] * $i['qty']); }, 0);

        return response()->json(['success' => true, 'count' => $count, 'subtotal' => $subtotal]);
    }

    // Update item quantity — accepts both resource-style (PUT /cart/{id}) and JSON body with item_id
    public function update(Request $request, $id = null)
    {
        // update by cart entry key (route param) or by item_id in body
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
        // remove by entry key
        $entryKey = $request->input('entry_key') ?: null;
        $cart = session()->get('cart', []);

        if ($entryKey && isset($cart[$entryKey])) {
            unset($cart[$entryKey]);
            session()->put('cart', $cart);
        }

        $count = array_sum(array_column($cart, 'qty')) ?: 0;
        $subtotal = array_reduce($cart, function ($carry, $i) { return $carry + ($i['price'] * $i['qty']); }, 0);

        return response()->json(['success' => true, 'count' => $count, 'subtotal' => $subtotal]);
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
